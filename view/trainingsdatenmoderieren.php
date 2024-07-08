<?php
include 'tiles/header.php';
if (!isset($_SESSION['logged'])) {
    header('Location: ./');
}

if (isset($_SESSION['aufgabe-bearbeitet'])) {
    echo $_SESSION['aufgabe-bearbeitet'];
    unset($_SESSION['aufgabe-bearbeitet']);
}

echo "Deine Aufgaben-IDs: ";
$userdataids = db_handler::getAllIDFromUserId($_SESSION['uid']);
echo "<pre>";
foreach ($userdataids as $ids) {
    
    echo $ids['id'] . ', ';

}
echo "</pre>";
echo "<form method='GET'>";
echo "<label for='aufgabensuche'>ID der Aufgabe</label>";
echo "<input type='text' id='aufgabensuche' name='aufgabensuche'>";
echo "<input type='submit' value='Suchen'>";
echo "</form>";

$permission = false;

if (isset($_GET['aufgabensuche'])) {
    $aufgabenId = (int)$_GET['aufgabensuche']; 

    $data = db_handler::getExerciseById($aufgabenId);

    if (!empty($data)) {
        if (($data['erstelltVon']) == ($_SESSION['uid'])) {
            $permission = true;
        }
    }

    /*
    if ($data) {
        echo "<br/>";
        echo "Datensatz gefunden.";
    } else {
        echo "<br/>";
        echo "Keine Aufgabe gefunden mit ID: $aufgabenId";
    }
    */
}

echo "<div>";
echo "<h1>Trainingsdaten bearbeiten</h1>";

