<?php 
include_once('polaczenie.php');
session_start();
$nazwaProduktu = $_POST['nazwaProd'];
$kodProduktu = $_POST['kodProduktu'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style.css">
    <style>
        #pdf {
            margin-left: 0px;
            position: absolute;
            top: 30rem;
            left: 50rem;
        }
    </style>
</head>
<body>
    <?php 
        if (isset($_POST['cena'])) {
            $cena = $_POST['cena']; 
            $cenaOryginalna = NULL;
            $marza = NULL;
        } else {
            $cenaOryginalna = $_POST['cenaOryginalna']; 
            $marza = $_POST['marza'];

            $cena = number_format(((floatval($cenaOryginalna) - (floatval($cenaOryginalna) * 0.57)) + ((floatval($cenaOryginalna) * floatval($marza)) / 100)), 2);
        }
        
        $kluczObcy = $polaczenie->query("SELECT `nr_oferty` FROM `produkty` ORDER BY nr_oferty DESC LIMIT 1;");
        unset($_SESSION['nr_oferty']);
        $nr_oferty = intval($kluczObcy->fetch_row()[0]) + 1;
        $_SESSION['nr_oferty'] = $nr_oferty;
        
        $idc = null;
        $idz = null;
        
        try {
            $insert = "INSERT INTO `produkty` (`nr_oferty`, `nazwa_produktu`, `kod_produktu`) VALUES (?, ?, ?);";
            $stmt = $polaczenie->prepare($insert);
            $stmt->bind_param("sss", $nr_oferty, $nazwaProduktu, $kodProduktu);
            $stmt->execute();
            echo "Produkt został dodany pomyślnie!<br>";
        } catch (Throwable $th) {
            echo "Błąd dodawania produktu: " . $th->getMessage();
        } 

        try {
            $insertCen = "INSERT INTO `ceny` (`idc`, `cena`, `cena_oryginalna`, `marza`, `nr_oferty`) VALUES (?, ?, ?, ?, ?);";
            $stmtCen = $polaczenie->prepare($insertCen);
            $stmtCen->bind_param("sssss", $idc, $cena, $cenaOryginalna, $marza, $nr_oferty);
            $stmtCen->execute();
            echo "Cena została dodana pomyślnie!<br>";
        } catch (Throwable $e) {
            echo "Błąd dodawania ceny: " . $e->getMessage();
        }

        $increment = 1;
        while (true) {
            if (isset($_POST['pozycjaZnakowania'.$increment]) || isset($_POST['technologiaZnakowania'.$increment]) || isset($_POST['iloscKolorow'.$increment]) || isset($_POST['kolor'.$increment]) || isset($_POST['wplyw'.$increment])) {
                $pozycjaZnakowania = $_POST['pozycjaZnakowania'.$increment];
                $technologiaZnakowania = $_POST['technologiaZnakowania'.$increment];
                $iloscKolorow = $_POST['iloscKolorow'.$increment];
                $kolor = $_POST['kolor'.$increment];
                $wplyw = $_POST['wplyw'.$increment];

                if ($_FILES['zdjecie'.$increment]['error'] !== UPLOAD_ERR_OK) {
                    switch ($_FILES['zdjecie'.$increment]['error']) {
                        case UPLOAD_ERR_INI_SIZE:
                        case UPLOAD_ERR_FORM_SIZE:
                            echo "Plik jest za duży!. <br> <a href='index.php'>Powrót do strony głównej</a>";
                            exit;
                        case UPLOAD_ERR_NO_FILE:
                            echo "Plik nie został przesłany!. <br> <a href='index.php'>Powrót do strony głównej</a>";
                            exit;
                        default:
                            echo "Wystąpił nieznany błąd. <br> <a href=' index.php'>Powrót do strony głównej</a>";
                            exit;
                    }
                }

                $zdjęcie = $_FILES['zdjecie'.$increment]['tmp_name'];
                $imageType = exif_imagetype($zdjęcie); // Get the image type

                // Create a new image resource
                switch ($imageType) {
                    case IMAGETYPE_JPEG:
                        $sourceImage = imagecreatefromjpeg($zdjęcie);
                        break;
                    case IMAGETYPE_PNG:
                        $sourceImage = @imagecreatefrompng($zdjęcie);
                        break;
                    case IMAGETYPE_GIF:
                        $sourceImage = imagecreatefromgif($zdjęcie);
                        break;
                    default:
                        die("Unsupported image type.");
                }

                // Create a new true color image in JPEG format
                $jpegImagePath = 'uploads/'.$increment.'temp_image_' . uniqid() . '.jpg';
                imagejpeg($sourceImage, $jpegImagePath, 100); // Save as JPEG with quality 100

                $_SESSION['imagePath'.$increment] = $jpegImagePath;

                // Read the JPEG file contents
                $zdjęcieImg = file_get_contents($jpegImagePath);

                // Now you can store $zdjęcieImg in the database
                try {
                    $insertZnak = "INSERT INTO `znakowanie` (`idz`, `pozycja_znakowania`, `technologia_znakowania`, `ilosc_kolorow`, `kolor`, `wplyw_na_cene`, `zdjecie_produktu`, `nr_oferty`) VALUES (?, ?, ?, ?, ?, ?, ?, ?);";
                    $stmtZnak = $polaczenie->prepare($insertZnak);
                    $stmtZnak->bind_param("ssssssss", $idz, $pozycjaZnakowania, $technologiaZnakowania, $iloscKolorow, $kolor, $wplyw, $zdjęcieImg, $nr_oferty);
                    $stmtZnak->send_long_data(0, $zdjęcieImg);
                    $stmtZnak->execute();
                    echo "Znakowanie zostało dodane pomyślnie!<br>";
                } catch (Throwable $e) {
                    echo "Błąd dodawania znakowania nr ".$increment.": ". $e->getMessage();
                }
                $increment++;
            } else {
                break;
            }
        } 
        $increment = 0;
    ?>
    <div class="main">
        <h1 style=" margin: auto;">Dane zostały dodane do Bazy Danych, utwórz plik pdf:</h1>
        <button class="przycisk" id="pdf">Utwórz plik pdf</button>
    </div>
<script> 
    document.getElementById("pdf").addEventListener("click", function(){ 
            window.location.href = "pdf.php";
        });
</script>
</body>
</html> 