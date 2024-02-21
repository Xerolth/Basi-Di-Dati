<?php
    function checkUsername($link, $username) {
        //controllo che lo username sia presente nel database
        $stmt = $link->prepare("SELECT nome_utente FROM utente WHERE nome_utente = ?");
        $stmt->execute([$username]);
        return $stmt->fetchColumn();
    }

    function checkMail($link, $mail) {
        //controllo che la mail sia presente nel database
        $stmt=$link->prepare("SELECT email FROM utente WHERE email = ?");
        $stmt->execute([$mail]);
        return $stmt->fetchColumn();
    }

    function checkDocumento($link, $documento) {
        //controllo che il documento non sia già in uso
        $stmt=$link->prepare("SELECT documento FROM utente WHERE documento = ?");
        $stmt->execute([$documento]);
        return $stmt->fetchColumn();
    }

    function checkTelefono($link, $telefono) {
        //controllo che il telefono non sia già in uso
        $stmt=$link->prepare("SELECT telefono FROM utente WHERE telefono = ?");
        $stmt->execute([$telefono]);
        return $stmt->fetchColumn();
    }

    //FUNZIONI LOGIN
    function findUser($link, $username) {
        $stmt=$link->prepare("SELECT id_utente, nome_utente, passhash, verificato FROM utente WHERE nome_utente=?");
        $stmt->execute(([$username]));
        //creo l'array associativo dei dati ottenuti
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // FUNZIONI BLOG
    function estraiTemi($link) {
        try {
            $stmt = $link->prepare("SELECT * FROM tema WHERE nome_tema NOT LIKE 'Personalizzato'");
            $stmt->execute();
            //poiché fetchAll restituisce un array che contiene un array associativo, 
            //specificando [0] prendo solo l'array associativo e quindi i dati dell'utente
            return $stmt->fetchAll(); 
        } catch (PDOException $e) {
            echo "Connessione Fallita: ".$e->getMessage();
        }
    }

    function estraiTema($link, $nome_tema) {
        try {
            $stmt=$link->prepare("SELECT * FROM tema WHERE nome_tema = ?");
            $stmt->execute([$nome_tema]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC)[0]; 
            
        } catch (PDOException $e) {
            echo "Connessione Fallita: ".$e->getMessage();
        }
    }

    function estraiListablog($link, $id_user) {
        try {
            $stmt=$link->prepare("SELECT * FROM blog WHERE autore_blog = ?");
            $stmt->execute([$id_user]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC); 
            
        } catch (PDOException $e) {
            echo "Connessione Fallita: ".$e->getMessage();
        }
    }

    function estraiPost($link, $id_blog) {
        try {
            $stmt=$link->prepare("SELECT * FROM post WHERE blog = ?");
            $stmt->execute([$id_blog]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC); 
            
        } catch (PDOException $e) {
            echo "Connessione Fallita: ".$e->getMessage();
        }
    }

    function estraiCoautori($link, $id_blog) {
        try {
            $stmt = $link->prepare("SELECT utente.id_utente, utente.nome_utente
            FROM utente INNER JOIN coautore
            ON utente.id_utente = coautore.id_utente
            WHERE coautore.id_blog = ?");
            $stmt->execute([$id_blog]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
          
        } catch (PDOException $e) {
            echo "Connessione Fallita: ".$e->getMessage();
        }
    }
        
    function estraiBlogcoautore($link, $id_utente) {
        try {
            $stmt = $link->prepare("SELECT blog.id_blog, blog.nome_blog, blog.descrizione_blog
            FROM coautore INNER JOIN blog
            ON coautore.id_blog = blog.id_blog
            WHERE coautore.id_utente = ?");
            $stmt->execute([$id_utente]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
          
        } catch (PDOException $e) {
            echo "Connessione Fallita: ".$e->getMessage();
        }
    }

    function datiPost($link, $id_post) {
        try {
            $stmt = $link->prepare("SELECT * FROM post WHERE id_post = ?");
            $stmt->execute([$id_post]);
            //poiché fetchAll restituisce un array che contiene un array associativo, 
            //specificando [0] prendo solo l'array associativo e quindi i dati dell'utente
            return $stmt->fetchAll(PDO::FETCH_ASSOC)[0]; 
        } catch (PDOException $e) {
            echo "Connessione Fallita: ".$e->getMessage();
        }
    }

    function estraiCommenti($link, $id_post) {
        try {
            $stmt = $link->prepare("SELECT utente.nome_utente, utente.id_utente, commento.id_commento, commento.testo_commento, commento.creazione_commento 
            FROM commento INNER JOIN utente ON commento.autore_commento=utente.id_utente 
            WHERE post = ? ORDER BY creazione_commento DESC");
            $stmt->execute([$id_post]);
            //poiché fetchAll restituisce un array che contiene un array associativo, 
            //specificando [0] prendo solo l'array associativo e quindi i dati dell'utente
            return $stmt->fetchAll(PDO::FETCH_ASSOC); 
        } catch (PDOException $e) {
            echo "Connessione Fallita: ".$e->getMessage();
        }
    }

    function verificaLike($link, $id_post, $id_utente) {
        try {   
            $stmt = $link->prepare("SELECT * FROM `like` WHERE id_post = :id_post AND id_utente = :id_utente");
            $stmt->bindvalue(":id_post", $id_post);
            $stmt->bindvalue(":id_utente", $id_utente);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC); 
        } catch (PDOException $e) {
            echo "Connessione Fallita: ".$e->getMessage();
        }
    }

    function numeroLike($link, $id_post) {
        try {   
            $stmt = $link->prepare("SELECT numlike FROM `post` WHERE id_post = ?");
            $stmt->execute([$id_post]);
            return $stmt->fetchColumn(); 
        } catch (PDOException $e) {
            echo "Connessione Fallita: ".$e->getMessage();
        }
    }


    // FUNZIONI GENERALI
    function datiUtente($link, $username) {
        try {
            $stmt = $link->prepare("SELECT * FROM utente WHERE nome_utente = ?");
            $stmt->execute([$username]);
            //poiché fetchAll restituisce un array che contiene un array associativo, 
            //specificando [0] prendo solo l'array associativo e quindi i dati dell'utente
            return $stmt->fetchAll(PDO::FETCH_ASSOC)[0]; 
        } catch (PDOException $e) {
            echo "Connessione Fallita: ".$e->getMessage();
        }
    }

    function datiUtenteId($link, $id_utente) {
        try {
            $stmt = $link->prepare("SELECT * FROM utente WHERE id_utente = ?");
            $stmt->execute([$id_utente]);
            //poiché fetchAll restituisce un array che contiene un array associativo, 
            //specificando [0] prendo solo l'array associativo e quindi i dati dell'utente
            return $stmt->fetchAll(PDO::FETCH_ASSOC)[0]; 
        } catch (PDOException $e) {
            echo "Connessione Fallita: ".$e->getMessage();
        }
    }


    function datiBlog($link, $id_blog) {
        try {
            $stmt = $link->prepare("SELECT * FROM blog WHERE id_blog = ?");
            $stmt->execute([$id_blog]);
            //poiché fetchAll restituisce un array che contiene un array associativo, 
            //specificando [0] prendo solo l'array associativo e quindi i dati dell'utente
            return $stmt->fetchAll(PDO::FETCH_ASSOC)[0];
        } catch (PDOException $e) {
            echo "Connessione Fallita: ".$e->getMessage();
        }
    }


    // FUNZIONI CATEGORIE
    function estraiCategorie($link) {
        try {
            $stmt = $link->prepare("SELECT * FROM categoria WHERE parent_categoria IS NULL");
            $stmt->execute();
            //poiché fetchAll restituisce un array che contiene un array associativo, 
            //specificando [0] prendo solo l'array associativo e quindi i dati dell'utente
            return $stmt->fetchAll(); 
        } catch (PDOException $e) {
            echo "Connessione Fallita: ".$e->getMessage();
        }
    }
    
    function estraiSottocategorie($link, $id_categoria) {
        try {
            $stmt = $link->prepare("SELECT * FROM `categoria` WHERE parent_categoria = ? ");
            $stmt->execute([$id_categoria]);
            //poiché fetchAll restituisce un array che contiene un array associativo, 
            //specificando [0] prendo solo l'array associativo e quindi i dati dell'utente
            return $stmt->fetchAll(PDO::FETCH_ASSOC); 
        } catch (PDOException $e) {
            echo "Connessione Fallita: ".$e->getMessage();
        }
    }

    function estraiInfocategoria($link, $nome_categoria) {
        try {
            $stmt = $link->prepare("SELECT * FROM categoria WHERE nome_categoria = ?");
            $stmt->execute([$nome_categoria]);
            return $stmt->fetchAll()[0];
          
        } catch (PDOException $e) {
            echo "Connessione Fallita: ".$e->getMessage();
        }
    }

    function categoriaBlog($link, $id_blog) {
        try {
            $stmt = $link->prepare("SELECT categoria.id_categoria, nome_categoria
            FROM appartiene INNER JOIN categoria ON appartiene.id_categoria = categoria.id_categoria 
            WHERE id_blog = ? AND categoria.parent_categoria IS NULL");
            $stmt->execute([$id_blog]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC)[0];
          
        } catch (PDOException $e) {
            echo "Connessione Fallita: ".$e->getMessage();
        }
    }

    function sottocategoriaBlog($link, $id_blog) {
        try {
            $stmt = $link->prepare("SELECT categoria.id_categoria, nome_categoria
            FROM appartiene INNER JOIN categoria ON appartiene.id_categoria = categoria.id_categoria 
            WHERE id_blog = ? AND categoria.parent_categoria IS NOT NULL");
            $stmt->execute([$id_blog]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC)[0];
          
        } catch (PDOException $e) {
            echo "Connessione Fallita: ".$e->getMessage();
        }
    }

    function blogPerCategoria($link, $id_categoria) {
        try {
            $stmt = $link->prepare("SELECT blog.id_blog, blog.nome_blog, blog.descrizione_blog
            FROM appartiene INNER JOIN blog ON appartiene.id_blog = blog.id_blog 
            WHERE id_categoria = ?");
            $stmt->execute([$id_categoria]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC); 
        } catch (PDOException $e) {
            echo "Connessione Fallita: ".$e->getMessage();
        }
    }


    

    

    
    