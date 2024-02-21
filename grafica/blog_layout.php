<div class="min-vh-100 <?php echo $info_tema['colore_sfondo']?> 
        <?php echo $info_tema['colore_font']?>">
    <div class="container d-flex flex-column align-items-center pt-5">
        <h1 class="display-1"><?php echo $dati_blog['nome_blog']?></h1>
        
        <?php if ($dati_blog['descrizione_blog']) { ?>
            <p class="text-center w-50 mt-3 "><?php echo $dati_blog['descrizione_blog']?></p>
        <?php } ?>

        <?php if (isset($_SESSION['id_user'])) { ?>
            <!-- Controllo che l'id dell'utente corrisponda all'id dell'autore del blog
            e controllo che l'username dell'utente sia contenuto nella lista dei coautori del blog -->
            <?php if ($_SESSION['id_user'] == $dati_blog['autore_blog'] 
                    || (in_array($_SESSION['user'], array_column($coautori, 'nome_utente')))) { ?>
                <a href="nuovoPost.php?id=<?php echo $_GET['id']?>">
                    <button type="button" class="btn <?php echo $info_tema['colore_bottoni']?> me-3 mb-5">Scrivi</button>
                </a>
            <?php } ?>
        <?php } ?>

        <?php foreach ($posts as $p) : ?>  
            <div class="w-75 d-flex flex-column align-items-center mb-3">
                <a href="post.php?id=<?php echo $p['id']?>" class="<?php echo $info_tema['colore_font']?>" style="text-decoration: none;">
                    <h5 class="display-6 mb-2">
                        <?php echo $p['titolo']?>
                        <!-- Controllo che l'utente sia loggato -->
                        <?php if (isset($_SESSION['id_user'])) { ?>
                            <!-- Controllo che l'id dell'utente corrisponda all'id dell'autore del blog
                            e controllo che l'username dell'utente sia contenuto nella lista dei coautori del blog -->
                            <?php if ($_SESSION['id_user'] == $dati_blog['autore_blog'] 
                                    || (in_array($_SESSION['user'], array_column($coautori, 'nome_utente')))) { ?>
                                <a href="cancellaPost.php?id=<?php echo $p['id']?>" class="text-decoration-none"><span class="fs-5 text-danger">x</span></a>
                            <?php } ?>
                        <?php } ?>
                    </h5>
                </a>

                <!-- Rendiamo il div cliccabile attraverso l'inserimento di un elemento <a> reso alle stesse dimensioni del div che lo contiene -->
                <div class="w-75 mt-2" 
                    style="background-image: url('<?php echo $p['copertina']?>'); background-size: cover; height: 20em;">
                    <a style=" display: block;
                                height: 100%;
                                width: 100%;
                                text-decoration: none;" href="post.php?id=<?php echo $p['id']?>"></a>
                </div>
               
            <p class="w-75 mt-3 overflow-hidden">
                <a href="post.php?id=<?php echo $p['id']?>" class="<?php echo $info_tema['colore_font']?>" style="text-decoration: none;">
                    <?php echo $p['descrizione']?>
                </a>
            </p>
        
            </div>
        <?php endforeach?>
    </div>
</div>