$(document).ready(function(){  
	$('#Categorie').on('change', function() {    
		// Quando c'è un cambiamento nel menù a tendina, seleziono il valore selezionato, che ha l'attributo "selected"
		let categoria = $(this).find(":selected").val();
		console.log(categoria);

		$.ajax({
			type: "GET",
			url: '', // La richiesta AJAX verrà fatta sulla stessa pagina. Es. Se l'url è /gestisciBlog.php?id=28 verrà aggunto il paramentro cat 
			data: 'cat=' + categoria, 
			// Passiamo nella richiesta la categoria scelta, contenuta nella variabile dataString 
			// Nella querystring dell'url sarà presente il parametro "cat" e il suo valore
			dataType: "json", 
			// Se la richiesta AJAX ha successo, lanciamo la seguente funzione
			success: function(data) {
				// la variabile data contiene un array json con i nomi delle sottocategorie
			   	if(data) {
                    $("#Sottocategorie").empty(); // Svuota il select da tutti gli input precedenti
					// Per ogni elemento dell'array json, appendo un elemento option al select delle sottocategorie
                    data.forEach(element => {
                        $('#Sottocategorie').append(`<option value="${element}">${element}</option>`);
                    });
				}
			}, 
			error: function() {
				alert("Qualcosa è andato storto, la categoria non esiste!");
			}
		});
 	}) 
});