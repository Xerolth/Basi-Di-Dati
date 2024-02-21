<?php

   //richiamo il file contenente tutte le query
   require 'config_files/query.php';

   //richiamo lo script responsabile della connessione a MySQL
   $link = require 'config_files/connect.php';

    //controllo che la sessione sia attiva
    if(!isset($_SESSION)) {
        session_start(); 
    }

    //controllo che nella query dell'url sia specificato l'id del blog dove creare il post
    if(!array_key_exists('id', $_GET)) {
        //se l'id non esiste nella query, allora rimando l'utente alla sua lista dei blog
        header("Location: listaBlog.php");
    } else {
        //altrimenti prendo in input l'id del blog da cancellare
        $id_blog = $_GET['id'];
    }
    
    //prendo in input le informazione del blog, se non esiste restituisce false
    $dati_blog = datiBlog($link, $id_blog);
    
    //se il blog non esiste, ed è stato inserito un id non esistente nella query dell'url, rimando alla lista blog 
    if(!$dati_blog) {
        header("Location: listaBlog.php");
    }
    

    if ($dati_blog['tema'] != "Personalizzato") {
        $info_tema = estraiTema($link, $dati_blog['tema']);
    }

    $lista_post = estraiPost($link, $id_blog);

    //estraggo i dati dei post della pagina e li salvo in un array di appoggio
    //se la lunghezza del testo del post è superiore a 300 caratteri, allora riduco l'anteprima del testo a 300
    $posts = [];
    foreach($lista_post as $i => $p) {
        $posts[$i]['id'] = $p['id_post'];
        $posts[$i]['copertina'] = $p['copertina'];
        $posts[$i]['titolo'] = $p['titolo'];
        if (strlen($p['testo_post']) > 300) {
            $posts[$i]['descrizione'] = substr($p['testo_post'], 0, 300); 
            $posts[$i]['descrizione'] = $posts[$i]['descrizione'] . "...";
        } else {
            $posts[$i]['descrizione'] = $p['testo_post'];
        }
    }

    //estraggo tutti i coautori del blog
    $coautori = estraiCoautori($link, $id_blog);
?>

<?php require 'config_files/header.php';?>

<?php require 'grafica/navbar.php';?>

<?php require 'grafica/blog_layout.php';?>