if (isset($_SESSION['isAdmin']) || $permission) {

    if (!empty($data)) {
        $correctAnswers = explode(",", $data['antwort']);
    
        echo "<form method='post' enctype='multipart/form-data' id='dataset" . htmlspecialchars($data['id'], ENT_QUOTES, 'UTF-8') . "'>";
            echo "<input type='hidden' name='id' value='" . htmlspecialchars($data['id'], ENT_QUOTES, 'UTF-8') . "'>";
    
            echo "<label for='frage'>Frage:</label><br>";
            echo "<input type='text' id='frage' name='frage' value='" . htmlspecialchars($data['frage'], ENT_QUOTES, 'UTF-8') . "' required><br>";
    
            echo "<label for='img'>Bild:</label><br>";
            echo "<input type='file' id='img' name='img' accept='image/*' ><br>";
            echo "<div id='bild-container'>";
            if (!empty($data['bild'])) {
                echo "<img class='bild' src='" . htmlspecialchars($data['bild'], ENT_QUOTES, 'UTF-8') . "'>";
    
                echo "<input type='hidden' name='current_img' value='" . htmlspecialchars($data['bild'], ENT_QUOTES, 'UTF-8') . "'>";
            }
            echo"</div>";
    
            echo "<input type='hidden' name='delete_img' id='delete_img' value='0'>";
            echo "<input type='hidden' name='img_var' id='img_var' value='" . $data['bild'] . "'>";
            echo "<input type='hidden' name='img_overwritten' id='img_overwritten' value='0'>";
    
            echo "<label for='antw1'>Antwort 1:</label><br>";
            echo "<div class='trainingsdatensatz-aw'>";
            echo "<input type='text' id='antw1' name='antw1' value='" . htmlspecialchars($data['antwort1'], ENT_QUOTES, 'UTF-8') . "' required>";
            echo "<label for='radioantwort1'> Richtige Antwort</label>";
            echo "<input type='checkbox' id='correctAnswer0' name='correctAnswer[]' value='0'" . (in_array('0', $correctAnswers) ? ' checked' : '') . "><br>";
            echo "</div>";
    
            echo "<label for='antw2'>Antwort 2:</label><br>";
            echo "<div class='trainingsdatensatz-aw'>";
            echo "<input type='text' id='antw2' name='antw2' value='" . htmlspecialchars($data['antwort2'], ENT_QUOTES, 'UTF-8') . "' required>";
            echo "<label for='radioantwort2'> Richtige Antwort</label>";
            echo "<input type='checkbox' id='correctAnswer1' name='correctAnswer[]' value='1'" . (in_array('1', $correctAnswers) ? ' checked' : '') . "><br>";
            echo "</div>";
    
            echo "<label for='antw3'>Antwort 3:</label><br>";
            echo "<div class='trainingsdatensatz-aw'>";
            echo "<input type='text' id='antw3' name='antw3' value='" . htmlspecialchars($data['antwort3'], ENT_QUOTES, 'UTF-8') . "' required>";
            echo "<label for='radioantwort3'> Richtige Antwort</label>";
            echo "<input type='checkbox' id='correctAnswer2' name='correctAnswer[]' value='2'" . (in_array('2', $correctAnswers) ? ' checked' : '') . "><br>";
            echo "</div>";
    
            echo "<label for='antw4'>Antwort 4:</label><br>";
            echo "<div class='trainingsdatensatz-aw'>";
            echo "<input type='text' id='antw4' name='antw4' value='" . htmlspecialchars($data['antwort4'], ENT_QUOTES, 'UTF-8') . "' required>";
            echo "<label for='radioantwort4'> Richtige Antwort</label>";
            echo "<input type='checkbox' id='correctAnswer3' name='correctAnswer[]' value='3'" . (in_array('3', $correctAnswers) ? ' checked' : '') . "><br>";
            echo "</div>";
    
            echo "<label for='tag'>Tag:</label><br>";
            echo "<input type='text' id='tag' name='tag' value='" . htmlspecialchars($data['tag'], ENT_QUOTES, 'UTF-8') . "'><br>";
    
            echo "<label for='cat'>Kategorie:</label><br>";
            echo "<input type='text' id='cat' name='cat' value='" . htmlspecialchars($data['kategorie'], ENT_QUOTES, 'UTF-8') . "'><br>";
    
            echo "<input type='submit' name='update' value='Aktualisieren'>";
            echo "<input type='submit' name='delete' value='Löschen'>";
        echo "</form><hr>";
    
    } else {
        echo "Keine Trainingsdaten.";
    }

} else {
    echo 'Du darfst nur deine Daten bearbeiten.';
}

echo "</div>";
?>

<script>
    document.querySelectorAll('form[id^="dataset"]').forEach(function(form) {
        form.addEventListener('submit', function(event) {
            let checkboxes = form.querySelectorAll('input[type="checkbox"][name="correctAnswer[]"]');
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
    });

    let imgInput = document.getElementById('img');
    let imgContainer = document.getElementById('bild-container');

    let imgTag = document.createElement('img');
    //imgTag.width = '800';

    let delImg = document.createElement('button');
    delImg.innerText = "Delete img";
    

    delImg.addEventListener('click', function() {
        imgInput.value = '';
        imgTag.src = '';
        imgContainer.innerHTML = '';

        let deleteInput = document.getElementById('delete_img');
        deleteInput.value = '1';
    });

    imgInput.addEventListener('change', function() {
        let imgObj = URL.createObjectURL(imgInput.files[0])

        imgTag.src = imgObj;

        imgTag.classList.add('bild');

        imgContainer.append(imgTag, delImg);

        const img_var = document.getElementById('img_var');
        const img_overwritten = document.getElementById('img_overwritten');

        if (imgContainer.childNodes[0].tagName === 'IMG') {
            img_overwritten.value = '1';
        }
    });

   
    document.addEventListener('DOMContentLoaded', function() {
        
           // imgContainer.append(imgTag, delImg);

           if (imgContainer.hasChildNodes()) {
            imgContainer.append(imgTag, delImg);
           }
     
    });



</script>

<?php
include 'tiles/footer.php';
?>