<!-- Pagina visualizzata dall'utente per effettuare il login con le proprie credenziali all'applicazion web -->

<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="utf-8">
        <title>Login | Il Ciarnuro - Simulatore di Investimenti</title>
        <link rel="stylesheet" type="text/css" href="stile.css">
        <link rel="icon" type="image/png" href="img/GCFflavicon.png">
        <script src="sw.js" type="text/javascript"></script>
    </head>
    <body>
        <div id="login-page" class="login-page" style="text-align: center;">
            <form id="login-box" action="elogin.php" method="get">
                <img src="img/logo.png" class="logo-login">
                <div style="text-align: left; padding:2rem;">
                    <div>
                        <?php
                            if(!empty($_REQUEST["msg"])) {
                                echo "<p style='color:green;'>".$_REQUEST["msg"]."</p>";
                            }
                            if(!empty($_REQUEST["err"])) {
                                echo "<p style='color:red;'>".$_REQUEST["err"]."</p>";
                            }
                        ?>
                        <p>Nome utente</p>
                        <input type="text" name="username" id="usernameLogin" class="login" required>
                    </div>
                <div>
                    <p>Password</p>
                    <input type="password" name="password" id="passwordLogin" class="login" required>
                </div>
                
                <div style="text-align: center;">
                <input class="bottone" type="submit" value="ACCEDI">
                </div>
            </form>
            <p style="text-align:center; margin-top:2rem;">
            Non hai un account? <a href="signin.php" style="color:white;">Sign in</a>
            </p>
        </div>
        </div>
    </body>
</html>