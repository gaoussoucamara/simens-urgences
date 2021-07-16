var base_url = window.location.toString();
var tabUrl = base_url.split("public");

//*********************************************************************
//*********************************************************************
//*********************************************************************
var identifierListeAfficher = 1;
$(function() {
	
    $( "button" ).button();

    //Au debut on cache le bouton modifier et on affiche le bouton valider
	$( "#bouton_constantes_valider" ).toggle(true);
	$( "#bouton_constantes_modifier" ).toggle(false);

	$( "#bouton_constantes_valider" ).click(function(){
		
		if(
		   //$('#poids'          )[0].checkValidity() == true &&
		   //$('#taille'         )[0].checkValidity() == true &&
		   $('#temperature'    )[0].checkValidity() == true &&
		   $('#tensionmaximale')[0].checkValidity() == true &&
		   $('#tensionminimale')[0].checkValidity() == true &&
		   $('#pouls'          )[0].checkValidity() == true
		  ){	
			
			$('#poids').attr( 'readonly', true );    
	   		$('#taille').attr( 'readonly', true );
	    	$('#temperature').attr( 'readonly', true);
			$('#glycemie_capillaire').attr( 'readonly', true);
	  		$('#pouls').attr( 'readonly', true);
	 		$('#frequence_respiratoire').attr( 'readonly', true);
	  		$("#tensionmaximale").attr( 'readonly', true );
	   		$("#tensionminimale").attr( 'readonly', true );
	   		
	   		
	   		$("#bouton_constantes_modifier").toggle(true); 
	   		$("#bouton_constantes_valider").toggle(false);
	   		
	   		return false; 
		}
   		 
	});
	
	$( "#bouton_constantes_modifier" ).click(function(){
		$('#poids').attr( 'readonly', false );
		$('#taille').attr( 'readonly', false ); 
		$('#temperature').attr( 'readonly', false);
		$('#glycemie_capillaire').attr( 'readonly', false);
		$('#pouls').attr( 'readonly', false);
		$('#frequence_respiratoire').attr( 'readonly', false);
		$("#tensionmaximale").attr( 'readonly', false );
		$("#tensionminimale").attr( 'readonly', false );
		
	 	$("#bouton_constantes_modifier").toggle(false);   //on cache le bouton permettant de modifier les champs
	 	$("#bouton_constantes_valider").toggle(true);    //on affiche le bouton permettant de valider les champs
	 	return  false;
	});

	
	$('#afficherAdmissionInfirmierSevice').css({'font-weight':'bold', 'font-size': '17px' });
	$("#titre i" ).replaceWith('<i class="icon-list-alt" style="padding-top: 3px;"></i>');
	$('#titre span').html('<span>RECHERCHER LE PATIENT</span>');
	
	
	//Pour la partie mode_entree_&_transport
	//Pour la partie mode_entree_&_transport
	$( ".divPrecissionProvenance").toggle(false);
	
});


$('#niveauAlerte div input[name=niveau][value="4"]').attr('checked', true); 
$('#blanc' ).parent().css({'background' : '#42f445'});
var boutons = $('#niveauAlerte div input[name=niveau]');
$(boutons).click(function(){

	if(boutons[0].checked){ 
		$('#blanc' ).parent().css({'background' : '#42f445'});
		$('#jaune' ).parent().css({'background' : '#eeeeee'});
		$('#orange').parent().css({'background' : '#eeeeee'});
		$('#rouge' ).parent().css({'background' : '#eeeeee', 'color' : '#000000'});
	}else
		if(boutons[1].checked){ 
			$('#blanc' ).parent().css({'background' : '#eeeeee'});
			$('#jaune' ).parent().css({'background' : 'yellow'});
			$('#orange').parent().css({'background' : '#eeeeee'});
			$('#rouge' ).parent().css({'background' : '#eeeeee', 'color' : '#000000'});
		}else
			if(boutons[2].checked){ 
				$('#blanc' ).parent().css({'background' : '#eeeeee'});
				$('#jaune' ).parent().css({'background' : '#eeeeee'});
				$('#orange').parent().css({'background' : 'orange'});
				$('#rouge' ).parent().css({'background' : '#eeeeee', 'color' : '#000000'});
			}else
				if(boutons[3].checked){ 
					$('#blanc' ).parent().css({'background' : '#eeeeee'});
					$('#jaune' ).parent().css({'background' : '#eeeeee'});
					$('#orange').parent().css({'background' : '#eeeeee'});
					$('#rouge' ).parent().css({'background' : 'red', 'color' : '#FFFFFF'});
				}
	
});

function clignoterAlerte1(){
	$('#rouge' ).parent().css({'background' : '#eeeeee'});
	setTimeout(function(){ clignoterAlerte2(); }, 500);
}

function clignoterAlerte2(){
	$('#rouge' ).parent().css({'background' : '#FF0000'});
	setTimeout(function(){ clignoterAlerte1(); }, 500);
}
//*********************************************************************
//*********************************************************************
//*********************************************************************


function ajouterPatient(){
	vart=tabUrl[0]+'public/urgence/ajout-patient';
    $(location).attr("href",vart);
    return false;
}

// BOITE DE DIALOG POUR LA CONFIRMATION DE SUPPRESSION
function visualisationInformations(id) {
	$("#visualisationInformations").dialog({
		resizable : false,
		height : 375,
		width : 485,
		autoOpen : false,
		modal : true,
		buttons : {
			"Terminer" : function() {
				$(this).dialog("close");
				return false;
			}
		}
	});
}

function visualiser(id) { 
	visualisationInformations(id);
	var cle = id;
	var chemin = tabUrl[0] + 'public/urgence/get-infos-vue-patient';
	$.ajax({
		type : 'POST',
		url : chemin,
		data : $(this).serialize(),
		data : 'id=' + cle,
		success : function(data) {
			var result = jQuery.parseJSON(data);
			$("#info").html(result);

			$("#visualisationInformations").dialog('open');

		},
		error : function(e) {
			console.log(e);
			alert("Une erreur interne est survenue!");
		},
		dataType : "html"
	});
}


var oTable;
function initialisation() {

	var asInitVals = new Array();
	oTable = $('#patientAAdmettre') .dataTable( {
						"sPaginationType" : "full_numbers",
						"aLengthMenu" : [ 5, 7, 10, 15 ],
						"aaSorting" : [], // On ne trie pas la liste automatiquement
						"oLanguage" : {
							"sInfo" : "_START_ &agrave; _END_ sur _TOTAL_ patients",
							"sInfoEmpty" : "0 &eacute;l&eacute;ment &agrave; afficher",
							"sInfoFiltered" : "",
							"sUrl" : "",
							"oPaginate" : {
								"sFirst" : "|<",
								"sPrevious" : "<",
								"sNext" : ">",
								"sLast" : ">|"
							}
						},

						"sAjaxSource" : "" + tabUrl[0] + "public/urgence/liste-admission-ajax",

						"fnDrawCallback" : function() {
							// markLine();
							clickRowHandler();
						}

					});

	$("#patientAAdmettre thead input").keyup(function() {
		/* Filter on the column (the index) of this element */
		oTable.fnFilter(this.value, $("#patientAAdmettre thead input").index(this));
	});

	/*
	 * Support functions to provide a little bit of 'user friendlyness' to the
	 * textboxes in the footer
	 */
	$("#patientAAdmettre thead input").each(function(i) {
		asInitVals[i] = this.value;
	});

	$("#patientAAdmettre thead input").focus(function() {
		if (this.className == "search_init") {
			this.className = "";
			this.value = "";
		}
	});

	$("#patientAAdmettre thead input").blur(function(i) {
		if (this.value == "") {
			this.className = "search_init";
			this.value = asInitVals[$("#patientAAdmettre thead input").index(this)];
		}
	});
	
	$('#patientAAdmettre thead th').unbind('click');

}

