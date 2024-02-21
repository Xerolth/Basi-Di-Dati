<?php

    //richiamo il file contenente tutte le query
    require 'config_files/query.php';

    //richiamo lo script responsabile della connessione a MySQL
    $link = require 'config_files/connect.php';

    //controllo che la sessione sia attiva
    if(!isset($_SESSION)) {
        session_start(); 
    }

    //controllo che nella query dell'url sia specificato il nome della categoria dove creare il post
    if(!array_key_exists('nome_categoria', $_GET)) {
        //se l'id non esiste nella query, allora rimando l'utente alla sua lista dei blog
        header("Location: index.php?p=1");
    } else {
        //altrimenti prendo in input l'id del blog da cancellare
        $nome_categoria = $_GET['nome_categoria'];
    }

    //prendo in input le informazione del blog, se non esiste restituisce false
    $categoria = estraiInfocategoria($link, $nome_categoria);
    //se il blog non esiste, ed è stato inserito un id non esistente nella query dell'url, rimando alla lista blog 
    if(count($categoria)==0) {
        header("Location: listaBlog.php");
    }

    $blogs = blogPerCategoria($link, $categoria['id_categoria']);

?>

<?php require 'config_files/header.php';?>

<?php require 'grafica/navbar.php';?>

<?php if ((count($blogs) != 0)) { ?>
    <h1 class="mt-5 mb-4 text-center"><?php echo $categoria['nome_categoria'] ?></h1>
    <div class="container w-100 mb-5 me-5 ms-5 d-flex flex-wrap align-items-center">
        <?php foreach ($blogs as $i => $b) : ?>  
            <div class="w-50 mt-2 mb-2" style="height:150px">
                <div style="margin-left: 10rem;" class="d-flex flex-column justify-content-left mb-1">
                    <p style class="display-6 fs-3"><a class="text-decoration-none" href="blog.php?id=<?php echo $b['id_blog'] ?>"><?php echo $b['nome_blog'] ?></a></p>
                    <p class="fs-6 text-secondary">progettoNew/blog.php?id=<?php echo $b['id_blog']?></p>
                    <p class="fs-5"><?php echo $b['descrizione_blog']?></p>
                </div>
            </div>
        <?php endforeach?>
    </div>
<?php } else { ?>
    <div class="container vh-100 w-100 mb-5 d-flex flex-column justify-content-center align-items-center" 
    style="margin-top: -72px;">
        <h2>Non esiste alcun blog in questa categoria.</h2>
        <a href="creazioneBlog.php" type="button" class="mt-3 btn btn-primary">Crea un nuovo blog</a>

    </div>
<?php } ?>




