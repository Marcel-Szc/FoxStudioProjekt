function wyswietlZdjecie(tablicaZdj) {
    let zdjecia = ""
    tablicaZdj.forEach((zdj) => {
    zdjecia += `<img src="${URL.createObjectURL(zdj)}" alt="image" height="25%" width="25%">`;
    });
    document.getElementById('pokazZdjecie').innerHTML = zdjecia;
}
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
    document.getElementById($ktoryOn).style.display = 'block';
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
                    console.log(position);
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
        techContainer.style.display = 'block';
    }
}
function toggleIloscKolorow() {
    const techInput = document.getElementById('technologiaZnakowania');
    const iloscKolorowContainer = document.getElementById('iloscKolorowContainer');
                    
    if (techInput.value === 'technologia znakowania' || techInput.value === '') {
        iloscKolorowContainer.style.display = 'none';
    } else {
        iloscKolorowContainer.style.display = 'block';
    }
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