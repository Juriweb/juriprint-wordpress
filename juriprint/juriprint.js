/* global wc_checkout_params */

/* checkout.js avec des modifs */

jQuery( function( $ ) {

    // affichage des <select> de dimensions selon le <select> forme pour le produit tampon
	$("#product-2575 form.variations_form table.variations select#pa_forme").change(function(){
        if($(this).val() == '') {
        	 $("#product-2575 form.variations_form table.variations tr:nth-child(2)").hide();
        	 $("#product-2575 form.variations_form table.variations select#pa_forme").prop('selectedIndex',0);
        	 $("#product-2575 form.variations_form table.variations select#pa_dimensions").prop('selectedIndex',0);
        } else {
            $("#product-2575 form.variations_form table.variations tr:nth-child(2)").show();
        }
    });

    // hide "Choisir une option" sur les <select> options
	$('table.variations select').each(function () {
	  	$(this).children('option:first').attr('hidden','hidden');
        // Ajout de la class "custom-select-option" sur les <option> d'un <select> pour faire la mise en forme
	  	$(this).children('option').addClass('custom-select-option');
	});

    // Regex et controle pour le champ numéro de téléphone sur les fiches produit
    $('.num-tel-rappel').keyup(function() {
        var val = this.value.replace(/\D/g, '');
        var newVal = '';
        while (val.length > 2 && val.length < 11) {
          newVal += val.substr(0, 2) + '.';
          val = val.substr(2);
        }
        newVal += val;
        this.value = newVal;
    });
    $('.num-tel-rappel').attr('maxlength','14');

});
