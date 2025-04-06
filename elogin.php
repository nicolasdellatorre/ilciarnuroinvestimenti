<?php
	include("connessione.php");

	$username = $_REQUEST['username'];
	$psw = $_REQUEST['password'];

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