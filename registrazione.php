<?php
    //controllo che la sessione sia attiva
    if(!isset($_SESSION)) {
        session_start(); 
    }

    //controllo se l'utente è già loggato. Se è già loggato lo rimando alla homepage
    if (isset($_SESSION['user'])){
        header("Location: index.php?p=1");  
    }

    //richiamo lo script responsabile della connessione a MySQL
    $link = require 'config_files/connect.php';
    
    //richiamo il file contenente tutte le query
    require 'config_files/query.php';

    //creo l'array degli errori
    $_SESSION['error'] = array();

    //definisco una serie di variabili vuote che andranno a contenere i valori presi in input nel form della richiesta POST
    $username="";
    $nome="";
    $cognome="";
    $mail="";
    $pw="";
    $conferma_pw="";
    $documento="";
    $telefono="";

    //controllo che sia stato premuto un pulsante del form con metodo post
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $username=trim(strtolower($_POST['username']));
        $nome=$_POST['nome'];
        $cognome=$_POST['cognome'];
        $mail=$_POST['mail'];
        $pw=$_POST['pw'];
        $conferma_pw=$_POST['confermaPw'];
        $documento=$_POST['documento'];
        $telefono=$_POST['telefono'];

        //controllo che non ci siano campi vuoti
        if($username==""||$nome==""||$cognome==""||$mail==""||$pw==""||$conferma_pw==""||$documento==""||$telefono==""){
            //se uno dei campi è vuoto visualizzo solo questo errore
            array_push($_SESSION['error'], "Compilare tutti i campi");
        } else {
            //creo una variabile per controllare se si verificano degli errori
            $errore = false;
            //controllo che lo username non sia già in uso
            if (checkUsername($link, $username)) {
                array_push($_SESSION['error'], "Nome Utente già in uso");
                $errore = true;
            }

            //controllo la lunghezza dello username
            if(strlen($username) < 6 || strlen($username) > 20) {
                array_push($_SESSION['error'], "Il Nome Utente deve avere tra i 6 e i 20 caratteri");
                $errore = true;
            }

            //controllo che lo username abbia solo caratteri alfanumerici
            if(!preg_match("/^[a-zA-Z0-9]+$/", $username)) {
                array_push($_SESSION['error'], "Il Nome Utente deve avere solo caratteri alfanumerici");
                $errore = true;
            }

            //se si sono verificati degli errori ripulisco il campo
            if($errore) {
                $username = "";
            }

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

            //controllo che il cognome abbia solo lettere, spazi e apostrofi
            if(!preg_match("/^[a-zA-Z]+\s?(\'[a-zA-Z]+)?[a-zA-Z]+$/", $cognome)) {
                array_push($_SESSION['error'], "Il Cognome deve avere solo lettere");
                $errore = true;
            }

            //se si sono verificati degli errori ripulisco il campo
            if($errore) {
                $cognome = "";
            }

            $errore = false;

            //controllo che la mail non sia già in uso
            if (checkMail($link, $mail)) {
                array_push($_SESSION['error'], "Email già in uso");
                $errore = true;
            }

            //controllo che la mail sia valida
            if(!(filter_var($mail, FILTER_VALIDATE_EMAIL))){
                array_push($_SESSION['error'], "Email non valida");
                $errore = true;
            }

            //se si sono verificati degli errori ripulisco il campo
            if($errore) {
                $cognome = "";
            }

            $errore = false;

            //controllo la lunghezza della password
            if(strlen($pw) < 6 || strlen($pw) > 20) {
                array_push($_SESSION['error'], "La Password deve avere tra i 6 e i 20 caratteri");
            }

            //controllo che password e confermaPw siano uguali
            if ($conferma_pw==$pw) {
                //se si creo il hash della password
                $pass_hash=password_hash($pw, PASSWORD_DEFAULT);
            } else {
                array_push($_SESSION['error'], "Password e conferma devono essere uguali");
            }

            //controllo che il documento non sia già in uso
            if (checkDocumento($link, $documento)) {
                array_push($_SESSION['error'], "Documento già in uso");
                $errore = true;
            }

            //controllo che il documento sia valido
            if(!preg_match("/^[A-Z]{2}[0-9]{7}$/", $documento)){
                array_push($_SESSION['error'], "Il codice Documento non è valido");
                $errore = true;
            }

            //se si sono verificati degli errori ripulisco il campo
            if($errore) {
                $documento = "";
            }

            $errore = false;

            //controllo che il telefono non sia già in uso
            if (checkTelefono($link, $telefono)) {
                array_push($_SESSION['error'], "Telefono già in uso");
                $errore = true;
            }

            //controllo che il telefono abbia solo numeri
            if(!preg_match("/^[0-9]+$/", $telefono)){
                array_push($_SESSION['error'], "Il numero di telefono deve contenere solo numeri");
                $errore = true;
            }

            //controllo che la lunghezza del numero sia corretta
            if(strlen($telefono) < 9 || strlen($telefono) > 15){
                array_push($_SESSION['error'], "Il numero di telefono deve avere tra le 9 e le 15 cifre");
                $errore = true;
            }

            //se si sono verificati degli errori ripulisco il campo
            if($errore) {
                $telefono = "";
            }
        }

        //se non ci sono errori nella compilazione del form eseguo la query
        if(count($_SESSION['error'])==0) {
            try {
                $stmt=$link->prepare("INSERT into utente(nome_utente, nome, cognome, email, passhash, telefono, documento, creazione_utente, avatar, verificato) 
                VALUES (:utente, :nome, :cognome, :mail, :passhash, :telefono, :documento, :creazione, :avatar, :verificato)");
                $stmt->bindvalue(":utente", $username);
                $stmt->bindvalue(":nome", $nome);
                $stmt->bindvalue(":cognome", $cognome);
                $stmt->bindvalue(":mail", $mail);
                $stmt->bindvalue(":passhash", $pass_hash);
                $stmt->bindvalue(":telefono", $telefono);
                $stmt->bindvalue(":documento", $documento);
                $stmt->bindvalue(":creazione", date("Y-m-d H:i:s"));
                $stmt->bindvalue(":avatar", "imgs/avatar/default.jpg");
                $stmt->bindvalue(":verificato", 0);
                $stmt->execute();

                //salvo id e username in sessione
                $_SESSION['id_user'] = $link->lastInsertId();
                $_SESSION['user'] = $username;
                $_SESSION['verificato'] = 0;
                $_SESSION['numero_blog'] = 0;

            } catch (PDOException $e) {
                echo "Connessione Fallita: " . $e->getMessage();
                exit();
            }

            //indirizzo l'utente alla homepage
            header("Location: index.php?p=1");
            
        }
            
    }
