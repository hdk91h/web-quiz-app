<?php 
include 'tiles/header.php';

if (!isset($_SESSION['logged'])) {
    header('Location: ./');
}
?>

<?php

$decksPerPage = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page2 = isset($_GET['page2']) ? (int)$_GET['page2'] : 1;
$offset = ($page - 1) * $decksPerPage;
$offset2 = ($page2 - 1) * $decksPerPage;

try {
    $meineDecks = db_handler::getDeckByUserID($_SESSION['uid'], $decksPerPage, $offset);
} catch (Exception $e) {
    echo "Error.";
}
$totalMeineDecks = db_handler::getDeckCountByUserID($_SESSION['uid']);
$totalPagesMeineDecks = ceil($totalMeineDecks / $decksPerPage);

try {
    $andereDecks = db_handler::getDeckNotByUserID($_SESSION['uid'], $decksPerPage, $offset2);
} catch (Exception $e) {
    echo "Error.";
}
$totalAndereDecks = db_handler::getDeckCountNotByUserID($_SESSION['uid']);
$totalPagesAndereDecks = ceil($totalAndereDecks / $decksPerPage);

    echo "<div class='container'>";
    echo "<h1>Dashboard</h1>";
    echo "<br/>";
    echo "<h2>Meine Decks</h2>";

    if (!empty($meineDecks)) {
        echo "<table>";
        echo "<tr>";
        echo "<th>ID</th>";
        echo "<th>Name</th>";
        echo "<th>Training</th>";
        echo "<th>Option</th>";
        echo "</tr>";
        foreach($meineDecks as $data) {
            echo "<tr>";
            echo "<td>{$data['id']}</td>";
            echo "<td>" . htmlentities($data['name'], ENT_QUOTES, 'UTF-8') . "</td>";
            echo "<td><input type='button' class='startquiz' id='{$data['id']}' value='Quiz starten' ></td>";
            echo "<td><input type='button' class='deletebtn' id='delete{$data['id']}' name='{$data['id']}' value='Deck löschen' ></td>";
            echo "</tr>";
        }
        echo "</table>";
            echo "<div class='deck-pagination'>";
        for ($i = 1; $i <= $totalPagesMeineDecks; $i++) {
            echo "<a  href='?page=$i'>$i</a> ";
        }
            echo "</div>";
    } else {
        echo "Noch leer hier ... ändere das! Erstelle Fragen und dein erstes Deck!";
    }
    ?>
    
    <br/>
    <h2>Andere Decks</h2>
    <?php
    if (!empty($andereDecks)) {
        echo "<table>";
        echo "<tr>";
        echo "<th>ID</th>";
        echo "<th>Name</th>";
        echo "<th>Training</th>";
        if (isset($_SESSION['isAdmin'])) {
            echo "<th>Option</th>";
        }
        echo "</tr>";
        foreach($andereDecks as $data) {
            echo "<tr>";
            echo "<td>$data[id]</td>";
            echo "<td>" . htmlentities($data['name'], ENT_QUOTES, 'UTF-8') . "</td>";
            echo "<td><input type='button' class='startquiz' id='{$data['id']}' value='Quiz starten' ></td>";
            if (isset($_SESSION['isAdmin'])) {
                echo "<td><input type='button' class='deletebtn' id='delete$data[id]' name='$data[id]' value='Deck löschen' ></td>";
            }
            echo "</tr>";
        }
        echo "</table>";
            echo "<div class='deck-pagination'>";
        for ($i = 1; $i <= $totalPagesAndereDecks; $i++) {
            echo "<a class='deck-pagination' href='?page2=$i'>$i</a> ";
        }
            echo "</div>";
    } else {
        echo "Noch leer hier ...";
    }
     
    //echo "<br/>";
    echo "<h2>Decksuche mit ID</h2>";
    echo "<div id='decksuche'></div>";


    if (isset($_GET['decksuchetxt'])) {
        $dataset = db_handler::getDeckByID($_GET['decksuchetxt']);

        echo "<table>";
        echo "<tr>";
        echo "<th>ID</th>";
        echo "<th>Name</th>";
        echo "<th>Training</th>";
        if (isset($_SESSION['isAdmin'])) {
            echo "<th>Option</th>";
        }
        echo "</tr>";
        foreach($dataset as $data) {
            echo "<tr>";
            echo "<td> $data[id]</td>";
            echo "<td>" . htmlentities($data['name'], ENT_QUOTES, 'UTF-8') . "</td>";
            echo "<td><input type='button' class='startquiz' id='$data[id]' value='Quiz starten' ></td>";
            if (isset($_SESSION['isAdmin'])) {
                echo "<td><input type='button' class='deletebtn' id='delete$data[id]' name='$data[id]' value='Deck löschen' ></td>";
            }

            echo "</tr>";
        }
        echo "</table>";

        echo "</div>";


    } else { echo 'Gib eine ID ein ...';}

    echo "<form method='GET'>";
    echo "<label for='decksuchetxt'>ID</label>";
    echo "<input type='text' id='decksuchetxt' name='decksuchetxt'>";
    echo "<input type='submit'>";
    echo "</form>";
?>

<script>

const startBtn = document.getElementsByClassName('startquiz');

for (let i = 0; i < startBtn.length; i++) {
    startBtn[i].addEventListener('click', (e) => {
        window.location.href = './quiz?quizid=' + e.target.id;
    });
}

const deleteBtn = document.getElementsByClassName('deletebtn');


for (let i = 0; i < deleteBtn.length; i++) {
    deleteBtn[i].addEventListener('click', (e) => {
        
        const confirmed = confirm('Möchtest du wirklich dieses Deck löschen?') 
        
        if (confirmed) {
            console.log(e.target.name);
            deleteDeck(e.target.name);
        }
        
    });
}

async function deleteDeck(id) {

    const request = await fetch(`./api/deletedeck?id=${id}`);

    const obj = await request.text();

    if (obj) {
        window.location.href = './dashboard';
    }
}



</script>

<?php 
include 'tiles/footer.php';
?>