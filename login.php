<?php 
include_once('polaczenie.php');
session_start();

$email = $_POST['email'];
$haslo = $_POST['haslo'];

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo 'Nieprawidłowy format email';
    exit();
}
if (strlen($haslo) < 8 || !preg_match('/[0-9]/', $haslo)) {
    echo 'Hasło musi mieć minimum 8 znaków i zawierać przynajmniej jedną cyfrę';
    exit();
}
$zapytanie = $polaczenie->prepare('SELECT * FROM users WHERE email = ? AND haslo = ?');
$zapytanie->bind_param('ss', $email, $haslo);
$zapytanie->execute();
$wynik = $zapytanie->get_result();

if($wynik->num_rows > 0){
    $_SESSION['zalogowany'] = true;
    $user = $wynik->fetch_assoc();
    $_SESSION['imie'] = $user['imie'];
    header('Location: index.php');
}
else {
    echo 'Nieprawidłowy email lub hasło';
}
?>