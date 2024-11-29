<?php 
    require('fpdf/fpdf.php');
    include_once('polaczenie.php');
    session_start();
    $increment = 0;
    if(isset($_SESSION['nr_oferty'])) {
        $nr_oferty = $_SESSION['nr_oferty'];
        while(true) {
            $increment++;
            if(isset($_SESSON['pozycjaZnakownia'.$increment]) || isset($_SESSON['technologiaZnakowania'.$increment]) || isset($_SESSON['iloscKolorow'.$increment]) || isset($_SESSON['kolor'.$increment]) || isset($_SESSON['wplyw'.$increment])) {
                $pozycja = $_SESSON['pozycjaZnakownia'.$increment];
                $technologia = $_SESSON['technologiaZnakowania'.$increment];
                $ilosc = $_SESSON['iloscKolorow'.$increment];
                $kolor = $_SESSON['kolor'.$increment];
                $wplyw = $_SESSON['wplyw'.$increment];
            } else {
                break;
            }
       }
       $produkt = $polaczenie->prepare("SELECT nazwa_produktu, kod_produktu, zdjecie_produktu FROM produkty WHERE nr_oferty =".$nr_oferty.";");
    }


    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(60,20,"Testowy plik PDF");
    $pdf->Output();
?>