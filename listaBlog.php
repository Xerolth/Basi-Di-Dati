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
    
    //creo l'array degli errori
    $_SESSION['error'] = array();

    $listaBlog = estraiListablog($link, $_SESSION['id_user']);
    $_SESSION['numero_blog'] = count($listaBlog);
    
    $listaAmministrati = estraiBlogcoautore($link, $_SESSION['id_user']);

    //se l'utente non ha creato e non amministra alcun blog, viene indirizzato alla pagina di creazione dei blog
    if(count($listaBlog) == 0 && count($listaAmministrati) == 0) {
        header("Location: creazioneBlog.php");
    }
       
?>

<?php require 'config_files/header.php';?>

<?php require 'grafica/navbar.php';?>

<div class="container w-100 d-flex flex-column align-items-center mb-4">
    <h1 class="mt-5 text-center">I tuoi blog</h1>
    <!-- se un utente ha creato meno di 6 blog vede il pulsante per crearne altri -->
    <?php if ($_SESSION['numero_blog'] != 6) { ?>
        <a href="creazioneBlog.php" class="fs-4 text-decoration-none">Crea un nuovo blog</a>
        <!-- mostro il contatore dei blog dell'utente -->
        <p class="text-center fs-5 text-secondary">Ricordati che puoi creare ancora <?php echo 6 - count($listaBlog) ?> blog!</p>
    <?php } ?>
</div>
<div class="container w-100 mb-5 me-5 ms-5 d-flex flex-wrap align-items-center">
    <!-- mostro i blog creati dall'utente e i pulsanti delle funzionalità-->
    <?php foreach ($listaBlog as $i => $b) : ?>
        <div class="w-50 mt-4 mb-4">
            <div style="margin-left: 10rem;" class="d-flex flex-column justify-content-left mb-1">
                <p style class="display-6 fs-3"><a class="text-decoration-none" href="blog.php?id=<?php echo $b['id_blog'] ?>"><?php echo $b['nome_blog'] ?></a></p>
                <p class="fs-6 text-secondary">progettoNew/blog.php?id=<?php echo $b['id_blog']?></p>
            </div>
            <div style="margin-left: 10rem" class="d-flex justify-content-left">
                <a href="nuovoPost.php?id=<?php echo $b['id_blog']?>">
                    <button type="button" class="btn btn-primary me-3">Scrivi</button>
                </a>
                <a href="personalizzaBlog.php?id=<?php echo $b['id_blog']?>">
                    <button type="button" class="btn btn-light border border-secondary me-3">Personalizza</button>
                </a>
                <a href="gestisciBlog.php?id=<?php echo $b['id_blog']?>">
                    <button type="button" class="btn btn-light border border-secondary me-3">Gestisci</button>
                </a>
                <a href="cancellaBlog.php?id=<?php echo $b['id_blog']?>">
                    <button type="button" class="btn btn-danger">Cancella</button>
                </a>
            </div>
        </div>
    <?php endforeach?>
    <!-- mostro i blog amministrati dall'utente e il pulsante di scrittura del post-->
    <?php foreach ($listaAmministrati as $i => $b) : ?>  
        <div class="w-50 mt-4 mb-4">
            <div style="margin-left: 10rem;" class="d-flex flex-column justify-content-left mb-1">
                <p style class="display-6 fs-3"><a class="text-decoration-none" href="blog.php?id=<?php echo $b['id_blog'] ?>"><?php echo $b['nome_blog'] ?></a></p>
                <p class="fs-6 text-secondary">progettoNew/blog.php?id=<?php echo $b['id_blog']?></p>
            </div>
            <div style="margin-left: 10rem" class="d-flex justify-content-left">
                <a href="nuovoPost.php?id=<?php echo $b['id_blog']?>">
                    <button type="button" class="btn btn-primary me-3">Scrivi</button>
                </a>
            </div>
        </div>
    <?php endforeach?>
</div>




<?php
    //cancello i messaggi di errore
    if (isset($_SESSION['error'])) {
    unset($_SESSION['error']);
    }
?>


