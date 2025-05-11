<!-- Pagina che elaboara le credenziali inseririte dall'utente nella pagina di login. Non viene visualizzata.
 	Se corrette, si viene reindirizzati alla home dell'applicazione, caricando i dati dell'utente
	Altrimenti, si rimane alla pagina di login con un messaggio di erroe
	Se ci si è loggati con le credenziali amministratore, si viene reindirizzati alla pagina per la gestione dell'applicazione -->

<?php
	include("connessione.php");

	$username = $_REQUEST['username'];
	$psw = $_REQUEST['password'];

	if ($username == "administrator" && $psw == "admingcf")  {
		session_start();
		$_SESSION["username"] = $username;
		header("Location:administrator.php");
		exit();
	}

	$sql="SELECT * FROM login WHERE username = '$username' AND psswrd = '$psw';";
	$result=mysqli_query($conn,$sql);
	if(mysqli_num_rows($result)>0){
        $sql="SELECT * FROM infoutenti WHERE username = '$username';";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_assoc($result);

		session_start();
		$_SESSION["username"] = $row["username"];

		header("Location:home.php");
	} else {
		header("Location:login.php?err=Username o password errata");
	}
?>