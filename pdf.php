<?php 
require('fpdf/tfpdf.php');
include_once('polaczenie.php');
session_start();

$increment = 1;
$data = []; // Array to hold session data

if (isset($_SESSION['nr_oferty'])) {
    $nr_oferty = $_SESSION['nr_oferty'];
    // Fetching price and margin
    $cenaBaza = $polaczenie->prepare("SELECT cena, marza FROM ceny WHERE nr_oferty = ?");
    if (!$cenaBaza) {
        die("Database query failed: " . $polaczenie->error);
    }
    $cenaBaza->bind_param("i", $nr_oferty);
    $cenaBaza->execute();
    $wynikCena = $cenaBaza->get_result();
    $cenaFetch = $wynikCena->fetch_assoc();

    // Fetching data from the znakowanie table
    $znakowanie = $polaczenie->prepare("SELECT pozycja_znakowania, technologia_znakowania, ilosc_kolorow, kolor, wplyw_na_cene FROM ZNAKOWANIE WHERE nr_oferty = ?");
    if (!$znakowanie) {
        die("Database query failed: " . $polaczenie->error);
    }
    $znakowanie->bind_param("i", $nr_oferty);
    $znakowanie->execute();
    $wynikZnakowanie = $znakowanie->get_result();

    // Fetching data into the array
    while ($znak = $wynikZnakowanie->fetch_assoc()) {
        $data[] = [
            'pozycja' => $znak['pozycja_znakowania'],
            'technologia' => $znak['technologia_znakowania'],
            'ilosc' => $znak['ilosc_kolorow'],
            'kolor' => $znak['kolor'],
            'cena' => intval($cenaFetch['cena']) + intval($znak['wplyw_na_cene']),
            'nr_oferty' => $nr_oferty
        ];
    }

    // Fetching product details
    $produkt = $polaczenie->prepare("SELECT nazwa_produktu, kod_produktu FROM produkty WHERE nr_oferty = ?");
    if (!$produkt) {
        die("Database query failed: " . $polaczenie->error);
    }
    $produkt->bind_param("i", $nr_oferty);
    $produkt->execute();
    $wynik = $produkt->get_result();
    $produkty = $wynik->fetch_assoc();
    
    // Creating PDF
    $pdf = new tFPDF();
    $pdf->AddPage();
    $directory = 'uploads/';
    $imagePath = $directory . $increment . 'temp_image_*.jpg'; // Pattern to match images
        $files = glob($imagePath); // Get all matching files
        foreach ($files as $file) { 
           $pdf->Image($file, 0, $pdf->GetY(), 210, 297, 'jpg');
        }
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
    $pdf->Ln(10);
    // Displaying data in the PDF
    foreach ($data as $item) {
        if ($increment % 5 == 0) {
            $pdf->AddPage(); 
        }
        $pdf->Cell(0, 10, "Pozycja: " . (isset($item['pozycja']) ? $item['pozycja'] : 'N/A'), 1, 1);
        $pdf->Cell(0, 10, "Technologia: " . (isset($item['technologia']) ? $item['technologia'] : 'N/A'), 1, 1);
        $pdf->Cell(0, 10, "Ilość kolorów: " . (isset($item['ilosc']) ? $item['ilosc'] : 'N/A'), 1, 1);
        $pdf->Cell(0, 10, "Kolor: " . (isset($item['kolor']) ? $item['kolor'] : 'N/A'), 1, 1);
        $pdf->Cell(0, 10, "Cena: " . (isset($item['cena']) ? $item['cena'] : 'N/A') . "zł", 1, 1);
        //$pdf->Image($item['filename'], 10, $pdf->GetY(), 50, 50, 'jpg');
        $pdf->Ln(5);
    }
    $pdf->Output();
    $increment++;
}
?>