function clickRowHandler() {
	var id;
	$('#patientAAdmettre tbody tr').contextmenu(
			{
				target : '#context-menu',
				onItem : function(context, e) {

					if ($(e.target).text() == 'Visualiser'
							|| $(e.target).is('#visualiserCTX')) {
						visualiser(id);
					} else if ($(e.target).text() == 'Suivant'
							|| $(e.target).is('#suivantCTX')) {
						admettre(id);

					}

				}

			}).bind('mousedown', function(e) {
		var aData = oTable.fnGetData(this);
		id = aData[6];
	});

	$("#patientAAdmettre tbody tr").bind('dblclick', function(event) {
		var aData = oTable.fnGetData(this);
		var id = aData[6];
		visualiser(id);
	});

	
	$('a,img,hass').tooltip({ animation: true, html: true, placement: 'bottom', show: { effect: "slideDown", delay: 250 }} );
}

function animation() {
	// ANIMATION
	// ANIMATION
	// ANIMATION

	$('#admission_urgence').toggle(false);

	$('#precedent').click(function() {
		$("#titre i" ).replaceWith('<i class="icon-list-alt" style="padding-top: 3px;"></i>');
		$('#titre span').html('<span>RECHERCHER LE PATIENT</span>');
		
		$('#contenu').animate({
			height : 'toggle'
		}, 1000);
		
		
		$('#admission_urgence').animate({
			height : 'toggle'
		}, 1000);
		
		
		// IL FAUT LE RECREER POUR L'ENLEVER DU DOM A CHAQUE
		// FOIS QU'ON CLIQUE SUR PRECEDENT
		$("#termineradmission")	.replaceWith("<button id='termineradmission' style='height:35px;'>Terminer</button>");
		
		return false;
		
	});
}

function getListeLits(id_salle){ 
	if(couloirClick == 1){
		$('#couloir').trigger('click');
	}
	
	$('#lit').html("");
	var chemin = tabUrl[0] + 'public/urgence/liste-lits';
	$.ajax({
		type : 'POST',
		url : chemin,
		data : {'id_salle':id_salle},
		success : function(data) {
			var result = jQuery.parseJSON(data);
			$("#lit").html(result);
		}
	});
}

function gestionDesChampsRequis(){
	//$("#poids").attr( 'required', true );    
	//$("#taille").attr( 'required', true );
	$("#temperature").attr( 'required', true);
	$("#tensionmaximale").attr( 'required', true );
	$("#tensionminimale").attr( 'required', true );
	$("#pouls").attr( 'required', true );
	$("#salle").attr( 'required', true );
	$("#lit").attr( 'required', false );
}

var entreIniMotif = 0;
var entreIniActes = 0;
function admettre(id_patient){ 

	gestionDesChampsRequis();
	if(entreIniMotif == 0){ afficherMotif(1); entreIniMotif = 1;}
	
	$("#termineradmission").replaceWith("<button id='termineradmission' style='height:35px;'>Admettre</button>");
	$("#annuleradmission" ).replaceWith("<button id='annuleradmission' style='height:35px;'>Annuler</button>");
	
	$("#titre i" ).replaceWith('<i class="icon-folder-open" style="padding-top: 3px;"></i>');
	$('#titre span').html('<span>ADMISSION</span>');
	
	// R�cup�ration des donn�es du patient
	var chemin = tabUrl[0] + 'public/urgence/admission';
	$.ajax({
		type : 'POST',
		url : chemin,
		data : {'id':id_patient},
		success : function(data) {
			var result = jQuery.parseJSON(data);
			
			$("#info_patient").html(result[0]);
			// PASSER A SUIVANT
			$('#admission_urgence').animate({
				height : 'toggle'
			}, 1000);
			$('#contenu').animate({
				height : 'toggle'
			}, 1000);
			
			var tabTypesActes = result[1];
			var myArrayTypeActes = [''];
    	    for(var i=1 ; i<=tabTypesActes.length ; i++){
    	    	myArrayTypeActes[i] = tabTypesActes[i];
    	    }
    	    
    	    var tabTypesExamenComp = result[2];
			var myArrayTypeExamenComp = [''];
    	    for(var i=1 ; i<=tabTypesExamenComp.length ; i++){
    	    	myArrayTypeExamenComp[i] = tabTypesExamenComp[i];
    	    }
			
    	    if(entreIniActes == 0){
        	    partDefautActe(myArrayTypeActes, 1);
    			partDefautActeEC(myArrayTypeExamenComp, 1);
        	    entreIniActes = 1;
    	    }
			
			$("#motif_admission_donnees").css({'height':'350px'});
			$("#constantes_donnees").css({'height':'330px'});
			$("#orientation_donnees").css({'height':'100px'});
			$("#actes_examencomplementaire_donnees").css({'height':'400px'});
			$("#actes_donnees").css({'height':'250px'});
			$("#examens_complementaires_donnees").css({'height':'250px'});

			//Reduction de linterface
			$("#accordionsUrgence").css({'min-height':'100px'});
			
		},
		error : function(e) {
			alert("Une erreur interne est survenue!");
		},
		dataType : "html"
	});
	// Fin R�cup�ration des donn�es de la maman

	// Annuler l'enregistrement d'une naissance
	$("#annuleradmission").click( function() {

		vart = tabUrl[0] + 'public/urgence/admission';
		$(location).attr("href", vart);
		return false;
		
	});
	
	//Envoyer le formulaire
	$('#termineradmission').click(function(){
		
		//Appel de la fonction pour l'envoi des demandes 
		//Appel de la fonction pour l'envoi des demandes
		envoiDonneesAuClickSurTerminer();
		envoiDonneesAuClickSurTerminerEC();
		
		if( /*$('#poids').val() && $('#taille').val() && */ $('#temperature').val() 
		    && $('#tensionmaximale').val() && $('#tensionminimale').val() && $('#pouls').val()
		    && $('#salle').val()){
			
			if($('#listePatientAdmisInfServiceForm')[0].checkValidity() == true){
				
    			$(this).attr('disabled', true);
    			$('#envoyerDonneesForm').trigger('click');
			}else{
				if(
				   //$('#poids'          )[0].checkValidity() == false ||
				   //$('#taille'         )[0].checkValidity() == false ||
				   $('#temperature'    )[0].checkValidity() == false ||
				   $('#tensionmaximale')[0].checkValidity() == false ||
				   $('#tensionminimale')[0].checkValidity() == false ||
				   $('#pouls'          )[0].checkValidity() == false 
				){ 
					$(".constantes_donnees_onglet").trigger('click');
				}else{
					$(".orientation_donnees_onglet").trigger('click');
				}
			}
			
		}else{
			if( /*!$('#poids').val() || !$('#taille').val() || */ !$('#temperature').val() ||
				!$('#tensionmaximale').val() || !$('#tensionminimale').val() || !$('#pouls').val()){ 
				$(".constantes_donnees_onglet").trigger('click');
			}else{
				if( couloirClick == 1 ){
					if($('#listePatientAdmisInfServiceForm')[0].checkValidity() == true){
						
		    			$(this).attr('disabled', true); 
		    			$('#envoyerDonneesForm').trigger('click');
					}else{ 
						$(".constantes_donnees_onglet").trigger('click');
					}
				}else{
					$(".orientation_donnees_onglet").trigger('click');
				}
			}
		}
		
	});

	$("#id_patient").val(id_patient);

}


