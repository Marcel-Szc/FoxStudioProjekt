<?php
    include_once('polaczenie.php');

    if(isset($_GET['position'])) {
        $position = $_GET['position'];
        $zapytanieTech = "SELECT DISTINCT technologia_znakowania FROM produkty WHERE pozycja_znakowania = ? AND technologia_znakowania IS NOT NULL ORDER BY technologia_znakowania ASC";
        $stmt = $polaczenie->prepare($zapytanieTech);
        $stmt->bind_param("s", $position);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $technologies = array();
        while($row = $result->fetch_assoc()) {
            $technologies[] = $row['technologia_znakowania'];
        }
        
        echo json_encode($technologies);
    }
?>
