<?php   
include_once('polaczenie.php');   
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
            <?php echo' 
                <h1>Dodaj produkt do bazy danych</h1>
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
                        </div>
                    </div>
                        <input type="submit" value="Dodaj produkt" id="dodajProdukt" class="przycisk">
                    </div>
                </form>
                <button id="dodajZnak" class="przycisk">Dodaj opcje znakowania</button>
                <button id="wroc" class="przycisk"><-</button>
                <button id="przod" class="przycisk">-></button>
            ';}
                ?>
            </div>
            </div>
        </section>
        </section>
        <script src="script.js"></script>
</body>
</html>