<?php
/**
 * @description Crea una connessione al DB di sistema usato per il progetto di Basi di dati
 * @return PDO|null Se si connette al DB ritorna un oggetto PDO, altrimenti null
 */
function connectDB(){
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "youblog";
    $conn=null;

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        $conn=null;
        print $e->getMessage();
    }
    var_dump($conn);
    return $conn;
}
connectDB();