//GESTION DE LA LISTE DES PATIENTS ADMIS PAR L'INFIMIER DE TRI
//GESTION DE LA LISTE DES PATIENTS ADMIS PAR L'INFIMIER DE TRI
//GESTION DE LA LISTE DES PATIENTS ADMIS PAR L'INFIMIER DE TRI

var oTableListeInfTri;
function initialisationListeInfTri() {

	var asInitValsListeInfTri = new Array();
	oTableListeInfTri = $('#patientAdmisParInfirmierTri') .dataTable( {
						"sPaginationType" : "full_numbers",
						"aLengthMenu" : [ 5, 7, 10, 15 ],
						"aaSorting" : [], // On ne trie pas la liste automatiquement
						"oLanguage" : {
							"sInfo" : "_START_ &agrave; _END_ sur _TOTAL_ patients",
							"sInfoEmpty" : "0 &eacute;l&eacute;ment &agrave; afficher",
							"sInfoFiltered" : "",
							"sUrl" : "",
							"oPaginate" : {
								"sFirst" : "|<",
								"sPrevious" : "<",
								"sNext" : ">",
								"sLast" : ">|"
							}
						},

						"sAjaxSource" : "" + tabUrl[0] + "public/urgence/liste-admission-infirmier-tri-ajax",

						"fnDrawCallback" : function() {
							// markLine();
							clickRowHandlerAdmisInftri();
						}

					} );

	$("#patientAdmisParInfirmierTri thead input").keyup(function() {
		/* Filter on the column (the index) of this element */
		oTableListeInfTri.fnFilter(this.value, $("#patientAdmisParInfirmierTri thead input").index(this));
	});

	/*
	 * Support functions to provide a little bit of 'user friendlyness' to the
	 * textboxes in the footer
	 */
	$("#patientAdmisParInfirmierTri thead input").each(function(i) {
		asInitValsListeInfTri[i] = this.value;
	});

	$("#patientAdmisParInfirmierTri thead input").focus(function() {
		if (this.className == "search_init") {
			this.className = "";
			this.value = "";
		}
	});

	$("#patientAdmisParInfirmierTri thead input").blur(function(i) {
		if (this.value == "") {
			this.className = "search_init";
			this.value = asInitValsListeInfTri[$("#patientAdmisParInfirmierTri thead input").index(this)];
		}
	});

	
	$('#afficherAdmissionInfirmierTri').click(function(){
		identifierListeAfficher = 1;
		$('#divPatientAAdmettre').fadeOut(function(){
			$('#divPatientAdmisParInfirmierTri').fadeIn();
		});
		$('#afficherAdmissionInfirmierTri').css({'font-weight':'bold', 'font-size': '17px' });
		$('#afficherAdmissionInfirmierSevice').css({'font-weight':'normal', 'font-size': '13px' });
		
		$('#titre span span').fadeOut(function(){
			$('#titre span').html('<span>PATIENTS ADMIS PAR L\'INFIRMIER DE TRI</span>');
		});
		
		if(entreIniMotif == 1){ 
			vart = tabUrl[0] + 'public/urgence/admission';
			$(location).attr("href", vart);
		}

	});

	$('#afficherAdmissionInfirmierSevice').click(function(){
		identifierListeAfficher = 0;
		$('#divPatientAdmisParInfirmierTri').fadeOut(function(){
			$('#divPatientAAdmettre').fadeIn();
		});
		$('#afficherAdmissionInfirmierTri').css({'font-weight':'normal', 'font-size': '13px'});
		$('#afficherAdmissionInfirmierSevice').css({'font-weight':'bold', 'font-size': '17px' });
		
		$('#titre span span').fadeOut(function(){
			$('#titre span').html('<span>RECHERCHER LE PATIENT</span>');
		});

	});
	
	$('#patientAdmisParInfirmierTri thead th').unbind('click');
	
	raffarichirLaListePatientAdmisParInfirmierTri();
}

//Raffraichir s'il y'a de nouvels admis par l'infirmier de tri
//Raffraichir s'il y'a de nouvels admis par l'infirmier de tri

function raffarichirLaListePatientAdmisParInfirmierTri(){
	setTimeout(function(){

		var chemin = tabUrl[0] + 'public/urgence/get-nb-patient-admis-non-vu';
		$.ajax({
			type : 'POST',
			url : chemin,
			success : function(data) {
				var result = jQuery.parseJSON(data);
				
				if(identifierListeAfficher == 1 && nbPatientAdmisNonVu < result){
					
					//Raffraichir la liste
					vart = tabUrl[0] + 'public/urgence/admission';
					$(location).attr("href", vart);
				}
				
				raffarichirLaListePatientAdmisParInfirmierTri();
				
				return false;
			}
		});
		
	},30000);
}


function clickRowHandlerAdmisInftri() {
	$('a,img,span').tooltip({ animation: true, html: true, placement: 'bottom', show: { effect: "slideDown", delay: 250 }} );
}

