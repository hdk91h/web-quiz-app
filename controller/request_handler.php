<?php

session_start();
$session_lifetime = 1800; // == 30 Minuten

require_once "./model/db_handler.php";
require_once "vendor/autoload.php";


$router = new \Bramus\Router\Router();

// static site routing

$router->get('/', function() {
   if (isset($_SESSION['logged'])) {
    require './view/dashboard.php';
   } else {
    require './view/home.php';
   }
});
$router->get('/index', function() {
    if (isset($_SESSION['logged'])) {
        require './view/dashboard.php';
       } else {
        require './view/home.php';
       }
 });
 $router->get('/dashboard', function() {
    require './view/dashboard.php';
 });
 $router->get('/logout', function() {
    require './view/logout.php';
 });
 $router->get('/login', function() {
    require './view/login.php';
 });
 $router->get('/neuer-nutzer', function() {
    require './view/neuernutzer.php';
 });
 $router->get('/trainingsdaten-anlegen', function() {
    require './view/trainingsdatenanlegen.php';
 });
 $router->get('/neues-deck', function() {
    require './view/deckanlegen.php';
 });
 $router->get('/trainingsdaten-bearbeiten', function() {
    require './view/trainingsdatenmoderieren.php';
 });
 $router->get('/fragenkatalog', function() {
    require './view/fragenkatalog.php';
 });

 $router->get('/quiz', function() {

    if (isset($_GET['quizid'])) {
        $quizId = $_GET['quizid']; 
        $_SESSION['quizid'] = $quizId;
    }

    require './view/quiz.php';
 });

 $router->get('api/getexercise', function() {

    $limit = 1;
    $offset = 0;

    if (isset($_GET['limit'])) {
        $limit = $_GET['limit'];
    }

    if (isset($_GET['offset'])) {
        $offset = $_GET['offset'];
    }

    $data = db_handler::getExercise($limit, $offset);
    
    //$data = array("Peter"=>35, 'test'=>42);
    echo json_encode($data);
    exit();
 });

 $router->get('api/getfilteredexercise', function() {

    $limit = 1;
    $offset = 0;
    $cat = '';
    $tag = '';

    if (isset($_GET['limit'])) {
        $limit = $_GET['limit'];
    }

    if (isset($_GET['offset'])) {
        $offset = $_GET['offset'];
    }

    if (isset($_GET['cat'])) {
        $cat = $_GET['cat'];
    }

    if (isset($_GET['tag'])) {
        $tag = $_GET['tag'];
    }

    $data = db_handler::getFilteredExercises($limit, $offset, $tag, $cat);
    
    //$data = array("Peter"=>35, 'test'=>42);
    echo json_encode($data);
    exit();
 });

 $router->get('api/getfilteredexercisecount', function() {

    $cat = '';
    $tag = '';

    if (isset($_GET['cat'])) {
        $cat = $_GET['cat'];
    }

    if (isset($_GET['tag'])) {
        $tag = $_GET['tag'];
    }

    $data = db_handler::getFilteredExerciseCount($tag, $cat);
    
    //$data = array("Peter"=>35, 'test'=>42);
    echo json_encode($data);
    exit();
 });

 $router->get('api/getquizid', function() {

    echo json_encode($_SESSION['quizid']);
    exit();

});

