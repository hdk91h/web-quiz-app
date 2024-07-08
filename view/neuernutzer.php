<?php
include 'tiles/header.php';
if (!isset($_SESSION['logged'])) {
    header('Location: ./');
}
?>


<?php

if (!isset($_SESSION['isAdmin'])) {
    header('Location: ./404');  
}

if (isset($_SESSION['neuer-nutzer'])) {
    echo "<div class='message'>";
    echo $_SESSION['neuer-nutzer'];
    echo "</div>";
    unset($_SESSION['neuer-nutzer']);
}
?>

<div class="container">
    <h1>Neuen Nutzer anlegen</h1>
    <form method='post'>
        <label for='name'>Username:</label>
        <input type='text' id='name' name='name'>

        <label for='password'>Password:</label>
        <input type='password' id='password' name='password'>

        <label for='email'>Email:</label>
        <input type='text' id='email' name='email'>

        <label for='admin'>Administrator</label>
        <input type='checkbox' id='admin' name='admin'>

        <input type='submit' value='Submit'>
    </form>
</div>

<?php
include 'tiles/footer.php';
?>