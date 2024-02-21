<?php
    //avvio la sessione se non è già avviata
    if(!isset($_SESSION)) {
        session_start(); 
    }

    if (!isset($_SESSION['user'])){
        header("Location: index.php?p=1");  
    }

    //richiamo il file contenente tutte le query
    require 'config_files/query.php';

    //richiamo lo script responsabile della connessione a MySQL
    $link = require 'config_files/connect.php';

    $_SESSION['error'] = array();

    //controllo che sia stato premuto un pulsante del form con metodo post
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $password=$_POST['password'];
        $username = $_SESSION['user'];

        if($password == "") {
            array_push($_SESSION['error'], "Inserire la password");
        } else {
            $dati_utente=findUser($link, $username);
            if(!password_verify($password, $dati_utente['passhash'])) {
                array_push($_SESSION['error'], "Password non valida");
            }
            
        }

        if(count($_SESSION['error']) == 0) {
            //scannerizzo la cartella avatar relativa all'utente, contenente l'avatar dell'utente
            //l'array che restituisce la funzione scandir restituisce un array simile: [".", "..", <avatar_utente>]
            $avatar = scandir("imgs/avatar/$username");
            //nella posizione 2 dell'array avatar è contenuta la stringa con il nome del file 
            //tramite questa stringa eliminiamo il file
            unlink("imgs/avatar/$username/$avatar[2]");
            //cancello la cartella che conteneva l'avatar dell'utente
            rmdir("imgs/avatar/$username");

            //estraggo la lista dei blog dell'utente
            $listaBlog = estraiListablog($link, $_SESSION['id_user']);
            
            //questa porzione di codice cancella tutti i file relativi ai post dei blog dell'utente
            foreach ($listaBlog as $blog) {
                //salvo l'id del blog
                $id_blog = $blog['id_blog'];
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
            }
            
            //estraggo la lista dei blog amministrati dall''utente
            $listaAmministrati = estraiBlogcoautore($link, $_SESSION['id_user']);
            
            if (count($listaAmministrati) > 0) {
                //questa porzione di codice cancella tutti i file relativi ai post creati dal coautore
                foreach ($listaAmministrati as $blog) {
                    //salvo l'id del blog
                    $id_blog = $blog['id_blog'];
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
                        
                        //verifico il nome della cartella contenente le immagini del post, 
                        //se il nome della cartella inizia con l'id dell'utente
                        //allora procederò con la cancellazione
                        if(str_starts_with($dir, $_SESSION['id_user'])) {
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
                    }
                }
            }
        
            //definiamo la query per cancellare l'utente
            try {
                $stmt = $link->prepare('DELETE FROM utente WHERE nome_utente = ?');
                $stmt->execute([$username]);
            } catch (PDOException $e) {
                echo "Connessione Fallita: ".$e->getMessage();
            }
            
            //distrugge la session dell'utente
            session_unset();
            session_destroy();

            //indirizzo l'utente alla homepage
            header("Location: index.php?p=1");
        }
    }
?>

<?php require 'config_files/header.php';?>

<?php require 'grafica/navbar.php';?>

<div class="d-flex flex-column align-items-center">
    <h1>Conferma i tuoi dati</h1>
    <!--Mostra eventuali errori-->
    <?php if (isset($_SESSION['error']) && count($_SESSION['error'])>0 ) { ?>
        <ul class="w-25 alert alert-danger"> 
            <?php foreach ($_SESSION['error'] as $err) : echo "<li>" . $err . "</li>";?>
            <?php endforeach?>
        </ul>
    <?php } ?>
    <form class="w-25" name="cancUtente" action="cancellaUtente.php" method="post">
        <div class="mb-3">
            <label for="exampleInputPassword1" class="form-label">Password</label>
            <input type="password" name="password" class="form-control">
        </div>
        
        <button type="submit" class="btn btn-primary">Cancella</button>
        <a class="btn btn-danger" href="profilo.php">Annulla</a>
    </form>
</div>

<?php
    //cancello i messaggi di errore
    if (isset($_SESSION['error'])) {
        unset($_SESSION['error']);
    }
?>