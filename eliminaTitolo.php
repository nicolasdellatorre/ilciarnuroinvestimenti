<!-- Pagina che viene richiamata nel momento in cui l'amministratore loggato intende eliminare un titolo dal database. Non viene visualizzata. -->

<?php
    session_start();
    if (!isset($_SESSION["username"]) || $_SESSION["username"] != "administrator") {
        header("Location: login.php?err=Si prega di loggarsi come amministratore");
        exit();
    }

    include("connessione.php"); 

    if (isset($_GET['id'])) {
        $id = $_GET['id'];

        $query = "DELETE FROM titoli WHERE ID = '$id';";


        if (mysqli_query($conn, $query)) {
            header("Location: administrator.php?msg=Titolo eliminato con successo");
        } else {
            header("Location: administrator.php?err=Errore durante l'eliminazione del titolo");
        }
    }
?>
