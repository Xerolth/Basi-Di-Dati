<div class="min-vh-100 <?php echo $info_tema['colore_sfondo']?> 
        <?php echo $info_tema['colore_font']?>">

    <div class="w-100 d-flex justify-content-center align-items-center" style="background-image: url('<?php echo $dati_post['copertina']?>'); background-size: cover; height: 30em;">
        <div class="w-100 d-flex justify-content-center align-items-center" style="background-size: cover; height: 30em; background-color: #000; opacity: 0.5;">
            <h1 class="display-1 text-light"><?php echo $dati_post['titolo']?></h1>
        </div>
    </div>

    <div class="container d-flex flex-column align-items-center w-50 pt-4 pb-2">
        <div class="d-flex justify-content-center align-items-end">
            <p class="ms-4 me-4">Di <?php echo $dati_utente['nome_utente']?></p>
            <p class="ms-4 me-4"><?php echo $dati_post['creazione_post']?></p>
            <p class="ms-4 me-4">Su <a class="text-decoration-none <?php echo $info_tema['colore_font']?>" href="blog.php?id=<?php echo $dati_blog['id_blog']?>"><?php echo $dati_blog['nome_blog']?></a></p>
            <p class="ms-4 me-4">
                    <?php if (isset($_SESSION['user'])) { ?>
                    <span class="d-flex">
                        <?php if ($like) { ?>
                            <img id="like" class="w-75" style="cursor:pointer" src="imgs/cuore_pieno.png" alt="Cuore pieno">
                        <?php } else {?>  
                            <img id="like" class="w-75" style="cursor:pointer" src="imgs/cuore_vuoto.png" alt="Cuore vuoto">
                        <?php } ?>
                        <span class="ms-2" id="numlike"><?php echo $num_like ?></span>
                    </span>
                <?php } else {?>
                    <span class="d-flex ">
                        <a href="registrazione.php">
                            <img class="w-75" style="cursor:pointer" src="imgs/cuore_vuoto.png" alt="Cuore vuoto">
                        </a>
                        <?php echo $num_like ?>
                    </span>
                <?php } ?>
            </p>
        </div>
        <div class="w-100 mt-2 mb-4 overflow-hidden" style="line-height: 1.8em">
            <?php foreach ($paragrafi as $p) : ?>
            <?php echo'<p>' . $p . '</p>'?>
            <?php endforeach?>
        </div>

        <div class="d-flex justify-content-center mb-5">
            <img src="<?php echo $dati_post['immagine']?>" class="w-50" alt="">
        </div>

        <?php if (isset($_SESSION['user'])) { ?>
        <div class="form-floating w-100" style="margin-bottom: 2.5em">
            <textarea class="form-control" placeholder="Commenta il post" id="box-commento"
                style="height: 100px"></textarea>
            <label for="box-commenti" style="color: #000">Commenta il post</label>
            <button class="btn <?php echo $info_tema['colore_bottoni']?> mt-2" id="commenta">Commenta</button>
        </div>
        <?php } ?>

        <div class="w-100" id="commenti">
            <?php foreach ($commenti as $c) : ?>
                <div class="mb-4" id="<?php echo $c['id_commento']?>">
                    <p style="margin-bottom: 0.5em">
                        <b><?php echo $c['nome_utente']?></b>
                        <?php if (isset($_SESSION['user'])) { ?>
                            <?php if ($_SESSION['user'] == $c['nome_utente'] ||
                                $_SESSION['id_user'] == $dati_post['autore_post'] ||
                                $_SESSION['id_user'] == $dati_blog['autore_blog'] ||
                                (in_array($_SESSION['user'], array_column($coautori, 'nome_utente')))) { ?>
                                    <a href="<?php echo $c['id_commento']?>" class="cancella-commento text-decoration-none">
                                        <span class="text-danger">x</span>
                                    </a>
                            <?php } ?>
                        <?php } ?>
                        
                    </p>
                    <p style="margin-bottom: 0.5em"><?php echo $c['testo_commento']?></p>
                    <p style="font-size: 0.8em"><?php echo $c['creazione_commento']?></p>
                </div>
            <?php endforeach?>
        </div>

    </div>
</div>