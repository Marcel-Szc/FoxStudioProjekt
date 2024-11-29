<?php
    include_once('polaczenie.php');

        $zapytaniePoz = "SELECT DISTINCT pozycja_znakowania FROM znakowanie;";
        $stmt = $polaczenie->prepare($zapytaniePoz);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $positions = array();
        while($row = $result->fetch_assoc()) {
            $positions[] = $row['pozycja_znakowania'];
        }
        
        echo json_encode($positions);
?>
