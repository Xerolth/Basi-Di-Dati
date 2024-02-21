<?php

    //richiamo il file contenente tutte le query
    require 'config_files/query.php';

    //richiamo lo script responsabile della connessione a MySQL
    $link = require 'config_files/connect.php';

    //controllo che la sessione sia attiva
    if(!isset($_SESSION)) {
        session_start(); 
    }

    //controllo che nella query dell'url sia specificato l'id del post dove creare il post
    if(!array_key_exists('q', $_GET)) {
        //se l'id non esiste nella query, allora rimando l'utente alla sua lista dei blog
        header("Location: index.php?p=1");
    } else {
        $query = $_GET['q'];
    }
    
    if(is_string($query)) {
        try {
            //la query utilizza una UNION per combinare un query di ricerca del blog tramite autore del blog, nome del blog e categoria del blog.
            $stmt = $link->prepare("SELECT id_blog, nome_blog, descrizione_blog, autore_blog FROM blog WHERE nome_blog LIKE :nome_blog 
                UNION SELECT blog.`id_blog`, blog.`nome_blog`, blog.`descrizione_blog`, blog.`autore_blog`
                        FROM blog INNER JOIN utente 
                        ON blog.autore_blog = utente.id_utente
                        WHERE utente.nome_utente LIKE :nome_utente
                UNION SELECT blog.id_blog, blog.nome_blog, blog.descrizione_blog, blog.autore_blog
                        FROM blog 
                        INNER JOIN appartiene ON blog.id_blog = appartiene.id_blog
                        INNER JOIN categoria ON appartiene.id_categoria = categoria.id_categoria
                        WHERE categoria.nome_categoria LIKE :nome_categoria");

            $stmt->bindvalue(":nome_blog", '%'.$query.'%');
            $stmt->bindvalue(":nome_utente", '%'.$query.'%');
            $stmt->bindvalue(":nome_categoria", '%'.$query.'%');
           
            $stmt->execute();
            $blogs = $stmt->fetchAll(PDO::FETCH_ASSOC); 
        } catch (PDOException $e) {
            echo "Connessione Fallita: ".$e->getMessage();
        }

        try {
            //cerco i post per titolo
            $stmt = $link->prepare("SELECT * FROM post WHERE titolo LIKE :titolo");
            $stmt->bindvalue(":titolo", '%'.$query.'%');
           
            $stmt->execute();
            $posts = $stmt->fetchAll(PDO::FETCH_ASSOC); 
        } catch (PDOException $e) {
            echo "Connessione Fallita: ".$e->getMessage();
        }
    }

    //estraggo i dati dei post trovati con la ricerca e li salvo in un array di appoggio
    $post = [];
    foreach($posts as $i => $p) {
        $post[$i]['id'] = $p['id_post'];
        $post[$i]['copertina'] = $p['copertina'];
        $post[$i]['titolo'] = $p['titolo'];
        $post[$i]['descrizione'] = substr($p['testo_post'], 0, 200);
    }
?>

<?php require 'config_files/header.php';?>

<?php require 'grafica/navbar.php';?>

<div class="container w-100 mb-5 d-flex flex-wrap align-items-center">
    <!-- se ottengo dei risultati li mostro -->
    <?php if ((count($blogs) != 0)) { ?>
        <h1 class="mt-5 w-100 text-center">Blog</h1>
        <div class="w-100 d-flex">
            <?php foreach ($blogs as $i => $b) : ?>
                <div class="w-50 mt-4 mb-4">
                    <div style="margin-left: 10rem;" class="d-flex flex-column justify-content-left mb-1">
                        <p style class="display-6 fs-3"><a class="text-decoration-none"
                                href="blog.php?id=<?php echo $b['id_blog'] ?>"><?php echo $b['nome_blog'] ?></a></p>
                        <p class="fs-6 text-secondary">progettoNew/blog.php?id=<?php echo $b['id_blog']?></p>
                    </div>
                </div>
            <?php endforeach?>
        </div>
    <!-- altrimenti mostro il messaggio di errore -->
    <?php } else { ?>
        <h1 class="mt-5 w-100 text-center">Blog</h1>
        <div class="w-100 d-flex">
            <h5 class="w-100 text-center mt-4">Non esiste alcun blog con questo nome!</h5>
        </div>
       
    <?php } ?>

    <?php if ((count($post) != 0)) { ?>
        <h1 class="mt-5 mb-4 w-100 text-center">Post</h1>
        <div class="w-100 d-flex">
            <?php foreach ($post as $i => $p) : ?>
                <div class="w-50 d-flex flex-column align-items-center mb-3">
                <a href="post.php?id=<?php echo $p['id']?>" class="text-dark text-decoration-none">
                    <h2 class="mb-2">
                        <?php echo $p['titolo']?>
                    </h2>
                </a>

                <!-- rendo il div cliccabile attraverso l'inserimento di un elemento <a> reso alle stesse dimensioni del div che lo contiene -->
                <div class="w-75 mt-2" 
                    style="background-image: url('<?php echo $p['copertina']?>'); background-size: cover; height: 20em;">
                    <a style=" display: block;
                                height: 100%;
                                width: 100%;
                                text-decoration: none;" href="post.php?id=<?php echo $p['id']?>"></a>
                </div>

                <p class="w-75 mt-3">
                    <a href="post.php?id=<?php echo $p['id']?>" class="text-dark text-decoration-none">
                        <?php echo $p['descrizione']?>...
                    </a>
                </p>
            </div>
            <?php endforeach?>
        </div>
    <?php } else { ?>
        <h1 class="mt-5 w-100 text-center">Post</h1>
        <div class="w-100 d-flex">
            <h5 class="w-100 text-center mt-4">Non esiste alcun post con questo nome!</h5>
        </div>
    <?php } ?>
</div>