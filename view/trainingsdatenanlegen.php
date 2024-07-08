<?php
include 'tiles/header.php';
if (!isset($_SESSION['logged'])) {
    header('Location: ./');
}
?>

<?php



if (isset($_SESSION['neuer-datensatz'])) {
    echo $_SESSION['neuer-datensatz'];
    unset($_SESSION['neuer-datensatz']);
}


echo "<div class='container'>";
echo "<h1>Neuen Trainingsdatensatz anlegen</h1><form method='post' enctype='multipart/form-data' id='dataset'>";

echo "<label for='frage'>Frage:</label><br>";
echo "<input type='text' id='frage' name='frage' required><br>";

echo "<label for='img'>Bild:</label><br>";
echo "<input type='file' id='img' name='img' accept='image/*'><br>";
echo "<div id='bild-container'></div>";

echo "<label for='antw1' >Antwort 1:</label><br>";
echo "<div class='trainingsdatensatz-aw'>";
echo "<input type='text' id='antw1' name='antw1' required>";
echo "<label for='radioantwort1' class='trainingsdatensatz-lab'> Richtige Antwort</label>";
echo "<input type='checkbox' id='correctAnswer0' name='correctAnswer[]' class='trainingsdatenanlegen-correctAw' value='0'><br>";
echo "</div>";

echo "<label for='antw2' >Antwort 2:</label><br>";
echo "<div class='trainingsdatensatz-aw'>";
echo "<input type='text' id='antw2' name='antw2' required>";
echo "<label for='radioantwort2' class='trainingsdatensatz-lab'> Richtige Antwort</label>";
echo "<input type='checkbox' id='correctAnswer1' name='correctAnswer[]' class='trainingsdatenanlegen-correctAw' value='1'><br>";
echo "</div>";

echo "<label for='antw3' >Antwort 3:</label><br>";
echo "<div class='trainingsdatensatz-aw'>";
echo "<input type='text' id='antw3' name='antw3' required>";
echo "<label for='radioantwort3' class='trainingsdatensatz-lab'> Richtige Antwort</label>";
echo "<input type='checkbox' id='correctAnswer2' name='correctAnswer[]' class='trainingsdatenanlegen-correctAw' value='2'><br>";
echo "</div>";

echo "<label for='antw4' >Antwort 4:</label><br>";
echo "<div class='trainingsdatensatz-aw'>";
echo "<input type='text' id='antw4' name='antw4' required>";
echo "<label for='radioantwort4' class='trainingsdatensatz-lab'> Richtige Antwort</label>";
echo "<input type='checkbox' id='correctAnswer3' name='correctAnswer[]' class='trainingsdatenanlegen-correctAw' value='3'><br>";
echo "</div>";

echo "<label for='tag'>Tag:</label><br>";
echo "<input type='text' id='tag' name='tag'><br>";

echo "<label for='cat'>Kategorie:</label><br>";
echo "<input type='text' id='cat' name='cat'><br>";

echo "<input type='submit' value='Submit'>";

echo "</form></div>";

?>

<script>
   document.getElementById('dataset').addEventListener('submit', function(event) {
    let checkboxes = document.querySelectorAll('input[type="checkbox"][name="correctAnswer[]"]');
    let checked = false;

    checkboxes.forEach(function(checkbox) {
        if (checkbox.checked) {
            checked = true;
        }
    });
    
    if (!checked) {
        alert('Mindestens eine richtige Antwort du wählen musst, junger Padawan.');
        event.preventDefault(); 
    }
});

    let imgInput = document.getElementById('img');
    let imgContainer = document.getElementById('bild-container');

    let imgTag = document.createElement('img');
    imgTag.width = '800';

    let delImg = document.createElement('a');
    delImg.innerText = "Delete img";
    delImg.href = '#';

    delImg.addEventListener('click', function() {
        imgInput.value = '';
        imgTag.src = '';
        imgContainer.innerHTML = '';
    });

    imgInput.addEventListener('change', function() {
        let imgObj = URL.createObjectURL(imgInput.files[0])

        imgTag.src = imgObj;

        imgContainer.append(imgTag, delImg);
    });

</script>

<?php
include 'tiles/footer.php';
?>