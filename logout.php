<?php
    session_start();
    //distrugge la session dell'utente
	session_unset();
	session_destroy();
    //ritorna alla pagina di login
	header("Location: index.php?p=1");
    exit;
?>