<?php 
require('fpdf/tfpdf.php');
include_once('polaczenie.php');
session_start();

$increment = 0;
$data = []; // Array to hold session data
if(isset($_SESSION['nr_oferty'])) {
    $nr_oferty = $_SESSION['nr_oferty'];
    while(true) {
        $increment++;
        if(isset($_SESSION['pozycjaZnakownia'.$increment]) || 
           isset($_SESSION['technologiaZnakowania'.$increment]) || 
           isset($_SESSION['iloscKolorow'.$increment]) || 
           isset($_SESSION['kolor'.$increment]) || 
           isset($_SESSION['wplyw'.$increment])) {
               
            $data[] = [
                'pozycja' => $_SESSION['pozycjaZnakownia'.$increment],
                'technologia' => $_SESSION['technologiaZnakowania'.$increment],
                'ilosc' => $_SESSION['iloscKolorow'.$increment],
                'kolor' => $_SESSION['kolor'.$increment],
                'wplyw' => $_SESSION['wplyw'.$increment],
                'zdjecie' => $_SESSION['zdjecie'],
                'nr_oferty' => $_SESSION['nr_oferty']
            ];
        } else {
            break;
        }
    }
    $produkt = $polaczenie->prepare("SELECT nazwa_produktu, kod_produktu FROM produkty WHERE nr_oferty =".$nr_oferty.";");
}

// Create PDF
$pdf = new tFPDF();
$pdf->AddPage();
$pdf->AddFont();
$pdf->SetFont('Arial','B',20);
$pdf->Cell(60, 20, "Dane produktu: ");
$pdf->Ln(20); // New line
$pdf->SetFont('DejaVu','',12);
$pdf->Cell(0, 10, "Zdjecie produktu: ", 0, 1);

// Display session data in PDF
foreach ($data as $item) {
    $pdf->Image($item['zdjecie'], 120, 40, 60, 60);
    $pdf->Cell(0, 10, "Pozycja: " . $item['pozycja'], 1, 1);
    $pdf->Cell(0, 10, "Technologia: " . $item['technologia'], 1, 1);
    $pdf->Cell(0, 10, "Ilocs kolorów: " . $item['ilosc'], 1, 1);
    $pdf->Cell(0, 10, "Kolor: " . $item['kolor'], 1, 1);
    $pdf->Cell(0, 10, "Wplyw na cene: +" . $item['wplyw'], 1, 1);
    $pdf->Ln(5); // New line for spacing
}


$pdf->Output();
//$data[] = [];
?>