<?php 
include_once('polaczenie.php');

$nazwaProduktu = $_POST['nazwaProduktu'];
$pozycjaZnakowania = $_POST['pozycjaZnakowania'];
$technologiaZnakowania = $_POST['technologiaZnakowania'];
$iloscKolorow = $_POST['iloscKolorow'];
$kolor = $_POST['kolor'];
$kodProduktu = $_POST['kodProduktu'];
$cena = 0;
if ($_POST['cena'] == ''){
    $cena = $_POST['cenaOryginalna'] + ($_POST['cenaOryginalna'] * $_POST['marza'] / 100);}
$zdjęcie = $_FILES['zdjęcie']['name'];
$zdjęcieTmp = $_FILES['zdjęcie']['tmp_name'];



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
    $sql = "INSERT INTO produkty (nazwaProduktu, pozycjaZnakowania, technologiaZnakowania, iloscKolorow, kolor, kodProduktu, cena, zdjecie) (filename) VALUES ('$zdjecie')";

    mysqli_query($db, $sql);

    // Now let's move the uploaded image into the folder: image
    if (move_uploaded_file($tempname, $folder)) {
        echo "<h3>&nbsp; Image uploaded successfully!</h3>";
    } else {
        echo "<h3>&nbsp; Failed to upload image!</h3>";
    }
    ?>
</body>
</html>