<!-- Questa pagina permette all'amminsitratore loggato di inserire i dati per la creazione di un nuovo titolo -->

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
        <title>Crea titolo | Il Ciarnuro - Simulatore di Investimenti</title>
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
                    <h3 style="font-size: 2rem; margin-left: 4.7rem; text-align: left; max-width: 50rem;">Gestione gran circuito frugale - inserimento titolo</h3>
                    <form action="eInserisci.php" method="get" style="text-align: left; padding-left: 4rem;  padding-right: 4rem;">
                        <label for="id">ID:</label>
                        <input type="text" id="id" name="id" class="login" required><br><br>

                        <label for="nome">Nome:</label>
                        <input type="text" id="nome" name="nome" class="login" required><br><br>

                        <label for="sigla">Sigla:</label>
                        <input type="text" id="sigla" name="sigla" class="login" maxlength="10" required><br><br>

                        <label for="data_lancio">Data Lancio:</label>
                        <input type="date" id="data_lancio" name="data_lancio" class="login" required><br><br>

                        <label for="tipo_strumento">Tipo Strumento:</label>
                        <select id="tipo_strumento" name="tipo_strumento" class="login" required>
                            <option value="AETC">AETC</option>
                            <option value="Azione singola">Azione singola</option>
                            <option value="Fondo azionario">Fondo azionario</option>
                            <option value="Fondo azionario emergente">Fondo azionario emergente</option>
                        </select><br><br>

                        <label for="prezzo_partenza">Prezzo Partenza:</label>
                        <input type="number" step="0.01" id="prezzo_partenza" name="prezzo_partenza" class="login" required><br><br>

                        <label for="settore_mercato">Settore Mercato:</label>
                        <input type="text" id="settore_mercato" name="settore_mercato" class="login" required><br><br>

                        <label for="settore_mercato_specifico">Settore Mercato Specifico:</label>
                        <input type="text" id="settore_mercato_specifico" name="settore_mercato_specifico" class="login"><br><br>

                        <label for="commissioni">Commissioni:</label>
                        <input type="number" step="0.01" id="commissioni" name="commissioni" class="login" required><br><br>

                        <label for="tassa">Tassa:</label>
                        <input type="number" step="0.01" id="tassa" name="tassa" class="login" required><br><br>

                        <label for="dividendo">Dividendo:</label>
                        <input type="number" step="0.01" id="dividendo" name="dividendo" class="login" required><br><br>

                        <label for="frequenza_dividendo">Frequenza Dividendo (in giorni):</label>
                        <input type="number" id="frequenza_dividendo" name="frequenza_dividendo" class="login" required><br><br>

                        <label for="descrizione">Descrizione:</label><br>
                        <textarea id="descrizione" name="descrizione" rows="5" cols="50" class="login" required></textarea><br><br>

                        <label for="variability">Variability:</label>
                        <input type="number" step="0.01" id="variability" name="variability" class="login" required><br><br>

                        <label for="volatility">volatility:</label>
                        <input type="number" step="0.01" id="volatility" name="volatility" class="login" required><br><br>

                        <label for="noisiness">Noisiness:</label>
                        <input type="number" step="0.01" id="noisiness" name="noisiness" class="login" required><br><br>

                        <label for="seed">Seed:</label>
                        <input type="number" step="0.01" id="seed" name="seed" class="login" required><br><br>

                        <button type="submit" class="bottone">Aggiungi</button>
                        </form>

                </div>
        </div>
    </body>
</html>