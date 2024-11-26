 <?php 
include_once('polaczenie.php');
$nazwaProduktu = $_POST['nazwaProd'];
$pozycjaZnakowania = $_POST['pozycjaZnakowania'];
$technologiaZnakowania = $_POST['technologiaZnakowania'];
$iloscKolorow = $_POST['iloscKolorow'];
$kolor = $_POST['kolor'];
$kodProduktu = $_POST['kodProduktu'];
$cenaOryginalna = $_POST['cenaOryginalna'];
$marza = $_POST['marza'];
if ($_FILES['zdjecie']['error'] !== UPLOAD_ERR_OK) {
    switch ($_FILES['zdjecie']['error']) {
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            echo "Plik jest za duży!. <br> <a href='index.php'>Powrót do strony glownej</a>";
            break;
        case UPLOAD_ERR_NO_FILE:
            echo "Plik nie został przesłany!. <br> <a href='index.php'>Powrót do strony glownej</a>";
            break;
        default:
        echo "wystąpił nieznany błąd. <br> <a href='index.php'>Powrót do strony glownej</a>";
    }
} else if (isset($_FILES['zdjecie']) && $_FILES['zdjecie']['error'] === UPLOAD_ERR_OK) {
    $zdjęcie = $_FILES['zdjecie']['tmp_name'];
    $zdjęcieImg = file_get_contents($zdjęcie);

    if(isset($_POST['cenaId'])) {
        $cena = $_POST['cenaId']; 
        $cenaOryginalna = NULL;
        $marza = NULL;
    } else
    {
        $cena = number_format(($cenaOryginalna + ($cenaOryginalna * $marza/100)), 2);
    }
    if($technologiaZnakowania == '') {
        $technologiaZnakowania = NULL;
    }
    if($iloscKolorow == '') {
        $iloscKolorow = NULL;
    }
    if($kolor == '') {
        $kolor = NULL;
    }
    $nr_oferty = NULL;
    $idc = NULL;
    $idz = NULL;
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Document</title>
    </head>
    <body>
        <?php  
            try {
                $insert = "INSERT INTO `produkty`( `nr_oferty`, `nazwa_produktu`, `kod_produktu`, `zdjecie_produktu`) VALUES (?, ?, ?, ?);";
                $stmt = $polaczenie->prepare($insert);
                $stmt->bind_param("ssss", $nr_oferty, $nazwaProduktu, $kodProduktu, $zdjęcieImg);
                $stmt->send_long_data(0, $zdjęcieImg);
                $stmt->execute();
                echo "Udało się!";
            } catch (Throwable $th) {
                echo $th;
            } 
            try{
                $insertCen = "INSERT INTO `ceny`(`idc`, `cena`, `cena_oryginalna`, `marza`, `nr_oferty`) VALUES (?, ?, ?, ?, ?);";
                $stmtCen = $polaczenie->prepare($insertCen);
                $stmtCen->bind_param("sssss", $idc, $cena, $cenaOryginalna, $marza, $nr_oferty);
                $stmtCen->execute();
                echo "Udało się!";
            } catch (Throwable $e){
                echo $e;
            }
            try {
                $insertZnak = "INSERT INTO `znakowanie`(`idz`, `pozycja_znakowania`,`technologia_znakowania`,`ilosc_kolorow`, `kolor`, `nr_oferty`) VALUES (?, ?, ?, ?, ?, ?);";
                $stmtZnak = $polaczenie->prepare($insertZnak);
                $stmtZnak->bind_param("ssssss", $idz, $pozycjaZnakowania, $technologiaZnakowania, $iloscKolorow, $kolor, $nr_oferty);
                $stmtZnak->execute();
                echo "Udało się!";
            } catch (Throwable $e){
                echo $e;
            }
        }
        ?>
    </body>
</html> 