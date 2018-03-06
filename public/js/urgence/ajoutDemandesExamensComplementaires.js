var $isuppEC = 1;
function creerLalisteActeEC ($listeDesElements) {
    	var index = $("LesActesEC").length; 
			     var  $liste = "<div id='Acte_EC_"+(index+1)+"'>"+
				             "<LesActesEC>"+
				             "<table class='table table-bordered' id='ExamenEC' style='margin-bottom: 0px; width: 100%;'>"+
                             "<tr style='width: 100%;'>" +
                             
                             "<th style='width: 4%;'>"+
                             "<label style='width: 100%; margin-top: 10px; margin-left: 5px; font-weight: bold; font-family: police2; font-size: 14px;' >"+(index+1)+"<span id='element_label'></span></label>" +
                             "</th >"+
                             
                             
                             "<th id='SelectTypeAnalyse_EC"+(index+1)+"' style='width: 32%;'>"+
                             "<select  onchange='getListeAnalysesEC(this.value,"+(index+1)+")'  style='width: 100%; margin-top: 3px; margin-bottom: 0px; font-size: 16px;' name='type_analyse_name_ec_"+(index+1)+"' id='type_analyse_name_ec_"+(index+1)+"' class='type_analyse_name_ec_"+(index+1)+"'>"+
			                 "<option value='' > --- S&eacute;l&eacute;ctionner un type ---  </option>";
                             for(var i = 1 ; i < $listeDesElements.length ; i++){
                            	 if($listeDesElements[i]){
                    $liste +="<option value='"+i+"'>"+$listeDesElements[i]+"</option>";
                            	 }
                             }   
                    $liste +="</select>"+                           
                             "</th>"+
                             
                             
                             "<th id='SelectAnalyse_EC"+(index+1)+"'  style='width: 55%;'  >"+
                             "<select style='width: 100%; margin-top: 3px; margin-bottom: 0px; font-size: 16px;' name='analyse_name_ec_"+(index+1)+"' id='analyse_name_ec_"+(index+1)+"' class='analyse_name_ec_"+(index+1)+"'>";
                    $liste +="</select>"+
                             
                             "</th >"+
                             
                             
                             "<th id='iconeActe_supp_vider' style='width: 9%;'  >"+
                             "<a id='supprimer_acte_selectionne_ec_"+ (index+1) +"'  style='width:50%;' >"+
                             "<img class='supprimerActeEC' style='margin-left: 5px; margin-top: 10px; cursor: pointer;' src='../images/images/sup.png' title='supprimer' />"+
                             "</a>"+
                             
                             "<a id='vider_analyse_selectionne_ec_"+ (index+1) +"'  style='width:30%;' >"+
                             "<img class='viderActeEC' style='margin-left: 15px; margin-top: 10px; cursor: pointer;' src='../images_icons/gomme.png' title='vider' />"+
                             "</a>"+
                             "<span id='analyse_effectuee_"+ (index+1) +"'  style='display: none;'>"+
                             "<img  style='margin-left: 10px; margin-top: 10px; cursor: pointer;' src='../images_icons/tick_16.png' title='analyse r&eacute;alis&eacute;e' />"+
                             "</span>"+
                             "</th >"+
                             
                             
                             "</tr>" +
                             "</table>" +
                             "</LesActesEC>" +
                             "</div>"+
                             
                             
                             "<script>"+
                                "$('#supprimer_acte_selectionne_ec_"+ (index+1) +"').click(function(){ " +
                                		"supprimer_acte_selectionne_ec("+ (index+1) +"); });" +
                                				
                                "$('#vider_analyse_selectionne_ec_"+ (index+1) +"').click(function(){ " +
                                		"vider_analyse_selectionne_ec("+ (index+1) +"); });" +
                             "</script>";
                    
                    //AJOUTER ELEMENT SUIVANT
                    $("#Acte_EC_"+index).after($liste);
                    
                    //CACHER L'ICONE AJOUT QUAND ON A CINQ LISTES
                    if((index+1) == 20){
                    	$("#ajouter_acte_ec").toggle(false);
                    }
                    
                    //AFFICHER L'ICONE SUPPRIMER QUAND ON A DEUX LISTES ET PLUS
                    if((index+1) == ($isuppEC+1)){
                    	$("#supprimer_acte_ec").toggle(true);
                    }
}


//NOMBRE DE LISTE AFFICHEES
function nbListeActeEC () {
	return $("LesActesEC").length;
}

