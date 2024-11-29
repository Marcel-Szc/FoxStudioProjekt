<?php
    include('polaczenie.php');

        $zapytanieTech = "SELECT DISTINCT technologia_znakowania FROM znakowanie WHERE technologia_znakowania IS NOT NULL ORDER BY technologia_znakowania ASC";
        $stmt = $polaczenie->prepare($zapytanieTech);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $technologies = array();
        while($row = $result->fetch_assoc()) {
            $technologies[] = $row['technologia_znakowania'];
        }
        
        echo json_encode($technologies);
?>
