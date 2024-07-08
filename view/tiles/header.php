<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Web Training App">
    <title>Web Training App</title>
    
    <?php 
        require './controller/init.php';
        echo "<base href='$base_url'>";
    ?>

    <link rel="stylesheet" href="./model/src/css/style.css">
</head>

<body>

    <div class="navbar">
        <?php include 'navbar.php'; ?>
    </div>