function admettreVersMedecin(id_patient, id_admission) { 

	gestionDesChampsRequis();
	identifierListeAfficher = 0;

	$("#termineradmission").replaceWith("<button id='termineradmission' style='height:35px;'>Admettre</button>");
	$("#annuleradmission" ).replaceWith("<button id='annuleradmission' style='height:35px;'>Annuler</button>");
	$('#titre span').html('<span>ADMISSION</span>');
	$("#precedent").css('visibility','hidden');
	
	
	//Envoyer le formulaire
	$('#termineradmission').click(function(){
	  	
		if( /*$('#poids').val() && $('#taille').val() && */ $('#temperature').val() 
		    && $('#tensionmaximale').val() && $('#tensionminimale').val() && $('#pouls').val()
		    && $('#salle').val()){
			
			if($('#listePatientAdmisInfServiceForm')[0].checkValidity() == true){
				
    			$(this).attr('disabled', true);
    			$('#envoyerDonneesForm').trigger('click');
			}else{
				if(
				   //$('#poids'          )[0].checkValidity() == false ||
				   //$('#taille'         )[0].checkValidity() == false ||
				   $('#temperature'    )[0].checkValidity() == false ||
				   $('#tensionmaximale')[0].checkValidity() == false ||
				   $('#tensionminimale')[0].checkValidity() == false ||
				   $('#pouls'          )[0].checkValidity() == false 
				){ 
					$(".constantes_donnees_onglet").trigger('click');
				}else{
					$(".orientation_donnees_onglet").trigger('click');
				}
			}
			
		}else{ 
			if( /*!$('#poids').val() || !$('#taille').val() || */ !$('#temperature').val() ||
				!$('#tensionmaximale').val() || !$('#tensionminimale').val() || !$('#pouls').val()){ 
				$(".constantes_donnees_onglet").trigger('click');
			}else{
				if( couloirClick == 1 ){
					if($('#listePatientAdmisInfServiceForm')[0].checkValidity() == true){
						
		    			$(this).attr('disabled', true); 
		    			$('#envoyerDonneesForm').trigger('click');
					}else{ 
						$(".constantes_donnees_onglet").trigger('click');
					}
				}else{
					$(".orientation_donnees_onglet").trigger('click');
				}
			}
		}
		
	});
	
	// R�cup�ration des donn�es du patient
	var chemin = tabUrl[0] + 'public/urgence/get-infos-admission-par-infirmier-tri';
	$.ajax({
		type : 'POST',
		url : chemin,
		data : {'id_patient':id_patient, 'id_admission':id_admission},
		success : function(data) {
			var result = jQuery.parseJSON(data);
			$("#info_patient").html(result[0]);
			// PASSER A SUIVANT
			$('#admission_urgence').animate({
				height : 'toggle'
			}, 1000);
			$('#contenu').animate({
				height : 'toggle'
			}, 1000);
			
			/*
			var tabTypesActes = result[1];
			var myArrayTypeActes = [''];
    	    for(var i=1 ; i<=tabTypesActes.length ; i++){
    	    	myArrayTypeActes[i] = tabTypesActes[i];
    	    }
    	    
    	    var tabTypesExamenComp = result[2];
			var myArrayTypeExamenComp = [''];
    	    for(var i=1 ; i<=tabTypesExamenComp.length ; i++){
    	    	myArrayTypeExamenComp[i] = tabTypesExamenComp[i];
    	    }
			
    	    if(entreIniActes == 0){
        	    partDefautActe(myArrayTypeActes, 1);
    			partDefautActeEC(myArrayTypeExamenComp, 1);
        	    entreIniActes = 1;
    	    }
			*/
			
			$("#motif_admission_donnees").css({'height':'350px'});
			$("#constantes_donnees").css({'height':'330px'});
			$("#orientation_donnees").css({'height':'100px'});
			$("#actes_examencomplementaire_donnees").css({'height':'400px'});
			
			$("#actes_donnees").css({'height':'250px'});
			$("#examens_complementaires_donnees").css({'height':'250px'});

			//Reduction de linterface
			$("#accordionsUrgence").css({'min-height':'100px'});
			
		},
		error : function(e) {
			console.log(e);
			alert("Une erreur interne est survenue!");
		},
		dataType : "html"
	});
	// Fin R�cup�ration des donn�es de la maman

	// Annuler l'enregistrement d'une naissance
	$("#annuleradmission").click( function() {

		vart = tabUrl[0] + 'public/urgence/admission';
		$(location).attr("href", vart);
		return false;
		
	});

	$("#id_patient").val(id_patient);
	$("#id_admission").val(id_admission);

}



function dep1(){
	$('#depliantBandelette').click(function(){
		$("#depliantBandelette").replaceWith("<img id='depliantBandelette' style='cursor: pointer; position: absolute; padding-right: 120px; margin-left: -5px;' src='../img/light/plus.png' />");
		dep();
	    $('#BUcheckbox').animate({
	        height : 'toggle'
	    },1000);
	 return false;
	});
}

function dep(){
	$('#depliantBandelette').click(function(){
		$("#depliantBandelette").replaceWith("<img id='depliantBandelette' style='cursor: pointer; position: absolute; padding-right: 120px; margin-left: -5px;' src='../img/light/minus.png' />");
		dep1();
	    $('#BUcheckbox').animate({
	        height : 'toggle'
	    },1000);
	 return false;
	});
}

function OptionCochee() {
	$("#labelAlbumine").toggle(false);
	$("#labelSucre").toggle(false);
	$("#labelCorpscetonique").toggle(false);
	
	$('#BUcheckbox input[name=albumine]').click(function(){
		$("#ChoixPlus").toggle(false);
		var boutons = $('#BUcheckbox input[name=albumine]');
		if(boutons[0].checked){	$("#labelAlbumine").toggle(false); $("#BUcheckbox input[name=croixalbumine]").attr('checked', false); }
		if(boutons[1].checked){ $("#labelAlbumine").toggle(true); $("#labelCroixAlbumine").toggle(true);}
	});

	$('#BUcheckbox input[name=sucre]').click(function(){
		$("#ChoixPlus2").toggle(false);
		var boutons = $('#BUcheckbox input[name=sucre]');
		if(boutons[0].checked){	$("#labelSucre").toggle(false); $("#BUcheckbox input[name=croixsucre]").attr('checked', false); }
		if(boutons[1].checked){ $("#labelSucre").toggle(true); $("#labelCroixSucre").toggle(true);}
	});

	$('#BUcheckbox input[name=corpscetonique]').click(function(){
		$("#ChoixPlus3").toggle(false);
		var boutons = $('#BUcheckbox input[name=corpscetonique]');
		if(boutons[0].checked){	$("#labelCorpscetonique").toggle(false); $("#BUcheckbox input[name=croixcorpscetonique]").attr('checked', false); }
		if(boutons[1].checked){ $("#labelCorpscetonique").toggle(true); $("#labelCroixCorpscetonique").toggle(true);}
	});


	//CHOIX DU CROIX
	//========================================================
	$("#ChoixPlus").toggle(false);
	$('#BUcheckbox input[name=croixalbumine]').click(function(){
		var boutons = $('#BUcheckbox input[name=croixalbumine]');
		if(boutons[0].checked){
			$("#labelCroixAlbumine").toggle(false); 
			$("#ChoixPlus").toggle(true); 
			$("#ChoixPlus label").html("1+");

		}
		if(boutons[1].checked){ 
			$("#labelCroixAlbumine").toggle(false); 
			$("#ChoixPlus").toggle(true); 
			$("#ChoixPlus label").html("2+");

		}
		if(boutons[2].checked){ 
			$("#labelCroixAlbumine").toggle(false); 
			$("#ChoixPlus").toggle(true); 
			$("#ChoixPlus label").html("3+");
			
		}
		if(boutons[3].checked){ 
			$("#labelCroixAlbumine").toggle(false); 
			$("#ChoixPlus").toggle(true); 
			$("#ChoixPlus label").html("4+");

		}
	});

	//========================================================
	$("#ChoixPlus2").toggle(false);
	$('#BUcheckbox input[name=croixsucre]').click(function(){
		var boutons = $('#BUcheckbox input[name=croixsucre]');
		if(boutons[0].checked){
			$("#labelCroixSucre").toggle(false); 
			$("#ChoixPlus2").toggle(true); 
			$("#ChoixPlus2 label").html("1+");

		}
		if(boutons[1].checked){ 
			$("#labelCroixSucre").toggle(false); 
			$("#ChoixPlus2").toggle(true); 
			$("#ChoixPlus2 label").html("2+");

		}
		if(boutons[2].checked){ 
			$("#labelCroixSucre").toggle(false); 
			$("#ChoixPlus2").toggle(true); 
			$("#ChoixPlus2 label").html("3+");
			
		}
		if(boutons[3].checked){ 
			$("#labelCroixSucre").toggle(false); 
			$("#ChoixPlus2").toggle(true); 
			$("#ChoixPlus2 label").html("4+");

		}
	});

	//========================================================
	$("#ChoixPlus3").toggle(false);
	$('#BUcheckbox input[name=croixcorpscetonique]').click(function(){
		var boutons = $('#BUcheckbox input[name=croixcorpscetonique]');
		if(boutons[0].checked){
			$("#labelCroixCorpscetonique").toggle(false); 
			$("#ChoixPlus3").toggle(true); 
			$("#ChoixPlus3 label").html("1+");

		}
		if(boutons[1].checked){ 
			$("#labelCroixCorpscetonique").toggle(false); 
			$("#ChoixPlus3").toggle(true); 
			$("#ChoixPlus3 label").html("2+");

		}
		if(boutons[2].checked){ 
			$("#labelCroixCorpscetonique").toggle(false); 
			$("#ChoixPlus3").toggle(true); 
			$("#ChoixPlus3 label").html("3+");
			
		}
		if(boutons[3].checked){ 
			$("#labelCroixCorpscetonique").toggle(false); 
			$("#ChoixPlus3").toggle(true); 
			$("#ChoixPlus3 label").html("4+");

		}
	});
}



