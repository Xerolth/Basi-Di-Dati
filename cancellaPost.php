<?php 
    //richiamo il file contenente tutte le query
    require 'config_files/query.php';

    //richiamo lo script responsabile della connessione a MySQL
    $link = require 'config_files/connect.php';

    //controllo che nella query dell'url sia specificato l'id del post da cancellare
    if(!array_key_exists('id', $_GET)) {
        //se l'id non esiste nella query, allora rimando l'utente alla homepage
        header("Location: index.php?p=1");
    } else {
        //altrimenti prendo in input l'id del blog da cancellare
        $id_post = $_GET['id'];
    }

    //prendo in input le informazione del blog, se non esiste restituisce false
    $esiste_post = datiPost($link, $id_post);
    //se il blog non esiste, ed è stato inserito un id non esistente nella query dell'url, rimando alla lista blog 
    if(!$esiste_post) {
        header("Location: index.php?p=1");
    } else {
        $id_blog = $esiste_post['blog'];
    }

    //definiamo la query per cancellare il blog, attraverso l'id che abbiamo preso in input dall'url
    try {
        $stmt = $link->prepare('DELETE FROM post WHERE id_post = ?');
        $stmt->execute([$id_post]);
    } catch (PDOException $e) {
        echo "Connessione Fallita: ".$e->getMessage();
    }

    if($esiste_post['copertina']) {
        unlink($esiste_post['copertina']);
    }

    if($esiste_post['immagine']) {
        unlink($esiste_post['immagine']);
    }

    //estraggo la posizione dell'ultimo carattere "/" presente nel percorso della copertina 
    //ad esempio il percorso può essere "imgs/post/41/utente1679678338/copertina_titolo" 
    $foo = strrpos($esiste_post['copertina'], '/');
    if ($foo) {
        //tramite la funzione substr cancello dal percorso della copertina tutto il contenuto dopo l'ultimo slash
        //cancellando di fatto il nome specifico del file, ottenendo "imgs/post/41/utente1679678338" 
        $cartella = substr($esiste_post['copertina'], 0, $foo);
        echo $cartella;
        rmdir($cartella);
    }
    
    header("Location: blog.php?id=$id_blog");
 
?>