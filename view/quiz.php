<?php
include 'tiles/header.php';
if (!isset($_SESSION['logged'])) {
    header('Location: ./');
}
?>

<?php 

$starttime = time();
?>

<div class="container">
    <h1>Quiz</h1>

    <div id="quiz">
        <div id="timer"></div>
        <div id="loader" class="loader"></div>
    </div>


    <script>
    
    // load animation
    const loader = document.getElementById('loader');
    loader.style.display = 'block';

    // countdown
    const startTime = <?php echo $starttime; ?> * 1000;
    const endTime = startTime + 90 * 60 * 1000;
    const timerInterval = setInterval(updateTimer, 1000);

    function updateTimer() {
        const currentTime = Math.floor(Date.now());
        const remainingTime = endTime - currentTime;

        if (remainingTime <= 0) {
            clearInterval(timerInterval);
            auswerten(checkBoxAr);
            return;
        }

        const minutes = Math.floor(remainingTime / (1000 * 60));
        const seconds = Math.floor((remainingTime % (1000 * 60)) / 1000);
        document.getElementById('timer').innerText = `${minutes}m ${seconds}s`;
    }


// prepare data
let data = [];
let answers = [];

const quiz = document.getElementById('quiz');

let seite = 0;
let checkBoxAr = [];
let quizId = getQuizId();

async function getQuizId() {
    const request = await fetch(`./api/getquizid`);
    const id = await request.json();
    await getQuizData(id)
}

async function getQuizData(id) {
    const request = await fetch(`./api/getquiz?quizid=${id}`);
    const obj = await request.json();
    createData(obj)
}

async function createData(obj) {
    try {
        for(d of obj) {
            data.push( 
                {
                    'id': d.id,
                    'frage': d.frage,
                    'a1': d.antwort1,
                    'a2': d.antwort2,
                    'a3': d.antwort3,
                    'a4': d.antwort4,
                    'corrects' : d.antwort,
                    'ersteller': d.erstelltVon,
                    'bild': d.bild
                }
            );
        }

    // setup
    shuffleArray(data);
    init();
    loader.style.display = 'none';
    frageAusgabe(seite);
    ausgaben(seite);
    quiz.append(anzahlFragen, fragenBox, btnback, btnweiter);
    checkboxMarkerBrain();
    updateTimer();

    } catch (err) {
        console.log("Error")
        quiz.innerHTML = "Fehler " + err + " gehe zurück ins Dashboard und versuche ein neues Quiz zu starten.";
    }
}

// Fisher-Yates (Knuth) Shuffle Algorithm
// sorgt dafür, dass alle Stellen fair getauscht werden.
function shuffleArray(array) {
    for (let i = array.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [array[i], array[j]] = [array[j], array[i]];
    }
}

// elements

const anzahlFragen = document.createElement('div');
anzahlFragen.classList.add('fragen-anzahl');

const a1wrapper = document.createElement('div');
a1wrapper.classList.add("a1");
const a2wrapper = document.createElement('div');
a2wrapper.classList.add("a2");
const a3wrapper = document.createElement('div');
a3wrapper.classList.add("a3");
const a4wrapper = document.createElement('div');
a4wrapper.classList.add("a4");

const bildBox = document.createElement('div');
bildBox.classList.add('bildbox');

const fragenBox = document.createElement('div');
fragenBox.classList.add('fragen-box');

const bild = document.createElement('img');
bild.classList.add('bild');
const frage = document.createElement('div');
frage.classList.add('frage');
const a1 = document.createElement('div');
const a2 = document.createElement('div');
const a3 = document.createElement('div');
const a4 = document.createElement('div');

const a1c = document.createElement('input');
a1c.type = 'checkbox';
a1c.id = 'a1c';
a1c.classList.add('check');
const a2c = document.createElement('input');
a2c.type = 'checkbox';
a2c.id = 'a2c';
a2c.classList.add('check');
const a3c = document.createElement('input');
a3c.type = 'checkbox';
a3c.id = 'a3c';
a3c.classList.add('check');
const a4c = document.createElement('input');
a4c.type = 'checkbox';
a4c.id = 'a4c';
a4c.classList.add('check');

const btnweiter = document.createElement('button');
btnweiter.innerHTML = "Weiter";
btnweiter.classList.add('wz-btn');
const btnback = document.createElement('button');
btnback.innerHTML = "Zurück";
btnback.classList.add('wz-btn');


function frageAusgabe(seite) {
  anzahlFragen.innerText = "Frage " + (seite + 1) + " von " + data.length;
}

function ausgaben(seite) {

if(data[seite].bild) {
    bild.src = data[seite].bild;
} else {
    bild.src = '';
}


  frage.innerText = data[seite].frage;


  a1.innerText = data[seite].a1;
  a2.innerText = data[seite].a2;
  a3.innerText = data[seite].a3;
  a4.innerText = data[seite].a4;


  const a1wrapper = document.createElement('div');
  a1wrapper.classList.add("a1");
  a1wrapper.classList.add("a-text");
  const a2wrapper = document.createElement('div');
  a2wrapper.classList.add("a2");
  a2wrapper.classList.add("a-text");
  const a3wrapper = document.createElement('div');
  a3wrapper.classList.add("a2");
  a3wrapper.classList.add("a-text");
  const a4wrapper = document.createElement('div');
  a4wrapper.classList.add("a4");  
  a4wrapper.classList.add("a-text");

  a1wrapper.append(a1c, a1);
  a2wrapper.append(a2c, a2);
  a3wrapper.append(a3c, a3);
  a4wrapper.append(a4c, a4);

  bildBox.append(bild);

    fragenBox.append(bildBox, frage, a1wrapper, a2wrapper, a3wrapper, a4wrapper);

  const checkboxState = checkBoxAr[seite][1];
  a1c.checked = checkboxState[0];
  a2c.checked = checkboxState[1];
  a3c.checked = checkboxState[2];
  a4c.checked = checkboxState[3];
}

