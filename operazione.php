<!-- Pagina che viene richiamata nel caso in cui l'utente loggato intenda acquistare o vendere un titolo da 'assets.php'. Non viene visualizzata.
    In caso di acquisto, si verifica che la liquidità sia sufficiente e viene effettuata l'operazione, si applicano eventuali commissioni e si sincronizzano i dati con il database.
    In caso di vendita, si verifica che la quantità posseduta in portafoglio sia sufficiente, si applicano eventuali tasse e si sincronizzano i dati con il database.-->

<?php
    session_start();
    if (!isset($_SESSION["username"])) {
        header("Location: login.php?err=Si prega di loggarsi");
        exit();
    }

    include("connessione.php");
    include("simulatoreInvestimenti.php");

    $quantita = $_GET["qt"];
    echo $_GET["azione"] . " " . $_GET["quantita"] . " " . $_GET["asset"];

    if ($_GET["azione"] == "compra") {
        $sql = "SELECT * FROM infoutenti WHERE username='" . $_SESSION["username"] . "';";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_assoc($result);
        $liquidita = $row["liquidita"];

        $sql = "SELECT * FROM titoli WHERE ID='" . $_GET["asset"] . "';";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_assoc($result);
        $valoreTitolo = generateValue($row["variability"], $row["volatility"], $row["noisiness"], $row["prezzoPartenza"], $row["seed"]);

        $costo = $quantita * $valoreTitolo + $row["commissioni"];

        if ($liquidita < $costo) {
            header("Location: assets.php?asset=" . $_GET["asset"] . "&msg=Liquidità insufficiente per completare l'operazione");
            exit();
        }

        $sql = "SELECT * FROM titoliacquistati WHERE username='" . $_SESSION["username"] . "' AND IDtitolo='" . $_GET["asset"] . "';";
        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) > 0) {
            $sql = "UPDATE titoliacquistati SET quantitaAttuale = quantitaAttuale + $quantita WHERE username='" . $_SESSION["username"] . "' AND IDtitolo='" . $_GET["asset"] . "';";
        } else {
            $sql = "INSERT INTO titoliacquistati (username, IDtitolo, dataPrimoAcquisto, quantitaAttuale) VALUES('" . $_SESSION["username"] . "', '" . $_GET["asset"] . "', '" . gameTimerAsDate() . "', $quantita);";
        }

        $result = mysqli_query($conn, $sql);

        if (!$result) {
            header("Location: assets.php?asset=" . $_GET["asset"] . "&msg=Errore");
            exit();
        }

        $sql = "UPDATE infoutenti SET liquidita = liquidita - $costo WHERE username='" . $_SESSION["username"] . "';";
        $result = mysqli_query($conn, $sql);

        if (!$result) {
            header("Location: assets.php?asset=" . $_GET["asset"] . "&msg=Errore");
            exit();
        }

        header("Location: assets.php?asset=" . $_GET["asset"] . "&msg=Operazione effettuata con successo");
        exit();
    } else {
        $sql = "SELECT * FROM titoli WHERE ID='" . $_GET["asset"] . "';";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_assoc($result);
        $valoreTitolo = generateValue($row["variability"], $row["volatility"], $row["noisiness"], $row["prezzoPartenza"], $row["seed"]);

        $ricavo = $quantita * $valoreTitolo - ($quantita * $valoreTitolo * $row["tassa"]);

        $sql = "SELECT * FROM titoliacquistati WHERE username='" . $_SESSION["username"] . "' AND IDtitolo='" . $_GET["asset"] . "';";
        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            if ($row["quantitaAttuale"] < $quantita) {
                header("Location: assets.php?asset=" . $_GET["asset"] . "&msg=Non hai abbastanza titoli da vendere");
                exit();
            }

            $nuovaQuantita = $row["quantitaAttuale"] - $quantita;
            if ($nuovaQuantita == 0) {
                $sql = "DELETE FROM titoliacquistati WHERE username='" . $_SESSION["username"] . "' AND IDtitolo='" . $_GET["asset"] . "';";
            } else {
                $sql = "UPDATE titoliacquistati SET quantitaAttuale = $nuovaQuantita WHERE username='" . $_SESSION["username"] . "' AND IDtitolo='" . $_GET["asset"] . "';";
            }

            $result = mysqli_query($conn, $sql);
            if (!$result) {
                header("Location: assets.php?asset=" . $_GET["asset"] . "&msg=Errore nell'aggiornamento dei titoli");
                exit();
            }

            $sql = "UPDATE infoutenti SET liquidita = liquidita + $ricavo WHERE username='" . $_SESSION["username"] . "';";
            $result = mysqli_query($conn, $sql);
            if (!$result) {
                header("Location: assets.php?asset=" . $_GET["asset"] . "&msg=Errore nell'aggiornamento della liquidità");
                exit();
            }

            header("Location: assets.php?asset=" . $_GET["asset"] . "&msg=Operazione di vendita effettuata con successo");
            exit();
        }
    }
?>
