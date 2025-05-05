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

?>
