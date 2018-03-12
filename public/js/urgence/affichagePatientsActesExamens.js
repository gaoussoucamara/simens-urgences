var base_url = window.location.toString();
var tabUrl = base_url.split("public");

//*********************************************************************
//*********************************************************************
//*********************************************************************


// GESTION DE L'INTERFACE D'AFFICHAGE DES PATIENTS AYANT DES ACTES ET EXAMENS COMPLEMENTAIRES -->
// GESTION DE L'INTERFACE D'AFFICHAGE DES PATIENTS AYANT DES ACTES ET EXAMENS COMPLEMENTAIRES -->
// GESTION DE L'INTERFACE D'AFFICHAGE DES PATIENTS AYANT DES ACTES ET EXAMENS COMPLEMENTAIRES -->

function affichagePatientsActesExamens(){
	
	$( "#affichagePatientsActesExamensMenuGauche" ).dialog({
		resizable: false,
	    height:680,
	    width:950,
	    autoOpen: false,
	    modal: true,
	    buttons: {
	        "Fermer": function() {
              $( this ).dialog( "close" );
	        }
	    }
	});
  
	$("#affichagePatientsActesExamensMenuGauche").dialog('open');
	
}


var oTableActesExamensComp;
function initialisationActesExamensComplementaires() {

	var asInitValsMG = new Array();
	oTableActesExamensComp = $('#actesExamensComplementairesPatients').dataTable( {
		"sPaginationType" : "full_numbers",
		"aLengthMenu" : [10, 15 ],
		"aaSorting" : [], // On ne trie pas la liste automatiquement
		"iDisplayLength": 10,
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
		
		"sAjaxSource" : "" + tabUrl[0] + "public/urgence/liste-actes-examens-complementaires-ajax",
		
		"fnDrawCallback" : function() {
			// markLine();
			clickRowHandler();
		}
		
	});
	
	$("#actesExamensComplementairesPatients thead input").keyup(function() {
		/* Filter on the column (the index) of this element */
		oTableActesExamensComp.fnFilter(this.value, $("#actesExamensComplementairesPatients thead input").index(this));
	});

	/*
	 * Support functions to provide a little bit of 'user friendlyness' to the
	 * textboxes in the footer
	 */
	$("#actesExamensComplementairesPatients thead input").each(function(i) {
		asInitValsMG[i] = this.value;
	});

	$("#actesExamensComplementairesPatients thead input").focus(function() {
		if (this.className == "search_init_mg") {
			this.className = "";
			this.value = "";
		}
	});

	$("#actesExamensComplementairesPatients thead input").blur(function(i) {
		if (this.value == "") {
			this.className = "search_init_mg";
			this.value = asInitValsMG[$("#actesExamensComplementairesPatients thead input").index(this)];
		}
	});
	
	$('#actesExamensComplementairesPatients thead th').unbind('click');
	
	
	
	//POUR LE FILTRE DES PATIENTS ADMIS 
	$('#afficherPatientAujourdhui').css({'font-weight':'bold', 'font-size': '17px' });
	oTableActesExamensComp.fnFilter( 'pat_admis_aujourdhui' );
	
	$('#afficherPatientAujourdhui').click(function(){
		oTableActesExamensComp.fnFilter( 'pat_admis_aujourdhui' );
		$('#afficherPatientAujourdhui').css({'font-weight':'bold', 'font-size': '17px' });
		$('#afficherPatientHier').css({'font-weight':'normal', 'font-size': '15px' });
		$('#afficherPatientAvantHier').css({'font-weight':'normal', 'font-size': '15px'});
		$('#afficherPatientAutresJour').css({'font-weight':'normal', 'font-size': '15px'});
	});
	
	$('#afficherPatientHier').click(function(){
		oTableActesExamensComp.fnFilter( 'admission_pat_hier' );
		$('#afficherPatientHier').css({'font-weight':'bold', 'font-size': '17px' });
		$('#afficherPatientAujourdhui').css({'font-weight':'normal', 'font-size': '15px'});
		$('#afficherPatientAvantHier').css({'font-weight':'normal', 'font-size': '15px'});
		$('#afficherPatientAutresJour').css({'font-weight':'normal', 'font-size': '15px'});
	});
	
	$('#afficherPatientAvantHier').click(function(){
		oTableActesExamensComp.fnFilter( 'patient_adm_avanthier' );
		$('#afficherPatientAvantHier').css({'font-weight':'bold', 'font-size': '17px' });
		$('#afficherPatientAujourdhui').css({'font-weight':'normal', 'font-size': '15px'});
		$('#afficherPatientHier').css({'font-weight':'normal', 'font-size': '15px'});
		$('#afficherPatientAutresJour').css({'font-weight':'normal', 'font-size': '15px'});
	});
	
	$('#afficherPatientAutresJour').click(function(){
		oTableActesExamensComp.fnFilter( 'autres_admissions_patients' );
		$('#afficherPatientAutresJour').css({'font-weight':'bold', 'font-size': '17px' });
		$('#afficherPatientAujourdhui').css({'font-weight':'normal', 'font-size': '15px'});
		$('#afficherPatientHier').css({'font-weight':'normal', 'font-size': '15px'});
		$('#afficherPatientAvantHier').css({'font-weight':'normal', 'font-size': '15px'});
	});
	
}

function clickRowHandler() {
	$('a,img,span').tooltip({ animation: true, html: true, placement: 'bottom', show: { effect: "slideDown", delay: 250 }} );
}

