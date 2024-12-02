<?php 
    require('fpdf/fpdf.php');
    include_once('polaczenie.php');
    session_start();
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(60,20,"Testowy plik PDF");
    $pdf->Ln();

    $increment = 0;
    if(isset($_SESSION['nr_oferty'])) {
        $nr_oferty = $_SESSION['nr_oferty'];
        while(true) {
            $increment++;
            if(isset($_SESSION['pozycjaZnakownia'.$increment]) || isset($_SESSION['technologiaZnakowania'.$increment]) || isset($_SESSION['iloscKolorow'.$increment]) || isset($_SESSION['kolor'.$increment]) || isset($_SESSION['wplyw'.$increment])) {
                $pozycja = $_SESSION['pozycjaZnakownia'.$increment];
                $technologia = $_SESSION['technologiaZnakowania'.$increment];
                $ilosc = $_SESSION['iloscKolorow'.$increment];
                $kolor = $_SESSION['kolor'.$increment];
                $wplyw = $_SESSION['wplyw'.$increment];
                $pdf->SetFont('Arial', 'B', 16);
                $pdf->Cell(60,20, 'hjgh');
                $pdf->Ln();
            } else {
                break;
            }
       }
       $produkt = $polaczenie->prepare("SELECT nazwa_produktu, kod_produktu FROM produkty WHERE nr_oferty =".$nr_oferty.";");
       $pdf->Image($_SESSION['zdjecie']);
       $pdf->Cell(40,20, 'test');
    }
    $pdf->Output();
?>