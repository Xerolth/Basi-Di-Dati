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

    //controllo se l'utente è già loggato. Se non è già loggato lo rimando alla homepage
    if (!isset($_SESSION['user'])){
        header("Location: index.php?p=1");  
    }

    //controllo che l'utente sia verificato
    $dati_utente = datiUtente($link, $_SESSION['user']);
    if (!$dati_utente['verificato']) {
        header("Location: index.php?p=1");  
    }

    $coautori = estraiCoautori($link, $id_blog);

    //controllo che l'id del creatore del blog corrisponda a quello dell'utente salvato in sessione
    //e controllo che l'utente loggato faccia parte della lista dei coautori del blog
    if ($dati_blog['autore_blog'] != $_SESSION['id_user'] 
        && !(in_array($_SESSION['user'], array_column($coautori, 'nome_utente')))) {
        header("Location: listaBlog.php");
    } 

    //creo l'array degli errori
    $_SESSION['error'] = array();

    $titolo = '';
    $testo_post = '';

    //controllo che sia stato premuto un pulsante del form con metodo post
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $titolo = $_POST['titolo'];
        $testo_post = $_POST['testo_post'];

        //creo una variabile per controllare se si verificano degli errori
        $errore = false;
        //controllo la lunghezza del nome del blog
        if(strlen($titolo) < 1 || strlen($titolo) > 50) {
            array_push($_SESSION['error'], "Il titolo del post deve avere tra 1 e i 50 caratteri");
            $errore = true;
        } 
        
        if (!preg_match("/^[À-ÿA-Za-z0-9!?&\",.\s]+$/", $titolo)) {
            array_push($_SESSION['error'], "Il titolo del post contiene dei caratteri non ammessi");
            $errore = true;
        }

        //se si sono verificati degli errori ripulisco il campo
        if($errore) {
            $titolo="";
        }

        // controllo il contenuto del post. Questa espressione regolare
        if (!preg_match("/^[À-ÿA-Za-z0-9!?&$#%:()\',.\-\"\s]+$/", $testo_post)) {
            array_push($_SESSION['error'], "Il contenuto del post non è valido.");
        }
        
        // controllo che l'estensione del file sia corretta
        $check_copertina = false;
        //controllo che il file sia stato uploadato, verificando che non abbia un nome vuoto
        if(array_key_exists('copertina', $_FILES) && $_FILES['copertina']['name']!="") {
           
            $estensioni_accettate = array('jpg', 'jpeg', 'png');
            foreach ($estensioni_accettate as $e) {
                if (str_ends_with($_FILES['copertina']['name'], $e)) {
                    $check_copertina = true;
                }
            }

            //se l'estensione della copertina è valida, allora proseguo con gli altri controlli
            if (!$check_copertina) {
                array_push($_SESSION['error'], "Il formato della copertina non è valido. Sono permessi solo file immagini in .jpg, .jpeg e .png");
            } else { 
                //la variabile identifica il percorso della cartella del blog che conterrà le cartelle dei vari post
                $percorso_blog = 'imgs/post/'.$dati_blog['id_blog'];
                //controllo che nella cartella post esista la cartella che identifica il blog. Se non esiste la creo
                if (!is_dir($percorso_blog)) {
                    mkdir($percorso_blog);
                }

                 //la variabile identifica il percorso della cartella del singolo post che conterrà le immagini del post
                $percorso_post = $_SESSION['id_user'].$_SESSION['user'].time();
                if (!is_dir($percorso_blog.'/'.$percorso_post)) {
                    mkdir($percorso_blog.'/'.$percorso_post);
                }

                //ripulisco il nome del file da caratteri problematici
                $nome_file = $_FILES['copertina']['name'];
                $chars = array(" ", "'", "-", "%", "$");
                foreach ($chars as $c) {
                    $nome_file = str_replace($c, '', $nome_file);
                }
                //per evitare file con nomi duplicati creo un percorso che specifica la destinazione del file.
                //Il percorso è una stringa formata dal percorso della cartella del post, dalla tipologia dell'immagine e dal nome del file
                $percorso_copertina = $percorso_blog.'/'.$percorso_post.'/'.'copertina_'.$nome_file;
                
                //controllo che il file non superi una certa dimensione
                if($_FILES['copertina']['size'] > 3145728){
                    array_push($_SESSION['error'], "La copertina caricata è troppo grande.");
                }
            }     
        } else {
            //se il nome della copertina non è stato uploadato, restituisco una un errore
            array_push($_SESSION['error'], "La copertina è obbligatoria.");
        }

        //creo una variabile flag per verificare se la seconda immagine è stata caricata
        $immagine = false;
        $percorso_immagine = NULL;
        if(array_key_exists('immagine', $_FILES) && $_FILES['immagine']['name']!="" && $check_copertina) {
            //controllo che l'estensione dell'immagine sia valida
            $check_immagine = false;
            $estensioni_accettate = array('jpg', 'jpeg', 'png');
            foreach ($estensioni_accettate as $e) {
                if (str_ends_with($_FILES['immagine']['name'], $e)) {
                    $check_immagine = true;
                }
            }

            //se l'estensione dell'immagine è valida, allora proseguo con gli altri controlli
            if (!$check_immagine) {
                array_push($_SESSION['error'], "Il formato dell'immagine non è valido. Sono permessi solo file immagini in .jpg, .jpeg e .png");
            } else {
                //ripulisco il nome del file da caratteri problematici
                $nome_file = $_FILES['immagine']['name'];
                $chars = array(" ", "'", "-", "%", "$");
                foreach ($chars as $c) {
                    $nome_file = str_replace($c, '', $nome_file);
                }

                $percorso_immagine = $percorso_blog.'/'.$percorso_post.'/'.'immagine'.$nome_file;
                $immagine = true; //se viene caricata la variabile flag diventa true

                if($_FILES['immagine']['size'] > 3145728){
                    array_push($_SESSION['error'], "L'immagine caricata è troppo grande.");
                }
            }
        }

        if (count($_SESSION['error'])==0) {
            //se non ci sono errori carico le immagini nel percorso univoco creato
            move_uploaded_file($_FILES['copertina']['tmp_name'], $percorso_copertina);
            if($immagine) {
                move_uploaded_file($_FILES['immagine']['tmp_name'], $percorso_immagine);
            }

            try {
                $stmt=$link->prepare("INSERT into post(titolo, testo_post, creazione_post, copertina, immagine, blog, autore_post) 
                    VALUES (:titolo, :testo_post, :creazione_post, :copertina, :immagine, :blog, :autore_post)");
                
                $stmt->bindvalue(":titolo", $titolo);
                $stmt->bindvalue(":testo_post", $testo_post);
                $stmt->bindvalue(":creazione_post", date("Y-m-d H:i:s"));
                $stmt->bindvalue(":copertina", $percorso_copertina);
                $stmt->bindvalue(":immagine", $percorso_immagine);
                $stmt->bindvalue(":blog", $id_blog);
                $stmt->bindvalue(":autore_post", $_SESSION['id_user']);
                $stmt->execute();

                //salvo id e username in sessione
                $id_post = $link->lastInsertId();

            } catch (PDOException $e) {
                echo "Connessione Fallita: " . $e->getMessage();
                exit();
            }

            //indirizzo l'utente alla homepage
            header("Location: post.php?&id=$id_post");
        }
    }
