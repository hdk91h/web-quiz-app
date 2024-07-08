<?php

echo "<img class='mobile-menu-btn' src='./model/src/img/system/bars-solid.svg' alt='menu-mobile-btn' width='65' height='65'>";

echo "<div class='top-nav'>";
echo "<ul>";


if (isset($_SESSION['logged'])) {

    echo "<li><a href='./dashboard'>Dashboard</a></li>";
    echo "<li><a href='./fragenkatalog'>Fragenkatalog</a></li>";
    echo "<li><a href='./trainingsdaten-anlegen'>Trainingsdaten anlegen</a></li>";
    echo "<li><a href='./trainingsdaten-bearbeiten'>Trainingsdaten bearbeiten</a></li>";
    echo "<li><a href='./neues-deck'>Deck anlegen</a></li>";

    // wenn isAdmin
    if (isset($_SESSION['isAdmin'])) {
        echo "<li><a href='./neuer-nutzer'>Nutzer hinzufügen</a></li>";
    }

    echo "<li><a href='./logout'>Logout</a></li>";
} else {
    echo "<li><a href='./'>Home</a></li>";
    echo "<li><a href='./login'>Login</a></li>";
}
echo "</ul>";
echo "</div>";
?>