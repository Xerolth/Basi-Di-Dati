<?php
    //avvio la sessione se non è già avviata
    if(!isset($_SESSION)) {
        session_start(); 
    }

    if (!isset($_SESSION['user'])){
        header("Location: homepage.php");  
    }

    //richiamo lo script responsabile della connessione a MySQL
    $link = require 'config_files/connect.php';
   
    //richiamo il file contenente tutte le query
    require 'config_files/query.php';

    //estraggo i dati dell'utente dal database in base allo username
    $dati = datiUtente($link, $_SESSION['user']);
    
    $_SESSION['error'] = array();

    $nome="";
    $cognome="";
    $descrizione="";

    //Controllo che sia stato premuto un pulsante del form con metodo post
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {


        // https://www.php.net/manual/en/features.file-upload.post-method.php
        //controllo che nella variabile globale $_FILES ci sia il file che abbiamo caricato e che sia presente il nome temporaneo
        if (array_key_exists('nuovo_avatar', $_FILES) && $_FILES['nuovo_avatar']['name']!="") { 
            
            // controllo che l'estensione del file sia corretta
            $check = false;
            $estensioni_accettate = array('jpg', 'jpeg', 'png');
            foreach ($estensioni_accettate as $e) {
                if (str_ends_with($_FILES['nuovo_avatar']['name'], $e)) {
                    $check = true;
                }
            }
            
            //se l'estensione del file è valida, allora proseguo con gli altri controlli
            if (!$check) {
                array_push($_SESSION['error'], "Il formato del file non è valido. Sono permessi solo file immagini in .jpg, .jpeg e .png");
            } else {

                //ripulisco il nome del file da caratteri problematici
                $nome_file = $_FILES['nuovo_avatar']['name'];
                $chars = array(" ", "'", "-", "%", "$");
                foreach ($chars as $c) {
                    $nome_file = str_replace($c, '', $nome_file);
                }
            
                //Per evitare file con nomi duplicati creo un percorso che specifica la destinazione del file.
                //Il percorso è formato da una cartella con lo username dell'utente dove verrà inserito il file con un nome univoco per via della funzione time() 
                $percorso_univoco_avatar = 'imgs/avatar/'.$_SESSION['user'].'/'.time()."_".$nome_file;

                //controllo che il file non superi una certa dimensione
                if($_FILES['nuovo_avatar']['size'] > 3145728){
                    array_push($_SESSION['error'], "Il file caricato è troppo grande.");
                }
            }

            if (count($_SESSION['error'])==0) {
                if($dati['avatar'] != "imgs/avatar/default.jpg") {
                    //cancello il vecchio avatar attraverso la funzione unlink, specificando l'url salvato nella tabella utente
                    unlink($dati['avatar']);
                }
                
                //creo la cartella che avrà come nome lo username dell'utente
                if (!is_dir("avatar/".$_SESSION['user'])) {
                    mkdir(dirname($percorso_univoco_avatar));
                }
                //aggiungo il file alla cartella dell'utente
                move_uploaded_file($_FILES['nuovo_avatar']['tmp_name'], $percorso_univoco_avatar);
            
                try {
                    $stmt = $link->prepare("UPDATE utente SET avatar = :avatar WHERE nome_utente = :nome_utente");
                    $stmt->bindValue(':avatar', $percorso_univoco_avatar);
                    $stmt->bindValue(':nome_utente', $_SESSION['user']);
                    $stmt->execute();
                } catch (PDOException $e) {
                    echo "Connessione Fallita: ".$e->getMessage();
                }

                // Controllo che l'utente sia verificato. 
                if(!$dati['verificato']) {
                    // Se l'utente non è verificato allora controllo che l'utente abbia un descrizione. Se l'utente ha aggiornato
                    // anche la sua descrizione, allora viene verificato.
                    if ($dati['descrizione_utente'] != null) {
                        //Passo la variabile da 0 a 1 per verificare l'utente
                        try {
                            $stmt = $link->prepare("UPDATE utente SET verificato = 1 WHERE nome_utente = ?");
                            $stmt->execute([$_SESSION['user']]);
                        } catch (PDOException $e) {
                            echo "Connessione Fallita: ".$e->getMessage();
                            exit();
                        }
                        
                        //cambio lo status dell'utente in sessione
                        $_SESSION['verificato'] = 1;
                    }
                }

                header("Location: profilo.php"); 
            }
        }
        
        //controllo che sia stata inviata una richiesta POST dal form di modifica nome e cognome, verificando che sia presente l'attributo nome
        if (array_key_exists('nome', $_POST)) {
            $nome= trim(ucfirst($_POST['nome'])); 
            $cognome= trim(ucfirst($_POST['cognome'])); 
           
            $errore = false;
            //controllo la lunghezza del nome
            if(strlen($nome) < 3 || strlen($nome) > 50) {
                array_push($_SESSION['error'], "Il nome deve avere tra i 3 e i 50 caratteri");
                $errore = true;
            }

            //controllo che il nome contenga solo lettere e spazi
            if(!preg_match("/^[a-zA-Z]+\s?[a-zA-Z]*$/", $nome)) {
                array_push($_SESSION['error'], "Il Nome deve contenere solo lettere");
                $errore = true;
            }

            //se si sono verificati degli errori ripulisco il campo
            if($errore) {
                $nome = "";
            }

            $errore = false;
            //controllo la lunghezza del nome
            if(strlen($cognome) < 3 || strlen($cognome) > 50) {
                array_push($_SESSION['error'], "Il cognome deve avere tra i 3 e i 50 caratteri");
                $errore = true;
            }

            $errore = false;
            //controllo che il cognome abbia solo lettere, spazi e apostrofi
            if(!preg_match("/^[a-zA-Z]+\s?(\'[a-zA-Z]+)?[a-zA-Z]+$/", $cognome)) {
                array_push($_SESSION['error'], "Il Cognome deve avere solo lettere");
                $errore = true;
            }
        
            //se si sono verificati degli errori ripulisco il campo
            if($errore) {
                $cognome = "";
            }

            //se non ci sono errori avvio la query di modifica nome e cognome
            if (count($_SESSION['error'])==0) {
                try {
                    $stmt = $link->prepare("UPDATE utente SET nome = :nome, cognome = :cognome
                    WHERE nome_utente = :nome_utente");
        
                    $stmt->bindValue(':nome_utente', $_SESSION['user']);
                    $stmt->bindValue(':nome', $nome);
                    $stmt->bindValue(':cognome', $cognome);
                
                    $stmt->execute();
                } catch (PDOException $e) {
                    echo("Codice errore " . $e->getMessage());
                    exit();
                }

                header("Location: profilo.php");
            }

        }

        if (array_key_exists('descrizione', $_POST)) {
            $descrizione= trim($_POST['descrizione']); 

            $errore = false;
            //controllo la lunghezza del nome del blog
            if(strlen($descrizione) > 240) {
                array_push($_SESSION['error'], "La descrizione del blog deve avere massimo 240 caratteri");
                $errore = true;
            } 

            // controllo il contenuto della descrizione
            if (!preg_match("/^[À-ÿA-Za-z0-9!?&$#%:()\',.\-\"\s]+$/", $descrizione)) {
                array_push($_SESSION['error'], "La descrizione inserita non è valida");
            }

            if (count($_SESSION['error'])==0) {
                try {
                    $stmt = $link->prepare("UPDATE utente SET descrizione_utente = :descrizione
                    WHERE nome_utente = :nome_utente");
        
                    $stmt->bindValue(':nome_utente', $_SESSION['user']);
                    $stmt->bindValue(':descrizione', $descrizione);
                
                    $stmt->execute();

                } catch (PDOException $e) {
                    echo("Codice errore " . $e->getMessage());
                    exit();
                }

                  // Controlliamo che l'utente sia verificato. 
                  if(!$dati['verificato']) {
                    // Se l'utente non è verificato allora controllo che l'utente abbia una foto profilo diversa da default. Se l'utente ha aggiornato
                    // anche la sua foto profilo, allora viene verificato.
                    if ($dati['avatar'] != 'imgs/avatar/default.jpg') {
                        //Passo la variabile da 0 a 1 per verificare l'utente
                        try {
                            $stmt = $link->prepare("UPDATE utente SET verificato = 1 WHERE nome_utente = ?");
                            $stmt->execute([$_SESSION['user']]);
                        } catch (PDOException $e) {
                            echo("Codice errore " . $e->getMessage());
                            exit();
                        }

                        $_SESSION['verificato'] = 1;
                    }
                }

                header("Location: profilo.php");
            }

        }
    }

    
?>
    
<?php require 'config_files/header.php';?>

<?php require 'grafica/navbar.php';?>

<div class="d-flex flex-row flex-column mb-3">
    <div class="d-flex align-items-center flex-column p-2">
        <div class="w-25">
            <img style="width: 300px; height: 300px;" class="rounded-circle" src="<?php echo $dati['avatar'] ?>" alt="">
        </div>

        <form class="d-flex align-items-center flex-column " method="post" enctype="multipart/form-data">
            <div class="form-group needs-validation mb-3">
                <input class="mt-3" type="file" accept="image/png, image/gif, image/jpeg, image/jpg" name="nuovo_avatar"> 
            </div>
            <button type="submit" class="w-50 btn btn-primary">Cambia avatar</button>
        </form>
    </div>
    
    <div class="d-flex align-items-center flex-column p-2 w-100">
        <?php if (isset($_SESSION['error']) && count($_SESSION['error'])>0 ) { ?>
            <ul class="alert alert-danger">
                <?php foreach ($_SESSION['error'] as $err) : echo "<li>" . $err . "</li>";?>
                <?php endforeach?>
            </ul>
        <?php } ?>

        <p class="fs-3"><?php echo $dati['nome_utente'] ?></p>

        <div class="sezione-descrizione w-25 mb-3"> 
            <!-- Poiché la descrizione rimane vuota in fase di registrazione, allora mostro il box di modifica -->
            <?php if (!$dati['descrizione_utente']) { ?>
                <form class='modifica-descrizione mt-3 w-100' method='post'>
                    <label for='campo-descrizione' class='form-label'>Descrizione</label>
                    <textarea name='descrizione' rows="5" cols="25" maxlength="240" class='form-control' id='campo-descrizione'> </textarea>
                    <button type='submit' class='mt-3 btn btn-primary'>Inserisci</button>
                </form>
            <?php } else { ?>
                <!-- altrimenti mostro la descrizione dell'utente -->
                <p id="descrizione-utente" class="text-center">
                    <?php echo $dati['descrizione_utente'] ?>
                </p>
                <p class="text-center">
                    <a href="" id="mod-descrizione"><span class="text-info text-decoration-underline fs-6">Modifica</span></a>
                </p>
            <?php } ?>
        </div>

        <p class="sezione-nome">
            Nome: <?php echo $dati['nome'] ?> <?php echo $dati['cognome'] ?> 
            <a href="" id="mod-Nome"><span class="text-info text-decoration-underline fs-6">Modifica</span></a>
        </p>
        <p>Email: <?php echo $dati['email'] ?></p>
        <p>Iscrizione: <?php echo $dati['creazione_utente'] ?></p>

        <div class="d-flex flex-column align-items-center">
            <!--mostro lo status di verifica dell'utente-->
            <?php if ($dati['verificato']) { ?>
                <p id="verifica">Status utente: Verificato</p>
            <?php } else { ?>
                <p id="verifica">Status utente: Non verificato</p>
                <div class="alert alert-warning w-50" role="alert">
                    Per verificare il tuo account personalizza il tuo avatar e la tua descrizione.
                    Per sfruttare al massimo le funzionalità del sito, verifica l'account.
                    Verificando l'account sarà possibile creare nuovi blog personali,
                    amministrare i blog creati da altri utenti. Senza verificare il tuo account
                    potrai comunque commentare e mettere mi piace ai post.
                </div>

            <?php } ?>
        </div>
     
        

        <a href="cancellaUtente.php">
            <button type="submit" class="btn btn-danger">Cancella il profilo</button>
        </a>
    </div>
</div>

<script src="js/modificaUtente.js"></script>