<?php   include_once('polaczenie.php'); 
    
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
       <?php 
           include('style.css');
       ?>
    </style> 
</head>
<body>
    <section class="header">
        <?php 
        if((!isset($_SESSION['zalogowany']))){
            echo '
                <img src="panda.jpg" class="doGory" alt="logo pandagadzety" height="81px" width="200px">
                <a class="button log" href="logowanie.html">Zaloguj się</a> 
            ';
        }
        else {
            echo '
            <div class="headerWrapper">
                <div class="imie"> Witaj '.$_SESSION['imie'].'!</div> 
                <img src="panda.jpg" alt="logo pandagadzety" height="40.5px" width="100px">
                <a class="button wylog" href="logout.php">Wyloguj się</a>
            </div>' ;
        
    ?></section>
    <section class="main">
        <section class="hero">
            <div class="heroWrapper">
                <h1>Dodaj produkt do bazy danych</h1>
        <?php echo' 
        <form action="dodaj.php" method="post" enctype="multipart/form-data">
                <div class="nazwaContainer">
                    <label for="nazwaProd">Nazwa produktu: </label>
                    <input list="nazwyProd" name="nazwaProd" id="nazwaProd" placeholder="nazwa produktu">
                    <datalist id="nazwyProd"></datalist>
                </div>
                <div class="kodProduktu">
                    <label for="kodProduktu">Kod produktu: </label>
                    <input list="kodyProduktu" name="kodProduktu" id="kodProduktu"> 
                    <datalist id="kodyProduktu"></datalist>
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
                <div class="wybor">
                    <div class="wlasny">
                        <label for="wlasny">Produkt własny: </label>
                        <input type="radio" name="wlasny" id="wlasny">
                    </div>
                    <div class="cudzy">
                        <label for="cudzy">Produkt sprowadzony: </label>
                        <input type="radio" name="cudzy" id="cudzy">
                    </div>   
                </div>
                <div class="sprowadzony" id="sprowadzony">
                    <div class="cenaOryginalna">
                        <label for="cenaOryginalna">Cena oryginalna(zł): </label>
                        <input type="text" name="cenaOryginalna" id="cenaOryginalna" placeholder="0.00">
                    </div>
                    <div class="marza">
                        <label for="marza">Marza(%): </label>
                        <input type="text" name="marza" id="marza" placeholder="0">
                    </div>
                </div>
                <div class="cena" id="cena" style="display: flex;">
                    <label for="cenaId">Cena(zł): </label>
                    <input type="text" name="cena" id="cenaId" placeholder="0.00">
                </div>
                <div class="zdjecie">
                    <label for="zdjecie">Zdjęcie(maksymalnie 16MB!): </label>
                    <input type="file" name="zdjecie" id="zdjecie" accept="image/gif, image/jpeg, image/png">
                </div>
                <input type="submit" value="Dodaj produkt" id="dodajProdukt" class="przycisk">
        </form>';}
            ?>
            </div>
            </div>
        </section>
        </section>
        <script>
            getNazwyProd(); toggleTechnologia(); toggleIloscKolorow(); getKolory(); toggleOff('sprowadzony'); toggleOff('cena');
            document.getElementById('zdjecie').addEventListener('change', function() {
                let tablicaZdj = [];
                const plik = document.getElementById('zdjecie').files;
                tablicaZdj.push(plik[0])
                wyswietlZdjecie(tablicaZdj);
            });
            function toggleOff($ktoryOff) {
                document.getElementById($ktoryOff).style.display = 'none';
            }
            function toggleOn($ktoryOn) {
                document.getElementById($ktoryOn).style.display = 'flex';
            }
            document.getElementById('cudzy').addEventListener('click', function() {
                if(document.getElementById('wlasny').checked) {
                    document.getElementById('wlasny').checked = false;
                }
                const $on = 'sprowadzony';
                const $off = 'cena';
                toggleOff($off);
                toggleOn($on);
            });
            document.getElementById('wlasny').addEventListener('click', function() {
                if(document.getElementById('cudzy').checked) {
                    document.getElementById('cudzy').checked = false;
                }
                const $off = 'sprowadzony';
                const $on = 'cena';
                toggleOff($off);
                toggleOn($on);
            });
            function getNazwyProd() { 
                fetch('get_nazwy.php')
                        .then(response => response.json())
                        .then(positions => {
                            positions.forEach(position => {
                                const option = document.createElement('option');
                                option.value = position;
                                document.getElementById("nazwyProd").appendChild(option);
                            });
                        });
            }
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
                    techContainer.style.display = 'flex';
                }
            }
            function toggleIloscKolorow() {
                const techInput = document.getElementById('technologiaZnakowania');
                const iloscKolorowContainer = document.getElementById('iloscKolorowContainer');
                                
                if (techInput.value === 'technologia znakowania' || techInput.value === '') {
                    iloscKolorowContainer.style.display = 'none';
                } else {
                    iloscKolorowContainer.style.display = 'flex';
                }
            } 
            function getKolory() {
                fetch('get_kolory.php')
                        .then(response => response.json())
                        .then(positions => {
                            positions.forEach(position => {
                                const option = document.createElement('option');
                                option.value = position;
                                document.getElementById("kolory").appendChild(option);
                            });
                        });
            } 
            document.getElementById('technologiaZnakowania').addEventListener('change', function() {
                toggleIloscKolorow();
            });
            document.getElementById('pozycjaZnakowania').addEventListener('change', function() {
                const position = this.value;
                toggleTechnologia();
                getTechnologie(position);
            });
            function getKodyProduktu(nazwaProduktu) {
                const kodyList = document.getElementById('kodyProduktu');
                kodyList.innerHTML = '';     
                if(nazwaProduktu) {
                    fetch('get_kod_produktu.php?product=' + encodeURIComponent(nazwaProduktu))
                        .then(response => response.json())
                        .then(positions => {
                            positions.forEach(position => {
                                const option = document.createElement('option');
                                option.value = position;
                                kodyList.appendChild(option);
                            });
                        });
                }
            }
            function getPozycjeZnakowania(nazwaProduktu) {
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
            }
            document.getElementById('nazwaProd').addEventListener('change', function() {
                const nazwaProduktu = document.getElementById('nazwaProd').value;
                getPozycjeZnakowania(nazwaProduktu);
                getKodyProduktu(nazwaProduktu);
            });
        </script>
</body>
</html>