$("#mod-Nome").click(function (e) {
    e.preventDefault(); 
    
    $(".sezione-nome").append(
        "<form class='modifica-nome mt-3 w-100' method='post'>" +
        "<label for='exampleFormControlInput1' class='form-label'>Modifica Nome</label>" +
        "<input name='nome' type='text' class='form-control' id='exampleFormControlInput1'>" +
        "<form class='w-100' method='post'> <label for='exampleFormControlInput1' class='form-label'>Modifica Cognome</label>" +
        "<input name='cognome' type='text' class='form-control' id='exampleFormControlInput1'>" +
        "<button type='submit' class='mt-3 btn btn-primary'>Modifica</button>" +
        "<a id='chiudi-ModNome' class='mt-3 ms-2 btn btn-danger'>Chiudi</a>" +
        "</form>"
    );
        
    $(this).css("display", "none");
   
    $("#chiudi-ModNome").click(function (e) {
        e.preventDefault(); 
        $(".modifica-nome").remove();
        
        $("#mod-Nome").css("display", "inline-block");
       
    });

});

$("#mod-descrizione").click(function (e) {
    e.preventDefault(); 

    // Creo una variabile che contiene la vecchia descrizione dell'utente e ripulisco il contenuto da eventuali whitespaces
    const desc =  $.trim($("#descrizione-utente").text());     

    $("#descrizione-utente").css("display", "none");

    $(".sezione-descrizione").append(
        "<form class='modifica-descrizione mt-3 w-100' method='post'>" +
        "<label for='campo-descrizione' class='form-label'>Descrizione</label>" +
        "<textarea name='descrizione' rows='5' cols='25' maxlength='240' class='form-control' id='campo-descrizione'>" +
        desc +
        "</textarea>" +
        // centro pulsante
        "<button type='submit' class='mt-3 btn btn-primary'>Inserisci</button>" +
        "<a id='chiudi-mod-descrizione' class='mt-3 ms-2 btn btn-danger'>Chiudi</a>" +
        "</form>"
    );
        
    $(this).css("display", "none");
   
    $("#chiudi-mod-descrizione").click(function (e) {
        e.preventDefault(); 
        $(".modifica-descrizione").remove();
        $("#descrizione-utente").css("display", "inline-block");
        $("#mod-descrizione").css("display", "inline-block");
       
    });

});