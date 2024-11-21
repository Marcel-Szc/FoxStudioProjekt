<?php
include_once('polaczenie.php');


$zapytanieNazw = 'SELECT DISTINCT nazwa_produktu FROM produkty ORDER BY nazwa_produktu ASC';
$stmt = $polaczenie->prepare($zapytanieNazw);
$stmt->execute();
$result = $stmt->get_result();
    
$positions = array();
while($row = $result->fetch_assoc()) {
    $positions[] = $row['nazwa_produktu'];
}
    
echo json_encode($positions);

?>
