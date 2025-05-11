<!-- Pagina che permette a un nuovo utente di creare un account -->

<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="utf-8">
        <title>Sign-in | Il Ciarnuro - Simulatore di Investimenti</title>
        <link rel="stylesheet" type="text/css" href="stile.css">
        <link rel="icon" type="image/png" href="img/GCFflavicon.png">
        <script src="sw.js" type="text/javascript"></script>
    </head>
    <body>
    <div id="signin-page" class="signin-page">
      <div id="signin-box">
        <img src="img/logo.png" class="logo-login">
        <form action="esignin.php" method="get">
            <table>
                <?php
                    if(!empty($_REQUEST["msg"])) {
                        echo "<tr>";
                        echo "<td colspan='2' style='color:red;'>";
                        echo $_REQUEST["msg"];
                        echo "</td></tr>";
                    }
                ?>
                <tr>
                    <td>
                        <p>Nome Completo Personaggio</p>
                        <input type="text" id="nome" name="nome" class="sign" required>
                    </td>
                    <td>
                        <p>Specie</p>
                        <select class="selectsign" id="specie" name="specie">
                            <option value="umano">Umano</option>
                            <option value="krentoriani">Krentoriani</option>
                            <option value="sauniA">Sauni Arcaici</option>
                            <option value="sauniE">Sauni Eletti</option>
                            <option value="quark">Qüark</option>
                            <option value="pravosianiG">Pravosiani Guerrieri</option>
                            <option value="pravosianiGS">Pravosiani Guide Spirituali</option>
                            <option value="veriS">Veri Syviar</option>
                            <option value="ivosiani">Ivosiani</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>
                        <p>Username</p>
                        <input type="text" name="username" id="username" class="sign" required>    
                    </td>
                    <td>
                        <p> Sesso</p>
                        <select class="selectsign" id="sesso" name="sesso">
                            <option value="m">Maschio</option>
                            <option value="f">Femmina</option>
                            <option value="n">Altro</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>
                        <p>Password</p>
                        <input type="password" name="password" id="password" class="sign" required>
                    </td>
                    <td>
                        <p>Conferma password</p>
                        <input type="password" name="password2" id="passwordConf" class="sign" required>
                    </td>
                </tr>
                <tr>
                    <td>
                        <p>Saldo iniziale</p>
                        <input type="number" name="saldoIniziale" id="saldoIniziale" class="sign" required value="0">
                    </td>
                </tr>
            </table>
            <div style="text-align: center;">
                <input id="bottoneReg" type="submit" value="REGISTRATI">
            </div>
        </form>
        Hai già un account? <a href="login.php" style="color: white;">Log in</a>
      </div>
    </div>
    </body>
</html>