<?php
include 'tiles/header.php';
?>

<?php

if (!isset($_SESSION['logged'])) {

    if (isset($_SESSION['login_message'])) {
        echo "<div>";
        echo $_SESSION['login_message'];
        echo "</div>";
    }

    echo "<div class='container'>";
    echo "<h1>Login</h1>";
    echo "<form method='post'>";

    echo "<label for='name'>Username:</label><br>";
    echo "<input type='text' id='name' name='name'><br>";

    echo "<label for='pass'>Password:</label><br>";
    echo "<input type='password' id='pass' name='pass'><br><br>";

    echo "<input type='submit' value='Submit'>";

    echo "</form>";
    echo "</div>";
} else {
    echo "Du bist als Nutzer " . $_SESSION['name'] . " angemeldet.";
    echo "<br />";
    echo "<a href='/'>Gehe zurück zur Hauptseite</a>";
}

?>


<?php
include 'tiles/footer.php';
?>