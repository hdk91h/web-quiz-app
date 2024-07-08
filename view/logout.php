<?php
include 'tiles/header.php';
if (!isset($_SESSION['logged'])) {
    header('Location: ./');
}
?>

<div class="container">
    <h1>Logout</h1>

    <?php

    session_destroy();
    header('location: ./');

    ?>

    <?php
    include 'tiles/footer.php';
    ?>