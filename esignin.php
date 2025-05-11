<!-- Pagina che elabora i dati inseriti dall'utente nella pagina di signin. Non viene visualizzata.
    Inserisce i dati del nuovo utente nel database (se lo username non è già in uso) -->

<?php
    include("connessione.php");

    $nomeCompleto = $_REQUEST['nome'];
    $specie = $_REQUEST['specie'];
    $username = $_REQUEST['username'];
    $sesso = $_REQUEST['sesso'];
    $passwd = $_REQUEST['password'];
    $passwd2 = $_REQUEST['password2'];
    $saldoIniziale = $_REQUEST['saldoIniziale'];

    if ($passwd != $passwd2) {
        header("Location:signin.php?msg=Le password non coincidono");
        exit();
    }

    $check_sql = "SELECT username FROM login WHERE username = '$username'";
    $check_result = mysqli_query($conn, $check_sql);

    if (mysqli_num_rows($check_result) > 0) {
        header("Location:signin.php?msg=Errore: Username già in uso");
        exit();
    }

    $sql = "INSERT INTO login(username, psswrd) VALUES ('$username', '$passwd')";
    if (!mysqli_query($conn, $sql)) {
        header("Location:signin.php?msg=Errore nella creazione dell'utente");
        exit();
    }

    $sql2 = "INSERT INTO infoutenti(username, liquidita, nomeCompleto, sesso, specie) VALUES ('$username', '$saldoIniziale', '$nomeCompleto', '$sesso', '$specie')";
    if (!mysqli_query($conn, $sql2)) {
        header("Location:signin.php?msg=Errore nella creazione delle informazioni utente");
        exit();
    }

    header("Location:login.php?msg=Registrazione effettuata con successo!");
    exit();
?>