?>

<?php require 'config_files/header.php';?>

<?php require 'grafica/navbar.php';?>

<!--form di registrazione-->
<div class="d-flex flex-column align-items-center">
    <h1 class="mt-4">Registrati</h1>
    <!--Mostra eventuali errori nella registrazione ciclando l'array $_SESSION['error']-->
    <?php if (isset($_SESSION['error']) && count($_SESSION['error'])>0 ) { ?>
        <ul class="w-25 alert alert-danger"> 
            <?php foreach ($_SESSION['error'] as $err) : echo "<li>" . $err . "</li>";?>
            <?php endforeach?>
        </ul>
    <?php } ?>
    <form class="w-25" method="post">
        <div class="mb-3">
            <label for="username" class="form-label">Nome Utente</label>
            <input name="username" type="text" class="form-control" 
            value="<?php echo $username ?>" required>
        </div>
        <div class="mb-3">
            <label for="nome" class="form-label">Nome</label>
            <input name="nome" type="text" class="form-control" 
            value="<?php echo $nome ?>" required>
        </div>
        <div class="mb-3">
            <label for="exampleFormControlInput1" class="form-label">Cognome</label>
            <input name="cognome" type="text" class="form-control"
            value="<?php echo $cognome ?>" required>
        </div>
        <div class="mb-3">
            <label for="exampleFormControlInput1" class="form-label">Email</label>
            <input name="mail" type="email" class="form-control" placeholder="nome@esempio.com"
            value="<?php echo $mail ?>" required>
        </div>
        <div class="mb-3">
            <label for="exampleFormControlInput1" class="form-label">Password</label>
            <input name="pw" type="password" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="exampleFormControlInput1" class="form-label">Conferma Password</label>
            <input name="confermaPw" type="password" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="exampleFormControlInput1" class="form-label">Documento</label>
            <input name="documento" type="text" class="form-control"
            value="<?php echo $documento ?>" required>
            <div class="form-text">Il documento deve essere composto da 9 caratteri. <br> Ad esempio AB0000000.</div>
        </div>
        <div class="mb-3">
            <label for="exampleFormControlInput1" class="form-label">Telefono</label>
            <input name="telefono" type="text" class="form-control" placeholder="Mobile o Fisso"
            value="<?php echo $telefono ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Registrati</button>
    </form>
</div>

<?php
    //cancello i messaggi di errore
    if (isset($_SESSION['error'])) {
    unset($_SESSION['error']);
    }
?>