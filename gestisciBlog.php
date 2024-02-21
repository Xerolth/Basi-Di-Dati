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

    //estraggo la lista di tutte le categorie
    $categorie = estraiCategorie($link);
    //estraggo le informazioni sulla categoria attualmente impostata
    $categoria = categoriaBlog($link, $id_blog);

    //sulla base della categoria estratta, estraggo la lista di tutte le rispettive sottocategorie 
    $sottocategorie = estraiSottocategorie($link, $categoria['id_categoria']);
    //estraggo la sottocategoria attualmente impostata
    $sottocategoria = sottocategoriaBlog($link, $id_blog);

    //estraggo tutti i coautori del blog
    $coautori = estraiCoautori($link, $id_blog);

    //creo l'array degli errori
    $_SESSION['error'] = array();

    //creo due variabili che conterrano il valore della categoria e della sottocategoria, presi in input dal form
    $cat = ''; 
    $sottocat = '';
    //creo una variabile che conterrà l'username del coautore, preso dall'input del form
    $coautore = '';

    //controllo che sia stato premuto un pulsante del form con metodo post
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if(array_key_exists('categoria', $_POST)) {
            $cat = $_POST['categoria'];
            $sottocat = $_POST['sottocategoria'];
            
            //controllo che la categoria esista nella lista ottenuta dal database
            $f = false;
            foreach($categorie as $c) {
                if ($cat == $c['nome_categoria']) {
                    $f = true;
                    $id_nuova_categoria = $c['id_categoria'];
                }
            }
    
            // se la categoria non esiste mando il messaggio di errore
            if (!$f) {
                array_push($_SESSION['error'], "La categoria inserita non esiste. Riprova");
            } else {
                //estraggo le informazioni sulla categoria
                $nuova_categoria = estraiInfocategoria($link, $cat);
    
                $f = false;
                $sottocategorie = estraiSottocategorie($link, $nuova_categoria['id_categoria']);
                foreach($sottocategorie as $c) {
                    if ($sottocat == $c['nome_categoria']) {
                        $f = true;
                        $id_nuova_sottocategoria = $c['id_categoria'];
                    }
                }
        
                // se la categoria non esiste mando il messaggio di errore
                if (!$f) {
                    array_push($_SESSION['error'], "La sottocategoria inserita non è valida. Riprova");
                } 
            }
    
          

            if (count($_SESSION['error'])==0) {
                try {
                    $stmt = $link->prepare("UPDATE appartiene SET id_blog = :id_blog, id_categoria = :id_categoria
                    WHERE id_blog = :id_blog AND id_categoria = :id_categoria_attuale");
        
                    $stmt->bindValue(':id_blog', $id_blog);
                    $stmt->bindValue(':id_categoria', $id_nuova_categoria);
                    $stmt->bindValue(':id_categoria_attuale', $categoria['id_categoria']);
    
                    $stmt->execute();
                } catch (PDOException $e) {
                    echo("Codice errore " . $e->getMessage());
                    exit();
                }
    
                // aggiorno la sottocategoria del blog
                try {
                    $stmt = $link->prepare("UPDATE appartiene SET id_blog = :id_blog, id_categoria = :id_categoria
                    WHERE id_blog = :id_blog AND id_categoria = :id_categoria_attuale");
        
                    $stmt->bindValue(':id_blog', $id_blog);
                    $stmt->bindValue(':id_categoria', $id_nuova_sottocategoria);
                    $stmt->bindValue(':id_categoria_attuale', $sottocategoria['id_categoria']);
                
                    $stmt->execute();
                } catch (PDOException $e) {
                    echo("Codice errore " . $e->getMessage());
                    exit();
                }
    
                header("Location: gestisciBlog.php?id=$id_blog");
            }
        }
        
        if(array_key_exists("coautore", $_POST)) {
            $coautore = trim(strtolower($_POST['coautore']));

            //controlliamo che il campo aggiungi coautore sia stato compilato.
            if($coautore == "") {
                array_push($_SESSION['error'], "Il campo coautore è vuoto.");
            } else if (in_array($coautore, array_column($coautori, 'nome_utente'))) {
                //verifica se il valore di $coautore è presente nei valori 'nome_utente' dell'array $coautori.
                //utilizziamo la funzione array_column per creare un nuovo array formato da tutti i valori 
                // che hanno la chiave 'nome_utente'
                //dopo, utilizziamo la funzione in_array per verificare se $coautore è presente in quel nuovo array.
                array_push($_SESSION['error'], "L'utente è già un coautore.");
            } else if ($coautore == $dati_utente['nome_utente']) {
                //controllo che l'utente inserito non sia l'autore del blog
                array_push($_SESSION['error'], "Sei già autore del blog.");
            } else if (!checkUsername($link, $coautore)) {
                array_push($_SESSION['error'], "L'utente inserito non esiste.");
            } else {
                $dati_utente = datiUtente($link, $coautore);
                if (!$dati_utente['verificato']) {
                    array_push($_SESSION['error'], "L'utente inserito non è verificato.");
                }
            }

            if (count($_SESSION['error'])==0) {
                try {
                    $stmt=$link->prepare("INSERT into coautore(id_utente, id_blog) 
                    VALUES (:id_utente, :id_blog)");
        
                    $stmt->bindValue(':id_utente', $dati_utente['id_utente']);
                    $stmt->bindValue(':id_blog', $id_blog);
    
                    $stmt->execute();
                } catch (PDOException $e) {
                    echo("Codice errore " . $e->getMessage());
                    exit();
                }

                header("Location: gestisciBlog.php?id=$id_blog");
            }
        }

        if(array_key_exists("rimuovi-coautore", $_POST)) {
            $coautore = trim(strtolower($_POST['rimuovi-coautore']));

            //controlliamo che il campo rimuovi coautore sia stato compilato.
            if($coautore == "") {
                array_push($_SESSION['error'], "Il campo coautore è vuoto.");
            } else if (!(in_array($coautore, array_column($coautori, 'nome_utente')))) {
                //verifica se il valore di $coautore è presente nei valori 'nome_utente' dell'array $coautori.
                //utilizziamo la funzione array_column per creare un nuovo array formato da tutti i valori 
                // che hanno la chiave 'nome_utente'
                //dopo, utilizziamo la funzione in_array per verificare se $coautore è presente in quel nuovo array.
                array_push($_SESSION['error'], "L'utente non è un coautore.");
            } else if ($coautore == $dati_utente['nome_utente']) {
                //controllo che l'utente inserito non sia l'autore del blog
                array_push($_SESSION['error'], "Non puoi rimuovere te stesso.");
            } else if (!checkUsername($link, $coautore)) {
                array_push($_SESSION['error'], "L'utente non esiste.");
            } else {
                $dati_utente = datiUtente($link, $coautore);
            }

            if (count($_SESSION['error'])==0) {
                try {
                    $stmt=$link->prepare("DELETE FROM coautore
                    WHERE id_utente = :id_utente AND id_blog = :id_blog");
        
                    $stmt->bindValue(':id_utente', $dati_utente['id_utente']);
                    $stmt->bindValue(':id_blog', $id_blog);
    
                    $stmt->execute();
                } catch (PDOException $e) {
                    echo("Codice errore " . $e->getMessage());
                    exit();
                }

                header("Location: gestisciBlog.php?id=$id_blog");
            }
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

<div class="d-flex flex-column align-items-center">
    <h1 class="mt-4 mb-4">Gestisci <?php echo $dati_blog['nome_blog']?></h1>
    <!--Mostra eventuali errori nella registrazione-->
    <?php if (isset($_SESSION['error']) && count($_SESSION['error'])>0 ) { ?>
        <ul class="w-50 alert alert-danger"> 
            <?php foreach ($_SESSION['error'] as $err) : echo "<li>" . $err . "</li>";?>
            <?php endforeach?>
        </ul>
    <?php } ?>

    <form class="w-50" method="post">
        <div class="form-group needs-validation mb-3">
            <label for="Categoria" class="form-label text-body-secondary">Categoria</label>
            <select class='form-control' name="categoria" id="Categorie">
                <!--Seleziona la categoria predefinita, che corrisponde alla categoria selezionata dall'utente--> 
                <?php foreach ($categorie as $i => $c) : ?> 
                    <!--Controllo che la categoria estratta corrisponda alla categoria del blog--> 
                    <?php if ($c['nome_categoria'] == $categoria['nome_categoria']) { ?>
                        <option class="me-1 mb-2" selected="selected" value="<?php echo $c['nome_categoria'] ?>"><?php echo $c['nome_categoria'] ?></option>
                    <?php } else { ?>
                        <option class="me-1 mb-2" value="<?php echo $c['nome_categoria'] ?>"><?php echo $c['nome_categoria'] ?></option>
                    <?php } ?> 
                <?php endforeach?>
            </select>
        </div>

        <div class="form-group needs-validation mt-4 mb-3">
            <label for="sottocategorie" class="form-label text-body-secondary">Sottocategoria</label>
            <select class='form-control'name="sottocategoria" id="Sottocategorie">
                <!--Seleziona la categoria predefinita, che corrisponde alla categoria selezionata dall'utente--> 
                <?php foreach ($sottocategorie as $i => $c) : ?> 
                    <!--Controllo che la categoria estratta corrisponda alla categoria del blog--> 
                    <?php if ($c['nome_categoria'] == $sottocategoria['nome_categoria']) { ?>
                        <option class="me-1 mb-2" selected="selected" value="<?php echo $c['nome_categoria'] ?>"><?php echo $c['nome_categoria'] ?></option>
                    <?php } else { ?>
                        <option class="me-1 mb-2" value="<?php echo $c['nome_categoria'] ?>"><?php echo $c['nome_categoria'] ?></option>
                    <?php } ?> 
                <?php endforeach?>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Aggiorna</button>
    </form>

    <div class="w-50 d-flex flex-column">
        <h3 class="mt-5 mb-2">Coautori</h3>
        <div class="mb-5 d-flex justify-content-between">
            <form class="mt-2 w-50" method="post">
                    <div class="form-group needs-validation mb-3">
                        <input name="coautore" type="text" class="w-75 form-control" placeholder="Username">
                    </div>

                    <button type="submit" class="btn btn-primary">Aggiungi</button>
                </form>

                <?php if ($coautori) { ?>
                    <form class="w-50" method="post">
                        <div class="mt-2 form-group needs-validation mb-3">
                            <select class='w-75 form-control' name="rimuovi-coautore">
                                <?php foreach ($coautori as $i => $c) : ?> 
                                    <option class="me-1 mb-2" value="<?php echo $c['nome_utente'] ?>"><?php echo $c['nome_utente'] ?></option>
                                <?php endforeach?>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-danger">Rimuovi</button>
                    </form>
                <?php } ?> 
                
        </div>
    </div>

</div>

<?php
    //cancello i messaggi di errore
    if (isset($_SESSION['error'])) {
    unset($_SESSION['error']);
    }
?>

<script src="js/gestisciCategorie.js"></script>