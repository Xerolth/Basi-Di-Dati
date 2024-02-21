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

    //controllo che l'id del creatore del blog corrisponda a quello dell'utente salvato in sessione
    if ($dati_blog['autore_blog'] != $_SESSION['id_user']) {
        header("Location: listaBlog.php");
    }

    $listaTemi = estraiTemi($link);

    //creo l'array degli errori
    $_SESSION['error'] = array();

    $nome_blog = '';
    $descrizione_blog = '';
    $tema = '';

    //controllo che sia stato premuto un pulsante del form con metodo post
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nome_blog = $_POST['nome_blog'];
        $descrizione_blog = $_POST['descrizione_blog'];
        $tema = $_POST['tema'];

       //controllo che non ci siano campi vuoti
       if($nome_blog=="" || $descrizione_blog=="" || $tema=="") {
            //se uno dei campi è vuoto visualizzo solo questo errore
            array_push($_SESSION['error'], "Compilare tutti i campi");
        } else {
            //creo una variabile per controllare se si verificano degli errori
            $errore = false;
            //controllo la lunghezza del nome del blog
            if(strlen($nome_blog) < 5 || strlen($nome_blog) > 20) {
                array_push($_SESSION['error'], "Il Nome del blog deve avere tra i 5 e i 20 caratteri");
                $errore = true;
            } 
            
            if (!preg_match("/^[À-ÿA-Za-z0-9!?&\",.\s]+$/", $nome_blog)) {
                array_push($_SESSION['error'], "Il Nome del blog contiene dei caratteri non ammessi");
                $errore = true;
            }

            //se si sono verificati degli errori ripulisco il campo
            if($errore) {
                $nome_blog="";
            }

            $errore = false;
            //controllo la lunghezza del nome del blog
        
            if(strlen($descrizione_blog) > 240) {
                array_push($_SESSION['error'], "La descrizione del blog deve avere massimo 240 caratteri");
                $errore = true;
            } 
            
            if (!preg_match("/^[À-ÿA-Za-z0-9!?&$#%()\',.\-\"\s\n]+$/", $descrizione_blog)) {
                array_push($_SESSION['error'], "La descrizione del blog contiene dei caratteri non ammessi");
                $errore = true;
            } 
            
            if($errore) {
                $descrizione_blog="";
            }
        
            $check = false;
            foreach($listaTemi as $t) {
                if ($tema == $t['nome_tema']) {
                    $check = true;
                }
            }
    
            // se il tema non esiste aggiungo all'array $_SESSION['error'] il messaggio di errore
            if (!$check) {
                array_push($_SESSION['error'], "Il tema inserito non esiste. Riprova");
            }
        }

        if (count($_SESSION['error'])==0) {
            try {
                $stmt=$link->prepare("UPDATE blog SET nome_blog = :nome_blog, descrizione_blog = :descrizione_blog, tema = :tema
                    WHERE id_blog = :id_blog");

                $stmt->bindvalue(":id_blog", $id_blog);
                $stmt->bindvalue(":nome_blog", $nome_blog);
                $stmt->bindvalue(":descrizione_blog", $descrizione_blog);
                $stmt->bindvalue(":tema", $tema);
        
                $stmt->execute();

            } catch (PDOException $e) {
                echo "Connessione Fallita: " . $e->getMessage();
                exit();
            }
      
            header("Location: personalizzaBlog.php?id=$id_blog");
        }
    }

?>

<?php require 'config_files/header.php';?>

<?php require 'grafica/navbar.php';?>

<div class="d-flex flex-column align-items-center">
    <h1 class="mt-4 mb-4">Personalizza <?php echo $dati_blog['nome_blog']?></h1>
    <!--Mostra eventuali errori nella registrazione-->
    <?php if (isset($_SESSION['error']) && count($_SESSION['error'])>0 ) { ?>
        <ul class="w-50 alert alert-danger"> 
            <?php foreach ($_SESSION['error'] as $err) : echo "<li>" . $err . "</li>";?>
            <?php endforeach?>
        </ul>
    <?php } ?>
    <form class="w-50 mb-5" method="post">
        <div class="mb-3">
            <label for="nome_blog" class="form-label">Nome del blog</label>
            <input name="nome_blog" type="text" class="form-control" 
            value="<?php echo $dati_blog['nome_blog']?>" required>
        </div>

        <div class="form-group needs-validation mb-3">
            <label for="descrizione_blog" class="form-label">Descrizione</label>
            <textarea name='descrizione_blog' rows="2" cols="25" maxlength="200" class='form-control'><?php echo $dati_blog['descrizione_blog']?></textarea>
        </div>

        <div class="scelta-template form-group needs-validation mt-5 mb-4">
            <label for="titolo" class="form-label">Temi</label>
            <div class="radio-element">    
                <?php foreach ($listaTemi as $i => $tema) : ?>
                    <?php if ($tema['nome_tema'] == $dati_blog['tema']) { ?>
                        <input class="me-1 mb-3" type="radio" id="<?php echo $tema['nome_tema']?>" name="tema" value="<?php echo $tema['nome_tema']?>" checked>
                        <label for="<?php echo $tema['nome_tema']?>"><?php echo $tema['nome_tema']?></label></br>
                    <?php } else { ?>
                        <input class="me-1 mb-3" type="radio" id="<?php echo $tema['nome_tema']?>" name="tema" value="<?php echo $tema['nome_tema']?>">
                        <label for="<?php echo $tema['nome_tema']?>"><?php echo $tema['nome_tema']?></label> </br>
                    <?php } ?>
                <?php endforeach ?>
            </div>                 
        </div>

        <button type="submit" class="btn btn-primary">Personalizza</button>
    </form>
</div>

<?php
    //cancello i messaggi di errore
    if (isset($_SESSION['error'])) {
    unset($_SESSION['error']);
    }
?>