<?php 
include_once('polaczenie.php');
session_start();
$imie = $_POST['imie'];
$haslo = $_POST['haslo'];

$zapytania = 'SELECT * FROM users WHERE imie = "'.$imie.'" AND haslo = "'.$haslo.'"';

$wynik = $polaczenie->query($zapytania);

if($wynik->num_rows > 0){
    $_SESSION['zalogowany'] = true;
    $_SESSION['imie'] = $imie;
    header('Location: index.php');
}
else {
    echo 'nie zalogowano';
}

?>