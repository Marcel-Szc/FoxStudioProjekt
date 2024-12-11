<?php 
// Włączenie pliku z połączeniem do bazy danych
include_once('polaczenie.php');
// Rozpoczęcie sesji
session_start();

// Przypisanie wartości z formularza do zmiennych
$nazwaProduktu = $_POST['nazwaProd'];
$kodProduktu = $_POST['kodProduktu'];

// Rozpoczęcie dokumentu HTML
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php 
        // Sprawdzenie, czy cena została przesłana w formularzu
        if (isset($_POST['cena'])) {
            // Przypisanie ceny z formularza
            $cena = $_POST['cena']; 
            $cenaOryginalna = NULL; // Inicjalizacja zmiennej oryginalnej ceny
            $marza = NULL; // Inicjalizacja zmiennej marży
        } else {
            // Przypisanie oryginalnej ceny i marży z formularza
            $cenaOryginalna = $_POST['cenaOryginalna']; 
            $marza = $_POST['marza'];

            // Obliczenie ceny na podstawie oryginalnej ceny i marży
            $cena = number_format(((floatval($cenaOryginalna) - (floatval($cenaOryginalna) * 0.57)) + ((floatval($cenaOryginalna) * floatval($marza)) / 100)), 2);
        }
        
        // Zapytanie do bazy danych w celu uzyskania ostatniego numeru oferty
        $kluczObcy = $polaczenie->query("SELECT `nr_oferty` FROM `produkty` ORDER BY nr_oferty DESC LIMIT 1;");
        unset($_SESSION['nr_oferty']); // Usunięcie numeru oferty z sesji
        $nr_oferty = intval($kluczObcy ->fetch_row()[0]) + 1; // Inkrementacja numeru oferty
        $_SESSION['nr_oferty'] = $nr_oferty; // Przechowanie numeru oferty w sesji
        
        $idc = null; // Inicjalizacja zmiennej ID dla cen
        $idz = null; // Inicjalizacja zmiennej ID dla znakowania
        
        try {
            // Przygotowanie zapytania do dodania produktu do bazy danych
            $insert = "INSERT INTO `produkty` (`nr_oferty`, `nazwa_produktu`, `kod_produktu`) VALUES (?, ?, ?);";
            $stmt = $polaczenie->prepare($insert); // Przygotowanie zapytania
            $stmt->bind_param("sss", $nr_oferty, $nazwaProduktu, $kodProduktu); // Powiązanie parametrów
            $stmt->execute(); // Wykonanie zapytania
            echo "Produkt został dodany pomyślnie!<br>"; // Informacja o sukcesie
        } catch (Throwable $th) {
            echo "Błąd dodawania produktu: " . $th->getMessage(); // Obsługa błędów
        } 

        try {
            // Przygotowanie zapytania do dodania ceny do bazy danych
            $insertCen = "INSERT INTO `ceny` (`idc`, `cena`, `cena_oryginalna`, `marza`, `nr_oferty`) VALUES (?, ?, ?, ?, ?);";
            $stmtCen = $polaczenie->prepare($insertCen); // Przygotowanie zapytania
            $stmtCen->bind_param("sssss", $idc, $cena, $cenaOryginalna, $marza, $nr_oferty); // Powiązanie parametrów
            $stmtCen->execute(); // Wykonanie zapytania
            echo "Cena została dodana pomyślnie!<br>"; // Informacja o sukcesie
        } catch (Throwable $e) {
            echo "Błąd dodawania ceny: " . $e->getMessage(); // Obsługa błędów
        }

        $increment = 1; // Inicjalizacja zmiennej do iteracji
        while (true) {
            // Sprawdzenie, czy dane o znakowaniu zostały przesłane
            if (isset($_POST['pozycjaZnakowania'.$increment]) || isset($_POST['technologiaZnakowania'.$increment]) || isset($_POST['iloscKolorow'.$increment]) || isset($_POST['kolor'.$increment]) || isset($_POST['wplyw'.$increment])) {
                // Przypisanie wartości z formularza do zmiennych
                $pozycjaZnakowania = $_POST['pozycjaZnakowania'.$increment];
                $technologiaZnakowania = $_POST['technologiaZnakowania'.$increment];
                $iloscKolorow = $_POST['iloscKolorow'.$increment];
                $kolor = $_POST['kolor'.$increment];
                $wplyw = $_POST['wplyw'.$increment];

                // Sprawdzenie błędów przesyłania plików
                if ($_FILES['zdjecie'.$increment]['error'] !== UPLOAD_ERR_OK) {
                    switch ($_FILES['zdjecie'.$increment]['error']) {
                        case UPLOAD_ERR_INI_SIZE:
                        case UPLOAD_ERR_FORM_SIZE:
                            echo "Plik jest za duży!. <br> <a href='index.php'>Powrót do strony głównej</a>";
                            exit; // Zakończenie skryptu
                        case UPLOAD_ERR_NO_FILE:
                            echo "Plik nie został przesłany!. <br> <a href='index.php'>Powrót do strony głównej</a>";
                            exit; // Zakończenie skryptu
                        default:
                            echo "Wystąpił nieznany błąd. <br> <a href=' index.php'>Powrót do strony głównej</a>";
                            exit; // Zakończenie skryptu
                    }
                }

                // Przechowywanie tymczasowej ścieżki do przesłanego zdjęcia
                $zdjęcie = $_FILES['zdjecie'.$increment]['tmp_name'];
                $imageType = exif_imagetype($zdjęcie); // Uzyskanie typu obrazu

                // Tworzenie nowego zasobu obrazu
                switch ($imageType) {
                    case IMAGETYPE_JPEG:
                        $sourceImage = imagecreatefromjpeg($zdjęcie); // Ładowanie obrazu JPEG
                        break;
                    case IMAGETYPE_PNG:
                        $sourceImage = @imagecreatefrompng($zdjęcie); // Ładowanie obrazu PNG
                        break;
                    case IMAGETYPE_GIF:
                        $sourceImage = imagecreatefromgif($zdjęcie); // Ładowanie obrazu GIF
                        break;
                    default:
                        die("Unsupported image type."); // Obsługa nieobsługiwanego typu obrazu
                }

                // Tworzenie nowego obrazu w formacie JPEG
                $jpegImagePath = 'uploads/'.$increment.'temp_image_' . uniqid() . '.jpg'; // Ścieżka do zapisanego obrazu
                imagejpeg($sourceImage, $jpegImagePath, 100); // Zapis obrazu jako JPEG z jakością 100

                $_SESSION['imagePath'.$increment] = $jpegImagePath; // Przechowywanie ścieżki obrazu w sesji

                // Odczytanie zawartości pliku JPEG
                $zdjęcieImg = file_get_contents($jpegImagePath);

                // Teraz można przechować $zdjęcieImg w bazie danych
                try {
                    // Przygotowanie zapytania do dodania danych o znakowaniu do bazy danych
                    $insertZnak = "INSERT INTO `znakowanie` (`idz`, `pozycja_znakowania`, `technologia_znakowania`, `ilosc_kolorow`, `kolor`, `wplyw_na_cene`, `zdjecie_produktu`, `nr_oferty`) VALUES (?, ?, ?, ?, ?, ?, ?, ?);";
                    $stmtZnak = $polaczenie->prepare($insertZnak); // Przygotowanie zapytania
                    $stmtZnak->bind_param("ssssssss", $idz, $pozycjaZnakowania, $technologiaZnakowania, $iloscKolorow, $kolor, $wplyw, $zdjęcieImg, $nr_oferty); // Powiązanie parametrów
                    $stmtZnak->send_long_data(0, $zdjęcieImg); // Wysłanie dużych danych
                    $stmtZnak->execute(); // Wykonanie zapytania
                    echo "Znakowanie zostało dodane pomyślnie!<br>"; // Informacja o sukcesie
                } catch (Throwable $e) {
                    echo "Błąd dodawania znakowania nr ".$increment.": ". $e->getMessage(); // Obsługa błędów
                }
                $increment++; // Inkrementacja zmiennej
            } else {
                break; // Zakończenie pętli, jeśli nie ma więcej danych
            }
        } 
        $increment = 0; // Resetowanie zmiennej inkrementacyjnej
    ?>
    <div class="main">
        <h1 style=" margin: auto;">Dane zostały dodane do Bazy Danych, utwórz plik pdf:</h1>
        <button class="przycisk" id="pdf">Utwórz plik pdf</button> <!-- Przycisk do generowania PDF -->
    </div>
<script> 
    // Dodanie zdarzenia kliknięcia do przycisku generowania PDF
    document.getElementById("pdf").addEventListener("click", function(){ 
        window.location.href = "pdf.php"; // Przekierowanie do skryptu generującego PDF
    });
</script>
</body>
</html> 