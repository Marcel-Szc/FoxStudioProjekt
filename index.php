<?php   include_once('polaczenie.php'); 
    
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
    <?php 
        if($_SESSION['zalogowany'] == true){
            echo '<div class=imie>'.$_SESSION['imie'].'</div>';
            echo '<a href="logout.php">Wyloguj</a>';
        }
        else {
            header('Location: logowanie.php');
        }
    ?>
    </header>
        <section class="main">
            <form action="dodaj.php" method="post">
                <label for="nazwaProd">Nazwa produktu: </label>
                <input list="nazwyProd" name="nazwaProd" id="nazwaProd">
                <datalist id="nazwyProd"></datalist>

                <label for="pozycjaZnakowania">Kod znakowania: </label>
                <input type="text" name="pozycjaZnakowania" id="pozycjaZnakowania" list="pozycjeZnakowania" onchange="toggleTechnologia()">
                <datalist id="pozycjeZnakowania"></datalist>

                <div id="technologiaContainer">
                    <label for="technologiaZnakowania">Technologia znakowania: </label>
                    <input type="text" name="technologiaZnakowania" id="technologiaZnakowania" list="technologieZnakowania">
                    <datalist id="technologieZnakowania"></datalist>
                </div>

                <input type="submit" value="Dodaj produkt">
            </form>
        </section>
<script>
    function getNazwyProd() { 
        fetch('get_nazwy.php')
                .then(response => response.json())
                .then(positions => {
                    positions.forEach(position => {
                        const option = document.createElement('option');
                        option.value = position;
                        document.getElementById("nazwyProd").appendChild(option);
                        console.log(position);
                    });
                });
    }
    getNazwyProd();

    function getTechnologie(position) {
        const techList = document.getElementById("technologieZnakowania");
        techList.innerHTML = ''; // Clear existing options
        
        if(position && position.toLowerCase() !== 'bez znakowania') {
            fetch('get_technologie.php?position=' + encodeURIComponent(position))
                .then(response => response.json())
                .then(technologies => {
                    technologies.forEach(tech => {
                        const option = document.createElement('option');
                        option.value = tech;
                        techList.appendChild(option);
                    });
                });
        }
    }
    function toggleTechnologia() {
        const pozycjaInput = document.getElementById('pozycjaZnakowania');
        const techContainer = document.getElementById('technologiaContainer');
                        
        if (pozycjaInput.value.toLowerCase() === 'bez znakowania' || pozycjaInput.value === '') {
            techContainer.style.display = 'none';
            document.getElementById('technologiaZnakowania').value = '';
        } else {
            techContainer.style.display = 'block';
        }
    }
    toggleTechnologia();
    document.getElementById('nazwaProd').addEventListener('change', function() {
        const productName = this.value;
        const positionsList = document.getElementById('pozycjeZnakowania');
            
        // Clear existing options
        positionsList.innerHTML = '';
            
        if(productName) {
            fetch('get_positions.php?product=' + encodeURIComponent(productName))
                .then(response => response.json())
                .then(positions => {
                    positions.forEach(position => {
                        const option = document.createElement('option');
                        option.value = position;
                        positionsList.appendChild(option);
                    });
                });
        }
    });

    document.getElementById('pozycjaZnakowania').addEventListener('change', function() {
        const position = this.value;
        toggleTechnologia();
        getTechnologie(position);
    });
    
    </script>
</body>
</html>