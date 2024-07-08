<?php
    if ($_SERVER['HTTP_HOST'] == 'localhost:3000') {
        $base_url = "http://localhost:3000";
    } else {
        $base_url = "https://netztratsch.de";
    }

    $dbuser = "root";
    $dbpw = "";
    $dbhost = "localhost";
    $dbdb = "training";
?>