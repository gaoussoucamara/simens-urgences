
var temoinTaille = 0;
var temoinPoids = 0;
var temoinTemperature = 0;
var temoinTensionMaximale = 0;
var temoinTensionMinimale = 0;

function maskDeSaisie(){
    $(function(){
    	$("#pressionarterielle").mask("299/299");
    	$("#glycemie_capillaire").mask("9,99");
    });
    
    $("#poids").blur(function(){
    	var valeur = $('#poids').val();
    	if(isNaN(valeur/1) || valeur > 300 || valeur == ""){
			valeur = null;
			$("#poids").css("border-color","#FF0000");
			$("#erreur_poids").fadeIn().text("Max: 300kg").css({"color":"#ff5b5b","padding":" 0 10px 0 10px","margin-top":"-18px","font-size":"13px","font-style":"italic"});
			temoinPoids = 2;
    	} 
    	else{
    		$("#poids").css("border-color","");
			$("#erreur_poids").fadeOut();
			temoinPoids = 0;
    	}
    	return false;
    });
    
    $("#temperature").blur(function(){
    	var valeur = $('#temperature').val();
    	if(isNaN(valeur/1) || valeur > 45 || valeur < 34  || valeur == ""){
			$("#temperature").css("border-color","#FF0000");
    		$("#erreur_temperature").fadeIn().text("Min: 34°C, Max: 45°C").css({"color":"#ff5b5b","padding":" 0 10px 0 10px","margin-top":"-18px","font-size":"13px","font-style":"italic"});
			temoinTemperature = 3;
    	} 
    	else{
    		$("#temperature").css("border-color","");
			$("#erreur_temperature").fadeOut();
			temoinTemperature = 0;
    	}
    	return false;
    });
    
    $("#tensionmaximale").blur(function(){
    	var valeur = $('#tensionmaximale').val();
		if(isNaN(valeur/1) || valeur > 300 || valeur == ""){
			$("#tensionmaximale").css("border-color","#FF0000");
    		$("#erreur_tensionmaximale").fadeIn().text("300mmHg").css({"color":"#ff5b5b","padding":" 0 10px 0 10px","margin-top":"-18px","font-size":"13px","font-style":"italic"});
			temoinTensionMaximale = 5;
		}else{
			$("#tensionmaximale").css("border-color","");
			$("#erreur_tensionmaximale").fadeOut();
			temoinTensionMaximale = 0;
		}
    });
    
    $("#tensionminimale").blur(function(){
    	var valeur = $('#tensionminimale').val();
		if(isNaN(valeur/1) || valeur > 200 || valeur == ""){
			$("#tensionminimale").css("border-color","#FF0000");
    		$("#erreur_tensionminimale").fadeIn().text("200mmHg").css({"color":"#ff5b5b","padding":" 0 10px 0 105px","margin-top":"-18px","font-size":"13px","font-style":"italic"});
			temoinTensionMinimale = 6;
		}else{
			$("#tensionminimale").css("border-color","");
			$("#erreur_tensionminimale").fadeOut();
			temoinTensionMinimale = 0;
		}
    });
}            

function supprimer_dernier_caractere(elm) {
	  var val = $(elm).val();
	var cursorPos = elm.selectionStart;
	$(elm).val(
	   val.substr(0,cursorPos-1) + // before cursor - 1
	  val.substr(cursorPos,val.length) // after cursor
	);
	elm.selectionStart = cursorPos-1; // replace the cursor at the right place
	elm.selectionEnd = cursorPos-1;
}

