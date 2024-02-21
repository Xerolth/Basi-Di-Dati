<?php
    //richiamo il file contenente tutte le query
    require 'config_files/query.php';

    //richiamo lo script responsabile della connessione a MySQL
    $link = require 'config_files/connect.php';
 
    //controllo che la sessione sia attiva
    if(!isset($_SESSION)) {
        session_start(); 
    }

    //verifica che l'url index.php abbia associato abbia il paramentro p, che rappresenta la pagina dell'homepage
    if(!array_key_exists('p', $_GET)) {
        //reindirizziamo l'utente sulla prima pagina dell'homepage, aggiungedo il parametro p=1
        header("Location: index.php?p=1"); 
    } else {
        //se il valore p, che specifica la pagina, è presente, allora lo prendo in input e lo trasformo in un int
        $p = intval($_GET['p']); 
    }

    //definisco il numero di post per pagina da mostrare
    $post_pagina = 5;
    //definisco il limite che corrisponde all'indice iniziale dalla quale inizierò a mostrare i post 
    //lo calcolo sottraendo 1 al numero attuale di pagina e moltiplicando il valore per il numero di post totali
    //da mostrare nella pagina
    $limite = ($p-1) * $post_pagina; 

    //estraiamo n record, con n = $post_pagina, che partono dall'indice $limite, definito precedentemente
    try {   
        $stmt = $link->prepare("SELECT * FROM post ORDER BY creazione_post DESC LIMIT :limite, :post_pagina");
        //PDO::PARAM_INT è una costante definita nella classe PDO di PHP, 
        //che viene utilizzata per indicare all'oggetto PDO di trattare un valore come un intero 
        //durante l'esecuzione di una query parametrizzata. https://www.php.net/manual/en/pdo.constants.php
        $stmt->bindvalue(":limite", intval($limite), PDO::PARAM_INT);
        $stmt->bindvalue(":post_pagina", intval($post_pagina), PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC); 
    } catch (PDOException $e) {
        echo "Connessione Fallita: ".$e->getMessage();
    }

    //calcoliamo il numero totale dei post presenti nell'intero sito
    try {   
        $stmt = $link->prepare("SELECT COUNT(id_post) FROM post");
        $stmt->execute();
        $totale_post = $stmt->fetchColumn(); 
    } catch (PDOException $e) {
        echo "Connessione Fallita: ".$e->getMessage();
    }
    
    //calcolo il numero di indici delle pagine della homepage, dividendo il numero totale di post per i post per pagina e arrotondando per eccesso
    $totale_pagine = ceil($totale_post / $post_pagina);

    //estraggo i dati dei post della pagina e li salvo in un array di appoggio
    $posts = [];
    foreach($data as $i => $p) {
        $posts[$i]['id'] = $p['id_post'];
        $posts[$i]['copertina'] = $p['copertina'];
        $posts[$i]['titolo'] = $p['titolo'];
        if (strlen($p['testo_post']) > 300) {
            $posts[$i]['descrizione'] = substr($p['testo_post'], 0, 300);
            $posts[$i]['descrizione'] = $posts[$i]['descrizione'] . "...";
        } else {
            $posts[$i]['descrizione'] = $p['testo_post'];
        }
    }
?>

<?php require 'config_files/header.php';?>

<?php require 'grafica/navbar.php';?>

<div class="container w-100 mt-5 mb-5 d-flex flex-wrap align-items-center">

    <?php foreach($posts as $p) : ?>
        <div class="w-100 d-flex flex-column align-items-center mb-5">
                <a href="post.php?id=<?php echo $p['id']?>" class="text-dark text-decoration-none">
                    <h2 class="mb-2"><?php echo $p['titolo']?></h2>
                </a>

                <!-- rendo il div cliccabile attraverso l'inserimento di un elemento <a> reso alle stesse dimensioni del div che lo contiene -->
                <div class="w-75 mt-2" 
                    style="background-image: url('<?php echo $p['copertina']?>'); background-size: cover; height: 25em;">
                    <a style=" display: block;
                                height: 100%;
                                width: 100%;
                                text-decoration: none;" href="post.php?id=<?php echo $p['id']?>"></a>
                </div>

                <p class="w-75 mt-3 overflow-hidden">
                    <a href="post.php?id=<?php echo $p['id']?>" class="text-dark text-decoration-none">
                        <?php echo $p['descrizione']?>
                    </a>
                </p>
        </div>
    <?php endforeach ?>

    <div class="d-flex w-100 justify-content-center">
        <h5>Pagina</h5>
        <!-- mostro gli indici delle pagine -->
        <?php for ($i = 1; $i <= $totale_pagine; $i++) : ?>
        <a class="ms-2" href="?p=<?php echo $i ?>"><?php echo $i ?></a>
        <?php endfor?>
    </div>
</div>