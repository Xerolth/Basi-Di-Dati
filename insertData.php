<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "youblog";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // prepare sql and bind parameters
    $stmt = $conn->prepare("INSERT INTO utenti (NomeUtente, Email, Password, Telefono)
    VALUES (':NomeUtente', ':Email', ':Password', ':Telefono')");
    
    // insert a row
    $nomeutente = $_POST["nome"];
    $email = $_POST["email"];
    $passw = $_POST["password"];
    $telefono = $_POST["telefono"];
    
    $stmt->bindParam(':NomeUtente', $nomeutente);
    $stmt->bindParam(':Email', $email);
    $stmt->bindParam(':Password', $passw);
    $stmt->bindParam(':Telefono', $telefono);

    $stmt->execute();
    
    echo "success";
}catch(PDOException $e) {
  echo "Error: " . $e->getMessage();
}
$conn = null;
?>