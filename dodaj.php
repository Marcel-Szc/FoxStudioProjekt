<?php 
    include_once('polaczenie.php');
    require('fpdf/fpdf.php');
    session_start();

    if((!isset($_SESSION['zalogowany']))){
        header("index.php");
    }
    else {
        echo '
        <div class="headerWrapper">
            <div class="imie"> Witaj '.$_SESSION['imie'].'!</div> 
            <img src="panda.jpg" alt="logo pandagadzety" height="40.5px" width="100px">
            <a class="button wylog" href="logout.php">Wyloguj się</a>
        </div>' ;
    

        $nazwaProduktu = $_POST['nazwaProd'];
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php 
        if (isset($_POST['cenaId'])) {
            $cena = $_POST['cenaId']; 
        } else {
            $cena = number_format((floatval($cenaOryginalna) + ((floatval($cenaOryginalna) * floatval($marza)) / 100)), 2);
        }
        
        $technologiaZnakowania = empty($technologiaZnakowania) ? null : $technologiaZnakowania;
        $iloscKolorow = empty($iloscKolorow) ? null : $iloscKolorow;
        $kolor = empty($kolor) ? null : $kolor;
        
        $kluczObcy = $polaczenie->query("SELECT `nr_oferty` FROM `produkty` ORDER BY nr_oferty DESC LIMIT 1;");
        $nr_oferty = intval($kluczObcy->fetch_row()[0]) + 1;
        
        $idc = null;
        $idz = null;
        
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
        $increment = 0;
        while(true) {
            $increment++;
            if(isset($_POST['pozycjaZnakowania'.$increment]) || isset($_POST['technologiaZnakowania'.$increment]) || isset($_POST['iloscKolorow'.$increment]) || isset( $_POST['kolor'.$increment]) || isset($_POST['wplyw'.$increment])) {
                $pozycjaZnakowania = $_POST['pozycjaZnakowania'.$increment];
                $technologiaZnakowania = $_POST['technologiaZnakowania'.$increment];
                $iloscKolorow = $_POST['iloscKolorow'.$increment];
                $kolor = $_POST['kolor'.$increment];
                $wplyw = $_POST['wplyw'.$increment];
                try {
                    $insertZnak = "INSERT INTO `znakowanie` (`idz`, `pozycja_znakowania`, `technologia_znakowania`, `ilosc_kolorow`, `kolor`, `wplyw_na_cene`, `nr_oferty`) VALUES (?, ?, ?, ?, ?, ?, ?);";
                    $stmtZnak = $polaczenie->prepare($insertZnak);
                    $stmtZnak->bind_param("sssssss", $idz, $pozycjaZnakowania, $technologiaZnakowania, $iloscKolorow, $kolor, $wplyw, $nr_oferty);
                    $stmtZnak->execute();
                    echo "Znakowanie zostało dodane pomyślnie!<br>";

                    $_SESSION['pozycjaZnakownia'.$increment] = $pozycjaZnakowania;
                    $_SESSION['technologiaZnakowania'.$increment] = $technologiaZnakowania;
                    $_SESSION['iloscKolorow'.$increment] = $iloscKolorow;
                    $_SESSION['kolor'.$increment] = $kolor;
                    $_SESSION['wplyw'.$increment] = $wplyw;
                    $_SESSION['nr_oferty'] = $nr_oferty;
                } catch (Throwable $e) {
                    echo "Błąd dodawania znakowania nr ".$increment.": ". $e->getMessage();
                }
            } else {
                break;
            }
        } 
    }
    ?>

    <div class="main">
        <h1 style=" margin: auto;">Dane zostały dodane do Bazy Danych, utwórz plik pdf:</h1>
        <button class="przycisk" id="pdf">Utwóz plik pdf</button>
    </div>
<script> 
    document.getElementById("pdf").addEventListener("click", function(){ 
            window.location.href = "pdf.php";
        });
</script>
</body>
</html> 