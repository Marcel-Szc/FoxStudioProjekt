<?php
    include_once('polaczenie.php');

    $product = $_GET['product'];
    $zapytaniePoz = "SELECT DISTINCT kod_produktu FROM produkty WHERE nazwa_produktu = ?";
    $stmt = $polaczenie->prepare($zapytaniePoz);
    $stmt->bind_param("s", $product);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $positions = array();
    while($row = $result->fetch_assoc()) {
        $positions[] = $row['kod_produktu'];
    }
    
    echo json_encode($positions);
?>