<?php
include_once('polaczenie.php');


$zapytanieTech = 'SELECT DISTINCT technologia_znakowania FROM produkty WHERE technologia_znakowania IS NOT NULL ORDER BY technologia_znakowania ASC'; // Zapytanie do bazy
$stmt = $polaczenie->prepare($zapytanieTech);
$stmt->execute();
$result = $stmt->get_result();
    
$positions = array();
while($row = $result->fetch_assoc()) {
    $positions[] = $row['technologia_znakowania'];
}
    
echo json_encode($positions);

?>
