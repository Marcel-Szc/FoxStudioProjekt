<?php 
require('fpdf/tfpdf.php');
include_once('polaczenie.php');
session_start();

$increment = 0;
$data = []; // Array to hold session data
if(isset($_SESSION['nr_oferty'])) {
    $nr_oferty = $_SESSION['nr_oferty'];
    $cenaBaza = $polaczenie->prepare("SELECT cena, marza FROM ceny WHERE nr_oferty =".$nr_oferty.";");
    $cenaBaza->execute();
    $wynikCena = $cenaBaza->get_result();
    $cenaFetch = $wynikCena->fetch_assoc();

    while(true) {
        $increment++;
        if(isset($_SESSION['pozycjaZnakownia'.$increment]) || isset($_SESSION['technologiaZnakowania'.$increment]) || isset($_SESSION['iloscKolorow'.$increment]) || isset($_SESSION['kolor'.$increment]) || isset($_SESSION['wplyw'.$increment])) {

            $data[] = [
                'pozycja' => $_SESSION['pozycjaZnakownia'.$increment],
                'technologia' => $_SESSION['technologiaZnakowania'.$increment],
                'ilosc' => $_SESSION['iloscKolorow'.$increment],
                'kolor' => $_SESSION['kolor'.$increment],
                'wplyw' => $_SESSION['wplyw'.$increment],
                'cena' => $cenaFetch['cena'] + $_SESSION['wplyw'.$increment],
                'zdjecie' => $_SESSION['zdjecie'],
                'nr_oferty' => $_SESSION['nr_oferty']
            ];
            
        } else {
            break;
        }
    }
    $produkt = $polaczenie->prepare("SELECT nazwa_produktu, kod_produktu FROM produkty WHERE nr_oferty =".$nr_oferty.";");
    $produkt->execute();
    $wynik = $produkt->get_result();
    $produkty = $wynik->fetch_assoc();
    
    $pdf = new tFPDF();
    $pdf->AddPage();
    $pdf->AddFont('DejaVu','','DejaVuSansCondensed-Bold.ttf',true);
    $pdf->SetFont('Arial','B',20);
    $pdf->Cell(60, 20, "Dane produktu: ");
    $pdf->Ln(20);
    $pdf->SetFont('DejaVu','',12);
    $pdf->Cell(0, 10, "Nazwa produktu: ".$produkty['nazwa_produktu'], 1, 1);
    $pdf->Cell(0, 10, "Kod produktu: ".$produkty['kod_produktu'], 1, 1);
    if ($cenaFetch['marza'] != NULL) {
        $marza = "Marza: ".$cenaFetch['marza'];
        $pdf->Cell(0, 10, "Marza produktu: ".$marza."%", 1, 1);
    }
   
    
    // Image handling
    $imagePath = $data[0]['zdjecie']; // Assuming the image is the same for all items
    list($width, $height) = getimagesize($imagePath);
    $maxWidth = 66.66666666667; // Set your desired max width
    $maxHeight = 40; // Set your desired max height

    // Calculate the scaling factor based on the height
    if ($height > $maxHeight) {
        $ratio = $maxHeight / $height; // Scale down more if height is greater than maxHeight
    } else {
        $ratio = min($maxWidth / $width, $maxHeight / $height); // Normal scaling
    }

    $newWidth = $width * $ratio;
    $newHeight = $height * $ratio;

    $pdf->Cell(0, 62, "Zdjecie produktu: ", 0, 1);
    $pdf->Image($imagePath, 60, 62, $newWidth, $newHeight); // Add image with new dimensions
    
    $incrementCheck = 0;
    foreach ($data as $item) {
        $incrementCheck++;
        if($incrementCheck % 4 == 0){
            $pdf->AddPage(); 
        }
        
        $pdf->Cell(0, 10, "Pozycja: " . $item['pozycja'], 1, 1);
        $pdf->Cell(0, 10, "Technologia: " . $item['technologia'], 1, 1);
        $pdf->Cell(0, 10, "Ilość kolorów: " . $item['ilosc'], 1, 1);
        $pdf->Cell(0, 10, "Kolor: " . $item['kolor'], 1, 1);
        $pdf->Cell(0, 10, "Cena: " . $item['cena'] . "zł", 1, 1);
        $pdf->Ln(5);
    }
    
    $pdf->Output();
}
?>