<?php 
include 'tiles/header.php';

if (!isset($_SESSION['logged'])) {
    header('Location: ./');
}
?>

<?php 

echo "<div class='container'>";
echo "<h1 id='sprung'>Fragenkatalog</h1>";


    echo "<div id='wrapper-filter>";

        echo "<label for='userfilter'>Meine Aufgaben Filter</label><br />";
        echo "<input type='checkbox' id='userfilter' name='userfilter'>";

        echo "<br/>";

        echo "<label for='catfilter'>Kategorie Filter</label><br />";
        echo "<input type='text' id='catfilter' name='catfilter'>";

        echo "<br/>";

        echo "<label for='tagfilter'>Tag Filter</label><br />";
        echo "<input type='text' id='tagfilter' name='tagfilter'>";

    echo "</div>";

echo "<div id='disctext'>Kategorie oder Tag eingeben und Enter klicken!</div>";

echo "<br/>";

echo "<h2>Fragen durchsuchen</h2>";

echo "<div id='loader' class='loader'></div>";

echo "<div id='inhalt'></div>";

echo "<div id='pagi'></div>";

echo "<br/>";

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

const limit = 5;
let page = 1;
let offset = (page - 1) * limit;

let filterTag = '';
let filterCat = '';
let filterUser = false;

apiFilteredExerciseCountCall('', '')
apiFilteredExerciseCall(limit, offset, '', '');

let auswahlAr = [];

