<!-- Una volta che l'amministratore loggato ha modificato i dati rigurdanti il titolo da mdificare, viene richiamata questa pagina. Non visibile all'utente.
 La pagina modifica il titolo selezionato nel database --> 

<?php
    session_start();
    if (!isset($_SESSION["username"]) || $_SESSION["username"] != "administrator") {
        header("Location: login.php?err=Si prega di loggarsi come amministratore");
        exit();
    }

    include("connessione.php");

    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $nome = $_GET['nome'];
        $sigla = $_GET['sigla'];
        $data_lancio = $_GET['data_lancio'];
        $tipo_strumento = $_GET['tipo_strumento'];
        $prezzo_partenza = $_GET['prezzo_partenza'];
        $settore_mercato = $_GET['settore_mercato'];
        $settore_mercato_specifico = $_GET['settore_mercato_specifico'];
        $commissioni = $_GET['commissioni'];
        $tassa = $_GET['tassa'];
        $dividendo = $_GET['dividendo'];
        $frequenza_dividendo = $_GET['frequenza_dividendo'];
        $descrizione = htmlspecialchars($_GET["descrizione"], ENT_QUOTES);
        $variability = $_GET['variability'];
        $volatility = $_GET['volatility'];
        $noisiness = $_GET['noisiness'];
        $seed = $_GET['seed'];

        $query = "
            UPDATE titoli SET 
                nome = '$nome', 
                sigla = '$sigla', 
                dataLancio = '$data_lancio', 
                tipoStrumento = '$tipo_strumento', 
                prezzoPartenza = $prezzo_partenza, 
                settoreMercato = '$settore_mercato', 
                settoreSpecifico = '$settore_mercato_specifico', 
                commissioni = $commissioni, 
                tassa = $tassa, 
                dividendo = $dividendo, 
                freqDividendo = $frequenza_dividendo, 
                descrizione = '$descrizione', 
                variability = $variability, 
                volatility = $volatility, 
                noisiness = $noisiness, 
                seed = $seed 
            WHERE ID = '$id'";

        if (mysqli_query($conn, $query)) {
            header("Location: administrator.php?msg=Titolo modificato con successo");
        } else {
            header("Location: administrator.php?err=Errore durante la modifica del titolo");
        }
    }
?>