//========================================================
//========================================================
//========================================================
//========================================================

function albumineOption(){
	  
	  $("#labelAlbumine").toggle(true);
		
	    var boutons = $('#BUcheckbox input[name=croixalbumine]');
		if(boutons[0].checked){
			$("#labelCroixAlbumine").toggle(false); 
			$("#ChoixPlus").toggle(true); 
			$("#ChoixPlus label").html("1+");

		}
		if(boutons[1].checked){ 
			$("#labelCroixAlbumine").toggle(false); 
			$("#ChoixPlus").toggle(true); 
			$("#ChoixPlus label").html("2+");

		}
		if(boutons[2].checked){ 
			$("#labelCroixAlbumine").toggle(false); 
			$("#ChoixPlus").toggle(true); 
			$("#ChoixPlus label").html("3+");
			
		}
		if(boutons[3].checked){ 
			$("#labelCroixAlbumine").toggle(false); 
			$("#ChoixPlus").toggle(true); 
			$("#ChoixPlus label").html("4+");

		}
}

//========================================================
//========================================================
	
function sucreOption(){
	  
	  $("#labelSucre").toggle(true);
	  
	  var boutons = $('#BUcheckbox input[name=croixsucre]');
	  if(boutons[0].checked){
			$("#labelCroixSucre").toggle(false); 
			$("#ChoixPlus2").toggle(true); 
			$("#ChoixPlus2 label").html("1+");

		}
		if(boutons[1].checked){ 
			$("#labelCroixSucre").toggle(false); 
			$("#ChoixPlus2").toggle(true); 
			$("#ChoixPlus2 label").html("2+");

		}
		if(boutons[2].checked){ 
			$("#labelCroixSucre").toggle(false); 
			$("#ChoixPlus2").toggle(true); 
			$("#ChoixPlus2 label").html("3+");
			
		}
		if(boutons[3].checked){ 
			$("#labelCroixSucre").toggle(false); 
			$("#ChoixPlus2").toggle(true); 
			$("#ChoixPlus2 label").html("4+");

		}
}

//========================================================
//========================================================
	
function corpscetoniqueOption(){
	  
	  $("#labelCorpscetonique").toggle(true);
	  
	  var boutons = $('#BUcheckbox input[name=croixcorpscetonique]');
		if(boutons[0].checked){
			$("#labelCroixCorpscetonique").toggle(false); 
			$("#ChoixPlus3").toggle(true); 
			$("#ChoixPlus3 label").html("1+");

		}
		if(boutons[1].checked){ 
			$("#labelCroixCorpscetonique").toggle(false); 
			$("#ChoixPlus3").toggle(true); 
			$("#ChoixPlus3 label").html("2+");

		}
		if(boutons[2].checked){ 
			$("#labelCroixCorpscetonique").toggle(false); 
			$("#ChoixPlus3").toggle(true); 
			$("#ChoixPlus3 label").html("3+");
			
		}
		if(boutons[3].checked){ 
			$("#labelCroixCorpscetonique").toggle(false); 
			$("#ChoixPlus3").toggle(true); 
			$("#ChoixPlus3 label").html("4+");

		}
}

function getModeEntre(id){
	if(id == 1){
		$(".ajusteColonne").toggle(false);
		$(".divPrecissionProvenance").toggle(true);
		$(".divModeTransport label").css({'margin-left':'0px'});
	}else 
		if(id == 2 || id == 0){
	    	$(".ajusteColonne").toggle(true);
  			$(".divPrecissionProvenance").toggle(false);
  			$(".divModeTransport label").css({'margin-left':'15px'});
	     }
}





































function getAutoCompletionListeMotifsAdmission(){ 
	
	$.ajax({
		type : 'POST',
		url : tabUrl[0] + 'public/urgence/liste-pathologies',
		data : null,
		success : function(data) {
			var result = jQuery.parseJSON(data); 
			
			var script ="<script>" +
					    "$( '.form-author input' ).autocomplete({"+
			            "source: arrayPathologies,"+
			            "});" +
			            "</script>";
			result += script;
			
			setTimeout(function(){ $('#scriptAutoCompletion').html(result); },1000);
		}
	});

	getlisteMotifsAdmissionMultiSelectPlugin2(); 
	getListePathologiesPopupCorrection();
	recupererListeTypesPathologies();
	recupererListeDesPathologies();
	getInfosStatistiquesParDefaut();
}

function getListePathologiesPopupCorrection(){ 
	
	$.ajax({
		type : 'POST',
		url : tabUrl[0] + 'public/urgence/liste-pathologies-select-option',
		data : null,
		success : function(data) {
			var result = jQuery.parseJSON(data); 
			$('.listeMotifsAdmissionAutoCompletion .listeSelectAC').html(result); 
		}
	});

}

function autoConfigScript(){
	$('#gestionInfosACorriger select, .listeMotifsAdmissionAutoCompletion select').change(function(){
		var tabMotisAdmissionSelect = $('#gestionInfosACorriger select').val();
		var valeurDeCorrection = $('.listeMotifsAdmissionAutoCompletion select').val();
		if(tabMotisAdmissionSelect && valeurDeCorrection){ $('.multiSelLineSubmit').toggle(true);}
		else{ $('.multiSelLineSubmit').toggle(false); }
	});
	
	$('#gestionInfosACorriger select, .listeMotifsAdmissionAutoCompletion select').trigger('change');
}

function appelScriptAuto(val){
	var tabMotisAdmissionSelect = $('#gestionInfosACorriger select').val();
	var valeurDeCorrection = $('.listeMotifsAdmissionAutoCompletion select').val();
	if(tabMotisAdmissionSelect && valeurDeCorrection){ $('.multiSelLineSubmit').toggle(true);}
	else{ $('.multiSelLineSubmit').toggle(false); }
}

function getListeMotifsAdmissionMultiSelectPlugin(){
	$('.listeMotifsAdmissionMultiSelect').multiSelect({
		  selectableHeader: "<label>Motifs &agrave; corriger</label><input type='text' style='font-size: 12px; height: 30px; padding-left: 5px;' class='search-input searchInputMultiSelectMotifs' autocomplete='off' placeholder='Rechercher un motif'>",
		  selectionHeader: "<label>Motifs s&eacute;lectionn&eacute;s</label><input type='text' style='font-size: 12px; height: 30px; padding-left: 5px;' class='search-input searchInputMultiSelectMotifs' autocomplete='off' placeholder='Rechercher un motif'>",
		  afterInit: function(ms){
		    var that = this,
		        $selectableSearch = that.$selectableUl.prev(),
		        $selectionSearch = that.$selectionUl.prev(),
		        selectableSearchString = '#'+that.$container.attr('id')+' .ms-elem-selectable:not(.ms-selected)',
		        selectionSearchString = '#'+that.$container.attr('id')+' .ms-elem-selection.ms-selected';

		    that.qs1 = $selectableSearch.quicksearch(selectableSearchString)
		    .on('keydown', function(e){
		      if (e.which === 40){
		        that.$selectableUl.focus();
		        return false;
		      }
		    });

		    that.qs2 = $selectionSearch.quicksearch(selectionSearchString)
		    .on('keydown', function(e){
		      if (e.which == 40){
		        that.$selectionUl.focus();
		        return false;
		      }
		    });
		  },
		  afterSelect: function(){
		    this.qs1.cache();
		    this.qs2.cache();
		  },
		  afterDeselect: function(){
		    this.qs1.cache();
		    this.qs2.cache();
		  }
		
	});
	
	autoConfigScript();
	
}

