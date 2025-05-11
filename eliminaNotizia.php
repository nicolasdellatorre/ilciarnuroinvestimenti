<!-- Pagina che viene richiamata nel momento in cui l'amministratore loggato intende eliminare una notizia dal database. Non viene visualizzata. -->
 
<?php
    session_start();
    if (!isset($_SESSION["username"]) || $_SESSION["username"] != "administrator") {
        header("Location: login.php?err=Si prega di loggarsi come amministratore");
        exit();
    }

    include("connessione.php"); 

    if (isset($_GET['id'])) {
        $id = $_GET['id'];

        $query = "DELETE FROM notizie WHERE ID = '$id';";


        if (mysqli_query($conn, $query)) {
            header("Location: administrator.php?msg=Notizia eliminata con successo");
        } else {
            header("Location: administrator.php?err=Errore durante l'eliminazione della notizia");
        }
    }
?>