function checkBoxMarker(id, seite) {
  const index = id === 'a1c' ? 0 : id === 'a2c' ? 1 : id === 'a3c' ? 2 : 3;
  checkBoxAr[seite][1][index] = document.getElementById(id).checked;
}

function auswerten(checkBoxAr) {
  quiz.innerHTML = "";

  let punkte = 0;
  const punkteDiv = document.createElement('div');
  punkteDiv.innerText = "Punkte: " + punkte;
  punkteDiv.classList.add('fragen-stat')
  quiz.append(punkteDiv);

  for (let i = 0; i < data.length; i++) {

      const bildBox = document.createElement('div');
      bildBox.classList.add('bildbox');

      const bild = document.createElement('img');
      bild.classList.add('bild');

      const fragenBox = document.createElement('div');
      fragenBox.classList.add('fragen-box');

      const frage = document.createElement('div');
      frage.classList.add('frage');
      
      const a1wrapper = document.createElement('div');
      a1wrapper.classList.add("a1");
      a1wrapper.classList.add("a-text");
      const a2wrapper = document.createElement('div');
      a2wrapper.classList.add("a2");
      a2wrapper.classList.add("a-text");
      const a3wrapper = document.createElement('div');
      a3wrapper.classList.add("a2");
      a3wrapper.classList.add("a-text");
      const a4wrapper = document.createElement('div');
      a4wrapper.classList.add("a4"); 
      a4wrapper.classList.add("a-text"); 

      const a1 = document.createElement('div');
      const a2 = document.createElement('div');
      const a3 = document.createElement('div');
      const a4 = document.createElement('div');
      
      const a1c = document.createElement('input');
      a1c.type = 'checkbox';
      a1c.id = 'a1c';
      a1c.disabled = true;
      a1c.classList.add('check');
      const a2c = document.createElement('input');
      a2c.type = 'checkbox';
      a2c.id = 'a2c';
      a2c.disabled = true;
      a2c.classList.add('check');
      const a3c = document.createElement('input');
      a3c.type = 'checkbox';
      a3c.id = 'a3c';
      a3c.disabled = true;
      a3c.classList.add('check');
      const a4c = document.createElement('input');
      a4c.type = 'checkbox';
      a4c.id = 'a4c';
      a4c.disabled = true;
      a4c.classList.add('check');

      if (data[i].bild) {
        bild.src = data[i].bild;
      }

      frage.innerText = data[i].frage;

      a1.innerText = data[i].a1;
      a2.innerText = data[i].a2;
      a3.innerText = data[i].a3;
      a4.innerText = data[i].a4;

      const checkboxState = checkBoxAr[i][1];
      a1c.checked = checkboxState[0];
      a2c.checked = checkboxState[1];
      a3c.checked = checkboxState[2];
      a4c.checked = checkboxState[3];

      const correctAnswers = data[i].corrects.split(',').map(Number);

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

      let isCorrect = true;
      correctAnswers.forEach(correctIndex => {
          if (!checkboxState[correctIndex]) {
              isCorrect = false;
          }
      });

      for (let j = 0; j < checkboxState.length; j++) {
          if (checkboxState[j] && !correctAnswers.includes(j)) {
              isCorrect = false;
          }
      }

      if (isCorrect) {
          punkte++;
          frage.classList.add('correct');
      } else {
          frage.classList.add('incorrect');
      }

      a1wrapper.append(a1c, a1);
      a2wrapper.append(a2c, a2);
      a3wrapper.append(a3c, a3);
      a4wrapper.append(a4c, a4);

      bildBox.append(bild);

      fragenBox.append(bildBox, frage, a1wrapper, a2wrapper, a3wrapper, a4wrapper);
      quiz.append(fragenBox);
  }

  punkteDiv.innerText = "Punkte: " + punkte + " von " + data.length + " das sind: " + (punkte/data.length*100).toFixed(2) + " %";
}

btnweiter.addEventListener('click', () => {
  if (seite < data.length - 1) {
      seite += 1;
  }
  frageAusgabe(seite);
  ausgaben(seite);

  if (btnweiter.innerHTML == "Auswerten") {
      confirm("Test abgeben?") ? auswerten(checkBoxAr) : console.log("abgebrochen");
  }

  if (seite === data.length - 1) {
      btnweiter.innerHTML = "Auswerten";
  }
});

btnback.addEventListener('click', () => {
  if (seite > 0) {
      seite -= 1;
  }
  frageAusgabe(seite);
  ausgaben(seite);

  if (seite < data.length - 1) {
      btnweiter.innerHTML = "Weiter";
  }
});

function init() {
  for (let i = 0; i < data.length; i++) {
      checkBoxAr.push([i, [false, false, false, false]]);
  }

}

function checkboxMarkerBrain() {

const checkBoxIds = ['a1c', 'a2c', 'a3c', 'a4c'];

    checkBoxIds.forEach(id => {
        const checkbox = document.getElementById(id);
        if (checkbox) {
            checkbox.addEventListener('change', () => {
                checkBoxMarker(id, seite);
            });
        }
    });
}

    </script>

    <?php
    include 'tiles/footer.php';
    ?>