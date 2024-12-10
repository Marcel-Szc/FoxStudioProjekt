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

            <div class="headerWrapper">
                <span>
                    <img src="panda.jpg" alt="logo pandagadzety" height="40px" width="100px">
                </span>
            </div>
    <?php
        $files = glob('uploads/*');  

        foreach($files as $file) { 
            if(is_file($file))  
                unlink($file);  
        }   
    ?></section>
    <section class="main">
        <section class="hero">
            <div class="heroWrapper">
                <h1>Dodaj produkt do bazy danych</h1>
                <form action="dodaj.php" method="post" enctype="multipart/form-data">
                    <div class="mainForm">
                        <h2>Dane produktu: </h2>
                        <div class="nazwaContainer">
                            <label for="nazwaProd">Nazwa produktu: </label>
                            <input list="nazwyProd" name="nazwaProd" id="nazwaProd" placeholder="nazwa produktu" required>
                            <datalist id="nazwyProd"></datalist>
                        </div>
                        <div class="kodProduktu">
                            <label for="kodProduktu">Kod produktu: </label>
                            <input placeholder="kod produktu" list="kodyProduktu" name="kodProduktu" id="kodProduktu" required> 
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
                            <input type="text" name="cena" id="cenaId" placeholder="0.00" >
                        </div>
                    </div>
                    <div class="sideForm">
                        
                    </div>
                    <div id="dodajProduktBG">
                    </div>
                    <div id="dodajProduktWrapper">
                        <p>Dane są poprawne, kliknij "Potwierdz dodanie produktu"</p>
                        <ul id="podsumowanie"> </ul>
                        <input type="submit" value="Potwierdz dodanie produktu" id="potwierdzProdukt" class="przycisk">
                    </div>
                </form>
                <button id="powrot" class="przycisk">Powrot</button>
                <button id="dodajProdukt" class="przycisk">Dodaj produkt</button>
                <button id="dodajZnak" class="przycisk">Dodaj opcje znakowania</button>
                <button id="usunZnak" class="przycisk">Usuń opcje znakowania</button>
                <button id="wroc" class="przycisk"><-</button>
                <button id="przod" class="przycisk">-></button>
            </div>
            </div>
        </section>
        </section>
        <script>
            let optionCount =  0;  
            let opcjaPowrotu = 0;  

            // Funkcja inicjalizująca stronę, ustawia domyślne wartości i ukrywa niepotrzebne elementy
            function base() {
                getNazwyProd(); 
                dodajZnakowanie(); 
                toggleOff('sprowadzony'); // Ukrywa sekcję dla produktów sprowadzonych
                document.getElementById('wlasny').checked = true; // Ustawia domyślnie produkt własny
                document.getElementById('wroc').style.display = "none"; // Ukrywa przycisk powrotu
                document.getElementById('przod').style.display = "none"; // Ukrywa przycisk przodu
                document.getElementById('dodajProduktBG').style.display = 'none'; // Ukrywa tło dla dodawania produktu
                document.getElementById('dodajProduktWrapper').style.display = 'none'; // Ukrywa wrapper dla dodawania produktu
                document.getElementById('powrot').style.display = 'none'; // Ukrywa przycisk powrotu
                document.getElementById('usunZnak').style.display = 'none'; // Ukrywa przycisk usuwania znaku
            }  
            base();  

            // Funkcja do ukrywania elementu
            function toggleOff($ktoryOff) {
                document.getElementById($ktoryOff).style.display = 'none'; // Ustawia styl display na none
            }  

            // Funkcja do pokazywania elementu
            function toggleOn($ktoryOn) {
                document.getElementById($ktoryOn).style.display = 'flex'; // Ustawia styl display na flex
            }  

            // Event listener dla przycisku "Produkt sprowadzony"
            document.getElementById('cudzy').addEventListener('click', function() {
                if (document.getElementById('wlasny').checked) {
                    document.getElementById('wlasny').checked = false; // Odznacza produkt własny
                }
                const $on = 'sprowadzony'; // Sekcja do pokazania
                const $off = 'cena'; // Sekcja do ukrycia
                toggleOff($off); // Ukrywa sekcję ceny
                toggleOn($on); // Pokazuje sekcję sprowadzonego produktu
            });  

            // Event listener dla przycisku "Produkt własny"
            document.getElementById('wlasny').addEventListener('click', function() {
                if (document.getElementById('cudzy').checked) {
                    document.getElementById('cudzy').checked = false; // Odznacza produkt sprowadzony
                }
                const $off = 'sprowadzony'; // Sekcja do ukrycia
                const $on = 'cena'; // Sekcja do pokazania
                toggleOff($off); // Ukrywa sekcję sprowadzonego produktu
                toggleOn($on); // Pokazuje sekcję ceny
            });  

            // Funkcja do pobierania nazw produktów
            function getNazwyProd() { 
                fetch('get_nazwy.php') // Wysyła zapytanie do serwera
                    .then(response => response.json()) // Oczekuje na odpowiedź w formacie JSON
                    .then(positions => {
                        positions.forEach(position => {
                            const option = document.createElement('option'); // Tworzy nowy element opcji
                            option.value = position; // Ustawia wartość opcji
                            document.getElementById("nazwyProd").appendChild(option); // Dodaje opcję do datalist
                        });
                    });
            }  

            // Funkcja do pobierania kodów produktu na podstawie nazwy
            function getKodyProduktu(nazwaProduktu) {
                const kodyList = document.getElementById('kodyProduktu'); // Pobiera element datalist dla kodów
                kodyList.innerHTML = ''; // Czyści poprzednie opcje
                if (nazwaProduktu) {
                    fetch('get_kod_produktu.php?product=' + encodeURIComponent(nazwaProduktu)) // Wysyła zapytanie do serwera
                        .then(response => response.json()) // Oczekuje na odpowiedź w formacie JSON
                        .then(positions => {
                            positions.forEach(position => {
                                const option = document.createElement('option'); // Tworzy nowy element opcji
                                option.value = position; // Ustawia wartość opcji
                                kodyList.appendChild(option); // Dodaje opcję do datalist
                            });
                        });
                }
            }  

            // Event listener dla zmiany w polu nazwy produktu
            document.getElementById("nazwaProd").addEventListener('change', function() {
                getKodyProduktu(document.getElementById("nazwaProd").value); // Wywołuje funkcję pobierającą kody produktu
            });  

            // Funkcja do pobierania pozycji znakowania
            function getPozycjeZnakowania(increment) {
                fetch('get_positions.php') // Wysyła zapytanie do serwera
                    .then(response => response.json()) // Oczekuje na odpowiedź w formacie JSON
                    .then(positions => {
                        positions.forEach(position => {
                            const option = document.createElement('option'); // Tworzy nowy element opcji
                            option.value = position; // Ustawia wartość opcji
                            document.getElementById(`pozycjeZnakowania${increment}`).appendChild(option); // Dodaje opcję do datalist
                        });
                    });
            }  

            // Funkcja do pobierania technologii
            function getTechnologie(increment) {
                fetch('get_technologie.php') // Wysyła zapytanie do serwera
                    .then(response => response.json()) // Oczekuje na odpowiedź w formacie JSON
                    .then(technologies => {
                        technologies.forEach(tech => {
                            const option = document.createElement('option'); // Tworzy nowy element opcji
                            option.value = tech; // Ustawia wartość opcji
                            document.getElementById(`technologieZnakowania${increment}`).appendChild(option); // Dodaje opcję do datalist
                        });
                    });
            }  

            // Funkcja do pobierania kolorów
            function getKolory(increment) {
                fetch('get_kolory.php') // Wysyła zapytanie do serwera
                    .then(response => response.json()) // Oczekuje na odpowiedź w formacie JSON
                    .then(positions => {
                        positions.forEach(position => {
                            const option = document.createElement('option'); // Tworzy nowy element opcji
                            option.value = position; // Ustawia wartość opcji
                            document.getElementById(`kolory${increment}`).appendChild(option); // Dodaje opcję do datalist
                        });
                    });
            }  

            // Funkcja do dodawania opcji znakowania
            function dodajZnakowanie() {
                if (optionCount >= 1) {
                    document.getElementById('wroc').style.display = "block"; // Pokazuje przycisk powrotu
                    document.getElementById('usunZnak').style.display = "block"; // Pokazuje przycisk usuwania znaku
                }
                
                if (opcjaPowrotu > 0) {
                    let currentWrapper = "sideFormWrapper" + opcjaPowrotu; // Pobiera aktualny wrapper
                    document.getElementById(currentWrapper).style.display = "none"; // Ukrywa aktualny wrapper
                }
                
                const sideForm = document.getElementsByClassName('sideForm')[0]; // Pobiera element sideForm
                
                optionCount++; // Zwiększa licznik opcji
                
                const nowy = document.createElement('div'); // Tworzy nowy div dla opcji
                let ile = 'sideFormWrapper' + optionCount; // Ustawia ID dla nowego wrappera
                nowy.setAttribute('id', ile); // Ustawia ID dla nowego div
                // Ustawia HTML dla nowego div
                nowy.innerHTML = ` <h2>Opcje znakowania ${optionCount}:</h2>
                    <div class="pozycjaContainer" id="pozycjaContainer${optionCount}">
                        <label for="pozycjaZnakowania${optionCount}"> Pozycja znakowania: </label>
                        <input list="pozycjeZnakowania${optionCount}" name="pozycjaZnakowania${optionCount}" id="pozycjaZnakowania${optionCount}" placeholder="pozycja znakowania">
                        <datalist id="pozycjeZnakowania${optionCount}"></datalist>
                    </div>
                    <div id="technologiaContainer">
                        <label for="technologiaZnakowania">Technologia znakowania: </label>
                        <input list="technologieZnakowania${optionCount}" name="technologiaZnak
                        <input list="technologieZnakowania${optionCount}" name="technologiaZnakowania${optionCount}" id="technologiaZnakowania${optionCount}"  placeholder="technologia znakowania">
                        <datalist id="technologieZnakowania${optionCount}"></datalist>
                    </div>       
                    <div id="iloscKolorowContainer">
                        <label for="iloscKolorow">Ilosc kolorow: </label>   
                        <input type="number" name="iloscKolorow${optionCount}" id="iloscKolorow${optionCount}" value="1">
                    </div>
                    <div class="kolor">
                        <label for="kolor">Kolor: </label>
                        <input list="kolory${optionCount}" name="kolor${optionCount}" id="kolor${optionCount}">
                        <datalist id="kolory${optionCount}"></datalist>
                    </div>
                    <div class="wplyw">
                        <label for="wplyw">Wpływ na cenę(zł): </label>
                        <input name="wplyw${optionCount}" id="wplyw${optionCount}" placeholder="0.00">
                    </div>
                    <div class="zdjecie">
                        <label for="zdjecie">Zdjęcie(maksymalnie 16MB!): </label>
                        <input type="file" name="zdjecie${optionCount}" id="zdjecie${optionCount}" style="border-width: 0px;" accept="image/gif, image/jpeg, image/png" required>
                    </div>`; // Ustawia HTML dla nowego div
                sideForm.appendChild(nowy); // Dodaje nowy div do sideForm
                
                // Ładowanie technologii, pozycji i kolorów dla nowej opcji
                getTechnologie(optionCount); // Wywołuje funkcję do pobierania technologii
                getPozycjeZnakowania(optionCount); // Wywołuje funkcję do pobierania pozycji znakowania
                getKolory(optionCount); // Wywołuje funkcję do pobierania kolorów
                
                opcjaPowrotu = optionCount; // Ustawia opcję powrotu na aktualny licznik
            }  

            // Event listener dla przycisku "Dodaj znakowanie"
            document.getElementById('dodajZnak').addEventListener('click', function() {
                document.getElementById('przod').style.display = "none"; // Ukrywa przycisk przodu
                dodajZnakowanie(); // Wywołuje funkcję dodającą znakowanie
            });  

            // Event listener dla przycisku "Wróć"
            document.getElementById('wroc').addEventListener('click', function() { 
                let sideFormWrapper = "sideFormWrapper" + opcjaPowrotu; // Pobiera aktualny wrapper
                document.getElementById(sideFormWrapper).style.display = "none"; // Ukrywa aktualny wrapper
                opcjaPowrotu--; // Zmniejsza licznik opcji powrotu
                document.getElementById('przod').style.display = "block"; // Pokazuje przycisk przodu
                let sideFormUnwrapper = "sideFormWrapper" + opcjaPowrotu; // Pobiera wrapper do pokazania
                document.getElementById(sideFormUnwrapper).style.display = "block"; // Pokazuje wrapper
                if (opcjaPowrotu == 1) {
                    document.getElementById('wroc').style.display = "none"; // Ukrywa przycisk powrotu, jeśli to pierwsza opcja
                }
            });  

            // Event listener dla przycisku "Przód"
            document.getElementById('przod').addEventListener('click', function() { 
                let sideFormWrapper = "sideFormWrapper" + opcjaPowrotu; // Pobiera aktualny wrapper
                document.getElementById(sideFormWrapper).style.display = "none"; // Ukrywa aktualny wrapper
                opcjaPowrotu++; // Zwiększa licznik opcji powrotu
                document.getElementById('wroc').style.display = "block"; // Pokazuje przycisk powrotu
                let sideFormUnwrapper = "sideFormWrapper" + opcjaPowrotu; // Pobiera wrapper do pokazania
                document.getElementById(sideFormUnwrapper).style.display = "block"; // Pokazuje wrapper
                if (opcjaPowrotu == optionCount) {
                    document.getElementById('przod').style.display = "none"; // Ukrywa przycisk przodu, jeśli to ostatnia opcja
                }
            });  

            // Event listener dla przycisku "Usuń znakowanie"
            document.getElementById('usunZnak').addEventListener('click', function() {
                document.getElementById("sideFormWrapper" + optionCount).innerHTML = ""; // Czyści zawartość ostatniego wrappera
                document.getElementById("sideFormWrapper" + optionCount).remove(); // Usuwa ostatni wrapper
                optionCount--; // Zmniejsza licznik opcji
                if (opcjaPowrotu === optionCount) {
                    document.getElementById('przod').style.display = "none"; // Ukrywa przycisk przodu, jeśli to ostatnia opcja
                }
                if(opcjaPowrotu == optionCount - 1) {
                    document.getElementById("sideFormWrapper" + optionCount).style.display = "flex"; // Pokazuje poprzedni wrapper
                }
                if (optionCount == 1) {
                    document.getElementById('usunZnak').style.display = "none"; // Ukrywa przycisk usuwania, jeśli to jedyna opcja
                    document.getElementById('wroc').style.display = "none"; // Ukrywa przycisk powrotu, jeśli to jedyna opcja
                    document.getElementById('przod').style.display = "none"; // Ukrywa przycisk przodu, jeśli to jedyna opcja
                }
            });  

            // Event listener dla przycisku "Dodaj produkt"
            document.getElementById('dodajProdukt').addEventListener('click', function() {
                const nazwaProd = document.getElementById('nazwaProd').value; // Pobiera nazwę produktu
                const kodProduktu = document.getElementById('kodProduktu').value; // Pobiera kod produktu
                const cenaId = parseFloat(document.getElementById('cenaId').value); // Pobiera cenę
                const cenaOryginalna = parseFloat(document.getElementById('cenaOryginalna').value); // Pobiera cenę oryginalną
                const marza = parseFloat(document.getElementById('marza').value); // Pobiera marżę

                let totalIloscKolorow = 0; // Inicjalizuje zmienną do zliczania kolorów
                let totalWplywNaCene = 0; // Inicjalizuje zmienną do zliczania wpływu na cenę

                if (nazwaProd === '') {
                    alert('Nazwa produktu nie może być pusta.'); // Sprawdza, czy nazwa produktu jest pusta
                    return;
                } 
                if (kodProduktu === '') {
                    alert('Kod produktu nie może być pusty.'); // Sprawdza, czy kod produktu jest pusty
                    return;
                }

                const isWlasnyChecked = document.getElementById('wlasny').checked; // Sprawdza, czy wybrano produkt własny
                const isCudzyChecked = document.getElementById('cudzy').checked; // Sprawdza, czy wybrano produkt sprowadzony

                if (isWlasnyChecked) {
                    if (isNaN(cenaId)) {
                        alert('Cena musi być liczbą'); // Sprawdza, czy cena jest liczbą
                        return;
                    }
                } else if (isCudzyChecked) {
                    if (isNaN(cenaOryginalna)) {
                        alert('Cena oryginalna musi być liczbą'); // Sprawdza, czy cena oryginalna jest liczbą
                        return;
                    }
                    if (isNaN(marza)) {
                        alert('Marza musi być liczbą'); // Sprawdza, czy marża jest liczbą
                        return;
                    }
                } else {
                    alert('Wybierz opcję cenową (własny lub cudzy).'); // Sprawdza, czy wybrano opcję cenową
                    return;
                }

                for (let i = 1; i <= optionCount; i++) {
                    const sideFormWrapper = document.querySelectorAll(`#sideFormWrapper${i} input`); // Pobiera wszystkie inputy w wrapperze
                    for (let j = 0; j < sideFormWrapper.length; j++) {
                        if (sideFormWrapper[j].value.trim() === '') {
                            alert('Wszystkie pola muszą zostać wypełnione!'); // Sprawdza, czy wszystkie pola są wypełnione
                            return;
                        }
                    }

                    const iloscKolorow = parseInt(document.getElementById(`iloscKolorow${i}`).value); // Pobiera ilość kolorów
                    const wplywNaCene = parseFloat(document.getElementById(`wplyw${i}`).value); // Pobiera wpływ na cenę

                    if (isNaN(iloscKolorow) || isNaN(wplywNaCene)) {
                        alert('Ilość kolorów i Wpływ na cenę muszą być liczbą'); // Sprawdza, czy ilość kolorów i wpływ na cenę są liczbami
                        return;
                    }

                    totalIloscKolorow += iloscKolorow; // Zlicza całkowitą ilość kolorów
                    totalWplywNaCene += wplywNaCene; // Zlicza całkowity wpływ na cenę
                }

                let podsumowanie = ''; // Inicjalizuje zmienną do podsumowania
                if (isCudzyChecked) {
                    const calculatedPrice = (cenaOryginalna * 0.57) + (cenaOryginalna * (marza / 100)); // Oblicza cenę dla produktu sprowadzonego
                    podsumowanie = `<li>Nazwa produktu: ${nazwaProd}</li><li> Kod produktu: ${kodProduktu}</li><li> Cena oryginalna: ${cenaOryginalna}</li><li> Marza: ${marza}%</li><li> Cena: ${calculatedPrice.toFixed(2)} zł</li><li> Ilość kolorów: ${totalIloscKolorow}</li><li> Wpływ na cenę: ${totalWplywNaCene}</li>`; // Tworzy podsumowanie dla produktu sprowadzonego
                } else if (isWlasnyChecked) {
                    podsumowanie = `<li>Nazwa produktu: ${nazwaProd}</li><li> Kod produktu: ${kodProduktu}</li><li> Cena: ${cenaId} zł</li><li> Ilość kolorów: ${totalIloscKolorow}</li><li> Wpływ na cenę: ${totalWplywNaCene}</li>`; // Tworzy podsumowanie dla produktu własnego
                }
                document.getElementById('dodajProduktBG').style.display = 'block'; // Pokazuje tło dla dodawania produktu
                document.getElementById('dodajProduktWrapper').style.display = 'flex'; // Pokazuje wrapper dla dodawania produktu
                document.getElementById('powrot').style.display = 'block'; // Pokazuje przycisk powrotu
                document.getElementById('podsumowanie').innerHTML = podsumowanie; // Ustawia podsumowanie w HTML
            });  

            // Event listener dla przycisku "Powrót" w podsumowaniu
            document.getElementById('powrot').addEventListener('click', function() {
                document.getElementById('dodajProduktBG').style.display = 'none'; // Ukrywa tło dla dodawania produktu
                document.getElementById('dodajProduktWrapper').style.display = 'none'; // Ukrywa wrapper dla dodawania produktu
                document.getElementById('powrot').style.display = 'none'; // Ukrywa przycisk powrotu
            });  
        </script>
</body>
</html>