<?php
    $hostname = "localhost";
    $dbname = "postscriptum";
    $user = "root";
    $pass = "";

    try {
        //crea la connessione al db
        $link = new PDO ("mysql:host=$hostname;dbname=$dbname", $user, $pass);
        //gestione errori
        $link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $link;
    } catch (PDOException $e) {
        echo "Connessione Fallita: " . $e->getMessage();
    }
?>