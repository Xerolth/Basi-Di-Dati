<?php

    //richiamo il file contenente tutte le query
    require 'config_files/query.php';

    //richiamo lo script responsabile della connessione a MySQL
    $link = require 'config_files/connect.php';

    //controllo che la sessione sia attiva
    if(!isset($_SESSION)) {
        session_start(); 
    }

    //controllo che nella query dell'url sia specificato l'id del post dove creare il post
    if(!array_key_exists('id', $_GET)) {
        //se l'id non esiste nella query, allora rimando l'utente alla sua lista dei blog
        header("Location: listaBlog.php");
    } else {
        $id_post = $_GET['id'];
    }

    //prendo in input le informazione del blog, se non esiste restituisce false
    $dati_post = datiPost($link, $id_post);
    
    //se il blog non esiste, ed è stato inserito un id non esistente nella query dell'url, rimando alla lista blog 
    if(!$dati_post) {
        header("Location: index.php?p=1");
    }

    $dati_post = datiPost($link, $id_post);
    $dati_blog = datiBlog($link, $dati_post['blog']);
    $dati_utente = datiUtenteId($link, $dati_post['autore_post']);
    $coautori = estraiCoautori($link, $dati_blog['id_blog']);

    $paragrafi = preg_split('/\n|\r\n?/', $dati_post['testo_post']);

    if ($dati_blog['tema'] != "Personalizzato") {
        $info_tema = estraiTema($link, $dati_blog['tema']);
    }

    $commenti = estraiCommenti($link, $id_post);
    $num_like = numeroLike($link, $id_post);

    if(isset($_SESSION['user'])) {
        $like = verificaLike($link, $id_post, $_SESSION['id_user']);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        //controllo che sia stata fatta una richiesta AJAX POST verificando che esista un parametro "cat" nell'url
        if(array_key_exists('userinfo', $_GET)) {
            //attraverso la funzione json_encode restituisco l'array delle sottocategorie in JSON sulla pagina
            echo json_encode($_SESSION);
            // per evitare di ottenere l'intera pagina HTML come risposta dalla richiesta AJAX, utilizziamo la funzione exit()
            exit();
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        //controllo che sia stata fatta una richiesta AJAX POST verificando che esista un parametro "commento" nell'array post
        if(array_key_exists('commento', $_POST)) {
            var_dump($_POST);
            //attraverso la funzione json_encode restituisco l'array delle sottocategorie in JSON sulla pagina
            $id_user = $_POST['id_user'];
            $commento = $_POST['commento'];
            if(isset($_GET)) {
                $id_post = $_GET['id'];
            } 
            
            try {
                $stmt=$link->prepare("INSERT into commento(testo_commento, creazione_commento, post, autore_commento) 
                VALUES (:testo_commento, :creazione_commento, :post, :autore_commento)");

                $stmt->bindvalue(":testo_commento", $commento);
                $stmt->bindvalue(":creazione_commento", date("Y-m-d H:i:s"));
                $stmt->bindvalue(":post", $id_post);
                $stmt->bindvalue(":autore_commento", $id_user);
                $stmt->execute();

                //salvo id e username in sessione
                $id_commento = $link->lastInsertId();

            } catch (PDOException $e) {
                echo "Connessione Fallita: " . $e->getMessage();
                exit();
            }
        }

        //controllo che sia stata fatta una richiesta AJAX POST verificando che esista un parametro "cancellaCommento" nell'array post
        if(array_key_exists('cancellaCommento', $_POST)) {
            //attraverso la funzione json_encode restituisco l'array delle sottocategorie in JSON sulla pagina
            $id_commento = $_POST['cancellaCommento'];
            
            try {
                $stmt = $link->prepare('DELETE FROM commento WHERE id_commento = ?');
                $stmt->execute([$id_commento]);
            } catch (PDOException $e) {
                echo "Connessione Fallita: ".$e->getMessage();
            }
        }

        //controllo che sia stata fatta una richiesta AJAX POST verificando che esista un parametro "like" nell'array post
        if(array_key_exists('like', $_POST)) {
            if(!$like) {
                try {
                    $stmt=$link->prepare("INSERT into `like` (id_utente, id_post) VALUES (:id_utente, :id_post)");
                    $stmt->bindvalue(":id_utente", $_SESSION['id_user']);
                    $stmt->bindvalue(":id_post", $id_post);
                    $stmt->execute();
                } catch (PDOException $e) {
                    echo "Connessione Fallita: " . $e->getMessage();
                    exit();
                }

                try {
                    $stmt = $link->prepare("UPDATE post SET numlike = :numlike WHERE id_post = :id_post");
                    $stmt->bindvalue(":numlike", $num_like+1);
                    $stmt->bindvalue(":id_post", $id_post);
                    $stmt->execute();
                   
                } catch (PDOException $e) {
                    echo "Connessione Fallita: " . $e->getMessage();
                    exit();
                }
            } else {
               header("Location: post.php?id=$id_post"); 
            }
        }

        if(array_key_exists('rimuoviLike', $_POST)) {
            if($like) {
                try {
                    $stmt=$link->prepare("DELETE FROM `like` WHERE id_utente = :id_utente AND id_post = :id_post");
                    $stmt->bindvalue(":id_utente", $_SESSION['id_user']);
                    $stmt->bindvalue(":id_post", $id_post);
                    $stmt->execute();
                } catch (PDOException $e) {
                    echo "Connessione Fallita: " . $e->getMessage();
                    exit();
                }

                if ($num_like != 0) {
                    try {
                        $stmt = $link->prepare("UPDATE post SET numlike = :numlike WHERE id_post = :id_post");
                        $stmt->bindvalue(":numlike", $num_like-1);
                        $stmt->bindvalue(":id_post", $id_post);
                        $stmt->execute();
                    } catch (PDOException $e) {
                        echo "Connessione Fallita: " . $e->getMessage();
                        exit();
                    }
                }
                
            } else {
               header("Location: post.php?id=$id_post"); 
            }
        }
    }

?>

<?php require 'config_files/header.php';?>

<?php require 'grafica/navbar.php';?>

<?php require 'grafica/post_layout.php';?>

<script src="js/gestisciLikeCommenti.js"></script>


