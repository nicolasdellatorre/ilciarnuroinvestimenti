<!-- Pagina che chiude la sessione, permettendo all'utente o all'amministratore loggato di effettuare il logout -->

<?php
    session_start();

    session_unset();

    session_destroy();

    header("Location: login.php?msg=Logout effettuato con successo!");
?>