$router->get('api/getquiz', function() {

    $quizId = '';
    $deckAndExerciseData = '';

    if (isset($_GET['quizid']) && !empty($_GET['quizid'])) {
        $quizId = $_GET['quizid'];

        $deckAndExerciseData = json_encode(db_handler::getDeckAndExerciseByID($quizId));
        echo $deckAndExerciseData;

        //print_r( db_handler::getDeckAndExerciseByID($quizId));
        //echo $quizId;
        //header('Location /quiz?=' . $quizId);
    } else {
        echo json_encode(['status' => 'error id', 'msg' => 'ID nicht angegeben.']);
        exit();
    }

    if (empty($deckAndExerciseData)) {
        echo json_encode(['status' => 'error id', 'msg' => 'ID nicht angegeben.']);
        exit();
    }

});

 $router->get('api/deletedeck', function() {
    $id = '';

    if (isset($_GET['id'])) {
        $id = $_GET['id'];
    }

    $decks = db_handler::getDeckByUserIDOhneLimit($_SESSION['uid']);

    foreach ($decks as $data) {
        if ($data['id'] == $id || isset($_SESSION['isAdmin'])) {
            echo "Erfolgreich gelöscht.";

            db_handler::deleteDeck($id);
            db_handler::deleteDeckExercise($id);

        } else {
            echo "Permission error.";
        }
    }
 });

 $router->get('api/getuid', function() {
    
    echo json_encode($_SESSION['uid']);
 });

 $router->get('api/getvote', function () {

    if (isset($_GET['aid']) && isset($_GET['uid'])) {
        $data = db_handler::getVoteForExercise($_GET['aid'], $_GET['uid']);
    }
    
    echo json_encode($data);
 });

 $router->get('api/deletevote', function () {

    if (isset($_GET['aid']) && isset($_GET['uid'])) {
        db_handler::deleteVoteForExercise($_GET['aid'], $_GET['uid']);
    }
    //echo json_encode($data);
 });

 $router->get('api/createvote', function () {
    if (isset($_GET['wert']) && isset($_GET['aid']) && isset($_GET['uid'])) {
        db_handler::createVoteForExercise($_GET['wert'], $_GET['aid'], $_GET['uid']);
    }
    
 });

 $router->get('api/getvotepos', function () {
    if (isset($_GET['aid'])) {
        $data = db_handler::getPositiveVotesForExercise($_GET['aid']);
    }
    echo json_encode($data);
 });

 $router->get('api/getvoteneg', function () {
    if (isset($_GET['aid'])) {
        $data = db_handler::getNegativeVotesForExercise($_GET['aid']);
    }
    echo json_encode($data);
 });

 // post

 $router->post('/login', function() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        
        if (isset($_POST['name']) && isset($_POST['pass'])){
        
            if (db_handler::login($_POST['name'], $_POST['pass'])) {                     
                $_SESSION['name'] = $_POST['name'];                                                 
                $_SESSION['logged'] = true;
                header('Location: ./dashboard');
            } else {   
                $_SESSION['login_message'] = "Username oder Password falsch!";
                header('Location: ./login');                                                                
            }
        }
    }
 });

 $router->post('/neues-deck', function() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $input = json_decode(file_get_contents('php://input'), true);

        if (isset($input['data']) && count($input['data']) > 0 && isset($input['deckname']) && strlen($input['deckname']) > 0) {
            $data = $input['data'];
            $deckname = $input['deckname'];

            // sql

            db_handler::createDeck($deckname, $_SESSION['uid']);

            $deckid = db_handler::getDeckByName($deckname);

            foreach ($data as $in) {
                db_handler::createDeckExercise($deckid['id'], $in['id']);
            }

            $_SESSION['neues-deck'] = "Deck angelegt.";
            header('Content-Type: application/json'); 
            echo json_encode(['status' => 'success', 'message' => 'Deck angelegt.']);
            exit();
        } else {
            //$_SESSION['neues-deck'] = "Bitte alle Felder ausfüllen.";
            header('Content-Type: application/json'); 
            echo json_encode(['status' => 'error', 'message' => 'Bitte alle Felder ausfüllen.']);
            exit();
        }
    }
});

 $router->post('/neuer-nutzer', function() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        
        if (isset($_POST['name']) && isset($_POST['email']) && isset($_POST['password'])) {
            $name = trim($_POST['name']);
            $email = trim($_POST['email']);
            $password = trim($_POST['password']);
            
            if (!empty($name) && !empty($email) && !empty($password)) {
                $admin = isset($_POST['admin']) ? 1 : 0;
        
                db_handler::createUser($name, $password, $email, $admin);
        
                $_SESSION['neuer-nutzer'] = "Nutzer angelegt.";
                header('Location: ./neuer-nutzer');
                exit();
            } else {
                $_SESSION['neuer-nutzer'] = "Bitte alle Felder ausfüllen.";
                header('Location: ./neuer-nutzer');
                exit();
            }
        } else {
            $_SESSION['neuer-nutzer'] = "Bitte alle Felder ausfüllen.";
            header('Location: ./neuer-nutzer');
            exit();
        }
    }
 });

 $router->post('/trainingsdaten-anlegen', function() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['frage'], $_POST['antw1'], $_POST['antw2'], $_POST['antw3'], $_POST['antw4'])) {
            $frage = trim($_POST['frage']);
            $aw1 = trim($_POST['antw1']);
            $aw2 = trim($_POST['antw2']);
            $aw3 = trim($_POST['antw3']);
            $aw4 = trim($_POST['antw4']);
            $tag = isset($_POST['tag']) ? trim($_POST['tag']) : "null";
            $cat = isset($_POST['cat']) ? trim($_POST['cat']) : "null";

            // Prüfung der Checkboxen für die richtigen Antworten
            $correctAnswers = [];
            if (isset($_POST['correctAnswer']) && is_array($_POST['correctAnswer'])) {
                foreach ($_POST['correctAnswer'] as $answer) {
                    $correctAnswers[] = intval($answer);
                }
            }

            if (!empty($frage) && !empty($aw1) && !empty($aw2) && !empty($aw3) && !empty($aw4) && !empty($correctAnswers)) {
                $target_file = null;
                if (isset($_FILES['img']) && $_FILES['img']['error'] === UPLOAD_ERR_OK) {
                    $file = $_FILES['img'];
                    
                    // Maximal erlaubte Dateigröße: 5 MB
                    $max_file_size = 5 * 1024 * 1024;
        
                    // Überprüfen, ob die Datei tatsächlich ein Bild ist
                    $check = getimagesize($file['tmp_name']);
                    if ($check === false) {
                        $_SESSION['neuer-datensatz'] = "Die Datei ist kein gültiges Bild.";
                        header('Location: ./trainingsdaten-anlegen');
                        exit();
                    }
                    
                    // Überprüfen der Dateigröße
                    if ($file['size'] > $max_file_size) {
                        $_SESSION['neuer-datensatz'] = "Das Bild ist größer als 5 MB.";
                        header('Location: ./trainingsdaten-anlegen');
                        exit();
                    }
                    
                    // Zielverzeichnis für das Bild
                    $target_dir = "./model/src/img/";
                    
                    // Zufälligen Dateinamen erstellen
                    $imageFileType = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                    $random_name = uniqid() . '.' . $imageFileType;
                    $target_file = $target_dir . $random_name;
                    
                    // Überprüfen, ob der Zielordner existiert, wenn nicht, erstelle ihn
                    if (!is_dir($target_dir)) {
                        mkdir($target_dir, 0777, true);
                    }
                    
                    // Bild in den Zielordner verschieben
                    if (!move_uploaded_file($file['tmp_name'], $target_file)) {
                        $_SESSION['neuer-datensatz'] = "Datensatz nicht angelegt, beim Hochladen des Bildes ist ein Fehler aufgetreten.";
                        header('Location: ./trainingsdaten-anlegen');
                        exit();
                    }
                }
                
                // Daten in Datenbank
                db_handler::createExercise($frage, implode(", ", $correctAnswers), $target_file, $tag, $cat, $aw1, $aw2, $aw3, $aw4);
                $_SESSION['neuer-datensatz'] = "Datensatz angelegt.";
                header('Location: ./trainingsdaten-anlegen');
                exit();
            } else {
                $_SESSION['neuer-datensatz'] = "Datensatz nicht angelegt; fülle die benötigten Felder aus!";
                header('Location: ./trainingsdaten-anlegen');
                exit();
            }
        } else {
            $_SESSION['neuer-datensatz'] = "Datensatz nicht angelegt; fülle die benötigten Felder aus!";
            header('Location: ./trainingsdaten-anlegen');
            exit();
        }
    }
});