function getlisteMotifsAdmissionMultiSelectPlugin2(){ 
	
	$.ajax({
		type : 'POST',
		url : tabUrl[0] + 'public/urgence/liste-motifs-admission-multi-select',
		data : null,
		success : function(data) {
			var result = jQuery.parseJSON(data);
			setTimeout(function(){ 
				$('#gestionInfosACorriger').html(result);
				getListeMotifsAdmissionMultiSelectPlugin();
			});
		}
	});

}

function confirmerModificationMotifsAdmission(){

	$( "#confirmerModificationMotifsAdmission" ).dialog({
		resizable: false,
	    height:530,
	    width:450,
	    autoOpen: false,
	    modal: true,
	    buttons: {
	    	"Annuler": function() {
	    		$( this ).dialog( "close" );
		    },
	        "Confirmer": function() {
              
	        	$( this ).dialog( "close" );
	        	$('.multiSelLineSubmit').toggle(false);
	        	$('.multiSelLineChargement').toggle(true);
              
	        	var tabMotisSelectionnes = $('#gestionInfosACorriger select').val();
	        	var valeurDeCorrection = $('.listeMotifsAdmissionAutoCompletion select').val();
              
	        	$('.ms-container .ms-selectable input, .ms-container .ms-selection input').attr('disabled', true);
	        	$('.listeMotifsAdmissionAutoCompletion select').attr('disabled', true);
	        	$('.ms-container .ms-list').unbind('click').css({'background-color':'#eee'});
              
	        	$.ajax({
	        		type : 'POST',
	        		url : tabUrl[0] + 'public/urgence/modification-liste-motifs-admission-select',
	        		data : {'tabMotisSelectionnes':tabMotisSelectionnes, 'valeurDeCorrection':valeurDeCorrection.split(',')[0] },
	        		success : function(data) {
	        			
	        			getlisteMotifsAdmissionMultiSelectPlugin2();
	        			setTimeout(function(){ $('.listeMotifsAdmissionAutoCompletion select').attr('disabled', false).val(''); },1500);
	        			$('.multiSelLineChargement').toggle(false);
	        			$('.multiSelLineEffectuer').toggle(true);
	        			setTimeout(function(){ $('.multiSelLineEffectuer').fadeOut(); },5000);
	        			getInfosStatistiquesParDefaut();
	        		}
              
	        	});
              
	        
	        }
	   
	    }
	  
	});
	
	$("#confirmerModificationMotifsAdmission").dialog('open');
  
}

function modifierMotifsAdmission(){
	$( ".multiSelLineChargement, .multiSelLineEffectuer").toggle(false);
	$( "#modifierMotifsAdmission" ).dialog({
		resizable: false,
	    height:630,
	    width:750,
	    autoOpen: false,
	    modal: true,
	    buttons: {
	        "Fermer": function() {
              $( this ).dialog( "close" );
	        }
	   
	    }
	  
	});
}
  
function corrigerMotifsAdmission(){
	
	modifierMotifsAdmission();
	$("#modifierMotifsAdmission").dialog('open');
	
}

function correctionMotifsAdmission(){ 
	
	var tabMotisAdmissionSelect = $('#gestionInfosACorriger select').val();
	var valeurDeCorrection = $('.listeMotifsAdmissionAutoCompletion select').val();
	if(tabMotisAdmissionSelect && valeurDeCorrection){
		var tabMotifs = "";
		for(var i=0 ; i<tabMotisAdmissionSelect.length ; i++){
			if(i%2 == 0){
				tabMotifs +="<tr class='ligneColorGray'><td class='col1Confirm'>"+(i+1)+"</td><td class='col2Confirm'>"+tabMotisAdmissionSelect[i]+"</td></tr>";
			}else{
				tabMotifs +="<tr class='ligneColorWhite'><td class='col1Confirm'>"+(i+1)+"</td><td class='col2Confirm'>"+tabMotisAdmissionSelect[i]+"</td></tr>";
			}
		}
		
		$('.infosConfirmation').html(tabMotifs);
		$('.affichageMessageInfosRemplace div').html(valeurDeCorrection.split(',')[1]);
		confirmerModificationMotifsAdmission();
	}
	
}
















/**
 * GESTION DE LA PREMIERE PARTIE
 */
var listeTypesPathologiesSelectOption = "";
function recupererListeTypesPathologiesOptionSelect(){
	$.ajax({
		type : 'POST',
		url : tabUrl[0] + 'public/urgence/liste-types-pathologies-select',
		data : null,
		success : function(data) {
			var result = jQuery.parseJSON(data);
			listeTypesPathologiesSelectOption = result;
		}
	});

}

function ajouterPathologies(){
	
	$( "#ajouterPathologies" ).dialog({
		resizable: false,
	    height:680,
	    width:750,
	    autoOpen: false,
	    modal: true,
	    buttons: {
	        "Fermer": function() {
              $( this ).dialog( "close" );
	        }
	    }
	});
  
	$("#ajouterPathologies").dialog('open');
	
	$("#contenuInterfaceAjoutTypePathologies").toggle(false);
	$(".ligneBoutonsAjout").toggle(true);
	recupererListeTypesPathologiesOptionSelect();
	
}

function recupererListeTypesPathologies(){
	$.ajax({
		type : 'POST',
		url : tabUrl[0] + 'public/urgence/liste-types-pathologies',
		data : null,
		success : function(data) {
			var result = jQuery.parseJSON(data);
			$('.listeTypePathologiesExistantes table').html(result);
		}
	});

}

function modifierInfosTypePathologie(idType){
	
	var libelleTypePathologie = $('.listeTypePathologiesExistantes table .LTPE2_'+idType+' span').html();
	var html ="<tr><td>"+libelleTypePathologie+"</td></tr>";

	$('#infosConfirmationModification').html(html);
	
	$( "#modifierTypePathologie" ).dialog({
		resizable: false,
	    height:300,
	    width:450,
	    autoOpen: false,
	    modal: true,
	    buttons: {
	    	"Annuler": function() {
	    		$( this ).dialog( "close" );
		    },
	        "Modifier": function() {

	        	var libelleTypePathologieModif = $('#affichageMessageInfosRemplaceModification input').val();
	        	if(libelleTypePathologieModif){
		        	var reponse = confirm("Confirmer la modification du type de pathologie");
					if (reponse == false) { return false; }
					else{ 
				      	$('.listeTypePathologiesExistantes table .LTPE2_'+idType+' span').html(libelleTypePathologie+ " <img style='margin-left: 5px; width: 18px; height: 18px;' src='../images/loading/Chargement_1.gif' />");
			        	$( this ).dialog( "close" );
			        	
			        	$.ajax({
			        	
			        		type : 'POST',
			        		url : tabUrl[0] + 'public/urgence/modifier-type-pathologie',
			        		data : {'idType' : idType, 'libelleTypePathologie' : libelleTypePathologieModif },
			        		success : function(data) {
			        			var result = jQuery.parseJSON(data);
			        			$('.listeTypePathologiesExistantes table .LTPE2_'+idType+' span').html(result);
			        			$('#affichageMessageInfosRemplaceModification input').val('');
			        		}
			        	});
					}
	        	}
	        	
	        }
	    }
	});
	
	$("#modifierTypePathologie").dialog('open');
}
	
