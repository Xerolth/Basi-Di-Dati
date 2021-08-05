<?php
require("./functions.php");

//richiamo la funzione da functions
$conn = connectDB();

//controllare se è già presente il nome utente
$contrNome = $conn->prepare("SELECT * FROM `utenti` WHERE NomeUtente = :Nome");

$contrNome->bindParam(':Nome', $_POST["nome"]);

//eseguo la query
$contrNome->execute();

//controllo che il nome non sia stato già utilizzato
if($contrNome->rowCount()>0) {
    echo "il nome utente è già stato utilizzato";
    die();
}

//controllare se è già presente la mail
$contrEmail = $conn->prepare("SELECT * FROM `utenti` WHERE Email = :Email");

$contrEmail->bindParam(':Email', $_POST["email"]);

//eseguo la query
$contrEmail->execute();

//controllo che la mail non sia stata già utilizzata
if($contrEmail->rowCount()>0) {
    echo "l'email è già stata utilizzata";
    die();
}

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