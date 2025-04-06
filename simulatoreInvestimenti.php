<?php

function mulberry32($a) {
    return function() use ($a) {
        $t = $a += 0x6D2B79F5;
        $t = ($t ^ ($t >> 15)) * ($t | 1);
        $t ^= $t + (($t ^ ($t >> 7)) * ($t | 61));
        return (($t ^ ($t >> 14)) >> 0) / 4294967296;
    };
}

// Funzione del timer di gioco
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

function generateValue($variability, $volatility, $noisiness, $valoreBase, $seed) {
    $timestamp = gameTimer();
    $timestamp = number_format($timestamp, 5, '.', '');

    $v = 0;
    $sumAmplitudes = 0;
    $hAmplitude = [];
    $hPhase = [];
    $hfhAmplitude = [];
    $hfhPhase = [];

    $rng = mulberry32($seed);

    for ($i = 0; $i < 1024; $i++) {
        $hAmplitude[$i] = $variability * ($rng() * 1000 / ($i * 0.7 + $rng() * 0.3));
        $hPhase[$i] = $rng() * 2 * pi();
    }

    for ($i = 0; $i < 1024; $i++) {
        $hfhAmplitude[$i] = $noisiness * ($rng() / ($i * 0.3 + $rng() * 0.7));
        $hfhPhase[$i] = $rng() * 2 * pi();
    }

    $baseF = $volatility / 100;

    // Calcolo armoniche basse
    for ($i = 0; $i < count($hAmplitude); $i++) {
        $v += $hAmplitude[$i] * sin($baseF * $timestamp * $i + $hPhase[$i]);
        $sumAmplitudes += $hAmplitude[$i];
    }

    // Calcolo armoniche alte
    for ($i = 0; $i < count($hfhAmplitude); $i++) {
        $hfValue = $hfhAmplitude[$i] * sin($baseF * 1000 * $timestamp * $i + $hfhPhase[$i]);
        $ultraHfValue = $hfhAmplitude[$i] * sin($baseF * 100000 * $timestamp * $i + $hfhPhase[$i]) * 0.2;
        $v += $hfValue + $ultraHfValue;
        $sumAmplitudes += $hfhAmplitude[$i];
    }

    $v /= $sumAmplitudes; // Stabilizza maggiormente il valore di v
    $v += 1;

    $v = abs($v) * $valoreBase;

    // Ottimizzazione per valori bassi
    if ($v < 0.1) {
        $v = exp($v - 2.4);
    }
    $v = max($v - 0.05, 0.001);

    return $v;
}

function previous($variability, $volatility, $noisiness, $valoreBase, $seed, $t) {
    $t = number_format($t, 5, '.', '');
    
    $v = 0;
    $sumAmplitudes = 0;
    $hAmplitude = [];
    $hPhase = [];
    $hfhAmplitude = [];
    $hfhPhase = [];

    $rng = mulberry32($seed);

    for ($i = 0; $i < 1024; $i++) {
        $hAmplitude[$i] = $variability * ($rng() * 1000 / ($i * 0.7 + $rng() * 0.3));
        $hPhase[$i] = $rng() * 2 * pi();
    }

    for ($i = 0; $i < 1024; $i++) {
        $hfhAmplitude[$i] = $noisiness * ($rng() / ($i * 0.3 + $rng() * 0.7));
        $hfhPhase[$i] = $rng() * 2 * pi();
    }

    $baseF = $volatility / 100;

    // Calcolo armoniche basse
    for ($i = 0; $i < count($hAmplitude); $i++) {
        $v += $hAmplitude[$i] * sin($baseF * $t * $i + $hPhase[$i]);
        $sumAmplitudes += $hAmplitude[$i];
    }

    // Calcolo armoniche alte
    for ($i = 0; $i < count($hfhAmplitude); $i++) {
        $hfValue = $hfhAmplitude[$i] * sin($baseF * 1000 * $t * $i + $hfhPhase[$i]);
        $ultraHfValue = $hfhAmplitude[$i] * sin($baseF * 100000 * $t * $i + $hfhPhase[$i]) * 0.2;
        $v += $hfValue + $ultraHfValue;
        $sumAmplitudes += $hfhAmplitude[$i];
    }

    $v /= $sumAmplitudes; // Stabilizza maggiormente il valore di v
    $v += 1;

    $v = abs($v) * $valoreBase;

    // Ottimizzazione per valori bassi
    if ($v < 0.1) {
        $v = exp($v - 2.4);
    }
    $v = max($v - 0.05, 0.001);

    return $v;
}

?>
