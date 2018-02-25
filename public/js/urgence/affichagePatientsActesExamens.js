var base_url = window.location.toString();
var tabUrl = base_url.split("public");

//*********************************************************************
//*********************************************************************
//*********************************************************************

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
	
}

function clickRowHandler() {
	$('a,img,hass').tooltip({ animation: true, html: true, placement: 'bottom', show: { effect: "slideDown", delay: 250 }} );
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

