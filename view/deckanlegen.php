<?php 
include 'tiles/header.php';
if (!isset($_SESSION['logged'])) {
    header('Location: ./');
}
?>

<?php 

echo "<div class='container'>";

if (isset($_SESSION['neues-deck'])) {
    echo $_SESSION['neues-deck'];
    unset($_SESSION['neues-deck']);
}

echo "<div id='status'></div>";

echo "<h1 id='sprung'>Neues Deck anlegen</h1>";

echo "<label for='deckname'>Deckname</label><br />";
echo "<input type='text' id='deckname' name='deckname' required>";

echo "<h2>Fragen auswählen</h2>";

echo "<div id='loader' class='loader'></div>";

echo "<table id='tb-deckanlegen'></table>";

echo "<div id='pagi'></div>";

echo "<br/>";

echo "<div id='wrapper-filter>";

echo "<label for='catfilter'>Kategorie Filter</label><br />";
echo "<input type='text' id='catfilter' name='catfilter'>";

echo "<br/>";

echo "<label for='tagfilter'>Tag Filter</label><br />";
echo "<input type='text' id='tagfilter' name='tagfilter'>";

echo "</div>";

echo "<br/>";

echo "<div id='disctext'>Kategorie oder Tag eingeben und Enter klicken!</div>";


echo "<br/>";

echo "<input type='button' id='deckanlegen' value='Deck anlegen'>";

echo "</div>";

echo "</div>";



?>

<script>

const loader = document.getElementById('loader');

loader.style.display = 'block';

async function apiExerciseCall(limit, offset) {
    let getObject = await fetch(`./api/getexercise?limit=${limit}&offset=${offset}`)
    .catch(err => {
        console.log("connection error " + err.getObject.data);
    });
    let getJson = await getObject.json();
    //console.log(getJson);
}

async function apiFilteredExerciseCall(limit, offset, tag, cat) {
    let getObject = await fetch(`./api/getfilteredexercise?limit=${limit}&offset=${offset}&tag=${tag}&cat=${cat}`)
    .catch(err => {
        console.log("connection error " + err.getObject.data);
    });
    let getJson = await getObject.json();

    const event = new CustomEvent('apiFilteredExerciseCallEvent', { detail: getJson });
    document.dispatchEvent(event);

    //console.log(getJson);
}

async function apiFilteredExerciseCountCall(tag, cat) {
    let getObject = await fetch(`./api/getfilteredexercisecount?tag=${tag}&cat=${cat}`)
    .catch(err => {
        console.log("connection error " + err.getObject.data);
    });
    let getJson = await getObject.json();

    const event = new CustomEvent('apiFilteredExerciseCountCallEvent', { detail: getJson });
    document.dispatchEvent(event);

    //console.log(getJson);
}

document.addEventListener('apiFilteredExerciseCallEvent', (event) => {
    getExerciseOutput(event.detail);
});

document.addEventListener('apiFilteredExerciseCountCallEvent', (event) => {
    pages(event.detail);

});

const limit = 10;
let page = 1;
let offset = (page - 1) * limit;

let filterTag = '';
let filterCat = '';

apiFilteredExerciseCountCall('', '')
apiFilteredExerciseCall(limit, offset, '', '');

let auswahlAr = [

];
async function pages(data) {
    //console.log(data)

    let totalExercise = data;
    let totalPage = Math.ceil(totalExercise / limit);

    pagination(totalPage)

}

const table = document.getElementById('tb-deckanlegen');

const trtitel= document.createElement('tr');

const thauswahl = document.createElement('th');
thauswahl.innerText = 'Auswahl';

const thid = document.createElement('th');
thid.innerText = 'ID';

const thfrage = document.createElement('th');
thfrage.innerText = 'Frage';

const thtag = document.createElement('th');
thtag.innerText = 'TAG';

const thkat = document.createElement('th');
thkat.innerText = 'Kategorie';

const thdate = document.createElement('th');
thdate.innerText = 'Erstellt am';

