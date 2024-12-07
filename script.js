let optionCount = 0;
let opcjaPowrotu = 0;
function base(){
    getNazwyProd(); dodajZnakowanie(); toggleOff('sprowadzony');
    document.getElementById('wlasny').checked = true;
    document.getElementById('wroc').style.display = "none";
    document.getElementById('przod').style.display = "none";
}
base();
function toggleOff($ktoryOff) {
    document.getElementById($ktoryOff).style.display = 'none';
}
function toggleOn($ktoryOn) {
    document.getElementById($ktoryOn).style.display = 'flex';
}
document.getElementById('cudzy').addEventListener('click', function() {
    if(document.getElementById('wlasny').checked) {
        document.getElementById('wlasny').checked = false;
        document.getElementById('wlasny').unchecked;
    }
    const $on = 'sprowadzony';
    const $off = 'cena';
    toggleOff($off);
    toggleOn($on);
});
document.getElementById('wlasny').addEventListener('click', function() {
    if(document.getElementById('cudzy').checked) {
        document.getElementById('cudzy').checked = false;
        document.getElementById('cudzy').unchecked;
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
document.getElementById("nazwaProd").addEventListener('change', function() {
    getKodyProduktu(document.getElementById("nazwaProd").value);
});
function getPozycjeZnakowania(increment) {
    fetch('get_positions.php')
        .then(response => response.json())
        .then(positions => {
            positions.forEach(position => {
                const option = document.createElement('option');
                option.value = position;
                document.getElementById(`pozycjeZnakowania${increment}`).appendChild(option);
            });
        });
}
function getTechnologie(increment) {;
        fetch('get_technologie.php')
            .then(response => response.json())
            .then(technologies => {
                technologies.forEach(tech => {
                    const option = document.createElement('option');
                    option.value = tech;
                    document.getElementById(`technologieZnakowania${increment}`).appendChild(option);
                });
            });
}   
function getKolory(increment) {
    fetch('get_kolory.php')
            .then(response => response.json())
            .then(positions => {
                positions.forEach(position => {
                    const option = document.createElement('option');
                    option.value = position;
                    document.getElementById(`kolory${increment}`).appendChild(option);
                });
            });
} 
function dodajZnakowanie() {
    if (optionCount >= 1) {
        document.getElementById('wroc').style.display = "block";
    }
    
    // Hide the currently displayed option
    if (opcjaPowrotu > 0) {
        let currentWrapper = "sideFormWrapper" + opcjaPowrotu;
        document.getElementById(currentWrapper).style.display = "none";
    }
    
    const sideForm = document.getElementsByClassName('sideForm')[0];
    
    optionCount++;
        
    const nowy = document.createElement('div');
    let ile = 'sideFormWrapper' + optionCount;
    nowy.setAttribute('id', ile);
    nowy.innerHTML = `
            <h2>Opcje znakowania ${optionCount}:</h2>
        <div class="pozycjaContainer" id="pozycjaContainer${optionCount}">
            <label for="pozycjaZnakowania${optionCount}"> Pozycja znakowania: </label>
            <input list="pozycjeZnakowania${optionCount}" name="pozycjaZnakowania${optionCount}" id="pozycjaZnakowania${optionCount}" placeholder="pozycja znakowania">
            <datalist id="pozycjeZnakowania${optionCount}"></datalist>
        </div>
        <div id="technologiaContainer">
            <label for="technologiaZnakowania">Technologia znakowania: </label>
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
        </div>
    `;
    sideForm.appendChild(nowy);
    
    // Load technologies, positions, and colors for the new option
    getTechnologie(optionCount);
    getPozycjeZnakowania(optionCount);
    getKolory(optionCount);
    
    opcjaPowrotu = optionCount;
}
document.getElementById('dodajZnak').addEventListener('click', function(){
    document.getElementById('przod').style.display = "none";
    dodajZnakowanie();
});
document.getElementById('wroc').addEventListener('click', function() { 
    let sideFormWrapper = "sideFormWrapper" + opcjaPowrotu;
    document.getElementById(sideFormWrapper).style.display = "none";
    opcjaPowrotu--;
    console.log(opcjaPowrotu);
    document.getElementById('przod').style.display = "block";
    let sideFormUnwrapper = "sideFormWrapper" + opcjaPowrotu;
    document.getElementById(sideFormUnwrapper).style.display = "block";
    if(opcjaPowrotu == 1) {
        document.getElementById('wroc').style.display = "none";
    }
});
document.getElementById('przod').addEventListener('click', function() { 
    let sideFormWrapper = "sideFormWrapper" + opcjaPowrotu;
    document.getElementById(sideFormWrapper).style.display = "none";
    opcjaPowrotu++;
    document.getElementById('wroc').style.display = "block";
    let sideFormUnwrapper = "sideFormWrapper" + opcjaPowrotu;
    document.getElementById(sideFormUnwrapper).style.display = "block";
    if(opcjaPowrotu == optionCount) {
        document.getElementById('przod').style.display = "none";
    }
});
