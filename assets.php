<!-- L'utente loggato visualizza sulla sinistra un elenco dei propri titoli e, al di sotto, tutti gli altri titoli.
    Cliccando su ciascun titolo, viene caricata sulla sinistra la pagina informativa contentente informazioni riguardanti il titolo:
    il grafico con l'andamento giornaliero, mensile o annuale, il valore di un asset, la viarazione percentuale rispetto al giorno precedente, la descrizione e il settore in cui opera.
    Da questa interfaccia è possibile acquistare i titolo o venderlo.
    Se il titolo è in portafoglio, vengono visualizzati: la quantità, il valore e la variazione percentuale del titolo rispetto al primo acquisto.
    Informazioni riguardanti i dividendi e le commissioni.
    È possibile inserire una propria valutazione rispetto al titolo.
    Si visualizza il rating della sezione tecnica e un rating delle aziende. -->

<?php
    session_start();
    if(!isset($_SESSION["username"])) {
        header("Location: login.php?err=Si prega di loggarsi");
    }

    include("connessione.php");
    include("simulatoreInvestimenti.php");

    $sql = "SELECT * FROM infoutenti WHERE username='".$_SESSION["username"]."';";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="utf-8">
        <title>Assets | Il Ciarnuro - Simulatore di Investimenti</title>
        <link rel="stylesheet" type="text/css" href="stile.css">
        <link rel="icon" type="image/png" href="img/GCFflavicon.png">
    </head>
    <body style="margin: 0; overflow-x:hidden;">
        <div id="assetPag">
            <div id="sopra" style="display: flex;">
            <a href="home.php">
                <img src="img/homeIco.png" style="width: 3vw; height: auto;">
            </a>
            <img src="img/Logo.png" style="width: 15vw; height: auto; margin-left: 36vw;">
            <p style="margin-left: 29vw; margin-right: 0.7vw;"><?php echo gameTimerAsDate(); ?></p>
            <img src="img/iconaUtente.png" style="width: 3.1vw; height: auto;">
            </div>
            <div id="sinistra">
                <h3>Liquidità: <?php echo number_format($row["liquidita"], 2, '.', "'") . " Kr"; ?></h3>
                <h2>I tuoi titoli</h2>
                <?php
                    $sql = "SELECT * FROM titoliacquistati INNER JOIN titoli ON titoliacquistati.IDtitolo = titoli.ID WHERE username = '".$_SESSION['username']."';";
                    $result = mysqli_query($conn, $sql);
                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<button onclick="."location.href='assets.php?asset=".$row["ID"]."'".">";
                            echo "<p>".$row["nome"]." (".$row["sigla"].")</p>";
                            echo "<b><span>";
                            if ($row["tipoStrumento"] == "AETF indicizzato") {
                                $valoreTitolo = aetf($row["ID"], gameTimer());
                            } else {
                                $valoreTitolo = generateValue($row["variability"], $row["volatility"], $row["noisiness"], $row["prezzoPartenza"], $row["seed"]);
                            }
                            $posseduto = $valoreTitolo * $row["quantitaAttuale"];
                            echo number_format($posseduto, 2, '.', "'") . " Kr ";
                            if ($row["tipoStrumento"] == "AETF indicizzato") {
                                $valoreIeri = aetf($row["ID"], gameTimer() - 1);
                            } else {
                                $valoreIeri = previous($row["variability"], $row["volatility"], $row["noisiness"], $row["prezzoPartenza"], $row["seed"], gameTimer() - 1);
                            }
                            
                            $diff = $valoreTitolo - $valoreIeri;
                            $percent = $diff /  $valoreIeri;
                            $percent = round($percent * 100, 2);
                            if ($percent > 0) {
                                echo "<span style='color: #41cc9d;'> +".$percent."%</span>";
                            } else {
                                echo "<span style='color: #cc4141;'> ".$percent."%</span>";
                            }
                            echo "</b></button><hr>";
                        }
                    }
                ?>
                <hr style="border: 3px solid;">
                <h2 style="margin-bottom: 0;">Tutti i titoli</h2>
                <?php
                    $sql = "SELECT * FROM titoli";
                    $result = mysqli_query($conn, $sql);
                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<button onclick="."location.href='assets.php?asset=".$row["ID"]."'".">";
                            echo "<p>".$row["nome"]." (".$row["sigla"].")</p>";
                            echo "<b><span>";
                            if ($row["tipoStrumento"] == "AETF indicizzato") {
                                $valoreTitolo = aetf($row["ID"], gameTimer());
                            } else {
                                $valoreTitolo = generateValue($row["variability"], $row["volatility"], $row["noisiness"], $row["prezzoPartenza"], $row["seed"]);
                            }
                            echo number_format($valoreTitolo, 2, '.', "'") . " Kr ";
                            
                            if ($row["tipoStrumento"] == "AETF indicizzato") {
                                $valoreIeri = aetf($row["ID"], gameTimer() - 1);
                            } else {
                                $valoreIeri = previous($row["variability"], $row["volatility"], $row["noisiness"], $row["prezzoPartenza"], $row["seed"], gameTimer() - 1);
                            }
                            
                            $diff = $valoreTitolo - $valoreIeri;
                            $percent = $diff /  $valoreIeri;
                            $percent = round($percent * 100, 2); 
                            if ($percent > 0) {
                                echo "<span style='color: #41cc9d;'> +".$percent."%</span>";
                            } else {
                                echo "<span style='color: #cc4141;'> ".$percent."%</span>";
                            }
                            echo "</b></button><hr>";
                        }
                    }
                ?>
            </div>
            <div id="destra">
            <?php
                if (!isset($_GET["asset"])) {
                    echo "<h2 style='margin-bottom:0;padding-bottom:40rem;'>Selezionare un titolo per aprire la visualizzazione dettagliata</h2>";
                } else {
                    $sql = "SELECT * FROM titoli WHERE ID = '" . $_GET["asset"] . "';";
                    $result = mysqli_query($conn, $sql);
                    $row = mysqli_fetch_assoc($result);

                    echo "<h1 style='margin-bottom:0;'>" . $row["nome"] . " (" . $row["sigla"] . ")</h1>";

                    echo "<div style='display: flex;'>";
                    echo "<div style='text-align: center; left: 3vw; margin-right: 1vh; width: 44vw;'>";
                        if ($row["tipoStrumento"] == "AETF indicizzato") {
                            $valoreTitolo = aetf($row["ID"], gameTimer());
                        } else {
                            $valoreTitolo = generateValue($row["variability"], $row["volatility"], $row["noisiness"], $row["prezzoPartenza"], $row["seed"]);
                        }
                        echo "<h1>" . number_format($valoreTitolo, 2, '.', "'") . " Kr</h1>";
                        
                        if ($row["tipoStrumento"] == "AETF indicizzato") {
                            $valoreIeri = aetf($row["ID"], gameTimer() - 1);
                        } else {
                            $valoreIeri = previous($row["variability"], $row["volatility"], $row["noisiness"], $row["prezzoPartenza"], $row["seed"], gameTimer() - 1);
                        }
                        $diff = $valoreTitolo - $valoreIeri;
                        $percent = round(($diff / $valoreIeri) * 100, 2);
                        if ($percent > 0) {
                            echo "<h2 style='color: #41cc9d;'>+" . $percent . "%</h2>";
                        } else {
                            echo "<h2 style='color: #cc4141;'>" . $percent . "%</h2>";
                        }
                        $variazione = $percent;
                        

                        $sql = "SELECT * FROM titoliacquistati 
                                INNER JOIN titoli ON titoliacquistati.IDtitolo = titoli.ID 
                                WHERE username = '" . $_SESSION['username'] . "' 
                                AND IDtitolo = '" . $_GET["asset"] . "';";
                        $result = mysqli_query($conn, $sql);

                        echo "<form action='operazione.php' method='GET'>";
                        echo "<input name='qt' type='number' value='0' min='0' style='border-radius: 25px; height: 3vh; padding: 1vh;'>";
                        echo "<input type='hidden' name='asset' value='" . $row["ID"] . "'>";
                        echo "<br><br>";
                        echo "<button type='submit' name='azione' value='compra' class='bottone' style='margin-top: 0; width: 6rem; height: 2.3rem;'>Compra</button>";
                        
                        if (mysqli_num_rows($result) > 0) {                            
                            echo "<button type='submit' name='azione' value='vendi' class='bottone' style='margin-top: 0; width: 6rem; height: 2.3rem;'>Vendi</button>";
                        } else {
                            echo "<button type='submit' name='azione' value='vendi' class='bottone' style='margin-top: 0; width: 6rem; height: 2.3rem;' disabled>Vendi</button>";
                        }

                        if (isset($_GET["msg"])) echo "<p>".$_GET["msg"]."</p>";

                        echo "</form>";                        

                        $posseduto = 0;
                        if (mysqli_num_rows($result) > 0) {
                            $titoloinportafoglio = mysqli_fetch_assoc($result);
                            echo "<div class='infoStock'>";
                                echo "<p><b>Nel tuo portafoglio</b></p>";
                                echo "<p>Quantità " . number_format($titoloinportafoglio["quantitaAttuale"], 2, '.', "'") . "</p>";
                                $posseduto = number_format($titoloinportafoglio["quantitaAttuale"] * $valoreTitolo, 2, '.', "'");
                                echo "<p>Valore " . $posseduto . "</p>";

                                $timestamp = strtotime($titoloinportafoglio["dataPrimoAcquisto"]);
                                $giorniTrascorsi = $timestamp / (60 * 60 * 24);
                                if ($row["tipoStrumento"] == "AETF indicizzato") {
                                    $valorePrimoAcquisto = aetf($row["ID"], $giorniTrascorsi);
                                } else {
                                    $valorePrimoAcquisto = previous($row["variability"], $row["volatility"], $row["noisiness"], $row["prezzoPartenza"], $row["seed"], $giorniTrascorsi);
                                }
                                $diff = $valoreTitolo - $valorePrimoAcquisto;
                                $percent = round(($diff / $valorePrimoAcquisto) * 100, 2);
                                echo "<p>Gain/Loss " . ($percent >= 0 ? '+' : '') . $percent . "%</p>";
                            echo "</div>";
                        }
                        
                        echo "<div class='infoStock'>";
                            echo "<p><b>Dividendi</b></p>";
                            echo "<p>Percentuale " . round($row["dividendo"] * 100, 2) . "%</p>";
                            if ($row["dividendo"] > 0) {
                                echo "<p>Frequenza " . $row["freqDividendo"] . " giorni</p>";
                                if ($posseduto > 0) echo "<p>Prossimo " . number_format($posseduto * $row["dividendo"], 2, '.', "'") . "</p>";
                            }
                        echo "</div>";
                        
                        echo "<div class='infoStock' style='background: #ff6f79'>";
                            echo "<p><b>Commissioni</b></p>";
                            echo "<p>Per operazione " . $row["commissioni"] . " Kr</p>";
                            echo "<p>Tassa " . number_format($row["tassa"] * 100, 2, '.', "'") . " %</p>";
                        echo "</div>";
                        
                        echo "<div class='infoStock'>";
                            echo "<p><b>Cosa ne pensi oggi su " . $row["sigla"] . "?</b></p>";
                            echo "<button class='bottone' style='background: #41cc9d; width: 6rem; height: 2.3rem; margin-top: 0;'>Ribassista</button>";
                            echo "<button class='bottone' style='background: #cc4141; width: 6rem; height: 2.3rem; margin-top: 0;'>Rialzista</button>";
                        echo "</div>";
                        echo "<div class='infoStock'>";
                            echo "<p><b>Sezione tecnica</b></p>";
                            echo "<p style='font-weight: bold; color: " . ($variazione > 0 ? '#41cc9d' : '#cc4141') . ";'>" . ($variazione > 0 ? 'Compra adesso' : 'Mantieni o vendi') . "</p>";
                        echo "</div>";
                    echo "</div>"; 

                    echo "<div>";
                        echo "<select id='timeframeSelect' style='margin-top: 5vh; position: absolute; right:2vh; width:12vw; height: 4vh; border-radius:20px;'>
                                <option value='giornaliero'>Giornaliero</option>
                                <option value='mensile'>Mensile</option>
                                <option value='annuale'>Annuale</option>
                                </select>";

                        echo "<div id='chartContainer' style='height:55vh;width:100%;margin-top:10vh;'>
                                <canvas id='trendChart'></canvas>
                                </div>";
                        echo "<div style='display:flex;'>";
                            echo "<p style='width: 53vw;'>";
                                echo $row["descrizione"] . "<br><br>";
                                echo "<b>" . $row["settoreMercato"]; "</b>";
                                if(!empty($row["settoreSpecifico"])) echo "<b> - " . $row["settoreSpecifico"] . "</b>";
                            echo "</p>";
                            echo "<div class='infoStock' style='padding: 1vh;'>";
                                echo "<p><b>Rating delle aziende</b></p>";
                                echo "<p>Krolik <br>(lungo termine) <b>ND</b></p>";
                                echo "<p>Fitzgerald Quur (speculativo) <b>ND</b></p>";
                            echo "</div>";
                        echo "</div>";
                    echo "</div>";
                    echo "</div>"; 

                    $annuale_labels = [];
                    $annuale_data   = [];
                    for ($i = 52; $i > 0; $i--) {
                        $annuale_labels[] = date("Y-m-d", (gameTimer()-$i)*60*60*24);
                        if ($row["tipoStrumento"] == "AETF indicizzato") {
                            $annuale_data[] = aetf($row["ID"], gameTimer() - $i*7);
                        } else {
                            $annuale_data[] = previous($row["variability"], $row["volatility"], $row["noisiness"], $row["prezzoPartenza"], $row["seed"], gameTimer() - $i*7);
                        }
                    }

                    $mensile_labels = [];
                    $mensile_data   = [];
                    for ($i = 30; $i > 0; $i--) {
                        $mensile_labels[] = date("Y-m-d", (gameTimer()-$i)*60*60*24);
                        if ($row["tipoStrumento"] == "AETF indicizzato") {
                            $mensile_data[] = aetf($row["ID"], gameTimer() - $i);
                        } else {
                            $mensile_data[] = previous($row["variability"], $row["volatility"], $row["noisiness"], $row["prezzoPartenza"], $row["seed"], gameTimer() - $i);
                        }
                    }

                    $giornaliero_labels = [];
                    $giornaliero_data   = [];
                    for ($i = 24; $i > 0; $i--) {
                        $giornaliero_labels[] = date("H:i", (gameTimer()-$i/24)*60*60*24);
                        if ($row["tipoStrumento"] == "AETF indicizzato") {
                            $giornaliero_data[] = aetf($row["ID"], gameTimer() - $i/24);
                        } else {
                            $giornaliero_data[] = previous($row["variability"], $row["volatility"], $row["noisiness"], $row["prezzoPartenza"], $row["seed"], gameTimer() - $i/24);
                        }
                        
                    }
                    ?>
                    
                    <!-- Inclusione di Chart.js da CDN e script per la generazione e l'aggiornamento del grafico -->
                    <script src="lib/chart.js"></script>
                    <script>
                    // I dataset vengono passati in JavaScript in formato JSON
                    const datasetAnnuale = {
                        labels: <?php echo json_encode($annuale_labels); ?>,
                        data: <?php echo json_encode($annuale_data); ?>
                    };
                    const datasetMensile = {
                        labels: <?php echo json_encode($mensile_labels); ?>,
                        data: <?php echo json_encode($mensile_data); ?>
                    };
                    const datasetGiornaliero = {
                        labels: <?php echo json_encode($giornaliero_labels); ?>,
                        data: <?php echo json_encode($giornaliero_data); ?>
                    };

                    // Funzione che restituisce il dataset in base al timeframe scelto
                    function getDataset(timeframe) {
                        if(timeframe === 'annuale') {
                            return datasetAnnuale;
                        } else if(timeframe === 'mensile') {
                            return datasetMensile;
                        } else if(timeframe === 'giornaliero') {
                            return datasetGiornaliero;
                        }
                    }

                    // Inizializzazione del grafico con il timeframe "annuale" di default
                    const ctx = document.getElementById('trendChart').getContext('2d');
                    let currentDataset = getDataset('giornaliero');
                    let trendChart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: currentDataset.labels,
                            datasets: [{
                                label: '<?php echo $row["sigla"]; ?>',
                                data: currentDataset.data,
                                borderColor: '#007bff',
                                backgroundColor: 'rgba(0,123,255,0.5)',
                                fill: true
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                x: {
                                    title: {
                                        display: true,
                                        text: 'Tempo'
                                    }
                                },
                                y: {
                                    title: {
                                        display: true,
                                        text: 'Valore'
                                    }
                                }
                            }
                        }
                    });

                    // Quando l'utente cambia il timeframe, aggiorniamo il grafico
                    document.getElementById('timeframeSelect').addEventListener('change', function() {
                        const timeframe = this.value;
                        const newDataset = getDataset(timeframe);
                        trendChart.data.labels = newDataset.labels;
                        trendChart.data.datasets[0].data = newDataset.data;
                        trendChart.update();
                    });
                    </script>
                <?php
                }
                ?>

            </div>
        </div>
    </body>
</html>