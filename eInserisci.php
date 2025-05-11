<!-- Una volta che l'amministratore loggato ha inserito i dati rigurdanti il titolo da creare, viene richiamata questa pagina. Non visibile all'utente.
 La pagina inserisce il titolo appena creato nel database --> 

<?php
    session_start();
    if (!isset($_SESSION["username"]) || $_SESSION["username"] != "administrator") {
        header("Location: login.php?err=Si prega di loggarsi come amministratore");
        exit();
    }


    include("connessione.php");

    $id = $_GET["id"];
    $nome = $_GET["nome"];
    $sigla = $_GET["sigla"];
    $data_lancio = $_GET["data_lancio"];
    $tipo_strumento = $_GET["tipo_strumento"];
    $prezzo_partenza = $_GET["prezzo_partenza"];
    $settore_mercato = $_GET["settore_mercato"];
    $settore_specifico = $_GET["settore_mercato_specifico"];
    $commissioni = $_GET["commissioni"];
    $tassa = $_GET["tassa"];
    $dividendo = $_GET["dividendo"];
    $frequenza_dividendo = $_GET["frequenza_dividendo"];
    $descrizione = htmlspecialchars($_GET["descrizione"], ENT_QUOTES);
    $variability = $_GET["variability"];
    $volatility = $_GET["volatility"];
    $noisiness = $_GET["noisiness"];
    $seed = $_GET["seed"];

    $sql = "INSERT INTO titoli (
        ID, nome, sigla, dataLancio, tipoStrumento, prezzoPartenza,
        settoreMercato, settoreSpecifico, commissioni, tassa,
        dividendo, freqDividendo, descrizione, variability,
        volatility, noisiness, seed
    ) VALUES (
        '$id', '$nome', '$sigla', '$data_lancio', '$tipo_strumento', $prezzo_partenza,
        '$settore_mercato', '$settore_specifico', $commissioni, $tassa,
        $dividendo, $frequenza_dividendo, '$descrizione', $variability,
        $volatility, $noisiness, $seed
    );";

    if (mysqli_query($conn, $sql)) {
        header("Location: administrator.php?msg=Titolo creato con successo");
        exit();
    } else {
        header("Location: administrator.php?msg=Errore nella creazione del titolo");
        exit();
    }
?>
