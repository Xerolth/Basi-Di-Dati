<?php
require ("./functions.php");

//richiamo la funzione da functions
$conn = connectDB();

// preparo sql e collego i parametri
$stmt = $conn->prepare("INSERT INTO utenti (NomeUtente, Email, Password, Telefono)
VALUES (:NomeUtente, :Email, :Password, :Telefono)");
      
// passa i dati dal form
$nomeutente = $_POST["nome"];
$email = $_POST["email"];
$passw = $_POST["password"];
$telefono = $_POST["telefono"];
      
//invio delle credenziali
$stmt->bindParam(':NomeUtente', $nomeutente);
$stmt->bindParam(':Email', $email);
$stmt->bindParam(':Password', $passw);
$stmt->bindParam(':Telefono', $telefono);

$stmt->execute();
      
echo "success";
?>