 <?php 
include_once('polaczenie.php');
$nazwaProduktu = $_POST['nazwaProd'];
$pozycjaZnakowania = $_POST['pozycjaZnakowania'];
$technologiaZnakowania = $_POST['technologiaZnakowania'];
$iloscKolorow = $_POST['iloscKolorow'];
$kolor = $_POST['kolor'];
$kodProduktu = $_POST['kodProduktu'];
$cena = $_POST['cenaId'];
$cenaOryginalna = $_POST['cenaOryginalna'];
$marza = $_POST['marza'];
$data = $_POST['data'];
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
    if ($cena == '' || $cenaOryginalna != '' || $marza != ''){
        $cena = number_format(($cenaOryginalna + ($cenaOryginalna * $marza/100)), 2);
    } else {
        $cenaOryginalna = NULL;
        $marza = NULL;
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
                $insert = "INSERT INTO `produkty`( `nr_oferty`, `nazwa_produktu`, `kod_produktu`, `zdjecie_produktu`, `data`, `pozycja_znakowania`, `technologia_znakowania`, `ilosc_kolorow`, `kolor`, `cena`, `cena_oryginalna`, `marza`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);";
                $stmt = $polaczenie->prepare($insert);
                $stmt->bind_param("ssssssssssss", $nr_oferty, $nazwaProduktu, $kodProduktu, $zdjęcieImg, $data, $pozycjaZnakowania, $technologiaZnakowania, $iloscKolorow, $kolor, $cena, $cenaOryginalna, $marza);
                $stmt->send_long_data(0, $zdjęcieImg);
                $stmt->execute();
                echo "Udało się!";
            } catch (Throwable $th) {
                throw $th;
                echo "Nie udało się!";
            } }
        ?>
    </body>
</html> 