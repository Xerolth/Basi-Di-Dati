<?php 
    //richiamo il file contenente tutte le query
    require 'config_files/query.php';

    //richiamo lo script responsabile della connessione a MySQL
    $link = require 'config_files/connect.php';

    //controllo che nella query dell'url sia specificato l'id del blog da cancellare
    if(!array_key_exists('id', $_GET)) {
        //se l'id non esiste nella query, allora rimando l'utente alla sua lista dei blog
        header("Location: listaBlog.php");
    } else {
        //altrimenti prendo in input l'id del blog da cancellare
        $id_blog = $_GET['id'];
    }

    //prendo in input le informazione del blog, se non esiste restituisce false
    $esiste_blog = datiBlog($link, $id_blog);
    //se il blog non esiste, ed è stato inserito un id non esistente nella query dell'url, rimando alla lista blog 
    if(!$esiste_blog) {
        header("Location: listaBlog.php");
    }


    //il path per la memorizzazione dei post è impostato nel seguente modo:
    //imgs/posts/<id del blog>. In questo percorso ci saranno N cartelle per N post. 
    //Ogni cartella del post avrà al massimo due file.

    //tramite la funzione scandir ottengo la lista di tutte le cartelle dei post contenenti le immagini
    $posts = scandir("imgs/post/$id_blog");
    //per ogni cartella, quindi per ogni post, richiamo la funzione cancellaCartela
    foreach ($posts as $dir) {
        if ($dir === '.' || $dir === '..') {
            continue;
        }

        //tramite la funzione glob prendo tutti i file contenuti nella cartella dir, ovvero le immagini del post
        $files = glob("imgs/post/$id_blog/$dir./*");
        //controllo che ogni file nella cartella sia un file e lo cancello con unlink
        foreach ($files as $img) {
            if(is_file($img)) {
                unlink($img);
            }
        }
        //cancello la cartella del post
        rmdir("imgs/post/$id_blog/$dir");
    }
    //cancello la cartella del blog
    rmdir("imgs/post/$id_blog");
    
    //definiamo la query per cancellare il blog, attraverso l'id che abbiamo preso in input dall'url
    try {
        $stmt = $link->prepare('DELETE FROM blog WHERE id_blog = ?');
        $stmt->execute([$id_blog]);
    } catch (PDOException $e) {
        echo "Connessione Fallita: ".$e->getMessage();
    }

    header("Location: listaBlog.php");
?>