function visualiserListeActesExamensComp(id_patient){
	
	$("#contenuListePatientTableau").fadeOut(function(){
		$("#contenuInfoPatientActesExamen").fadeIn();
	});
  	$("#contenuInfoPatientActesExamensComplementaire").html("<div style='width: 100%; height: 30px; ' align='center'><img style='margin-top: 40px; margin-left: 5px; width: 70px; height: 70px;' src='../images/loading/Chargement_1.gif' /></div>");
	
  	$.ajax({
		type : 'POST',
		url : tabUrl[0] + 'public/urgence/get-infos-actes-examens-patient',
		data : {'id_patient': id_patient},
		success : function(data) {
			var result = jQuery.parseJSON(data);
			
			$("#contenuInfoPatientActesExamensComplementaire").html(result);
		}
  
	});
}

function retourVersListePatientsActesExamens(){
	$("#contenuInfoPatientActesExamen").fadeOut(function(){
		$("#contenuListePatientTableau").fadeIn();
	});
}





















// GESTION DE L'INTERFACE DE GENERATION DE REGISTRES -->
// GESTION DE L'INTERFACE DE GENERATION DE REGISTRES -->
// GESTION DE L'INTERFACE DE GENERATION DE REGISTRES -->

function imprimerRegistreDesPatientsAdmis(){
	
	$( "#generationDesRegistresDesPatientsAdmisMenuGauche" ).dialog({
		resizable: false,
	    height:680,
	    width:1150,
	    autoOpen: false,
	    modal: true,
	    buttons: {
	        "Fermer": function() {
              $( this ).dialog( "close" );
	        }
	    }
	});
  
	$("#generationDesRegistresDesPatientsAdmisMenuGauche").dialog('open');
	
}


var oTableListePatientsAdmisRegistre;
function initialisationListePatientsAdmisRegistre($date) {

	var asInitPatientsAdmisRegistreMG = new Array();
	oTableListePatientsAdmisRegistre = $('#listePatientsAdmisPourLeRegistre').dataTable( {
		"sPaginationType" : "full_numbers",
		"aLengthMenu" : [10, 15 ],
		"iDisplayLength": 9,
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
		
		"sAjaxSource" : "" + tabUrl[0] + "public/urgence/liste-patients-admis-registre-ajax/"+$date,
		
		"fnDrawCallback" : function() {
			// markLine();
			clickRowHandler();
		}
		
	});
	
	
	$("#listePatientsAdmisPourLeRegistre thead input").keyup(function() {
		/* Filter on the column (the index) of this element */
		oTableListePatientsAdmisRegistre.fnFilter(this.value, $("#listePatientsAdmisPourLeRegistre thead input").index(this));
	});

	/*
	 * Support functions to provide a little bit of 'user friendlyness' to the
	 * textboxes in the footer
	 */
	$("#listePatientsAdmisPourLeRegistre thead input").each(function(i) {
		asInitPatientsAdmisRegistreMG[i] = this.value;
	});

	$("#listePatientsAdmisPourLeRegistre thead input").focus(function() {
		if (this.className == "search_init_reg_mg") {
			this.className = "";
			this.value = "";
		}
	});

	$("#listePatientsAdmisPourLeRegistre thead input").blur(function(i) {
		if (this.value == "") {
			this.className = "search_init_reg_mg";
			this.value = asInitPatientsAdmisRegistreMG[$("#listePatientsAdmisPourLeRegistre thead input").index(this)];
		}
	});
	
	$('#listePatientsAdmisPourLeRegistre thead th').unbind('click');
}


function imprimerRegistreDesPatientsAdmisParPeriode(){
	
	var vart = tabUrl[0]+'public/urgence/impression-registre-patients-admis';
	var imprimerRegistreDesPatientsAdmisPdf = document.getElementById("imprimerRegistreDesPatientsAdmisPdf");
	imprimerRegistreDesPatientsAdmisPdf.setAttribute("action", vart);
	imprimerRegistreDesPatientsAdmisPdf.setAttribute("method", "POST");
	imprimerRegistreDesPatientsAdmisPdf.setAttribute("target", "_blank");
	
	var $date_select = $('#generationDesRegistresDesPatientsAdmisMenuGauche .champOP2MG input').val();
	
	// Ajout dynamique de champs dans le formulaire
	var champ = document.createElement("input");
	champ.setAttribute("type", "hidden");
	champ.setAttribute("name", 'date_select');
	champ.setAttribute("value", $date_select);
	imprimerRegistreDesPatientsAdmisPdf.appendChild(champ);
	
	$("#imprimerRegistreDesPatientsAdmisPdf button").trigger('click');
}

var empTabVide = "";
function emplacementTableauVide(){
	empTabVide = $('.zoneGenerationDesRegistresDesPatientsAdmis').html();
}


function getRegistreDesPatientsAdmisPourPeriode(){
	var $date_select = $('#generationDesRegistresDesPatientsAdmisMenuGauche .champOP2MG input').val();
	$date_select = $date_select.replace('-','');
	$date_select = $date_select.replace('-','');
	
	setTimeout(function(){ 
		$('.zoneGenerationDesRegistresDesPatientsAdmis table').remove();
		$('.zoneGenerationDesRegistresDesPatientsAdmis').html(empTabVide);
		oTableListePatientsAdmisRegistre = "";
		initialisationListePatientsAdmisRegistre($date_select);
	},1000);
}

