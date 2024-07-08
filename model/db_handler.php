<?php 

class db_handler {

    public static function db_connection() {

        require './controller/init.php';

        $user = $dbuser;
        $pw = $dbpw;
        $host = $dbhost;
        $db = $dbdb;

        try {
            $conn = new PDO("mysql:host=$host;dbname=$db", $user, $pw);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Database error: " .$e->getMessage();
        }
        return $conn;
    }

    public static function login($user, $password) {
        try {
            $conn = self::db_connection();
            $sql = "SELECT * FROM user WHERE name = :user";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':user', $user, PDO::PARAM_STR, 255);
            $stmt->execute();
            
            $userRecord = $stmt->fetch(PDO::FETCH_ASSOC);
    
            // Benutzername nicht gefunden
            if (!$userRecord) {
                return false;
            }
    
            if (password_verify($password, $userRecord['password'])) {

                $_SESSION['uid'] = $userRecord['id'];
                if ($userRecord['admin'] == 1) {
                    $_SESSION['isAdmin'] = true;
                }
                return true;
            } else {
                // Falsches Passwort
                return false;
            }
        } catch (Exception $e) {
            error_log("Login error: " . $e->getMessage());
            return false;
        }
    }

    public static function getVoteForExercise($id, $uid) {
        $conn = self::db_connection();
        $sql = "SELECT * FROM vote WHERE aufgaben_id = :id AND user_id = :uid";
        $stmt = $conn->prepare(($sql));
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':uid', $uid, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function deleteVoteForExercise($id, $uid) {
        $conn = self::db_connection();
        $sql = "DELETE FROM vote WHERE aufgaben_id = :id AND user_id = :uid";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':uid', $uid, PDO::PARAM_INT);
        $stmt->execute();
    }

    public static function createVoteForExercise($wert, $aufgaben_id, $uid) {
        $conn = self::db_connection();
        $sql = "INSERT INTO vote (wert, aufgaben_id, user_id) 
                    VALUES (:wert, :aufgabenid, :userid)";
        $stmt = $conn->prepare(($sql));
        $stmt->bindParam(':wert', $wert, PDO::PARAM_INT);
        $stmt->bindParam(':aufgabenid', $aufgaben_id, PDO::PARAM_INT);
        $stmt->bindParam(':userid', $uid, PDO::PARAM_INT);
        $stmt->execute();
    }

    public static function getPositiveVotesForExercise($id) {
        $conn = self::db_connection();
        $sql = "SELECT count(*) FROM vote WHERE aufgaben_id = :id AND wert = '1'";
        $stmt = $conn->prepare(($sql));
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getNegativeVotesForExercise($id) {
        $conn = self::db_connection();
        $sql = "SELECT count(*) FROM vote WHERE aufgaben_id = :id AND wert = '-1'";
        $stmt = $conn->prepare(($sql));
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function createUser($name, $password, $email, $admin) {
        $conn = self::db_connection();
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO user (name, password, email, admin)
                    VALUES (:name, :password, :email, :admin)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR, 255);
        $stmt->bindParam(':password', $password_hash, PDO::PARAM_STR, 255);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR, 255);
        $stmt->bindParam(':admin', $admin, PDO::PARAM_INT, 1);
        $stmt->execute();
    }

    public static function createDeckExercise($deckid, $aufgabenid) {
        $conn = self::db_connection();
        $sql = "INSERT INTO deckaufgaben (deckid, aufgabenid)
                    VALUES (:deckid, :aufgabenid)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':aufgabenid', $aufgabenid, PDO::PARAM_INT);
        $stmt->bindParam(':deckid', $deckid, PDO::PARAM_INT);
        $stmt->execute(); 
    }

    public static function createDeck($name, $inhaber) {
        $conn = self::db_connection();
        $sql = "INSERT INTO aufgabendeck (inhaber_id, name)
                    VALUES (:inhaber, :name)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':inhaber', $inhaber, PDO::PARAM_INT);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->execute();
    }

    public static function deleteDeck($id) {
        $conn = self::db_connection();
        $sql = "DELETE FROM aufgabendeck WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }

    public static function deleteDeckExercise($id) {
        $conn = self::db_connection();
        $sql = "DELETE FROM deckaufgaben WHERE deckid = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }

    public static function getDeckAll() {
        $conn = self::db_connection();
        $sql = "SELECT * FROM aufgabendeck";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getDeckByUserID($uid, $limit, $offset) {
        $conn = self::db_connection();
        $sql = "SELECT id, name FROM aufgabendeck WHERE inhaber_id = :uid LIMIT :limit OFFSET :offset";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':uid', $uid, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getDeckByUserIDOhneLimit($uid) {
        $conn = self::db_connection();
        $sql = "SELECT id, name FROM aufgabendeck WHERE inhaber_id = :uid";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':uid', $uid, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getDeckNotByUserID($uid, $limit, $offset) {
        $conn = self::db_connection();
        $sql = "SELECT id, name FROM aufgabendeck WHERE inhaber_id != :uid LIMIT :limit OFFSET :offset";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':uid', $uid, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getDeckCountByUserID($uid) {
        $conn = self::db_connection();
        $sql = "SELECT COUNT(*) as count FROM aufgabendeck WHERE inhaber_id = :uid";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':uid', $uid, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public static function getDeckCountNotByUserID($uid) {
        $conn = self::db_connection();
        $sql = "SELECT COUNT(*) as count FROM aufgabendeck WHERE inhaber_id != :uid";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':uid', $uid, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public static function getDeckByID($id) {
        $conn = self::db_connection();
        $sql = "SELECT * FROM aufgabendeck WHERE id = :id;";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getDeckAndExerciseByID($id) {
        $conn = self::db_connection();
        $sql = "SELECT * FROM aufgabendeck a, deckaufgaben b, aufgaben c WHERE a.id=b.deckid AND b.aufgabenid=c.id AND a.id = :id;";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getDeckExercise($inhaber) {
        $conn = self::db_connection();
        $sql = "SELECT * FROM aufgabendeck WHERE inhaber_id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $inhaber, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function getExerciseById($id) {
        $conn = self::db_connection();
        $sql = "SELECT * FROM aufgaben WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function getDeckByName($name) {
        $conn = self::db_connection();
        $sql = "SELECT * FROM aufgabendeck WHERE name = :name";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function getAllIDFromUserId($id) {
        $conn = self::db_connection();
        $sql = "SELECT id FROM aufgaben WHERE erstelltVon = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function updateExercise($frage, $bild, $antwort1, $antwort2, $antwort3, $antwort4, $tag, $cat, $correctAnswers, $id) {
        $conn = self::db_connection();
        $sql = "UPDATE aufgaben SET frage=:frage, bild=:bild, antwort1=:antwort1, antwort2=:antwort2, antwort3=:antwort3, antwort4=:antwort4, tag=:tag, kategorie=:cat, antwort=:correctAnswers WHERE id=:id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':frage', $frage);
        $stmt->bindParam(':bild', $bild);
        $stmt->bindParam(':antwort1', $antwort1);
        $stmt->bindParam(':antwort2', $antwort2);
        $stmt->bindParam(':antwort3', $antwort3);
        $stmt->bindParam(':antwort4', $antwort4);
        $stmt->bindParam(':tag', $tag);
        $stmt->bindParam(':cat', $cat);
        $stmt->bindParam(':correctAnswers', $correctAnswers);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
    }

    public static function deleteExerciseById($id) {
        $conn = self::db_connection();
        $sql = "DELETE FROM aufgaben WHERE id=:id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
    }

    public static function createExercise($question, $answer, $img = null, $tag = null, $category = null, $answer1, $answer2, $answer3, $answer4) {
        $conn = self::db_connection();
        $sql = "INSERT INTO aufgaben (frage, antwort, bild, tag, kategorie, erstelltVon, antwort1, antwort2, antwort3, antwort4)
                    VALUES (:question, :aw, :img, :tag, :category, :erstelltVon, :aw1, :aw2, :aw3, :aw4)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':question', $question, PDO::PARAM_STR, 255);
        $stmt->bindParam(':aw', $answer, PDO::PARAM_STR, 100);
        $stmt->bindParam(':img', $img, PDO::PARAM_STR, 255);
        $stmt->bindParam(':tag', $tag, PDO::PARAM_STR, 255);
        $stmt->bindParam(':category', $category, PDO::PARAM_STR, 255);
        $stmt->bindParam(':erstelltVon', $_SESSION['uid'], PDO::PARAM_INT, 255);
        $stmt->bindParam(':aw1', $answer1, PDO::PARAM_STR, 255);
        $stmt->bindParam(':aw2', $answer2, PDO::PARAM_STR, 255);
        $stmt->bindParam(':aw3', $answer3, PDO::PARAM_STR, 255);
        $stmt->bindParam(':aw4', $answer4, PDO::PARAM_STR, 255);
        $stmt->execute();
    }

    public static function getExercise($limit, $offset) {
        $conn = self::db_connection();
        $sql = 'SELECT * FROM aufgaben LIMIT :limit OFFSET :offset';
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getFilteredExerciseCount($tag, $category) {
        $conn = self::db_connection();
        $sql = 'SELECT COUNT(*) FROM aufgaben WHERE tag LIKE :tag AND kategorie LIKE :category';
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':tag' => '%' . $tag . '%',
            ':category' => '%' . $category . '%'
        ]);
        return $stmt->fetchColumn();
    }
    
   public static function getFilteredExercises($limit, $offset, $tag, $category) {
        $conn = self::db_connection();
        $sql = 'SELECT * FROM aufgaben WHERE tag LIKE :tag AND kategorie LIKE :category LIMIT :limit OFFSET :offset';
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':tag', $tagWithWildcards, PDO::PARAM_STR);
        $stmt->bindParam(':category', $categoryWithWildcards, PDO::PARAM_STR);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

        $tagWithWildcards = '%' . $tag . '%';
        $categoryWithWildcards = '%' . $category . '%';

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    public static function getAllExerciseCount() {
        $conn = self::db_connection();
        $sql = 'SELECT COUNT(*) FROM aufgaben';
        $stmt = $conn->query($sql);
        return $stmt = $stmt->fetchColumn();
    }
}
?>