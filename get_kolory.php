<?php
    include_once('polaczenie.php');
    
    $zapytanieKolor = 'SELECT DISTINCT kolor FROM produkty ORDER BY kolor ASC';
    $stmt = $polaczenie->prepare($zapytanieKolor);
    $stmt->execute();
    $result = $stmt->get_result();
    $positions = array();

    while($row = $result->fetch_assoc()) {
        if($row['kolor'] != NULL){
            $positions[] = $row['kolor'];
        }
    }

    echo json_encode($positions);

?>