//SUPPRIMER LE DERNIER ELEMENT
$(function () {
	//Au debut on cache la suppression
	$("#supprimer_acte_ec").click(function(){
		//ON PEUT SUPPRIMER QUAND C'EST PLUS DE DEUX LISTE
		if(nbListeActeEC () >  $isuppEC){ $("#Acte_EC_"+nbListeActeEC ()).remove(); }
		//ON CACHE L'ICONE SUPPRIMER QUAND ON A UNE LIGNE
		if(nbListeActeEC () == $isuppEC){
			$("#supprimer_acte_ec").toggle(false);
			$(".supprimerActeEC" ).replaceWith(
			  "<img class='supprimerActeEC' style='margin-left: 5px; margin-top: 10px;' src='../images/images/sup2.png' />"
			);
		}
		//Afficher L'ICONE AJOUT QUAND ON A 20 LIGNES
		if((nbListeActeEC()+1) == 20){
			$("#ajouter_acte_ec").toggle(true);
		}   
		
		Event.stopPropagation();
	});
});


var entreEC = 1;
//FONCTION INITIALISATION (Par d�faut)
function partDefautActeEC (Liste, n) {
	var i = 0;
	for( i ; i < n ; i++){
		creerLalisteActeEC(Liste);
	}
	if(n == 1){
		$(".supprimerActeEC" ).replaceWith(
				"<img class='supprimerActeEC' style='margin-left: 5px; margin-top: 10px;' src='../images/images/sup2.png' />"
			);
	}
	
	if(entreEC == 1){
		$('#ajouter_acte_ec').click(function(){
			creerLalisteActeEC(Liste);
			if(nbListeActeEC() == 2){
			$(".supprimerActeEC" ).replaceWith(
					"<img class='supprimerActeEC' style='margin-left: 5px; margin-top: 10px; cursor: pointer;' src='../images/images/sup.png' title='Supprimer' />"
			);
			}
		});
		entreEC = 0;
	}

	//AFFICHER L'ICONE SUPPRIMER QUAND ON A DEUX LISTES ET PLUS
    if(nbListeActeEC () > 1){
    	$("#supprimer_acte_ec").toggle(true);
    } else {
    	$("#supprimer_acte_ec").toggle(false);
      }
}

//SUPPRIMER ELEMENT SELECTIONNER
function supprimer_acte_selectionne_ec(id) {

	for(var i = (id+1); i <= nbListeActeEC(); i++ ){
		
		var element = $('#SelectTypeAnalyse_EC'+i+' select').val(); 
		$('#SelectTypeAnalyse_EC'+(i-1)+' select').val(element);
		
		var element2 = $("#SelectAnalyse_EC"+i+" select").val();
		var liste2 = $("#SelectAnalyse_EC"+i+" select").html();
		$("#SelectAnalyse_EC"+(i-1)+" select").html(liste2);
		$("#SelectAnalyse_EC"+(i-1)+" select").val(element2);;
		
	}

	if(nbListeActeEC() <= 2 && id <= 2){
		$(".supprimerActeEC" ).replaceWith(
			"<img class='supprimerActeEC' style='margin-left: 5px; margin-top: 10px;' src='../images/images/sup2.png' />"
		);
	}
	if(nbListeActeEC() != 1) {
		$('#Acte_EC_'+nbListeActeEC()).remove();
	}
	if(nbListeActeEC() == 1) {
		$("#supprimer_acte_ec").toggle(false);
	}
	if((nbListeActeEC()+1) == 20){
		$("#ajouter_acte_ec").toggle(true);
	}
	
	stopPropagation();
}

//VIDER LES CHAMPS DE L'ELEMENT SELECTIONNER
function vider_analyse_selectionne_ec(id) { 
	$('#SelectTypeAnalyse_EC'+(id)+' select').val('');
	$("#SelectAnalyse_EC"+id+" select").html('');

    stopPropagation();
}

var base_url = window.location.toString();
var tabUrl = base_url.split("public");

function envoiDonneesAuClickSurTerminerEC(){

		var typesDemandesEC = '';
		var examensDemandesEC = '';
		for(var i = 1; i <= nbListeActeEC(); i++ ){
			if($('#type_analyse_name_ec_'+i).val()) {
				typesDemandesEC += ','+$('#type_analyse_name_ec_'+i).val();
				examensDemandesEC += ','+$('#analyse_name_ec_'+i).val();
			}
		}
		
		$('#tabTypeExamenDemandesEC').val(typesDemandesEC);
		$('#tabExamenDemandesEC').val(examensDemandesEC);
		
}

function getListeAnalysesEC(valeur, index){
	$.ajax({
		type: 'POST',
		url: tabUrl[0]+'public/urgence/get-liste-examens-complementaires',
		data:{'id':valeur},
		success: function(data) {    
			var result = jQuery.parseJSON(data);  
			$("#analyse_name_ec_"+index).html(result);
		}
	});
}