function recupererListeDesPathologies(){
	$.ajax({
		type : 'POST',
		url : tabUrl[0] + 'public/urgence/liste-pathologies-pour-interface-ajout',
		data : null,
		success : function(data) {
			var result = jQuery.parseJSON(data);
			$('.listePathologiesExistantes table').html(result);
		}
	});
}


function afficherListePathologieDuType(id){
	$('.listePathologiesExistantes table').html('<tr> <td style="margin-top: 35px; border: 1px solid #ffffff; text-align: center;"> Chargement </td> </tr>  <tr> <td align="center" style="border: 1px solid #ffffff; text-align: center;"> <img style="margin-top: 13px; width: 50px; height: 50px;" src="../images/loading/Chargement_1.gif" /> </td> </tr>');
	$.ajax({
		type : 'POST',
		url : tabUrl[0] + 'public/urgence/liste-pathologies-pour-interface-ajout',
		data : {'id':id},
		success : function(data) {
			var result = jQuery.parseJSON(data); 
			$('.listePathologiesExistantes table').html(result);
			
			$('.LTPE1 a').html("<img src='../img/light/triangle_right.png'>");
			$('.iconeIndicateurChoix_'+id+' a').html("<img src='../images_icons/greenarrowright.png'>");
			
		}
	});
}



/**
 * GESTION DE LA DEUXIEME PARTIE
 */
var variableTypePathologie = 0;
var variablePathologie = 0;

function ajoutTypePathologie(){
	variableTypePathologie = 1;
	variablePathologie = 0;
	
	$('.ligneBoutonsAjout').fadeOut(function(){
		$(".ligneInfosAjoutPathologies .LIATP span").toggle(true);
		$(".ligneInfosAjoutPathologies .LIAP span").toggle(false);
		$("#contenuInterfaceAjoutTypePathologies").toggle(true);
	});
	
	$('.interfaceAjoutPathologies .contenuIAPath .identifCAP').remove();
	
	if($('.champsAjoutTP').length == 0){
		ajouterUneNouvelleLignePathologie();
	}
}

function ajoutPathologie(){
	variablePathologie = 1;
	variableTypePathologie = 0;
	$('.ligneBoutonsAjout').fadeOut(function(){
		$(".ligneInfosAjoutPathologies .LIAP span").toggle(true);
		$(".ligneInfosAjoutPathologies .LIATP span").toggle(false);
		$("#contenuInterfaceAjoutTypePathologies").toggle(true);
	});
	
	$('.interfaceAjoutPathologies .contenuIAPath .identifCAP').remove();
	
	if($('.champsAjoutTP').length == 0){
		ajouterUneNouvelleLignePathologie();
	}
}

function annulerAjoutPathologie(){
	$('#contenuInterfaceAjoutTypePathologies').fadeOut(function(){
		$(".ligneBoutonsAjout").toggle(true);
	});
}

function ajouterUneNouvelleLignePathologie(){
	
	if(variableTypePathologie == 1){
		
		var nbLigne = $('.champsAjoutTP').length;
		
		var ligne ="<tr class='champsAjoutTP  identifCAP  champsAjoutTP_"+(nbLigne+1)+"'>"+
	               "<td><input type='text' placeholder='Ecrire un nouveau type de pathologie &agrave; ajouter'></td>"+
	               "</tr>";
		
		$('.interfaceAjoutPathologies .contenuIAPath .champsAjoutTP_'+(nbLigne)).after(ligne);
		
		if((nbLigne+1) > 1){ 
			$('.iconeAnnulerAP').toggle(true);
		}else if((nbLigne+1) == 1){
			$('.iconeAnnulerAP').toggle(false);
		}
		
	}else 
		if(variablePathologie == 1){ 
			
			var nbLigne = $('.champsAjoutP').length;
			
			var ligne ="<tr class='champsAjoutP  identifCAP  champsAjoutTP_"+(nbLigne+1)+"'>"+
		               "<td> <select>"+listeTypesPathologiesSelectOption+"</select> <input type='text' placeholder='Ecrire une nouvelle pathologie &agrave; ajouter'></td>"+
		               "</tr>";
			
			$('.interfaceAjoutPathologies .contenuIAPath .champsAjoutTP_'+(nbLigne)).after(ligne);
			
			if((nbLigne+1) > 1){ 
				$('.iconeAnnulerAP').toggle(true);
			}else if((nbLigne+1) == 1){
				$('.iconeAnnulerAP').toggle(false);
			}
			
		}
	
}


function enleverUneLignePathologie(){
	
	if(variableTypePathologie == 1){
		
		var nbLigne = $('.champsAjoutTP').length;
		if(nbLigne > 1){
			$('.champsAjoutTP_'+nbLigne).remove();
			if(nbLigne == 2){ $('.iconeAnnulerAP').toggle(false); }
		}
		
	}else 
		if(variablePathologie == 1){ 
			
			var nbLigne = $('.champsAjoutP').length; 
			if(nbLigne > 1){
				$('.champsAjoutTP_'+nbLigne).remove();
				if(nbLigne == 2){ $('.iconeAnnulerAP').toggle(false); }
			}
			
		}

}


function validerAjoutPathologie(){
	
	if(variableTypePathologie == 1){
		var nbLigne = $('.champsAjoutTP').length;

		var tabTypePathologie = new Array();
		var j = 0;
		for(var i=1; i<=nbLigne ; i++){
			var valeurChamp = $('.champsAjoutTP_'+i+' input').val(); 
			if(valeurChamp){
				tabTypePathologie [j++] =  valeurChamp;
			}
		}
		
		if(tabTypePathologie.length != 0){
			var reponse = confirm("Confirmer l'enregistrement de(s) type(s) de pathologie");
			if (reponse == false) { return false; }
			else{ enregistrementTypePathologie(tabTypePathologie); }
		}
		
	}else
		if(variablePathologie == 1){ 
			var nbLigne = $('.champsAjoutP').length;
			
			var tabPathologie = new Array();
			var tabTypePathologie = new Array();
			var j = 0;
			for(var i=1; i<=nbLigne ; i++){
				var valeurChamp = $('.champsAjoutTP_'+i+' input').val();
				if(valeurChamp){
					tabPathologie [j] = valeurChamp;
					tabTypePathologie[j++] = $('.champsAjoutTP_'+i+' select').val();
				}
			}
			
			if(tabPathologie.length != 0){
				var reponse = confirm("Confirmer l'enregistrement de(s) pathologie(s)");
				if (reponse == false) { return false; }
				else{ enregistrementPathologie(tabTypePathologie, tabPathologie); }
			}
			
		}
}


