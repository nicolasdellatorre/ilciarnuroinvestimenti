<!-- Una volta che l'amministratore loggato ha inserito il contenuto della notizia da pubblicare, viene richiamata questa pagina. Non visibile all'utente.
 La pagina inserisce la notizia appena creata nel database --> 

<?php
    session_start();
    if (!isset($_SESSION["username"]) || $_SESSION["username"] != "administrator") {
        header("Location: login.php?err=Si prega di loggarsi come amministratore");
        exit();
    }
    include("connessione.php");
    include("simulatoreInvestimenti.php");

    $contenuto = htmlspecialchars($_GET["contenuto"], ENT_QUOTES);
    $data = date('Y-m-d', gameTimer() * 60 * 60 * 24);

    $sql = "INSERT INTO notizie (
        dataPubblicazione, contenuto
    ) VALUES (
        '$data', '$contenuto'
    );";

    if (mysqli_query($conn, $sql)) {
        header("Location: administrator.php?msg=Notizia pubblicata con successo");
        exit();
    } else {
        header("Location: administrator.php?msg=Errore nella pubblicazione della notizia");
        exit();
    }
?>
