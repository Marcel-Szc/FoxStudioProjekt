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
                <div class="nazwaContainer">
                    <label for="nazwaProd">Nazwa produktu: </label>
                    <input list="nazwyProd" name="nazwaProd" id="nazwaProd" placeholder="nazwa produktu">
                    <datalist id="nazwyProd"></datalist>
                </div>
                <div class="pozycjaContainer">
                    <label for="pozycjaZnakowania">Pozycja znakowania: </label>
                    <input list="pozycjeZnakowania" name="pozycjaZnakowania" id="pozycjaZnakowania" placeholder="pozycja znakowania">
                    <datalist id="pozycjeZnakowania"></datalist>
                </div>
                <div id="technologiaContainer">
                    <label for="technologiaZnakowania">Technologia znakowania: </label>
                    <input list="technologieZnakowania" name="technologiaZnakowania" id="technologiaZnakowania"  placeholder="technologia znakowania">
                    <datalist id="technologieZnakowania"></datalist>
                </div>       
                <div id="iloscKolorowContainer">
                    <label for="iloscKolorow">Ilosc kolorow: </label>   
                    <input type="number" name="iloscKolorow" id="iloscKolorow" value="1">
                </div>
                <div class="kolor">
                    <label for="kolor">Kolor: </label>
                    <input list="kolory" name="kolor" id="kolor">
                    <datalist id="kolory"></datalist>
                </div>
                <div class="kodProduktu">
                    <label for="kodProduktu">Kod produktu: </label>
                    <input list="kodyProduktu" name="kodProduktu" id="kodProduktu"> 
                    <datalist id="kodyProduktu"></datalist>
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
        techList.innerHTML = '';
        
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
            document.getElementById('iloscKolorowContainer').value = '';
        } else {
            techContainer.style.display = 'block';
        }
    }
    toggleTechnologia();
    function toggleIloscKolorow() {
        const techInput = document.getElementById('technologiaZnakowania');
        const iloscKolorowContainer = document.getElementById('iloscKolorowContainer');
                        
        if (techInput.value === 'technologia znakowania' || techInput.value === '') {
            iloscKolorowContainer.style.display = 'none';
        } else {
            iloscKolorowContainer.style.display = 'block';
        }
    }
    toggleIloscKolorow();   
    document.getElementById('pozycjaZnakowania').addEventListener('change', function() {
        toggleTechnologia();
    });
    document.getElementById('technologiaZnakowania').addEventListener('change', function() {
        toggleIloscKolorow();
    });
    document.getElementById('pozycjaZnakowania').addEventListener('change', function() {
        const position = this.value;
        toggleTechnologia();
        getTechnologie(position);
    });
    document.getElementById('nazwaProd').addEventListener('change', function() {
        const nazwaProduktu = document.getElementById('nazwaProd').value;
        const positionsList = document.getElementById('pozycjeZnakowania');
        positionsList.innerHTML = '';     
        if(nazwaProduktu) {
            fetch('get_positions.php?product=' + encodeURIComponent(nazwaProduktu))
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
    document.getElementById('nazwaProd').addEventListener('change', function() {
        const nazwaProduktu = document.getElementById('nazwaProd').value;
        const kodProduktu = document.getElementById('kodyProduktu');
        kodProduktu.innerHTML = '';     
        if(nazwaProduktu) {
            fetch('get_kod_produktu.php?product=' + encodeURIComponent(nazwaProduktu))
                .then(response => response.json())
                .then(positions => {
                    positions.forEach(position => {
                        const option = document.createElement('option');
                        option.value = position;
                        kodProduktu.appendChild(option);
                    });
                });
        }
    });
    </script>
</body>
</html>