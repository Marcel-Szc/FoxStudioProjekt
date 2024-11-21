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
<body onload="getNazwyProd(), toggleTechnologia(), toggleIloscKolorow(), toggleOff('sprowadzony'), toggleOff('cena')">
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
                        <label for="cenaOryginalna">Cena oryginalna: </label>
                        <input type="text" name="cenaOryginalna" id="cenaOryginalna" placeholder="0.00">
                        <label for="cenaOryginalna">zł</label>
                    </div>
                    <div class="marza">
                        <label for="marza">Marza: </label>
                        <input type="text" name="marza" id="marza" placeholder="0">
                        <label for="marza">%</label>
                    </div>
                </div>
                <div class="cena" id="cena">
                    <label for="cena">Cena: </label>
                    <input type="text" name="cena" id="cena" placeholder="0.00">
                    <label for="cena">zł</label>
                </div>
                <div class="zdjecie">
                    <label for="zdjecie">Zdjęcie: </label>
                    <input type="file" name="zdjecie" id="zdjecie" accept="image/gif, image/jpeg, image/png">
                    <div id="pokazZdjecie"></div>
                </div>
                <input type="submit" value="Dodaj produkt" id="dodajProdukt">
            </form>
            <div id="podsumowanie">
                <h2>Podsumowanie</h2>
                <div class="podsumowanieContainer">
                    <div id="cena_produktu"></div>
                    <div id="koszt_przygotowania"></div>
                    <div id="cena_nadruku"></div>
                    <div id="cena_jednostkowa"></div>
                    <div id="marza"></div>
                </div>
            </div>
        </section>
    <script src="formularz.js"></script>
    <script src="podsumowanie.js"></script>
    <script src="zabezpieczenia.js"></script>
</body>
</html>