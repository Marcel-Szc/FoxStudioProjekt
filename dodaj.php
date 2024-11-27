<?php 
include_once('polaczenie.php');

$nazwaProduktu = $_POST['nazwaProd'];
$pozycjaZnakowania = $_POST['pozycjaZnakowania'];
$technologiaZnakowania = $_POST['technologiaZnakowania'];
$iloscKolorow = $_POST['iloscKolorow'];
$kolor = $_POST['kolor'];
$kodProduktu = $_POST['kodProduktu'];
$cenaOryginalna = $_POST['cenaOryginalna'] ?? null; 
$marza = $_POST['marza'] ?? null; 

if ($_FILES['zdjecie']['error'] !== UPLOAD_ERR_OK) {
    switch ($_FILES['zdjecie']['error']) {
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            echo "Plik jest za duży!. <br> <a href='index.php'>Powrót do strony głównej</a>";
            exit;
        case UPLOAD_ERR_NO_FILE:
            echo "Plik nie został przesłany!. <br> <a href='index.php'>Powrót do strony głównej</a>";
            exit;
        default:
            echo "Wystąpił nieznany błąd. <br> <a href='index.php'>Powrót do strony głównej</a>";
            exit;
    }
}

$zdjęcie = $_FILES['zdjecie']['tmp_name'];
$zdjęcieImg = file_get_contents($zdjęcie);

if (isset($_POST['cenaId'])) {
    $cena = $_POST['cenaId']; 
} else {
    $cena = number_format((floatval($cenaOryginalna) + ((floatval($cenaOryginalna) * floatval($marza)) / 100)), 2);
}

$technologiaZnakowania = empty($technologiaZnakowania) ? null : $technologiaZnakowania;
$iloscKolorow = empty($iloscKolorow) ? null : $iloscKolorow;
$kolor = empty($kolor) ? null : $kolor;

$kluczObcy = $polaczenie->query("SELECT `nr_oferty` FROM `produkty` ORDER BY nr_oferty DESC LIMIT 1;");
$nr_oferty = intval($kluczObcy->fetch_row()[0]) + 1; // Fetch the result correctly

$idc = null;
$idz = null;

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
        $insert = "INSERT INTO `produkty` (`nr_oferty`, `nazwa_produktu`, `kod_produktu`, `zdjecie_produktu`) VALUES (?, ?, ?, ?);";
        $stmt = $polaczenie->prepare($insert);
        $stmt->bind_param("ssss", $nr_oferty, $nazwaProduktu, $kodProduktu, $zdjęcieImg);
        $stmt->send_long_data(0, $zdjęcieImg);
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

    try {
        $insertZnak = "INSERT INTO `znakowanie` (`idz`, `pozycja_znakowania`, `technologia_znakowania`, `ilosc_kolorow`, `kolor`, `nr_oferty`) VALUES (?, ?, ?, ?, ?, ?);";
        $stmtZnak = $polaczenie->prepare($insertZnak);
        $stmtZnak->bind_param("ssssss", $idz, $pozycjaZnakowania, $technologiaZnakowania, $iloscKolorow, $kolor, $nr_oferty);
        $stmtZnak->execute();
        echo "Znakowanie zostało dodane pomyślnie!<br>";
    } catch (Throwable $e) {
        echo "Błąd dodawania znakowania: " . $e->getMessage();
    }
    ?>
</body>
</html> 