<!-- Pagina che può essere visualizzata solamente se ci si logga come amministratori.
 Permette di gestire i titoli e le notizie presenti nel database -->

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
        <title>Admin | Il Ciarnuro - Simulatore di Investimenti</title>
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

            <?php if (!isset($_GET['impostazione'])) { ?>
                <div id="portofolio" style="display: column; flex-wrap: wrap; padding: 1.1rem; width: 92%; text-align: center; margin-left: 2.7rem;">
                    <h3 style="font-size: 2rem; margin-left: 4.7rem; text-align: left; max-width:40rem;">Gestione gran circuito frugale</h3>
                    <?php if (isset($_GET['msg'])) echo "<p>".$_GET['msg']."</p>"; ?>
                    <a href="administrator.php?impostazione=titoli"><button class="bottone" style="width: 50%; margin-top: 2rem; margin-left: 1.7rem; text-align: left; padding-left: 1rem">
                        Vai alla gestione dei titoli →
                    </button></a>
                    <a href="administrator.php?impostazione=notizie"><button class="bottone" style="width: 50%; margin-top: 2rem; margin-left: 1.7rem; text-align: left; padding-left: 1rem">
                        Vai alla gestione notizie →
                    </button></a>
                </div><br><br><br><br><br><br>  
            <?php } else if ($_GET['impostazione'] == 'titoli') { ?>
                <div id="portofolio" style="display: flex; flex-wrap: wrap; padding: 1.1rem; width: 92%; text-align: center; margin-left: 2.7rem;">
                    <a href="administrator.php" style="color: white; font-family: Arial, Helvetica, sans-serif; text-decoration: underline; font-size: 1.2rem;">
                        ← Indietro
                    </a>
                    <h3 style="font-size: 2rem; margin-left: 4.7rem; text-align: left; max-width: 40rem;">Gestione titoli Gran Circuito Frugale</h3>
                    <a href="creaTitolo.php"><button class="bottone" style="width: 100%; margin-top: 2rem; margin-left: 1.7rem; text-align: left; padding-left: 1rem">
                        Crea un nuovo titolo →
                    </button></a>
                    <table style='text-align: left; font-size:1.5rem; padding: 2rem;'>
                        <?php
                            $sql = "SELECT * FROM titoli WHERE tipoStrumento <> 'AETF indicizzato';";
                            $result = mysqli_query($conn, $sql);
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<tr><td>".$row["nome"]." (".$row["sigla"].")</td><td><a href='modificaTitolo.php?id=".$row["ID"]."'><img src='img/edit.webp' style='height: 2rem;'></a></td><td><a href='eliminaTitolo.php?id=".$row["ID"]."'><img src='img/drop.webp' style='height: 2rem;'></a></td></tr>";
                            }
                        ?>
                    </table>
                </div>
            <?php } else if ($_GET['impostazione'] == 'notizie') { ?>
                <div id="portofolio" style="display: flex; flex-wrap: wrap; padding: 1.1rem; width: 92%; text-align: center; margin-left: 2.7rem;">
                    <a href="administrator.php" style="color: white; font-family: Arial, Helvetica, sans-serif; text-decoration: underline; font-size: 1.2rem;">
                        ← Indietro
                    </a>
                    <h3 style="font-size: 2rem; margin-left: 4.7rem; text-align: left; max-width: 40rem;">Gestione notizie Gran Circuito Frugale</h3>
                    <a href="pubblicaNotizia.php"><button class="bottone" style="width: 100%; margin-top: 2rem; margin-left: 1.7rem; text-align: left; padding-left: 1rem">
                        Pubblica una nuova notizia →
                    </button></a>
                    <table style='text-align: left; font-size:1.5rem; padding: 2rem;'>
                        <?php
                            $sql = "SELECT * FROM notizie;";
                            $result = mysqli_query($conn, $sql);
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<tr><td>".$row["dataPubblicazione"]."</td><td>".$row["contenuto"]."</td><td><a href='eliminaNotizia.php?id=".$row["ID"]."'><img src='img/drop.webp' style='height: 2rem;'></a></td></tr>";
                            }
                        ?>
                    </table>
                </div>
            <?php } ?>
        </div>
    </body>
</html>