$router->post('/trainingsdaten-bearbeiten', function() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['update'])) {
            $id = $_POST['id'];
            $frage = $_POST['frage'];
            $bild = isset($_POST['current_img']) ? $_POST['current_img'] : null;
            $antwort1 = $_POST['antw1'];
            $antwort2 = $_POST['antw2'];
            $antwort3 = $_POST['antw3'];
            $antwort4 = $_POST['antw4'];
            $tag = $_POST['tag'];
            $cat = $_POST['cat'];
            $correctAnswers = implode(",", $_POST['correctAnswer']);
            
            $target_file = $bild;

                if (isset($_FILES['img']) && $_FILES['img']['error'] === UPLOAD_ERR_OK) {
                    $file = $_FILES['img'];
                    
                    // Maximal erlaubte Dateigröße: 5 MB
                    $max_file_size = 5 * 1024 * 1024;
        
                    // Überprüfen, ob die Datei tatsächlich ein Bild ist
                    $check = getimagesize($file['tmp_name']);
                    if ($check === false) {
                        $_SESSION['neuer-datensatz'] = "Die Datei ist kein gültiges Bild.";
                        header('Location: ./trainingsdaten-anlegen');
                        exit();
                    }
                    
                    // Überprüfen der Dateigröße
                    if ($file['size'] > $max_file_size) {
                        $_SESSION['neuer-datensatz'] = "Das Bild ist größer als 5 MB.";
                        header('Location: ./trainingsdaten-anlegen');
                        exit();
                    }
                    
                    // Zielverzeichnis für das Bild
                    $target_dir = "./model/src/img/";
                    
                    // Zufälligen Dateinamen erstellen
                    $imageFileType = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                    $random_name = uniqid() . '.' . $imageFileType;
                    $target_file = $target_dir . $random_name;
                    
                    // Überprüfen, ob der Zielordner existiert, wenn nicht, erstelle ihn
                    if (!is_dir($target_dir)) {
                        mkdir($target_dir, 0777, true);
                    }
                    
                    // Bild in den Zielordner verschieben
                    if (!move_uploaded_file($file['tmp_name'], $target_file)) {
                        $_SESSION['neuer-datensatz'] = "Datensatz nicht angelegt, beim Hochladen des Bildes ist ein Fehler aufgetreten.";
                        header('Location: ./trainingsdaten-anlegen');
                        exit();
                    }
                }
                // Lösche Bild aus Speicher, wenn aus DB gelöscht wird
                // unlink() löscht Dateien

                if (isset($_POST['delete_img']) && $_POST['delete_img'] == '1') {
                    
                    $id = $_POST['id'];

                    $exercise = db_handler::getExerciseById($id);
                    if ($exercise && !empty($exercise['bild']) && file_exists($exercise['bild'])) {
                        unlink($exercise['bild']);
                    }
                        $target_file = null;
                }

                // Bild überschreiben; lösche altes Bild

                if (isset($_POST['img_overwritten']) && $_POST['img_overwritten'] == '1') {
                    unlink($_POST['img_var']);
                }
                
            db_handler::updateExercise($frage, $target_file, $antwort1, $antwort2, $antwort3, $antwort4, $tag, $cat, $correctAnswers, $id);
            $_SESSION['aufgabe-bearbeitet'] = "Aufgabe erfolgreich bearbeitet!";
            header('Location: ./trainingsdaten-bearbeiten?aufgabensuche=' . $id);
            exit();

        } elseif (isset($_POST['delete'])) {
            $id = $_POST['id'];

            // Löscht Bild, wenn Aufgabe gelöscht wird

            $exercise = db_handler::getExerciseById($id);
            if ($exercise && !empty($exercise['bild']) && file_exists($exercise['bild'])) {
                unlink($exercise['bild']);
            }

            db_handler::deleteExerciseById($id);
            $_SESSION['aufgabe-bearbeitet'] = "Aufgabe erfolgreich gelöscht!";
            header('Location: ./trainingsdaten-bearbeiten?aufgabensuche=' . $id);
            exit();
        }
    }  
});

// 404 -> $router->trigger404()

$router->set404(function() {
    header('HTTP/1.1 404 Not Found');
    // obi wan gif handbewegung
    //echo "Dies ist nicht die Seite nach der Ihr sucht!";
    require './view/404.php';
    exit();
});

$router->run();

?>