function enregistrementTypePathologie(tabTypePathologie){

	$('.boutonAVAV button').attr('disabled', true);
	$('.champsAjoutTP input').attr('disabled', true);
	$.ajax({
		type : 'POST',
		url : tabUrl[0] + 'public/urgence/enregistrement-type-pathologie',
		data : {'tabTypePathologie' : tabTypePathologie},
		success : function(data) {
			
			$('.listeTypePathologiesExistantes table').html('<tr> <td style="margin-top: 35px; border: 1px solid #ffffff; text-align: center;"> Chargement </td> </tr>  <tr> <td align="center" style="border: 1px solid #ffffff; text-align: center;"> <img style="margin-top: 13px; width: 50px; height: 50px;" src="../images/loading/Chargement_1.gif" /> </td> </tr>');
			recupererListeTypesPathologies();
			$('.listePathologiesExistantes table').html('');
			recupererListeTypesPathologiesOptionSelect();
			$('.boutonAVAV button').attr('disabled', false);
			ajoutTypePathologie();
			
		}
	});
}

function enregistrementPathologie(tabTypePathologie, tabPathologie){
	
	$('.boutonAVAV button').attr('disabled', true);
	$('.champsAjoutP select, .champsAjoutP input').attr('disabled', true);
	$.ajax({
		type : 'POST',
		url : tabUrl[0] + 'public/urgence/enregistrement-pathologie',
		data : {'tabTypePathologie' : tabTypePathologie, 'tabPathologie' : tabPathologie},
		success : function(data) {
			
			afficherListePathologieDuType(tabTypePathologie[0]);
			$('.boutonAVAV button').attr('disabled', false);
			autoCompletionMotifsAdmission();
			getListePathologiesPopupCorrection();
			ajoutPathologie();
		}
	});
}

function autoCompletionMotifsAdmission(){
	$.ajax({
		type : 'POST',
		url : tabUrl[0] + 'public/urgence/liste-pathologies',
		data : null,
		success : function(data) {
			var result = jQuery.parseJSON(data); 
			
			var script ="<script>" +
					    "$( '.form-author input' ).autocomplete({"+
			            "source: arrayPathologies,"+
			            "});" +
			            "</script>";
			result += script;
			
			setTimeout(function(){ $('#scriptAutoCompletion').html(result); },1000);
		}
	});
}





































function afficherInfosStatistiques(){
	
	$( "#affichageInfosStatistiques" ).dialog({
		resizable: false,
	    height:680,
	    width:750,
	    autoOpen: false,
	    modal: true,
	    buttons: {
	        "Fermer": function() {
              $( this ).dialog( "close" );
	        }
	    }
	});
  
	$("#affichageInfosStatistiques").dialog('open');
	
	
}


function initialisationListeTypePathologie(){
	$.ajax({
		type : 'POST',
		url : tabUrl[0] + 'public/urgence/liste-type-pathologie-des-motifs-select',
		data : null,
		success : function(data) {
			var result = jQuery.parseJSON(data);
			$('.optionsTypePath .listeZRI1').html(result);
		}
	});
	

	$('.optionsPeriodePath button').css({'visibility':'hidden'});
	$('.champOP1 input, .champOP2 input').change(function(){
		var date_debut = $('.champOP1 input').val();
		var date_fin = $('.champOP2 input').val();
		if(date_debut && date_fin){
			$('.optionsPeriodePath button').css({'visibility':'visible'});
		}else{ 
			$('.optionsPeriodePath button').css({'visibility':'hidden'});
		}
	});
}

function getInfosStatistiquesParDefaut(){ 
	$.ajax({
		type : 'POST',
		url : tabUrl[0] + 'public/urgence/infos-statistiques-par-defaut',
		data : null,
		success : function(data) {
			var result = jQuery.parseJSON(data); 
			result +="<script>$('.champOP1 input, .champOP2 input').val('').trigger('change'); </script>";
			$('#affichageInfosStatistiques .zoneResultatsInfosStatiques').html(result);
		}
	});
}

function recupererTypePathologie(id_type_pathologie){
	$('#listeTableauInfosStatistiques').html('<table align="center" style="margin-top: 25px; margin-bottom: 15px;"> <tr> <td style="margin-top: 35px; border: 1px solid #ffffff; text-align: center;"> Chargement </td> </tr>  <tr> <td align="center" style="border: 1px solid #ffffff; text-align: center;"> <img style="margin-top: 13px; width: 50px; height: 50px;" src="../images/loading/Chargement_1.gif" /> </td> </tr><table>');
	$.ajax({
		type : 'POST',
		url : tabUrl[0] + 'public/urgence/infos-statistiques-optionnelles',
		data : {'id_type_pathologie' : id_type_pathologie},
		success : function(data) {
			var result = jQuery.parseJSON(data); 
			result +="<script>$('.champOP1 input, .champOP2 input').val('').trigger('change'); </script>";
			$('#affichageInfosStatistiques .zoneResultatsInfosStatiques').html(result);
		}
	});
}

function getInfosStatistiquesPourPeriode(){
	var date_debut = $('.champOP1 input').val();
	var date_fin = $('.champOP2 input').val();
	var id_type_pathologie = $('.optionsTypePath .listeZRI1 select').val();

	if(date_debut && date_fin){
		
		$('#listeTableauInfosStatistiques').html('<table align="center" style="margin-top: 25px; margin-bottom: 15px;"> <tr> <td style="margin-top: 35px; border: 1px solid #ffffff; text-align: center;"> Chargement </td> </tr>  <tr> <td align="center" style="border: 1px solid #ffffff; text-align: center;"> <img style="margin-top: 13px; width: 50px; height: 50px;" src="../images/loading/Chargement_1.gif" /> </td> </tr><table>');
		$.ajax({
			type : 'POST',
			url : tabUrl[0] + 'public/urgence/infos-statistiques-optionnelles-periode',
			data : {'id_type_pathologie' : id_type_pathologie, 'date_debut':date_debut, 'date_fin':date_fin },
			success : function(data) {
				var result = jQuery.parseJSON(data); 
				$('#affichageInfosStatistiques .zoneResultatsInfosStatiques').html(result);
			}
		});
		
	}
	
}


function imprimerInformationsStatistiques(){
	
	var date_debut = $('.champOP1 input').val();
	var date_fin = $('.champOP2 input').val();
	var id_type_pathologie = $('.optionsTypePath .listeZRI1 select').val();
	
	var lienImpression =  tabUrl[0]+'public/urgence/imprimer-informations-statistiques';
	var imprimerInformationsStatistiques = document.getElementById("imprimerInformationsStatistiques");
	imprimerInformationsStatistiques.setAttribute("action", lienImpression);
	imprimerInformationsStatistiques.setAttribute("method", "POST");
	imprimerInformationsStatistiques.setAttribute("target", "_blank");
	
	// Ajout dynamique de champs dans le formulaire
	var champ = document.createElement("input");
	champ.setAttribute("type", "hidden");
	champ.setAttribute("name", 'date_debut');
	champ.setAttribute("value", date_debut);
	imprimerInformationsStatistiques.appendChild(champ);
	
	var champ2 = document.createElement("input");
	champ2.setAttribute("type", "hidden");
	champ2.setAttribute("name", 'date_fin');
	champ2.setAttribute("value", date_fin);
	imprimerInformationsStatistiques.appendChild(champ2);
	
	var champ3 = document.createElement("input");
	champ3.setAttribute("type", "hidden");
	champ3.setAttribute("name", 'id_type_pathologie');
	champ3.setAttribute("value", id_type_pathologie);
	imprimerInformationsStatistiques.appendChild(champ3);
	

	$("#imprimerInformationsStatistiques button").trigger('click');
	
}
