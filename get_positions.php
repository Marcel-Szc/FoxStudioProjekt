<?php
include_once('polaczenie.php');

if(isset($_GET['product'])) {
    $product = $_GET['product'];
    $zapytaniePoz = "SELECT DISTINCT pozycja_znakowania FROM produkty WHERE nazwa_produktu = ?";
    $stmt = $polaczenie->prepare($zapytaniePoz);
    $stmt->bind_param("s", $product);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $positions = array();
    while($row = $result->fetch_assoc()) {
        $positions[] = $row['pozycja_znakowania'];
    }
    
    echo json_encode($positions);
}
?>
