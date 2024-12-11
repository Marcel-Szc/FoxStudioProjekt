<?php 
// Włączenie biblioteki FPDF do generowania PDF
require('fpdf/tfpdf.php');
// Włączenie pliku z połączeniem do bazy danych
include_once('polaczenie.php');
// Rozpoczęcie sesji
session_start();

// Inicjalizacja zmiennej do inkrementacji
$increment = 1;
// Inicjalizacja tablicy do przechowywania danych
$data = []; 

// Sprawdzenie, czy w sesji istnieje numer oferty
if (isset($_SESSION['nr_oferty'])) {
    // Przypisanie numeru oferty z sesji
    $nr_oferty = $_SESSION['nr_oferty'];
    
    // Przygotowanie zapytania do pobrania ceny i marży z bazy danych
    $cenaBaza = $polaczenie->prepare("SELECT cena, marza FROM ceny WHERE nr_oferty = ?");
    if (!$cenaBaza) {
        die("Database query failed: " . $polaczenie->error);
    }
    // Powiązanie parametru z zapytaniem
    $cenaBaza->bind_param("i", $nr_oferty);
    // Wykonanie zapytania
    $cenaBaza->execute();
    // Pobranie wyniku zapytania
    $wynikCena = $cenaBaza->get_result();
    // Pobranie danych o cenie i marży
    $cenaFetch = $wynikCena->fetch_assoc();

    // Przygotowanie zapytania do pobrania danych o znakowaniu
    $znakowanie = $polaczenie->prepare("SELECT pozycja_znakowania, technologia_znakowania, ilosc_kolorow, kolor, wplyw_na_cene FROM ZNAKOWANIE WHERE nr_oferty = ?");
    if (!$znakowanie) {
        die("Database query failed: " . $polaczenie->error);
    }
    // Powiązanie parametru z zapytaniem
    $znakowanie->bind_param("i", $nr_oferty);
    // Wykonanie zapytania
    $znakowanie->execute();
    // Pobranie wyniku zapytania
    $wynikZnakowanie = $znakowanie->get_result();

    // Pętla do przetwarzania danych o znakowaniu
    while ($znak = $wynikZnakowanie->fetch_assoc()) {
        // Dodanie danych do tablicy
        $data[] = [
            'pozycja' => $znak['pozycja_znakowania'],
            'technologia' => $znak['technologia_znakowania'],
            'ilosc' => $znak['ilosc_kolorow'],
            'kolor' => $znak['kolor'],
            'cena' => intval($cenaFetch['cena']) + intval($znak['wplyw_na_cene']),
            'nr_oferty' => $nr_oferty
        ];
    }

    // Przygotowanie zapytania do pobrania danych o produkcie
    $produkt = $polaczenie->prepare("SELECT nazwa_produktu, kod_produktu FROM produkty WHERE nr_oferty = ?");
    if (!$produkt) {
        die("Database query failed: " . $polaczenie->error);
    }
    // Powiązanie parametru z zapytaniem
    $produkt->bind_param("i", $nr_oferty);
    // Wykonanie zapytania
    $produkt->execute();
    // Pobranie wyniku zapytania
    $wynik = $produkt->get_result();
    // Pobranie danych o produkcie
    $produkty = $wynik->fetch_assoc();

    // Inicjalizacja obiektu PDF
    $pdf = new tFPDF();
    // Dodanie nowej strony do PDF
    $pdf->AddPage();
    // Dodanie czcionki do PDF
    $pdf->AddFont('DejaVu','','DejaVuSansCondensed-Bold.ttf',true);
    // Ustawienie czcionki
    $pdf->SetFont('DejaVu','',11);
    // Dodanie nazwy produktu do PDF
    $pdf->Cell(0, 10, "Nazwa produktu: ".$produkty['nazwa_produktu'], 0, 1);
    // Dodanie kodu produktu do PDF
    $pdf->Cell(0, 10, "Kod produktu: ".$produkty['kod_produktu'], 0, 1);
    // Ustawienie czcionki na mniejszą
    $pdf->SetFont('DejaVu','',9);
    // Sprawdzenie, czy marża jest dostępna
    if ($cenaFetch['marza'] != NULL) {
        // Przygotowanie tekstu marży
        $marza = "Marza: ".$cenaFetch['marza'];
        // Dodanie marży do PDF
        $pdf->Cell(0, 10, "Marza produktu: ".$marza."%", 1, 1);
    }
    // Dodanie odstępu
    $pdf->Ln(10);
    // Ustalenie katalogu z obrazami
    $directory = 'uploads/'; 
    // Inicjalizacja zmiennej do inkrementacji
    $increment = 1; 

    // Inicjalizacja tablicy do przechowywania danych obrazów
    $imageData = []; 

    // Pętla do przetwarzania obrazów
    while ($increment <= 100) { 
        // Wzorzec do wyszukiwania obrazów
        $imagePattern = $directory . $increment . 'temp_image_*.jpg';
        // Pobranie plików pasujących do wzorca
        $files = glob($imagePattern); 

        // Sprawdzenie, czy znaleziono jakiekolwiek pliki
        if (!empty($files)) {
            // Pętla do przetwarzania każdego obrazu
            foreach ($files as $imagePath) { 
                // Pobranie rozmiarów oryginalnego obrazu
                list($originalWidth, $originalHeight) = getimagesize($imagePath);
                
                // Ustalenie pożądanej szerokości
                $desiredWidth = 37; 
                // Obliczenie proporcji
                $aspectRatio = $originalHeight / $originalWidth;
                
                // Obliczenie nowej wysokości
                $newHeight = $desiredWidth * $aspectRatio;

                // Sprawdzenie, czy nowa wysokość nie przekracza 100
                if ($newHeight > 100) {
                    $newHeight = 100;
                    $desiredWidth = $newHeight / $aspectRatio;
                }

                // Dodanie danych obrazu do tablicy
                $imageData[] = [
                    'imagePath' => $imagePath,
                    'desiredWidth' => $desiredWidth,
                    'newHeight' => $newHeight
                ];
            }
        }

        // Inkrementacja zmiennej
        $increment++; 
    }

    // Inicjalizacja indeksu obrazów
    $imageIndex = 0; 
    // Inicjalizacja licznika iteracji
    $iterationCount = 0;
    // Pobranie wysokości strony PDF
    $pageHeight = $pdf->GetPageHeight();
    // Ustalenie dolnego marginesu
    $bottomMargin = 10; 

    // Pętla do przetwarzania danych o znakowaniu
    foreach ($data as $item) {
        // Obliczenie wysokości elementu
        $itemHeight = 10 + 10 + 10 + 10 + 10 + 5; 
        // Sprawdzenie, czy istnieje obraz do dodania
        if (isset($imageData[$imageIndex])) {
            $itemHeight += $imageData[$imageIndex]['newHeight'] + 5;
        }

        // Sprawdzenie, czy wysokość elementu przekracza wysokość strony
        if ($pdf->GetY() + $itemHeight > $pageHeight - $bottomMargin) {
            // Dodanie nowej strony
            $pdf->AddPage();
        }

        // Dodanie nagłówka dla danych znakowania
        $pdf->Cell(0, 10, "Dane znakowania: ", 0, 1);
        // Dodanie pozycji znakowania
        $pdf->Cell(0, 10, "Pozycja: " . (isset($item['pozycja']) ? $item['pozycja'] : 'N/A'), 1, 1);
        // Dodanie technologii znakowania
        $pdf->Cell(0, 10, "Technologia: " . (isset($item['technologia']) ? $item['technologia'] : 'N/A'), 1, 1);
        // Dodanie ilości kolorów
        $pdf->Cell(0, 10, "Ilość kolorów: " . (isset($item['ilosc']) ? $item['ilosc'] : 'N/A'),1, 1,);
        // Dodanie koloru
        $pdf->Cell(0, 10, "Kolor: " . (isset($item['kolor']) ? $item['kolor'] : 'N/A'), 1, 1);
        // Dodanie ceny
        $pdf->Cell(0, 10, "Cena: " . (isset($item['cena']) ? $item['cena'] : 'N/A') . "zł", 1, 1);
        // Dodanie odstępu
        $pdf->Ln(5);

        // Sprawdzenie, czy istnieje obraz do dodania
        if (isset($imageData[$imageIndex])) {
            // Dodanie obrazu do PDF
            $pdf->Image($imageData[$imageIndex]['imagePath'], 10, $pdf->GetY(), $imageData[$imageIndex]['desiredWidth'], $imageData[$imageIndex]['newHeight'], 'jpg');
            // Dodanie odstępu po obrazie
            $pdf->Ln($imageData[$imageIndex]['newHeight']); 
        }
        
        // Dodanie odstępu
        $pdf->Ln(5); 
        // Inkrementacja indeksu obrazów
        $imageIndex++; 
        // Inkrementacja licznika iteracji
        $iterationCount++;
    }

    // Generowanie i wyświetlanie PDF
    $pdf->Output();
}
?>