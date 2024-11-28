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
            <div class="mainForm">
                <h2>Dane produktu: </h2>
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
            </div>
            <div class="sideForm">
                <div id = "sideFormWrapper1">
                    <h2>Opcje znakowania 1:</h2>
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
                </div>
            </div>
                <input type="submit" value="Dodaj produkt" id="dodajProdukt" class="przycisk">
            </div>
        </form>
        <button id="dodajZnak" class="przycisk">Dodaj opcje znakowania</button>
        <button id="wroc" class="przycisk">Wróć do poprzedniego</button>
        ';}
            ?>
            </div>
            </div>
        </section>
        </section>
        <script>
            function base(){
                getNazwyProd(); getKolory(); toggleOff('sprowadzony');
                document.getElementById('wlasny').checked = true;
                document.getElementById('wroc').style.display = "none";
            }
            base();
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
            let optionCount = 1; // Zmienna do śledzenia liczby opcji znakowania
            let opcjaPowrotu = 0;
            document.getElementById('dodajZnak').addEventListener('click', function() {
                if (optionCount == 1) {
                    document.getElementById('wroc').style.display = "block";
                }
                const sideForm = document.getElementsByClassName('sideForm')[0];
                let sideFormWrapper = "sideFormWrapper" + optionCount;
                document.getElementById(sideFormWrapper).style.display = "none";
                optionCount++;
                 
                const nowy = document.createElement('div');
                let ile = 'sideFormWrapper' + optionCount;
                nowy.setAttribute('id', ile);
                nowy.innerHTML = `
                        <h2>Opcje znakowania ${optionCount}:</h2>
                    <div class="pozycjaContainer">
                        <label for="pozycjaZnakowania${optionCount}">Pozycja znakowania: </label>
                        <input list="pozycjeZnakowania" name="pozycjaZnakowania" id="pozycjaZnakowania${optionCount}" placeholder="pozycja znakowania">
                        <datalist id="pozycjeZnakowania"></datalist>
                    </div>
                    <div id="technologiaContainer">
                        <label for="technologiaZnakowania">Technologia znakowania: </label>
                        <input list="technologieZnakowania" name="technologiaZnakowania" id="technologiaZnakowania${optionCount}"  placeholder="technologia znakowania">
                        <datalist id="technologieZnakowania"></datalist>
                    </div>       
                    <div id="iloscKolorowContainer">
                        <label for="iloscKolorow">Ilosc kolorow: </label>   
                        <input type="number" name="iloscKolorow" id="iloscKolorow${optionCount}" value="1">
                    </div>
                    <div class="kolor">
                        <label for="kolor">Kolor: </label>
                        <input list="kolory" name="kolor" id="kolor${optionCount}">
                        <datalist id="kolory"></datalist>
                    </div>
                `;
                sideForm.appendChild(nowy);
                opcjaPowrotu = optionCount;
            });
            document.getElementById('wroc').addEventListener('click', function() { 
                let sideFormWrapper = "sideFormWrapper" + opcjaPowrotu;
                document.getElementById(sideFormWrapper).style.display = "none";
                opcjaPowrotu--;
                let sideFormUnwrapper = "sideFormWrapper" + opcjaPowrotu;
                document.getElementById(sideFormUnwrapper).style.display = "block";
                if(opcjaPowrotu == 1) {
                    document.getElementById('wroc').style.display = "none";
                }
            });

        </script>
</body>
</html>