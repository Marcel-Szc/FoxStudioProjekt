<?php 
session_start();
$_SESSION['zalogowany'] = false;
header('Location: index.php');
?>