<!-- Pagina che contiene le funzioni fondamentali per il funzionamento della logica dell'applicazione -->

<?php

function mulberry32($a) {
    return function() use ($a) {
        $t = $a += 0x6D2B79F5;
        $t = ($t ^ ($t >> 15)) * ($t | 1);
        $t ^= $t + (($t ^ ($t >> 7)) * ($t | 61));
        return (($t ^ ($t >> 14)) >> 0) / 4294967296;
    };
}

// Gestione del timer di gioco

$startDate = strtotime("2127-01-01");
$realStartDate = strtotime("2023-12-23");
$speedup = 20;

function gameTimer() {
    global $startDate, $realStartDate, $speedup;
    return (($startDate + (time() - $realStartDate) * $speedup) / (60 * 60 * 24));
}

function gameTimerAsDate($t = null) {
    if ($t === null) $t = gameTimer();
    return date('Y-m-d H:i:s', $t * 60 * 60 * 24);
}

function numberTo2Digits($n) {
    return ($n >= 0 && $n < 10) ? "0" . $n : (string)$n;
}

// Funzione che genera il valore di un titolo al timestamp attuale dati i parametri necessari

function generateValue($variability, $volatility, $noisiness, $valoreBase, $seed) {
    $timestamp = gameTimer();
    $timestamp = number_format($timestamp, 5, '.', '');

    $v = 0;
    $sumAmplitudes = 0;
    $hAmplitude = [];
    $hPhase = [];

    $rng = mulberry32($seed);

    for ($i = 0; $i < 128; $i++) {
        $hAmplitude[$i] = $variability * ($rng() * 1000 / ($i + 1));
        $hPhase[$i] = $rng() * 2 * pi();
    }

    $baseF = $volatility / 100;

    for ($i = 0; $i < count($hAmplitude); $i++) {
        $v += $hAmplitude[$i] * sin($baseF * $timestamp * $i + $hPhase[$i]);
        $sumAmplitudes += $hAmplitude[$i];
    }

    $v += ($rng() - 0.5) * $noisiness * 2;

    $v /= ($sumAmplitudes / 2 + 1e-9);
    $v = tanh($v);

    $v = $valoreBase * (1 + $v * 0.6); 

    return max($v, 0.001);
}

// Funzione che genera il valore del titolo a un certo timestamp espresso in giorni (logica identica alla precedente funzione)

function previous($variability, $volatility, $noisiness, $valoreBase, $seed, $t) {
    $timestamp = $t;
    $timestamp = number_format($timestamp, 5, '.', '');

    $v = 0;
    $sumAmplitudes = 0;
    $hAmplitude = [];
    $hPhase = [];

    $rng = mulberry32($seed);

    for ($i = 0; $i < 128; $i++) {
        $hAmplitude[$i] = $variability * ($rng() * 1000 / ($i + 1));
        $hPhase[$i] = $rng() * 2 * pi();
    }

    $baseF = $volatility / 100;

    for ($i = 0; $i < count($hAmplitude); $i++) {
        $v += $hAmplitude[$i] * sin($baseF * $timestamp * $i + $hPhase[$i]);
        $sumAmplitudes += $hAmplitude[$i];
    }

    $v += ($rng() - 0.5) * $noisiness * 2;

    $v /= ($sumAmplitudes / 2 + 1e-9); 
    $v = tanh($v); 

    $v = $valoreBase * (1 + $v * 0.6);
    return max($v, 0.001);
}

// Funzione che genera il valore di un AETF (asset che contiene altri titoli) dato l'ID

function aetf($id, $t) {
    include("connessione.php");

    $sql = "
        SELECT 
            tc.prezzoPartenza AS prezzoPartenzaComponente,
            tc.variability,
            tc.volatility,
            tc.noisiness,
            tc.seed,
            a.peso,
            te.prezzoPartenza AS prezzoPartenzaETF
        FROM aetf a
        INNER JOIN titoli tc ON a.IDcomponente = tc.ID
        INNER JOIN titoli te ON a.IDaetf = te.ID
        WHERE a.IDaetf = '$id';
    ";

    $result = mysqli_query($conn, $sql);
    if (!$result) return false;

    $variazioneMediaTitoli = 0;
    $prezzoETF = 0;

    while ($row = mysqli_fetch_assoc($result)) {
        $valoreBase = $row["prezzoPartenzaComponente"];
        $valoreTitolo = previous(
            $row["variability"],
            $row["volatility"],
            $row["noisiness"],
            $row["prezzoPartenzaComponente"],
            $row["seed"],
            $t
        );
        $variazioneMediaTitoli += (($valoreTitolo - $valoreBase) / $valoreBase) * $row["peso"];
        $prezzoETF = $row["prezzoPartenzaETF"];
    }

    $valore = $prezzoETF + $prezzoETF * $variazioneMediaTitoli;
    return $valore;
}

// Fuzione che calcola il valore di un dato indice a un certo istante di tempo $t espresso in giorni

function calcolaIndice($nome, $t) {
    include("connessione.php");
    $sql = "SELECT * FROM indici INNER JOIN titoli ON indici.componente = titoli.ID WHERE indici.nome = '$nome';";
    $result = mysqli_query($conn, $sql);
    $somma = 0;
    $count = 0;
    while ($row = mysqli_fetch_assoc($result)) {
        $count++;
        $somma += previous($row["variability"], $row["volatility"], $row["noisiness"], $row["prezzoPartenza"], $row["seed"], $t);
    }
    $valore = $somma / $count;
    return $valore;
}


?>