$(function() { 
    $( "button" ).button();

    	 /****** CONTROLE APRES VALIDATION ********/ 
    	 /****** CONTROLE APRES VALIDATION ********/ 

    	 	 var valid = true;  // VARIABLE GLOBALE utilis�e dans 'VALIDER LES DONNEES DU TABLEAU DES CONSTANTES'

    	 	 $("#bouton_constantes_valider").click(function(){

    	     	 valid = true;
    	         
    	         if( $("#poids").val() == "" || temoinPoids == 2){
    	         	 $("#poids").css("border-color","#FF0000");
    	             $("#erreur_poids").fadeIn().text("Max: 300kg").css({"color":"#ff5b5b","padding":" 0 10px 0 10px","margin-top":"-18px","font-size":"13px","font-style":"italic"});
    	             valid = false;
    	         }
    	         else{
    	         	$("#poids").css("border-color", "");
    	         	$("#erreur_poids").fadeOut();
    	         }
    	         
    	         if( $('#temperature').val() == "" || temoinTemperature == 3){
    	         	$("#temperature").css("border-color","#FF0000");
    	         	$("#erreur_temperature").fadeIn().text("Min: 34°C, Max: 45°C").css({"color":"#ff5b5b","padding":" 0 10px 0 10px","margin-top":"-18px","font-size":"13px","font-style":"italic"});
    	             valid = false;
    	         }
    	         else{
    	         	$("#temperature").css("border-color", "");
    	         	$("#erreur_temperature").fadeOut();
    	         }
    	         
    	         if( $("#tensionmaximale").val() == "" || temoinTensionMaximale == 5){
    	         	 $("#tensionmaximale").css("border-color","#FF0000");
    		    	 $("#erreur_tensionmaximale").fadeIn().text("300mmHg").css({"color":"#ff5b5b","padding":" 0 10px 0 10px","margin-top":"-18px","font-size":"13px","font-style":"italic"});
    	             valid = false;
    	         }
    	         else{
    	         	 $("#tensionmaximale").css("border-color", "");
    	             $("#erreur_tensionmaximale").fadeOut();
    	         }
    	         
    	         if( $("#tensionminimale").val() == "" || temoinTensionMinimale == 6 ){
    	         	 $("#tensionminimale").css("border-color","#FF0000");
    		    	 $("#erreur_tensionminimale").fadeIn().text("200mmHg").css({"color":"#ff5b5b","padding":" 0 10px 0 105px","margin-top":"-18px","font-size":"13px","font-style":"italic"});
    	             valid = false;
    	         }
    	         else{
    	         	 $("#tensionminimale").css("border-color", "");
    	             $("#erreur_tensionminimale").fadeOut();
    	         }
    	         
    	         return false;
   }); 
      	 	 	
   //******************* VALIDER LES DONNEES DU TABLEAU DES MOTIFS ******************************** 
   //******************* VALIDER LES DONNEES DU TABLEAU DES MOTIFS ******************************** 
  			   


  	
 //******************* VALIDER LES DONNEES DU TABLEAU DES CONSTANTES ******************************** 
 //******************* VALIDER LES DONNEES DU TABLEAU DES CONSTANTES ******************************** 

   //Au debut on d�sactive le code cons et la date de consultation qui sont non modifiables
  	var id_cons = $("#id_cons");
  	var date_cons = $("#date_cons");
  	id_cons.attr('readonly',true);
  	date_cons.attr('readonly',true);

  	var poids = $('#poids');
  	var taille = $('#taille');
  	var tension = $('#tension');
  	var bu = $('#bu');
  	var temperature = $('#temperature');
  	var glycemie_capillaire = $('#glycemie_capillaire');
  	var pouls = $('#pouls');
  	var frequence_respiratoire = $('#frequence_respiratoire');
  	var pressionarterielle = $("#pressionarterielle");
  	
	  //Au debut on cache le bouton modifier et on affiche le bouton valider
  	$( "#bouton_constantes_valider" ).toggle(true);
  	$( "#bouton_constantes_modifier" ).toggle(false);

  	//Au debut on active tous les champs
  	poids.attr( 'readonly', false ).css({'background':'#fff'});
  	taille.attr( 'readonly', false ).css({'background':'#fff'});
  	tension.attr( 'readonly', false).css({'background':'#fff'}); 
  	bu.attr( 'readonly', false).css({'background':'#fff'});  
  	temperature.attr( 'readonly', false).css({'background':'#fff'}); 
  	glycemie_capillaire.attr( 'readonly', false).css({'background':'#fff'});
  	pouls.attr( 'readonly', false).css({'background':'#fff'});
  	frequence_respiratoire.attr( 'readonly', false).css({'background':'#fff'});
  	pressionarterielle.attr( 'readonly', false ).css({'background':'#fff'});

  	$( "#bouton_constantes_valider" ).click(function(){
  		if(valid == true){
	   		poids.attr( 'readonly', true ).css({'background':'#f8f8f8'});    
	   		taille.attr( 'readonly', true ).css({'background':'#f8f8f8'});
	   		tension.attr( 'readonly', true).css({'background':'#f8f8f8'});
	   		bu.attr( 'readonly', true).css({'background':'#f8f8f8'});
	   		temperature.attr( 'readonly', true).css({'background':'#f8f8f8'});
	   		glycemie_capillaire.attr( 'readonly', true).css({'background':'#f8f8f8'});
	   		pouls.attr( 'readonly', true).css({'background':'#f8f8f8'});
	   		frequence_respiratoire.attr( 'readonly', true).css({'background':'#f8f8f8'});
	   		pressionarterielle.attr( 'readonly', true ).css({'background':'#f8f8f8'});
	   		
  		    $("#bouton_constantes_modifier").toggle(true);  //on affiche le bouton permettant de modifier les champs
  		    $("#bouton_constantes_valider").toggle(false); //on cache le bouton permettant de valider les champs
  		}
  		return false; 
  	});
  	
  	$( "#bouton_constantes_modifier" ).click(function(){
  		poids.attr( 'readonly', false ).css({'background':'#fff'});
  		taille.attr( 'readonly', false ).css({'background':'#fff'}); 
  		tension.attr( 'readonly', false).css({'background':'#fff'}); 
  		bu.attr( 'readonly', false).css({'background':'#fff'});
  		temperature.attr( 'readonly', false).css({'background':'#fff'});
  		glycemie_capillaire.attr( 'readonly', false).css({'background':'#fff'});
  		pouls.attr( 'readonly', false).css({'background':'#fff'});
  		frequence_respiratoire.attr( 'readonly', false).css({'background':'#fff'});
  		pressionarterielle.attr( 'readonly', false ).css({'background':'#fff'});
  		
  	 	$("#bouton_constantes_modifier").toggle(false);   //on cache le bouton permettant de modifier les champs
  	 	$("#bouton_constantes_valider").toggle(true);    //on affiche le bouton permettant de valider les champs
  	 	return  false;
  	});


 //******************* VALIDER LES DONNEES DU TABLEAU DES CONSTANTES ******************************** 
//******************* VALIDER LES DONNEES DU TABLEAU DES CONSTANTES ******************************** 
});
//Boite de dialogue de confirmation d'annulation
//Boite de dialogue de confirmation d'annulation
//Boite de dialogue de confirmation d'annulation

/***BOITE DE DIALOG POUR LA CONFIRMATION DE SUPPRESSION**/
$(document).ready(function() {
	var base_url = window.location.toString();
	var tabUrl = base_url.split("public");
	
	var theHREF = tabUrl[0]+"public/consultation/recherche";
	function confirmation(){
		
  		$( "#confirmation" ).dialog({
  		    resizable: false,
  		    height:170,
  		    width:505,
  		    autoOpen: false,
  		    modal: true,
  		    buttons: {
  		        "Oui": function() {
  		            $( this ).dialog( "close" );
  		            window.location.href = theHREF;   
  		        },
  		        "Non": function() {
  		            $( this ).dialog( "close" );
  		        }
  		    }
  		});
}
	
	$("#annuler").click(function() {
        event.preventDefault(); 
        confirmation(); 
        $("#confirmation").dialog('open');
    }); 

});

