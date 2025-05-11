<!-- Pagina che permette all'amministratore loggato di inserire il contenuto della nuova notizia da inserire nel DB e che poi sarà visibile a tutti gli utenti -->

<?php
    session_start();
    if (!isset($_SESSION["username"]) || $_SESSION["username"] != "administrator") {
        header("Location: login.php?err=Si prega di loggarsi come amministratore");
        exit();
    }

    include("connessione.php");
    include("simulatoreInvestimenti.php");
?>

<!DOCTYPE html>
<html lang="it" style="height: 100%; margin: 0; padding: 0;">
    <head>
        <meta charset="utf-8">
        <title>Pubblica notizia | Il Ciarnuro - Simulatore di Investimenti</title>
        <link rel="stylesheet" type="text/css" href="stile.css">
        <link rel="icon" type="image/png" href="img/GCFflavicon.png">
    </head>
    <body style="height: 100%; margin: 0; padding: 0;">
        <div id="home-page" style="background-image: url('img/landing-background.jpg'); background-size: cover; top:0; left:0; width:100%; height:100%; overflow: auto; text-align: center; display: grid; justify-items: center;">
            <div style="display: flex; align-items: center; justify-content: space-between; padding: 20px; width: 100%; position: relative; overflow: hidden;">
                <div style="position: absolute; left: 7.5em;">
                    <a href="logout.php" style="color: white; font-family: Arial, Helvetica, sans-serif; text-decoration: underline; font-size: 1.2rem;">
                        ← logout
                    </a>
                </div>
                <div style="position: absolute; left: 50%; transform: translateX(-50%);">
                    <img src="img/Logo.png" style="width: 25rem; height: auto;">
                </div>
                <div style="display: flex; align-items: center; gap: 2rem; margin-left: auto; padding-right: 20px;">
                    <p style="color: white; font-family: Arial, Helvetica, sans-serif;" id="pOrario"><?php echo gameTimerAsDate(); ?></p>
                    <img src="img/iconaUtente.png" style="width: 30px; height: 30px;" class="iconaUtente">
                    <p style="color: white; font-family: Arial, Helvetica, sans-serif;" id="nomeUtente">
                        Amministratore
                    </p>
                </div>
            </div>
                <div id="portofolio" style="display: column; flex-wrap: wrap; padding: 1.1rem; width: 92%; text-align: left; margin-left: 2.7rem;">
                    <a href="administrator.php" style="color: white; font-family: Arial, Helvetica, sans-serif; text-decoration: underline; font-size: 1.2rem;">
                        ← Indietro
                    </a>
                    <h3 style="font-size: 2rem; margin-left: 4.7rem; text-align: left; max-width: 50rem;">Gestione gran circuito frugale - inserimento notizia</h3>
                    <form action="ePubblica.php" method="get" style="text-align: left; padding-left: 4rem;  padding-right: 4rem;">
                        <label for="contenuto">Contenuto:</label><br>
                        <textarea style='font-size:1.5rem; height:10rem;' id="contenuto" name="contenuto" rows="20" cols="50" class="login" required></textarea><br><br>
                        <button type="submit" class="bottone">Pubblica</button>
                        </form>
                </div>
        </div>
    </body>
</html>