?>


<?php require 'config_files/header.php';?>

<?php require 'grafica/navbar.php';?>

<div class="d-flex flex-column align-items-center mt-4 mb-5">
    <h1>Nuovo post</h1>
    <!--Mostra eventuali errori nella registrazione-->
    <?php if (isset($_SESSION['error']) && count($_SESSION['error'])>0 ) { ?>
        <ul class="w-50 mt-3 alert alert-danger"> 
            <?php foreach ($_SESSION['error'] as $err) : echo "<li>" . $err . "</li>";?>
            <?php endforeach?>
        </ul>
    <?php } ?>

    <form class="w-50" method="post" enctype="multipart/form-data">
        <div class="form-group needs-validation mb-3">
            <label for="titolo" class="form-label">Titolo</label>
            <input name="titolo" type="text" class="form-control" value="<?php echo $titolo ?>" required>
        </div>

        <div class="form-group needs-validation mb-4">
            <label for="titolo" class="form-label">Copertina</label>
            <input class="w-50 form-control" type="file" accept="image/png, image/gif, image/jpeg, image/jpg" name="copertina"> 
        </div>

        <div class="form-group needs-validation mb-3">
            <textarea name='testo_post' rows="30" cols="25" class='form-control'><?php echo $testo_post ?></textarea>
        </div>

        <div class="form-group needs-validation mb-4">
            <label for="titolo" class="form-label">Immagine</label>
            <input class="w-50 form-control" type="file" accept="image/png, image/gif, image/jpeg, image/jpg" name="immagine"> 
        </div>


        <button type="submit" class="btn btn-primary me-3">Pubblica</button>
        <a href="blog.php?id=<?php echo $_GET['id']?>">
            <button type="button" class="btn btn-danger">Annulla</button>
        </a>
    </form>
</div>

<?php
    //cancello i messaggi di errore
    if (isset($_SESSION['error'])) {
    unset($_SESSION['error']);
    }
?>

