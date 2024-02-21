<?php

    //richiamo il file contenente tutte le query
    require 'config_files/query.php';

    //richiamo lo script responsabile della connessione a MySQL
    $link = require 'config_files/connect.php';

    //controllo che la sessione sia attiva
    if(!isset($_SESSION)) {
        session_start(); 
    }

    //controllo se l'utente è già loggato. Se non è già loggato lo rimando alla homepage
    if (!isset($_SESSION['user'])){
        header("Location: login.php");  
    }

    // controllo che l'utente sia verificato
    $dati = datiUtente($link, $_SESSION['user']);
    if (!$dati['verificato']) {
        header("Location: index.php?p=1");  
    }

    //se un utente possiede 6 blog non può crearne altri. Pertanto viene limitato venendo rispedito alla lista dei blog
    if($_SESSION['numero_blog'] == 6) {
        header("Location: listaBlog.php");
    }

    $listaCategorie = estraiCategorie($link);
    $listaTemi = estraiTemi($link);

    //creo l'array degli errori
    $_SESSION['error'] = array();

    $nome_blog = '';
    $descrizione_blog = '';
    $categoria = '';	
    $sottocat = '';
    $tema = '';

    //controllo che sia stato premuto un pulsante del form con metodo post
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nome_blog = trim($_POST['nome_blog']);
        $descrizione_blog = $_POST['descrizione_blog'];
        $categoria = $_POST['categoria'];
        $sottocat = $_POST['sottocategoria'];    

        //se non viene selezionato nessun tema nel form, il blog verrà impostato con il tema di default
        if (!array_key_exists("tema", $_POST)) {
            $tema = "Default";
        } else {
            $tema = $_POST['tema'];
        }
  
        //controllo che non ci siano campi vuoti
        if($nome_blog=="" || $descrizione_blog=="" || $categoria=="" || $sottocat=="") {
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

            // Controllo che la descrizione del blog non contenga caratteri particolari
            if (!preg_match("/^[À-ÿA-Za-z0-9!?&$#%:()\',.\-\"\s]+$/", $descrizione_blog)) {
                array_push($_SESSION['error'], "La descrizione del blog contiene dei caratteri non ammessi");
                $errore = true;
            }
            
            //se si sono verificati degli errori ripulisco il campo
            if($errore) {
                $descrizione_blog="";
            }

            // estraggo le categorie principali per verificare che l'input dell'utente
            // esista nel database e sia presente nella lista delle categorie estratte
            $check = false; // impostiamo un flag di errore, settato a False
            $id = 0;
            foreach($listaCategorie as $c) {
                if ($categoria == $c['nome_categoria']) {
                    $check = true;
                    $id_nuova_categoria = $c['id_categoria'];
                }
            }

            // se la categoria non esiste mando il messaggio di errore
            if (!$check) {
                array_push($_SESSION['error'], "La categoria inserita non esiste. Riprova");
            } else {
                $check = false;
                // estraggo le sottocategorie della categoria principale per verificare che le sottocategorie
                // ottenute tramite Jquery combacino con quelle estratte dal database
                $listaSottocat = estraiSottocategorie($link, $id_nuova_categoria);
                foreach($listaSottocat as $c) {
                    if ($sottocat == $c['nome_categoria']) {
                        $check = true;
                        $id_nuova_sottocat = $c['id_categoria'];
                    }
                }
        
                // se la categoria non esiste mando il messaggio di errore
                if (!$check) {
                    array_push($_SESSION['error'], "La sottocategoria inserita non è valida. Riprova");
                } 
            }

            // controllo che il tema esista nella lista ottenuta dal database
            $errore = false;
            foreach($listaTemi as $t) {
                if ($tema == $t['nome_tema']) {
                    $errore = true;
                }
            }

            // se la categoria non esiste mando il messaggio di errore
            if (!$errore) {
                array_push($_SESSION['error'], "Il tema inserito non esiste. Riprova");
            }    
        }

        if (count($_SESSION['error'])==0) {
            try {
                $stmt=$link->prepare("INSERT into blog(nome_blog, descrizione_blog, creazione_blog, autore_blog, tema) 
                    VALUES (:nome_blog, :descrizione_blog, :creazione_blog, :autore_blog, :tema)");

                $stmt->bindvalue(":nome_blog", $nome_blog);
                $stmt->bindvalue(":descrizione_blog", $descrizione_blog);
                $stmt->bindvalue(":creazione_blog", date("Y-m-d H:i:s"));
                $stmt->bindvalue(":autore_blog", $_SESSION['id_user']);
                $stmt->bindvalue(":tema", $tema);
               
                $stmt->execute();

                //tramite il metodo di PDO lastInsertId(), otteniamo l'id dell'ultimo elemento inserito nel database
                $id_blog = $link->lastInsertId();

            } catch (PDOException $e) {
                echo "Connessione Fallita: " . $e->getMessage();
                exit();
            }
       
            //associamo il blog appena creato alla categoria 
            try {
                $stmt=$link->prepare("INSERT into appartiene(id_blog, id_categoria) VALUES 
                    (:id_blog, :id_categoria)");

                $stmt->bindvalue(":id_blog", $id_blog);
                $stmt->bindvalue(":id_categoria", $id_nuova_categoria);
               
                $stmt->execute();

            } catch (PDOException $e) {
                echo "Connessione Fallita: " . $e->getMessage();
                exit();
            }

            //associamo il blog appena creato alla sottocategoria 
            try {
                $stmt=$link->prepare("INSERT into appartiene(id_blog, id_categoria) VALUES 
                    (:id_blog, :id_categoria)");

                $stmt->bindvalue(":id_blog", $id_blog);
                $stmt->bindvalue(":id_categoria", $id_nuova_sottocat);
               
                $stmt->execute();

            } catch (PDOException $e) {
                echo "Connessione Fallita: " . $e->getMessage();
                exit();
            }
            
            header("Location: listaBlog.php");
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        //controllo che sia stata fatta una richiesta AJAX verificando che esista un parametro "cat" nell'url
        if(array_key_exists('cat', $_GET)) {
            //estraggo le informazioni della categoria attraverso il nome ottenuto dalla richiesta ajax
            $categoria = estraiInfocategoria($link, $_GET['cat']); 

            //estraggo le sottocategorie
            $data = estraiSottocategorie($link, $categoria['id_categoria']);

            $sottocategorie = array();
            foreach ($data as $c) {
                array_push($sottocategorie, $c['nome_categoria']); //nel nuovo array creato, inserisco i nomi delle sottocategorie
            }
            
            //attraverso la funzione json_encode restituisco l'array delle sottocategorie in JSON sulla pagina
            echo json_encode($sottocategorie);
            // per evitare di ottenere l'intera pagina HTML come risposta dalla richiesta AJAX, utilizziamo la funzione exit()
            exit();
        }
    }
?>


<?php require 'config_files/header.php';?>

<?php require 'grafica/navbar.php';?>

<div class="mt-5 vh-100 d-flex flex-column align-items-center">
    <h1 class="display-5 mb-5 ">Crea il tuo blog!</h1>
    <!--Mostra eventuali errori nella creazione del blog-->
    <?php if (isset($_SESSION['error']) && count($_SESSION['error'])>0 ) { ?>
        <ul class="w-75 alert alert-danger"> 
            <?php foreach ($_SESSION['error'] as $err) : echo "<li>" . $err . "</li>";?>
            <?php endforeach?>
        </ul>
    <?php } ?>

    <form class="w-50" method="post">
        <div class="mb-3">
            <label for="nome_blog" class="form-label text-body-secondary">Nome del Blog</label>
            <input name="nome_blog" type="text" class="form-control" value="<?php echo $nome_blog ?>" required>
            <p class="mt-1 text-black-50" style="font-size: 0.8em">Il titolo può contenere solo i seguenti caratteri: ! ? , . " &</p>
        </div>

        <div class="mb-3">
            <label for="descrizione_blog" class="form-label text-body-secondary">Descrizione</label>
            <textarea name='descrizione_blog' rows="3" cols="25" minlength="1" maxlength="240" class='form-control'></textarea>
        </div>

        <div class="form-group needs-validation mt-4 mb-3">
            <label for="categoria" class="form-label text-body-secondary">Categoria</label>
            <select class='form-control' name="categoria" id="Categorie">
                <option value="">Seleziona una categoria</option>
                <?php foreach ($categorie as $i => $c) : ?>  
                    <option class="me-1 mb-2" value="<?php echo $c['nome_categoria'] ?>"><?php echo $c['nome_categoria'] ?></option>
                <?php endforeach?>
            </select>
        </div>

        <div class="form-group needs-validation mt-4 mb-3">
            <label for="sottocategoria" class="form-label text-body-secondary">Sottocategoria</label>
            <select class='form-control' name="sottocategoria" id="Sottocategorie">
                <option value="">Seleziona una sottocategoria</option>
                <?php foreach ($sottocategorie as $i => $sc) : ?>  
                    <option class="me-1 mb-2" value="<?php echo $sc['nome_categoria'] ?>"><?php echo $sc['nome_categoria'] ?></option>
                <?php endforeach?>
            </select>
        </div>

        <div class="scelta-template form-group needs-validation mt-4 mb-3">
        <label for="tema" class="form-label text-body-secondary">Tema Grafico</label>
            <div class="radio-element">
                <?php foreach ($listaTemi as $i => $tema) : ?>   
                    <input class="me-1 mb-3" type="radio" id="<?php echo $tema['nome_tema']?>" name="tema" value="<?php echo $tema['nome_tema']?>">
                    <label for="<?php echo $tema['nome_tema']?>"><?php echo $tema['nome_tema']?></label> </br>
                <?php endforeach ?>
            </div>                 
        </div>

        <button type="submit" class="btn mt-3 btn-info mb-5">Crea</button>
        <a class="btn btn-danger mt-3 mb-5" href="index.php?p=1">Annulla</a>
    </form>
</div>

<?php
    //cancello i messaggi di errore
    if (isset($_SESSION['error'])) {
    unset($_SESSION['error']);
    }
?>

<script src="js/gestisciCategorie.js"></script>

