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

    //controllo che sia stato premuto un pulsante del form con metodo post
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username=trim(strtolower($_POST['username']));
        $password=$_POST['password'];    
    
        //controllo se i campi sono vuoti
        if($username == "" || $password == "") {
            array_push($_SESSION['error'], "Inserire tutti i dati");
        } else {
            //controllo che lo username esista
            $dati_utente=findUser($link, $username);
            if(!$dati_utente) {
                array_push($_SESSION['error'], "Nome Utente non registrato");
                $username = "";
            //controllo che la password sia valida
            } else {
                if(!password_verify($password, $dati_utente['passhash'])) {
                    array_push($_SESSION['error'], "Password non valida");
                }
            }
        }

        //se non ci sono errori loggo l'utente
        if(count($_SESSION['error']) == 0) {
            $_SESSION['id_user'] = $dati_utente['id_utente'];
            $_SESSION['user'] = $username;
            $_SESSION['verificato'] = $dati_utente['verificato'];
            $listaBlog = estraiListablog($link, $_SESSION['id_user']);
            $_SESSION['numero_blog'] = count($listaBlog);

            //indirizzo l'utente alla homepage
            header("Location: index.php?p=1");
        }
    }
?>

<?php require 'config_files/header.php';?>

<?php require 'grafica/navbar.php';?>

<div class="d-flex flex-column align-items-center">
    <h1 class="mt-4">Accedi</h1>
    <!--Mostra eventuali errori nel login ciclando $_SESSION['error']-->
    <?php if (isset($_SESSION['error']) && count($_SESSION['error'])>0 ) { ?>
        <ul class="w-25 alert alert-danger"> 
            <?php foreach ($_SESSION['error'] as $err) : echo "<li>" . $err . "</li>";?>
            <?php endforeach?>
        </ul>
    <?php } ?>
    <form class="w-25" name="accedi" action="login.php" method="post">
        <div class="mb-3">
            <label for="exampleFormControlInput1" class="form-label">Nome Utente</label>
            <input type="text" name="username" class="form-control">
        </div>
        <div class="mb-3">
            <label for="exampleInputPassword1" class="form-label">Password</label>
            <input type="password" name="password" class="form-control">
        </div>
        
        <button type="submit" class="btn btn-primary">Accedi</button>
    </form>
</div>

<?php
    //cancello i messaggi di errore
    if (isset($_SESSION['error'])) {
        unset($_SESSION['error']);
    }
?>