async function pages(data) {
    //console.log(data)

    let totalExercise = data;
    let totalPage = Math.ceil(totalExercise / limit);

    pagination(totalPage)

}

    async function getUserId() {
        const req = await fetch('api/getuid');
        const userid = await req.json();
        return userid;
    }

    const inhalt = document.getElementById('inhalt');

    const body = document.createElement('div');
    body.id = 'wrapper-fragenkatalog';

    async function getExerciseOutput(data) {

        loader.style.display = 'none';

        let apiIdCall = getUserId();

        inhalt.innerHTML = '';
        body.innerHTML = '';

        for (let i = 0; i < data.length; i++) {

            if (filterUser) {
                if (data[i].erstelltVon === await apiIdCall) {
                    createElements();
                }
                else {continue}
            } else {
                createElements();
            }

           async function createElements() {
                const box = document.createElement('div');
                box.classList.add('box-fragenkatalog');

                const bild = document.createElement('img');

                if (data[i].bild) {
                    bild.src = data[i].bild;
                    bild.classList.add('fragenkatalog-bild');
                }

                const id = document.createElement('span');
                id.classList.add('fragenkatalog-id');
                const frage = document.createElement('h3');
                frage.classList.add('fragenkatalog-frage');
                const userFrage = document.createElement('span');
                userFrage.classList.add('fragenkatalog-userfrage');

                await apiIdCall == data[i].erstelltVon ? userFrage.innerHTML = `<a class="fragenkatalog-btn" href="trainingsdaten-bearbeiten?aufgabensuche=${data[i].id}">Bearbeiten</a>` : '';

                const awWrapper = document.createElement('div');
                awWrapper.classList.add('awWrapper');

                    const a1 = document.createElement('span');
                    a1.classList.add('fragenkatalog-aw');
                    const a2 = document.createElement('span');
                    a2.classList.add('fragenkatalog-aw');
                    const a3 = document.createElement('span');
                    a3.classList.add('fragenkatalog-aw');
                    const a4 = document.createElement('span');
                    a4.classList.add('fragenkatalog-aw');

                awWrapper.append(a1, a2, a3, a4);

                id.innerHTML = '<b>ID:</b> ' + data[i].id;
                frage.innerText = data[i].frage;

                a1.innerText = data[i].antwort1;
                a2.innerText = data[i].antwort2;
                a3.innerText = data[i].antwort3;
                a4.innerText = data[i].antwort4;

                const cat = document.createElement('div');
                cat.classList.add('fragenkatalog-cat');
                cat.innerText = "Kategorie: " + data[i].kategorie;

                const tag = document.createElement('div');
                tag.classList.add('fragenkatalog-tag');
                tag.innerText = "Tag: " + data[i].tag;

                const voteWrapper = document.createElement('div');
                voteWrapper.classList.add('vote-wrapper');

                const points = document.createElement('span');
                points.classList.add('points');
                points.innerText = await getVoteScore(data[i].id);

                const votepos = document.createElement('button');
                votepos.classList.add('vote');
                votepos.id = 'votepos'; 
                votepos.innerText = '+' ;

                const voteneg = document.createElement('button');
                voteneg.classList.add('vote');
                voteneg.id = 'voteneg';
                voteneg.innerText = '-' ;

                votepos.addEventListener('click', function () {
                    voteApi(data[i].id, 1);
                });

                voteneg.addEventListener('click', function () {
                    voteApi(data[i].id, -1);
                });

                voteWrapper.append(points, votepos, voteneg)

                box.append(id, bild, frage, awWrapper, userFrage, cat, tag, voteWrapper);
                body.append(box);  
                
                const correctAnswers = data[i].antwort.split(',').map(Number);

                correctAnswers.forEach(correctIndex => {
                const correctMessage = document.createElement('span');
                correctMessage.innerText = " - Richtige";
                correctMessage.classList.add('correct');
                if (correctIndex === 0) {
                a1.append(correctMessage);
                a1.classList.add('correct');
                }

                if (correctIndex === 1) {
                a2.append(correctMessage);
                a2.classList.add('correct');
                }
                if (correctIndex === 2) {
                a3.append(correctMessage);
                a3.classList.add('correct');
                }
                if (correctIndex === 3) {
                a4.append(correctMessage);
                a4.classList.add('correct');
                }
                });
           }
        }
        inhalt.append(body);
    }

    async function voteApi(id, wert) {
    let uid = await getUserId();

    const req = await fetch(`./api/getvote?aid=${id}&uid=${uid}`);
    const obj = await req.json();

    if (obj.length > 0) {

        if (obj[0].wert == 1 && wert == -1) {
            await fetch(`./api/deletevote?aid=${id}&uid=${uid}`);
            await fetch(`./api/createvote?wert=${-1}&aid=${id}&uid=${uid}`);
        } else if (obj[0].wert == -1 && wert == 1) {
            await fetch(`./api/deletevote?aid=${id}&uid=${uid}`);
            await fetch(`./api/createvote?wert=${1}&aid=${id}&uid=${uid}`);
        } else if (obj[0].wert == 1 && wert == 1) {
            await fetch(`./api/deletevote?aid=${id}&uid=${uid}`);
        } else if (obj[0].wert == -1 && wert == -1) {
            await fetch(`./api/deletevote?aid=${id}&uid=${uid}`);
        }
    } else if (wert == 1) {
        await fetch(`./api/createvote?wert=${1}&aid=${id}&uid=${uid}`);
    } else if (wert == -1) {
        await fetch(`./api/createvote?wert=${-1}&aid=${id}&uid=${uid}`);
    }

    apiFilteredExerciseCall(limit, offset, '', '');
}

                async function getVoteScore(id) {
                    const reqpos = await fetch(`./api/getvotepos?aid=${id}`);
                    const reqneg = await fetch(`./api/getvoteneg?aid=${id}`);

                    const pos = await reqpos.json();
                    const neg = await reqneg.json();

                    return (pos[0]['count(*)'] - neg[0]['count(*)'])
                    
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

    const userfilterCh = document.getElementById('userfilter');
    const catfilterTxt = document.getElementById('catfilter');
    const tagfilterTxt = document.getElementById('tagfilter');

    userfilterCh.addEventListener('change', function() {
        if (filterUser) {
            filterUser = false;
            apiFilteredExerciseCountCall(filterTag, filterCat);
            apiFilteredExerciseCall(limit, offset, filterTag, filterCat);
        } else {
            filterUser = true;
            apiFilteredExerciseCountCall(filterTag, filterCat);
            apiFilteredExerciseCall(limit, offset, filterTag, filterCat);
        }
    });

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
</script>

<?php 
include 'tiles/footer.php';
?>