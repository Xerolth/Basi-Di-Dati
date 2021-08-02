<?php

function connectDB(){
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "youblog";
    $conn = null;

    try {
        //mi collego al database
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        //gestisce gli errori ed eventualmente li trasforma in eccezioni
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


    }catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }

    return $conn;
}
?>