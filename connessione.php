<!-- Pagina che effettua la connessione al DB -->

<?php
    $serverName = "localhost";
    $userName = "root";
    $password = "root";
    $nomeDB = "ilciarnurogcf";

    $conn = mysqli_connect($serverName, $userName, $password, $nomeDB);

    if (!$conn) {
        die("Connessione non riuscita: " . mysqli_connect_error());
    }
?>