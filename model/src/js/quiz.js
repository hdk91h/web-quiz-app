
/*
const data = [
  {"id": 1, "frage" : "Was ist 2 + 2?", "a1": "4", "a2": "1", "a3": "3", "a4": "5", "corrects": "0"},
  {"id": 2, "frage": "Was ist 2 - 1?", "a1": "1", "a2": "1", "a3": "3", "a4": "5", "corrects": "1"},
  {"id": 3, "frage": "Was ist 2 + 3?", "a1": "5", "a2": "1", "a3": "3", "a4": "5", "corrects": "0, 1, 2, 3"}
];

const quiz = document.getElementById('quiz');
let seite = 0;
let checkBoxAr = [];

init();

const anzahlFragen = document.createElement('div');

const a1wrapper = document.createElement('div');
a1wrapper.classList.add("a1");
const a2wrapper = document.createElement('div');
a2wrapper.classList.add("a2");
const a3wrapper = document.createElement('div');
a3wrapper.classList.add("a3");
const a4wrapper = document.createElement('div');
a4wrapper.classList.add("a4");

const fragenBox = document.createElement('div');
fragenBox.classList.add('fragen-box');

const frage = document.createElement('div');
frage.classList.add('frage');
const a1 = document.createElement('div');
const a2 = document.createElement('div');
const a3 = document.createElement('div');
const a4 = document.createElement('div');

const a1c = document.createElement('input');
a1c.type = 'checkbox';
a1c.id = 'a1c';
const a2c = document.createElement('input');
a2c.type = 'checkbox';
a2c.id = 'a2c';
const a3c = document.createElement('input');
a3c.type = 'checkbox';
a3c.id = 'a3c';
const a4c = document.createElement('input');
a4c.type = 'checkbox';
a4c.id = 'a4c';

const btnweiter = document.createElement('button');
btnweiter.innerHTML = "Weiter";
const btnback = document.createElement('button');
btnback.innerHTML = "Zurück";

frageAusgabe(seite);
ausgaben(seite);

quiz.append(anzahlFragen, fragenBox, btnback, btnweiter);

function frageAusgabe(seite) {
  anzahlFragen.innerText = "Frage " + (seite + 1) + " von " + data.length;
}

function ausgaben(seite) {
  frage.innerText = data[seite].frage;
  a1.innerText = data[seite].a1;
  a2.innerText = data[seite].a2;
  a3.innerText = data[seite].a3;
  a4.innerText = data[seite].a4;
  
  const a1wrapper = document.createElement('div');
  a1wrapper.classList.add("a1");
  const a2wrapper = document.createElement('div');
  a2wrapper.classList.add("a2");
  const a3wrapper = document.createElement('div');
  a3wrapper.classList.add("a2");
  const a4wrapper = document.createElement('div');
  a4wrapper.classList.add("a4");  

  a1wrapper.append(a1c, a1);
  a2wrapper.append(a2c, a2);
  a3wrapper.append(a3c, a3);
  a4wrapper.append(a4c, a4);

  fragenBox.append(frage, a1wrapper, a2wrapper, a3wrapper, a4wrapper);

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

const checkBoxIds = ['a1c', 'a2c', 'a3c', 'a4c'];

checkBoxIds.forEach(id => {
  const checkbox = document.getElementById(id);
  if (checkbox) {
      checkbox.addEventListener('change', () => {
          checkBoxMarker(id, seite);
      });
  }
});


function auswerten(checkBoxAr) {
  quiz.innerHTML = "";

  let punkte = 0;
  const punkteDiv = document.createElement('div');
  punkteDiv.innerText = "Punkte: " + punkte;
  quiz.append(punkteDiv);

  for (let i = 0; i < data.length; i++) {

      const fragenBox = document.createElement('div');
      fragenBox.classList.add('fragen-box');

      const frage = document.createElement('div');
      frage.classList.add('frage');
      
      const a1wrapper = document.createElement('div');
      a1wrapper.classList.add("a1");
      const a2wrapper = document.createElement('div');
      a2wrapper.classList.add("a2");
      const a3wrapper = document.createElement('div');
      a3wrapper.classList.add("a2");
      const a4wrapper = document.createElement('div');
      a4wrapper.classList.add("a4");  

      const a1 = document.createElement('div');
      const a2 = document.createElement('div');
      const a3 = document.createElement('div');
      const a4 = document.createElement('div');
      
      const a1c = document.createElement('input');
      a1c.type = 'checkbox';
      a1c.id = 'a1c';
      a1c.disabled = true;
      const a2c = document.createElement('input');
      a2c.type = 'checkbox';
      a2c.id = 'a2c';
      a2c.disabled = true;
      const a3c = document.createElement('input');
      a3c.type = 'checkbox';
      a3c.id = 'a3c';
      a3c.disabled = true;
      const a4c = document.createElement('input');
      a4c.type = 'checkbox';
      a4c.id = 'a4c';
      a4c.disabled = true;

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
          correctMessage.innerText = " - Richtig!";
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

      fragenBox.append(frage, a1wrapper, a2wrapper, a3wrapper, a4wrapper);
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
*/