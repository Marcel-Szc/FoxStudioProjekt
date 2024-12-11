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
    $pdf->AddFont('DejaVu','','DejaVuSansCondensed-Bold.ttf',true);
    $pdf->SetFont('DejaVu','',11);
    $pdf->Cell(0, 10, "Nazwa produktu: ".$produkty['nazwa_produktu'], 0, 1);
    $pdf->Cell(0, 10, "Kod produktu: ".$produkty['kod_produktu'], 0, 1);
    $pdf->SetFont('DejaVu','',9);
    if ($cenaFetch['marza'] != NULL) {
        $marza = "Marza: ".$cenaFetch['marza'];
        $pdf->Cell(0, 10, "Marza produktu: ".$marza."%", 1, 1);
    }
    $pdf->Ln(10);
    // Displaying images for the current item
    $directory = 'uploads/'; // Ensure the directory is set
    $increment = 1; // Reset increment for image search

    // Create a separate array for images
    $imageData = []; 

    // Loop to find and display images for the current item
    while ($increment <= 100) { // Adjust the limit as needed
        $imagePattern = $directory . $increment . 'temp_image_*.jpg';
        $files = glob($imagePattern); // Get all matching files

        if (!empty($files)) {
            foreach ($files as $imagePath) { // Loop through each found image
                // Get the original dimensions of the image
                list($originalWidth, $originalHeight) = getimagesize($imagePath);
                
                // Set the desired width for the image
                $desiredWidth = 37; // Change this value as needed
                $aspectRatio = $originalHeight / $originalWidth;
                
                // Calculate the new height to maintain aspect ratio
                $newHeight = $desiredWidth * $aspectRatio;

                // Check if the new height exceeds 100 pixels
                if ($newHeight > 100) {
                    // Calculate the new width to maintain aspect ratio with a fixed height of 100
                    $newHeight = 100;
                    $desiredWidth = $newHeight / $aspectRatio;
                }

                // Store image properties in the new array
                $imageData[] = [
                    'imagePath' => $imagePath,
                    'desiredWidth' => $desiredWidth,
                    'newHeight' => $newHeight
                ];
            }
        }

        $increment++; // Increment to check the next set of images
    }

    // Displaying data in the PDF
    $imageIndex = 0; // Initialize an index for images
    $iterationCount = 0; // Initialize a counter for iterations
    $pageHeight = $pdf->GetPageHeight(); // Get the height of the page
    $bottomMargin = 10; // Set a bottom margin to avoid cutting off content

    foreach ($data as $item) {
        // Calculate the height needed for the current item
        $itemHeight = 10 + 10 + 10 + 10 + 10 + 5; // Heights of the cells and spacing (adjust as needed)
        if (isset($imageData[$imageIndex])) {
            $itemHeight += $imageData[$imageIndex]['newHeight'] + 5; // Add image height and spacing
        }

        // Check if adding this item would exceed the page height
        if ($pdf->GetY() + $itemHeight > $pageHeight - $bottomMargin) {
            $pdf->AddPage(); // Add a new page if it exceeds
        }

        // Now we can safely add the content
        $pdf->Cell(0, 10, "Dane znakowania: ", 0, 1);
        $pdf->Cell(0, 10, "Pozycja: " . (isset($item['pozycja']) ? $item['pozycja'] : 'N/A'), 1, 1);
        $pdf->Cell(0, 10, "Technologia: " . (isset($item['technologia']) ? $item['technologia'] : 'N/A'), 1, 1);
        $pdf->Cell(0, 10, "Ilość kolorów: " . (isset($item['ilosc']) ? $item['ilosc'] : 'N/A'), 1, 1);
        $pdf->Cell(0, 10, "Kolor: " . (isset($item['kolor']) ? $item['kolor'] : 'N/A'), 1, 1);
        $pdf->Cell(0, 10, "Cena: " . (isset($item['cena']) ? $item['cena'] : 'N/A') . "zł", 1, 1);
        $pdf->Ln(5);

        // Check if there is an image to display for this item
        if (isset($imageData[$imageIndex])) {
            $pdf->Image($imageData[$imageIndex]['imagePath'], 10, $pdf->GetY(), $imageData[$imageIndex]['desiredWidth'], $imageData[$imageIndex]['newHeight'], 'jpg');
            $pdf->Ln($imageData[$imageIndex]['newHeight']); // Add space after the image
        }
        
        $pdf->Ln(5); // Add space between items
        $imageIndex++; // Move to the next image for the next item
        $iterationCount++; // Increment the iteration counter
    }

    $pdf->Output();
}
?>