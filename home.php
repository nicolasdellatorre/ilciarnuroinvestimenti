<!-- Pagina principale dell'applicazione. Permette all'utente loggato di visualizzare il controvalore di titoli posseduti e la liquidità.
    L'utente può vedere un riepilogo dell'andamento dei titoli nel portafoglio, gli indici di borsa e le notizie pubblicate -->

<?php
    session_start();
    if(!isset($_SESSION["username"])) {
        header("Location: login.php?err=Si prega di loggarsi");
    }

    include("connessione.php");
    include("simulatoreInvestimenti.php");

    $sql = "SELECT * FROM infoutenti WHERE username = '".$_SESSION['username']."';";
    $result = mysqli_query($conn, $sql);
    $utente = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="utf-8">
        <!-- <meta http-equiv="refresh" content="0.5"> -->
        <title>Home | Il Ciarnuro - Simulatore di Investimenti</title>
        <link rel="stylesheet" type="text/css" href="stile.css">
        <link rel="icon" type="image/png" href="img/GCFflavicon.png">
    </head>
    <body style="margin: 0;">
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
                        <?php echo $utente["nomeCompleto"]; ?>
                    </p>
                </div>
            </div>


            <div id="portofolio" style="display: flex; flex-wrap: wrap; padding: 1.1rem; width: 92%; text-align: center; margin-left: 2.7rem;">
                <h3 style="font-size: 2rem; margin-left: 4.7rem; text-align: left; max-width: 20rem;">Il tuo portafoglio</h3>
                <div style="display: flex; justify-content: space-between; margin-left:5%; padding-bottom:0;">
                    <div style="display: flex;">
                        <div style="margin-right: 2rem; text-align: center;">
                            <?php   
                                $sql = "SELECT * FROM titoliacquistati INNER JOIN titoli ON titoliacquistati.IDtitolo = titoli.ID WHERE username = '".$_SESSION['username']."';";
                                $result = mysqli_query($conn, $sql);
                                $controvalore = 0;
                                if (mysqli_num_rows($result) > 0) {
                                    while ($row = mysqli_fetch_assoc($result)) {

                                        if ($row["tipoStrumento"] == "AETF indicizzato") {
                                            $valoreTitolo = aetf($row["ID"], gameTimer());
                                        } else {
                                            $valoreTitolo = generateValue($row["variability"], $row["volatility"], $row["noisiness"], $row["prezzoPartenza"], $row["seed"]);
                                        }
                                        
                                        $posseduto = $valoreTitolo * $row["quantitaAttuale"];
                                        $controvalore += $posseduto;

                                    }
                                }
                                $controvalore = round($controvalore, 0);
                            ?>
                            <p style="font-size: 2.5rem; font-weight: bold; margin-bottom: 0; margin-top: 4rem;" id="controvaloreHome"><?php echo number_format($controvalore, 0, '.', "'"); ?> Kr</p>
                            <p style="margin: 0; font-size: 1.7rem;">CONTROVALORE<br>TITOLI</p>
                        </div>
                        <div style="margin-right: 2rem; text-align: center;">
                            <p style="font-size: 2.5rem; font-weight: bold; margin-bottom: 0; margin-top: 4rem" id="liquiditaHome"><?php echo number_format($utente["liquidita"], 0, '.', "'"); ?> kr</p>
                            <p style="margin: 0; font-size: 1.7rem;">LIQUIDITÀ</p>
                        </div>
                    </div>

                    <div style="display: flex;flex-direction: column;">
                        <div id="stocks" style="display: flex;flex-direction: column; gap: 10px; margin-top: 20px; margin-left: 2rem; max-height: 10rem; overflow-y: auto;">
                            <?php
                                $sql = "SELECT * FROM titoliacquistati INNER JOIN titoli ON titoliacquistati.IDtitolo = titoli.ID WHERE username = '".$_SESSION['username']."';";
                                $result = mysqli_query($conn, $sql);
                                $num = 0;
                                if (mysqli_num_rows($result) > 0) {
                                    while ($row = mysqli_fetch_assoc($result)) {

                                        if ($row["tipoStrumento"] == "AETF indicizzato") {
                                            $valoreTitolo = aetf($row["ID"], gameTimer());
                                        } else {
                                            $valoreTitolo = generateValue($row["variability"], $row["volatility"], $row["noisiness"], $row["prezzoPartenza"], $row["seed"]);
                                        }

                                        $posseduto = $valoreTitolo * $row["quantitaAttuale"];

                                        echo "<div class='stock'>";
                                        echo "<span style='font-weight: bold;margin-right: 50%;'>".$row["nome"]." (".$row["sigla"].")"."</span>";
                                        echo "<span style='width: 10rem; text-align: right;'>";
                                        echo "<span id='val".$num."'>".number_format($posseduto, 2, '.', "'")." Kr</span><br>";
                                        
                                        if ($row["tipoStrumento"] == "AETF indicizzato") {
                                            $valoreIeri = aetf($row["ID"], gameTimer() - 1);
                                        } else {
                                            $valoreIeri = previous($row["variability"], $row["volatility"], $row["noisiness"], $row["prezzoPartenza"], $row["seed"], gameTimer() - 1);
                                        }
                                        
                                        $diff = $valoreTitolo - $valoreIeri;
                                        $percent = $diff /  $valoreIeri;
                                        $percent = round($percent * 100, 2); 
                                        if ($percent > 0) {
                                            echo "<span style='color: #41cc9d;' id='perc".$num."'>+".$percent."%</span>";
                                        } else {
                                            echo "<span style='color: #cc4141;' id='perc".$num."'>".$percent."%</span>";
                                        }
                                        
                                        echo "</span></div>";

                                        $num++;
                                    }
                                }
                            ?>
                        </div>
                        <a href="assets.php"><button class="bottone" style="width: 50%; margin-top: 2rem; margin-left: 1.7rem; text-align: left; padding-left: 1rem">
                            Vai a tutti i titoli →
                        </button></a>
                    </div>
                </div>
            </div>
            <?php
                $sql = "SELECT DISTINCT nome FROM indici;";
                $result = mysqli_query($conn, $sql);
                if (mysqli_num_rows($result) > 0) {
                    echo "<div id='indici' style='margin-top: 20px; padding: 20px; width: 92%; margin-left: 2.54rem;'>";
                    echo '<h3 style="font-size: 1.5rem; margin-left: 4.7rem">Indici</h3>';
                    echo '<div style="display: flex; gap: 1.1rem; overflow-x: auto; max-width: 100%; justify-content: center;">';
                    while ($row = mysqli_fetch_assoc($result)) {
                        $valoreIndice = calcolaIndice($row["nome"], gameTimer());
                        $valoreIndiceIeri = calcolaIndice($row["nome"], gameTimer() - 1);
                        $diff = ($valoreIndice - $valoreIndiceIeri) / $valoreIndiceIeri;
                        if ($diff < 0) {
                            echo '<div class="indice" style="background: #8B0000;">';
                        } else {
                            echo '<div class="indice" style="background: #008000;">';
                        }
                        echo '<p style="text-align: left; margin-left: 2.2rem; height: 2rem">'.$row["nome"].'</p>';
                        echo '<div style="display: flex;flex-direction: column;">';
                        echo '<p style="text-align: right; margin-top: 1rem; margin-left: 5rem; margin-bottom: 0; margin-right:1rem;">'.number_format($valoreIndice, 2, '.', "'").' Kr</p>';
                        if ($diff < 0) {
                            echo '<p style="color: #ff6666; text-align: right; margin-top: 0.1rem; margin-bottom: 1rem; margin-right:1rem;">'.round($diff * 100, 2).'%</p>';
                        } else {
                            echo '<p style="color: #66ff66; text-align: right; margin-top: 0.1rem; margin-bottom: 1rem; margin-right:1rem;">+ '.round($diff * 100, 2).'%</p>';
                        }
                        echo "</div></div>";
                    }
                    echo "</div></div>";
                }

            ?>

            <div id="news" style="display: flex; flex-direction: row; margin-top: 20px; gap: 1.1rem; padding: 20px; width: 92%; margin-left: 2.54rem;">
                <div id="primoPiano">
                    <div style="margin: 0; background: #00000081; padding: 1.2rem">
                        <h3 style="margin: 0 0 10px 0; margin-top: 20px; font-size: 3.3rem;">Benvenuto nel Gran Circuito Frugale</h3>
                        <p style="margin: 0; font-size: 1rem;">
                            Lo strumento più amato dagli investitori del mondo de Il Ciarnuro
                    </div>
                </div>
                <div id="menoImportante">
                    <h3 style="margin: 0 0 10px 0;font-size: 2rem;">LATEST NEWS</h3>
                    <ul id="tabMenoImportante" style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 10px;">
                        <?php
                            $sql = "SELECT * FROM notizie;";
                            $result = mysqli_query($conn, $sql);
                            if (mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo "<li>".$row["contenuto"]."</li>";
                                }
                            }
                        ?>
                    </ul>
                </div>
            </div>
        </div>
    </body>
</html>