trtitel.append(thauswahl, thid, thfrage, thtag, thkat, thdate);
table.append(trtitel);

    async function getExerciseOutput(data) {

        loader.style.display = 'none';

        table.innerHTML = '';
        table.append(trtitel);
       
        for (let i = 0; i < data.length; i++) {
            trbody = document.createElement('tr');
            const tdcheckbox = document.createElement('td');
            const checkbox = document.createElement('input');
            checkbox.type = 'checkbox';
            checkbox.id = data[i].id;

            checkbox.checked = auswahlAr.some(e => e.id === data[i].id);

            tdcheckbox.append(checkbox);

            checkbox.addEventListener('change', (e) => {

                // array für selected box 

                const result = auswahlAr.filter(e => e.id === data[i].id);

                if (result.length === 0) {
                    auswahlAr.push({'id': data[i].id, 'frage': data[i].frage});
                } else {
                    let hilfsar = auswahlAr.filter(e => e.id !== data[i].id);
                    auswahlAr = hilfsar;
                }
            });

            const tdid = document.createElement('td');
            tdid.innerText = data[i].id;
            const tdfrage = document.createElement('td');
            tdfrage.innerText = data[i].frage;
            const tdtag = document.createElement('td');
            tdtag.innerText = data[i].tag;
            const tdkat = document.createElement('td');
            tdkat.innerText = data[i].kategorie
            const tddate = document.createElement('td');
            tddate.innerText = data[i].erstelltAm;
            trbody.append(tdcheckbox, tdid, tdfrage, tdtag, tdkat, tddate);
            table.append(trbody);
        }

    }

const paginationDiv = document.getElementById('pagi');

    async function pagination(count) {

        paginationDiv.innerHTML = '';
        for (let i = 0; i < count; i++) {
            const btn = document.createElement('input');
            btn.type = 'button';
            btn.value = i + 1;

            btn.addEventListener('click', () => {
                page = btn.value;
                offset = (page - 1) * limit;
                apiFilteredExerciseCall(limit, offset, filterTag, filterCat);
                document.getElementById('sprung').scrollIntoView({ behavior: 'smooth' });
            });
            
            paginationDiv.append(btn);
        }
    }

    const statusmsg = document.getElementById('status');

    async function deckPost(data, deckname) {
    try {
        const response = await fetch('./neues-deck', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({data, deckname})
        });

        if (!response.ok) {
            
            throw new Error(`HTTP error! Status: ${response.status}`);
        }

        const result = await response.json();

        if (result.status === 'success') {
            
            statusmsg.innerText = result.message;
            window.location.href = './neues-deck';
            
        } else {
            statusmsg.innerText = result.message;
            console.log("Error: " + result.message);
        }

    } catch (error) {
        
        console.error("Fetch error: ", error);
    }
}


    const catfilterTxt = document.getElementById('catfilter');
    const tagfilterTxt = document.getElementById('tagfilter');

    catfilterTxt.addEventListener('change', () => {
    filterCat = catfilterTxt.value.toLowerCase();
    apiFilteredExerciseCountCall(filterTag, filterCat);
    apiFilteredExerciseCall(limit, offset, filterTag, filterCat);
    });

    tagfilterTxt.addEventListener('change', () => {
    filterTag = tagfilterTxt.value.toLowerCase();
    apiFilteredExerciseCountCall(filterTag, filterCat);
    apiFilteredExerciseCall(limit, offset, filterTag, filterCat);
    });

    const deckanlegenbtn = document.getElementById('deckanlegen');
    const deckname = document.getElementById('deckname');

    deckanlegenbtn.addEventListener('click', () => {
/*
        if (deckname.value.length <= 0) {
            alert('Wähle einen Decknamen und füge dem Deck Aufgaben hinzu.');
        } else {
            const decknameInhalt = deckname.value;

            deckPost(auswahlAr, decknameInhalt);
        }
*/
        const decknameInhalt = deckname.value;

            deckPost(auswahlAr, decknameInhalt);
    });

    /*
    window.addEventListener('beforeunload', (e) => {
        const confirmMsg = "Sicher, dass du diese Seite verlassen willst? Eingaben werden womöglich nicht gespeichert!";

        e.returnValue = confirmMsg;

        return confirmMsg;
    });
*/

</script>

<?php 
include 'tiles/footer.php';
?>