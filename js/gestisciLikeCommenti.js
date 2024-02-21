$('#commenta').on('click', function(e) { 
    e.preventDefault(); 
    let commento = $('#box-commento').val();
    let date_obj = new Date();
    let date = date_obj.getUTCFullYear() + "-" +  (date_obj.getUTCMonth() + 1) + "-" + date_obj.getUTCDate();
    let time = date_obj.getHours() + ":" + date_obj.getMinutes() + ":" + date_obj.getSeconds();
    let datetime = date + " " + time;

    const regex = /^[À-ÿA-Za-z0-9!?&$#%()\',.\-\"\s]+$/
    if (regex.test(commento)) {
        $.ajax({
            type: "GET",
            url: '', // La richiesta AJAX verrà fatta sulla stessa pagina. Es. Se l'url è /gestisciBlog.php?id=28 verà aggunto il paramentro cat 
            data: 'userinfo', 
            // Passiamo nella richiesta la categoria scelta, contenuta nella variabile dataString 
            // Nella querystring dell'url sarà presente il parametro "cat" e il suo valore
            dataType: "json", 
            // Se la richiesta AJAX ha successo, Richiamo la funzione inserisciCommento per effettuare la richiesta AJAX di tipo POST
            // Ed inserire il commento nel database
            success: function(data){
                inserisciCommento(data, commento, datetime);
            },
            error: function() {
                alert("Qualcosa è andato storto!");
            }
        });
    } else {
        alert("Attenzione, hai inserito dei caratteri non ammessi");
        commento = "";
    }

   
});

function inserisciCommento(data, commento, datetime) {
    if(data) {
        let id_user = data['id_user'];
        let username = data['user'];

        $.ajax({
            type: "POST",
            url: '', // La richiesta AJAX verrà fatta sulla stessa pagina. Es. Se l'url è /gestisciBlog.php?id=28 verà aggunto il paramentro cat 
            data: 'id_user=' + id_user + '&commento=' + commento,
            // Passiamo nella richiesta la categoria scelta, contenuta nella variabile dataString 
            // Nella querystring dell'url sarà presente il parametro "cat" e il suo valore
            // Se la richiesta AJAX ha successo, Richiamo la funzione inserisciCommento per effettuare la richiesta AJAX di tipo POST
            // Ed inserire il commento nel database
            success: function(data){
                $("#commenti").prepend(
                    "<div class='mb-4'>" +
                    "<p style='margin-bottom: 0.5em'><b>" + username + "</b></p>" +
                    "<p style='margin-bottom: 0.5em'>" + commento + "</p>" +
                    "<p style='font-size: 0.8em'>" + datetime + "</p>" +
                    "</div>"
                )
            },
            error: function() {
                alert("Qualcosa è andato storto!");
            }
        });
    }
}

$('.cancella-commento').on('click', function(e) { 
    e.preventDefault(); 
    // Prendo in input l'id del commento contenuto nell'href del tasto x per eliminare
    let id_commento = $(this).attr('href');

    // Effettuo un richiesta AJAX di tipo POST
    $.ajax({
        type: "POST",
        url: '', // La richiesta AJAX verrà fatta sulla stessa pagina. Es. Se l'url è /gestisciBlog.php?id=28
       // Passo i dati che saranno contneuti nell'array $_POST con la chiave cancellaCommento
        data: 'cancellaCommento=' + id_commento, 
        success: function(data){
            // Cancello, attraverso il metodo remove(), tutti i div che hanno un id che corrisponde all'id del commento
            // Specificato tra parentesi div[id='x']
            $('div[id="' + id_commento + '"]').remove();
        },
        error: function() {
            alert("Qualcosa è andato storto!");
        }
    });
});

$('#like').on('click', function(e) { 
    e.preventDefault(); 
    let like = $('#like').attr('src');
    let numlike = $('#numlike').text();
    

    if(like == 'imgs/cuore_vuoto.png') {
        $.ajax({
            type: "POST",
            url: '', // La richiesta AJAX verrà fatta sulla stessa pagina. Es. Se l'url è /gestisciBlog.php?id=28 verà aggunto il paramentro cat 
            data: 'like',
            // Passiamo nella richiesta la categoria scelta, contenuta nella variabile dataString 
            // Nella querystring dell'url sarà presente il parametro "cat" e il suo valore
            // Se la richiesta AJAX ha successo, Richiamo la funzione inserisciCommento per effettuare la richiesta AJAX di tipo POST
            // Ed inserire il commento nel database
            success: function(data){
                $("#like").attr("src", "imgs/cuore_pieno.png");
                $('#numlike').text(parseInt(numlike) + 1);
            },
            error: function() {
                alert("Qualcosa è andato storto!");
            }
        });
    } else {
        $.ajax({
            type: "POST",
            url: '', // La richiesta AJAX verrà fatta sulla stessa pagina. Es. Se l'url è /gestisciBlog.php?id=28 verà aggunto il paramentro cat 
            data: 'rimuoviLike',
            // Passiamo nella richiesta la categoria scelta, contenuta nella variabile dataString 
            // Nella querystring dell'url sarà presente il parametro "cat" e il suo valore
            // Se la richiesta AJAX ha successo, Richiamo la funzione inserisciCommento per effettuare la richiesta AJAX di tipo POST
            // Ed inserire il commento nel database
            success: function(data){
                $("#like").attr("src", "imgs/cuore_vuoto.png");
                $('#numlike').text(parseInt(numlike) - 1);
            },
            error: function() {
                alert("Qualcosa è andato storto!");
            }
        });
    }
   
    
});