    var base_url = window.location.toString();
	var tabUrl = base_url.split("public");
	var afficherInterfaceConsultation = 0;

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

		
		//POUR LES TAB
		//POUR LES TAB 
		$( "#tabsAntecedents" ).tabs();
		
		//Pour la partie mode_entree_&_transport
		//Pour la partie mode_entree_&_transport
		$( ".divPrecissionProvenance").toggle(false);
		

	});


	//$('#niveauAlerte div input[name=niveau][value="1"]').attr('checked', true); 
	//$('#blanc' ).parent().css({'background' : '#e1e1e1'});
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
	
	
	var oTable;
    function initialisation(){
      
    	var asInitVals = new Array();
    	
    	oTable = $('#patient').dataTable( {
    				"sPaginationType": "full_numbers",
    				"aLengthMenu": [5,7,10,15],
    				"aaSorting": [], //On ne trie pas la liste automatiquement
    				"oLanguage": {
    					"sInfo": "_START_ &agrave; _END_ sur _TOTAL_ patients",
    					"sInfoEmpty": "0 &eacute;l&eacute;ment &agrave; afficher",
    					"sInfoFiltered": "",
    					"sUrl": "",
    					"oPaginate": {
    						"sFirst":    "|<",
    						"sPrevious": "<",
    						"sNext":     ">",
    						"sLast":     ">|"
    						}
    				   },

    				"sAjaxSource":  tabUrl[0] + "public/consultation/liste-patients-admis-infirmier-service-ajax",
    				"fnDrawCallback": function() 
    				{
    					//markLine();
    					clickRowHandler();
    				}
        
        } );
    	
    	//le filtre du select
    	$('#filter_statut').change(function() 
    	{					
    		oTable.fnFilter( this.value );
    	});
	
    	$("tfoot input").keyup( function () {
    		/* Filter on the column (the index) of this element */
    		oTable.fnFilter( this.value, $("tfoot input").index(this) );
    	} );

    	/*
	     *Support functions to provide a little bit of 'user friendlyness' to the textboxes in 
	     *the footer
	     */
    	$("tfoot input").each( function (i) {
    		asInitVals[i] = this.value;
    	} );
	

    	$("tfoot input").focus( function () {
    		if ( this.className == "search_init" )
    		{
    			this.className = "";
    			this.value = "";
    		}
    	} );
	
    	$("tfoot input").blur( function (i) {
    		if ( this.value == "" )
    		{
    			this.className = "search_init";
    			this.value = asInitVals[$("tfoot input").index(this)];
    		}
    	} );
    	
    	
    	raffarichirLaListePatientAdmisParInfirmierService();
    }
    

    function clickRowHandler() 
    {
    	var id;
    	$('#patient tbody tr').contextmenu({
    		target: '#context-menu',
    		onItem: function (context, e) {
    			
    			if($(e.target).text() == 'Visualiser' || $(e.target).is('#visualiserCTX')){
    				visualiser(id);
    			} else 
    				if($(e.target).text() == 'Modifier' || $(e.target).is('#modifierCTX')){
    					modifier(id);
    				}
    			
    		}
    	
    	}).bind('mousedown', function (e) {
    			var aData = oTable.fnGetData( this );
    		    id = aData[7];
    	});
    	
    	
    	
    	$("#patient tbody tr").bind('dblclick', function (event) {
    		var aData = oTable.fnGetData( this );
    		var id = aData[7];
    		visualiser(id);
    	});
    	
    	$('a,img,hass').tooltip({ animation: true, html: true, placement: 'bottom', show: { effect: "slideDown", delay: 250 }} );
    }

    function getListeLits(id_salle){ 
    	if(couloirClick == 1){
    		$('#couloir').trigger('click');
    	}
    	
    	$('#lit').html("");
    	var chemin = tabUrl[0] + 'public/consultation/liste-lits';
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
    
    function consultation(id_patient, id_admission){ 
    	gestionDesChampsRequis();
    	afficherInterfaceConsultation = 1;
    	
    	$(".termineradmission").html("<button id='termineradmission' style='height:35px;'>Terminer</button>");
    	$(".annuleradmission" ).html("<button id='annuleradmission' style='height:35px;'>Annuler</button>");
    	
    	$("#titre span").html("CONSULTATION DU PATIENT");

    	$('#contenu').fadeOut(function(){
        	$(".chargementPageModification").toggle(true);
    	});
    	
    	//Envoyer le formulaire
    	$('#termineradmission').click(function(){
    	  	
    		if( /*$('#poids').val() && $('#taille').val() && */ $('#temperature').val() 
    		    && $('#tensionmaximale').val() && $('#tensionminimale').val() && $('#pouls').val()
    		    && $('#salle').val()){
    			
    			if($('#listePatientAdmisInfServiceForm')[0].checkValidity() == true){
    				
    				ajoutAutomatiqueAntecedentsMedicaux(); //return false;
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
    						
    						ajoutAutomatiqueAntecedentsMedicaux(); //return false;
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
    	
    	
    	var chemin = tabUrl[0] + 'public/consultation/get-infos-modification-admission';
    	$.ajax({
    		type : 'POST',
    		url : chemin,
    		data : {'id_patient' : id_patient, 'id_admission' : id_admission},
    		success : function(data) {
    			var result = jQuery.parseJSON(data);
    			$(".chargementPageModification").fadeOut(function(){
    				$('#admission_urgence').fadeIn();
        				
    				$("#motif_admission_donnees").css({'height':'350px'});
    				$("#constantes_donnees").css({'height':'330px'});
    				$("#orientation_donnees").css({'height':'100px'});
    				$("#historiques_donnees").css({'height':'460px'});
    				$("#rpu_hospitalisation_donnees").css({'height':'450px'});
    				$("#rpu_traumatisme_donnees").css({'height':'450px'});
    				$("#rpu_sortie_donnees").css({'height':'410px'});

    				//Reduction de l'interface
    				$("#accordionsUrgence").css({'min-height':'100px'});
    				
    				//Appel de la fonction pour l'affichage des historiques et terrain particulier
    				historiqueTerrainParticulier(id_patient);
    				historiquesDesConsultations(id_patient,id_admission);
    			});
    				
    			$("#info_patient").html(result);
    			
    			$('#annuleradmission').click(function() {
    	    		$("#titre span").html("LISTE DES PATIENTS ADMIS");
    	    		
    	    		$('#admission_urgence').fadeOut(function(){
    		    		$('#contenu').fadeIn();
    		    		vart=tabUrl[0]+'public/consultation/liste-patients-admis';
    		    	    $(location).attr("href",vart);
    		    	});
    	    		
    	    		return false;
    	    		
    	    	});
    		}
    	});
    }

    function historiqueTerrainParticulier(id_patient) {
    	var chemin = tabUrl[0] + 'public/consultation/get-historiques-terrain-particulier';
    	$.ajax({
    		type : 'POST',
    		url : chemin,
    		data : {'id_patient' : id_patient},
    		success : function(data) {
    			var result = jQuery.parseJSON(data);
    			$('#contenuScriptHistoriqueTerrainParticulier').html(result);
    		}
    	});
    }
  
    var oTableHistoriqueConsultationPatient;
    function historiquesDesConsultations(id_patient,id_admission){
        
    	var asInitVals = new Array();
    	oTableHistoriqueConsultationPatient = $('#ListeConsultationPatient').dataTable( {
    				"sPaginationType": "full_numbers",
    				"aLengthMenu": [3,5,7],
    				"iDisplayLength": 5,
    				"aaSorting": [], //On ne trie pas la liste automatiquement
    				"oLanguage": {
    					"sInfo": "_START_ &agrave; _END_ sur _TOTAL_ consultations",
    					"sInfoEmpty": "0 &eacute;l&eacute;ment &agrave; afficher",
    					"sInfoFiltered": "",
    					"sUrl": "",
    					"oPaginate": {
    						"sFirst":    "|<",
    						"sPrevious": "<",
    						"sNext":     ">",
    						"sLast":     ">|"
    					}
    				},

    				"sAjaxSource":  tabUrl[0] + "public/consultation/historiques-des-consultations-patient-ajax/"+id_patient,
    				"fnDrawCallback": function() 
    				{
    					//markLine();
    					//clickRowHandler();
    				}
        } );
    }

    //Raffraichir s'il y'a de nouvels admis par l'infirmier de service
	//Raffraichir s'il y'a de nouvels admis par l'infirmier de service

	function raffarichirLaListePatientAdmisParInfirmierService(){
		setTimeout(function(){ 

			var chemin = tabUrl[0] + 'public/consultation/get-nb-patient-admis-non-vu';
			$.ajax({
				type : 'POST',
				url : chemin,
				success : function(data) {
					var result = jQuery.parseJSON(data);

					if(afficherInterfaceConsultation == 0 && nbPatientAdmisInfimierService < result){
						//Raffraichir la liste
						var vart = tabUrl[0] + 'public/consultation/liste-patients-admis';
						$(location).attr("href", vart);
					}
					
					raffarichirLaListePatientAdmisParInfirmierService();
					
					return false;
				}
			});
			
		},30000);
	}
	
	
	
	  //*********************************************************************
	  //*********************************************************************
	  //*********************************************************************
	  	
	  	
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

	$(function(){ 
	 $('#rpu_traumatisme_date_heure').datetimepicker(
				$.datepicker.regional['fr'] = {
						dateFormat: 'dd/mm/yy -', 
		    			timeText: 'H:M', 
		    			hourText: 'Heure', 
		    			minuteText: 'Minute', 
		    			currentText: 'Actuellement', 
		    			closeText: 'F',
						//closeText: 'Fermer',
						changeYear: true,
						yearRange: 'c-80:c',
						prevText: '&#x3c;Préc',
						nextText: 'Suiv&#x3e;',
						monthNames: ['Janvier','Fevrier','Mars','Avril','Mai','Juin',
						'Juillet','Aout','Septembre','Octobre','Novembre','Decembre'],
						monthNamesShort: ['Jan','Fev','Mar','Avr','Mai','Jun',
						'Jul','Aout','Sep','Oct','Nov','Dec'],
						dayNames: ['Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi'],
						dayNamesShort: ['Dim','Lun','Mar','Mer','Jeu','Ven','Sam'],
						dayNamesMin: ['Di','Lu','Ma','Me','Je','Ve','Sa'],
						weekHeader: 'Sm',
						firstDay: 1,
						isRTL: false,
						showMonthAfterYear: false,
						yearRange: '1990:2050',
						changeMonth: true,
						changeYear: true,
						maxDate: 0,
						yearSuffix: ''}
		);
	});
	
	function getChoixMotif(id){
		if(id == 4){
			$('#respond .rpu_sortie_input_choix span').toggle(false);
			$('#element4').toggle(true);
			$('#rpu_sortie_transfert, #rpu_sortie_evacuation').val('');
		}else if(id == 5){
			$('#respond .rpu_sortie_input_choix span').toggle(false);
			$('#element5').toggle(true);
			$('#rpu_sortie_liste_mutation, #rpu_sortie_evacuation').val('');
		}else if(id == 6){
			$('#respond .rpu_sortie_input_choix span').toggle(false);
			$('#element6').toggle(true);
			$('#rpu_sortie_liste_mutation, #rpu_sortie_transfert').val('');
		}else{
			$('#respond .rpu_sortie_input_choix span').toggle(false);
			$('#rpu_sortie_liste_mutation, #rpu_sortie_transfert, #rpu_sortie_evacuation').val('');
		}

	}
	
	
	function AntecedentScript(){
		 $(function(){
			//CONSULTATION
			//CONSULTATION
			$("#titreTableauConsultation").toggle(false);
			$("#ListeConsultationPatient").toggle(false);
			$("#ListeCons").toggle(false);
			$("#boutonTerminerConsultation").toggle(false);
			$(".pager").toggle(false);
			
			//HOSPITALISATION
			//HOSPITALISATION
			$("#titreTableauHospitalisation").toggle(false);
			$("#boutonTerminerHospitalisation").toggle(false);
			$("#ListeHospitalisation").toggle(false);
			$("#ListeHospi").toggle(false);
			
			
			//CONSULTATION
			//CONSULTATION
			$(".image1").click(function(){
				
				 $("#MenuAntecedent").fadeOut(function(){ 
					 $("#titreTableauConsultation").fadeIn("fast");
					 $("#ListeConsultationPatient").fadeIn("fast"); 
					 $("#ListeCons").fadeIn("fast");
				     $("#boutonTerminerConsultation").toggle(true);
				     $(".pager").toggle(true);
				 });
			});
			
			
			//HOSPITALISATION
			//HOSPITALISATION
			$(".image2").click(function(){
				 $("#MenuAntecedent").fadeOut(function(){ 
					 $("#titreTableauHospitalisation").fadeIn("fast");
				     $("#boutonTerminerHospitalisation").toggle(true);
				     $("#ListeHospitalisation").fadeIn("fast");
				     $("#ListeHospi").fadeIn("fast");
				 });
			});
			
			
			
		 });

		 /*************************************************************************************************************/
		 
		 /*=================================== MENU ANTECEDENTS TERRAIN PARTICULIER ==================================*/
		 
		 /*************************************************************************************************************/
		 
		 
		 
		 
		 $(function(){
			    //ANTECEDENTS PERSONNELS
				//ANTECEDENTS PERSONNELS
				$("#antecedentsPersonnels").toggle(false);
				$("#AntecedentsFamiliaux").toggle(false);
				$("#MenuAntecedentPersonnel").toggle(false);
				$("#HabitudesDeVie").toggle(false);
				$("#AntecedentMedicaux").toggle(false);
				$("#AntecedentChirurgicaux").toggle(false);
				$("#GynecoObstetrique").toggle(false);
				
		//*****************************************************************
	    //*****************************************************************
				//ANTECEDENTS PERSONNELS
				//ANTECEDENTS PERSONNELS
				$(".image1_TP").click(function(){
					 $("#MenuTerrainParticulier").fadeOut(function(){ 
						 $("#MenuAntecedentPersonnel").fadeIn("fast");
					 });
				});
				
				$(".image_fleche").click(function(){
					 $("#MenuAntecedentPersonnel").fadeOut(function(){ 
						 $("#MenuTerrainParticulier").fadeIn("fast");
					 });
				});
				
				//HABITUDES DE VIE
				//HABITUDES DE VIE
				$(".image1_AP").click(function(){
					 $("#MenuAntecedentPersonnel").fadeOut(function(){ 
						 $("#HabitudesDeVie").fadeIn("fast");
					 });
				});
				
				//ANTECEDENTS MEDICAUX
				//ANTECEDENTS MEDICAUX
				$(".image2_AP").click(function(){
					 $("#MenuAntecedentPersonnel").fadeOut(function(){ 
						 $("#AntecedentMedicaux").fadeIn("fast");
					 });
				});
				
				//ANTECEDENTS CHIRURGICAUX
				//ANTECEDENTS CHIRURGICAUX
				$(".image3_AP").click(function(){
					 $("#MenuAntecedentPersonnel").fadeOut(function(){ 
						 $("#AntecedentChirurgicaux").fadeIn("fast");
					 });
				});
				
				//ANTECEDENTS CHIRURGICAUX
				//ANTECEDENTS CHIRURGICAUX
				$(".image4_AP").click(function(){
					 $("#MenuAntecedentPersonnel").fadeOut(function(){ 
						 $("#GynecoObstetrique").fadeIn("fast");
					 });
				});
				
				
				
	    //******************************************************************************
		//******************************************************************************
				$(".image2_TP").click(function(){
					$("#MenuTerrainParticulier").fadeOut(function(){ 
						 $("#AntecedentsFamiliaux").fadeIn("fast");
					 });
				}); 
				
		 });
		 
		 
	    setTimeout(function(){
	    	$(".TerminerHabitudeDeVie" ).html("<button id='TerminerHabitudeDeVie' style='height:35px;'>Terminer</button>");
	    	$(".TerminerAntecedentMedicaux" ).html("<button id='TerminerAntecedentMedicaux' style='height:35px;'>Terminer</button>");
	    	$(".TerminerAntecedentChirurgicaux").html("<button id='TerminerAntecedentChirurgicaux' style='height:35px;'>Terminer</button>");
	    	$(".TerminerGynecoObstetrique").html("<button id='TerminerGynecoObstetrique' style='height:35px;'>Terminer</button>");
	    	$(".TerminerAntecedentsFamiliaux").html("<button id='TerminerAntecedentsFamiliaux' style='height:35px;'>Terminer</button>");
	    	$(".TerminerConsultation").html("<button id='TerminerConsultation' style='height:35px;'>Terminer</button>");
	    	$(".TerminerHospitalisation").html("<button id='TerminerHospitalisation' style='height:35px;'>Terminer</button>");
	    	
	    	
	    	$("#TerminerHabitudeDeVie").click(function(){
	    		$("#HabitudesDeVie").fadeOut(function(){ 
	    			$("#MenuAntecedentPersonnel").fadeIn("fast");
	    		});
	    		
	    		return false;
	    	});
	    	
	    	$("#TerminerAntecedentMedicaux").click(function(){
				$("#AntecedentMedicaux").fadeOut(function(){ 
					 $("#MenuAntecedentPersonnel").fadeIn("fast");
				});
				
				return false;
			});
	    	
	    	$("#TerminerAntecedentChirurgicaux").click(function(){
				$("#AntecedentChirurgicaux").fadeOut(function(){ 
					$("#MenuAntecedentPersonnel").fadeIn("fast");
				});
				
				return false;
			});
	    	
	    	$("#TerminerGynecoObstetrique").click(function(){
				$("#GynecoObstetrique").fadeOut(function(){ 
					 $("#MenuAntecedentPersonnel").fadeIn("fast");
				 });
				
				return false;
			});
	    	
	    	$("#TerminerAntecedentsFamiliaux").click(function(){
				$("#AntecedentsFamiliaux").fadeOut(function(){ 
					$("#MenuTerrainParticulier").fadeIn("fast");
				});
				
				return false;
			}); 
	    	
	    	$("#TerminerConsultation").click(function(){
				$("#boutonTerminerConsultation").fadeOut();
				$(".pager").fadeOut();
				$("#titreTableauConsultation").fadeOut();
				$("#ListeCons").fadeOut();
				$("#ListeConsultationPatient").fadeOut(function(){ 
				    $("#MenuAntecedent").fadeIn("fast");
				});
				
				return false;
			});
	    	
	    	$("#TerminerHospitalisation").click(function(){
				$("#boutonTerminerHospitalisation").fadeOut();
				$("#ListeHospitalisation").fadeOut();
				$("#ListeHospi").fadeOut();
				$("#titreTableauHospitalisation").fadeOut(function(){ 
				    $("#MenuAntecedent").fadeIn("fast");
				});
				
				return false;
			});
	    	
	    	
	    }, 1000);
	}
	
	//HABITUDES DE VIE TESTER SI UNE HABITUDE EST COCHEE OU PAS
	//HABITUDES DE VIE TESTER SI UNE HABITUDE EST COCHEE OU PAS
	function scritpHabitudeDeVie() {
		
		 if(temoinAlcoolique != 1){
			 $("#dateDebAlcoolique, #dateFinAlcoolique").toggle(false);
		 }
				
		 if(temoinFumeurHV != 1){
			 $("#dateDebFumeur, #dateFinFumeur, #nbPaquetJour, #nbPaquetAnnee").toggle(false);
			 $('#nbPaquetFumeurHV').val("");
			 $('#nbPaquetAnnee').toggle(false);
			
		 }else{
		
			 if(nbPaquetFumeurHV != 0 ){
				 var nbPaquetAnnee = nbPaquetFumeurHV*365;
				 $("#nbPaquetAnnee label").html("<span style='font-weight: bold; color: green;'>"+nbPaquetAnnee+"</span> p/an");
			 
			 }else{
				 $('#nbPaquetFumeurHV').val("");
				 $('#nbPaquetAnnee').toggle(false);
			 }
		 }
		
		 if(temoinDroguerHV != 1){
			 $("#dateDebDroguer, #dateFinDroguer").toggle(false);
		 }
		 
		 $("#DivNoteAutresHV").toggle(false);
			
			if($('#DateDebutAlcooliqueHV').val() == '00/00/0000'){ $('#DateDebutAlcooliqueHV').val("");}
			if($('#DateFinAlcooliqueHV').val() == '00/00/0000'){ $('#DateFinAlcooliqueHV').val("");}
			$('#HabitudesDeVie input[name=AlcooliqueHV]').click(function(){
				var boutons = $('#HabitudesDeVie input[name=AlcooliqueHV]');
				if( boutons[1].checked){ $("#dateDebAlcoolique, #dateFinAlcoolique").toggle(true); }
				if(!boutons[1].checked){ $("#dateDebAlcoolique, #dateFinAlcoolique").toggle(false); }
			});
			
			if($('#DateDebutFumeurHV').val() == '00/00/0000'){ $('#DateDebutFumeurHV').val("");}
			if($('#DateFinFumeurHV').val() == '00/00/0000'){ $('#DateFinFumeurHV').val("");}
			$('#HabitudesDeVie input[name=FumeurHV]').click(function(){
				var boutons = $('#HabitudesDeVie input[name=FumeurHV]');
				if( boutons[1].checked){ $("#dateDebFumeur, #dateFinFumeur, #nbPaquetJour, #nbPaquetAnnee").toggle(true); }
				if(!boutons[1].checked){ $("#dateDebFumeur, #dateFinFumeur, #nbPaquetJour, #nbPaquetAnnee").toggle(false); }
				if($('#nbPaquetFumeurHV').val() == ""){ $('#nbPaquetAnnee').toggle(false);} 
			});
			
			$('#nbPaquetFumeurHV').keyup(function(){
				var valeur = $('#nbPaquetFumeurHV').val();
				if(isNaN(valeur/1) || valeur > 10){
					$('#nbPaquetFumeurHV').val("");
					valeur = null;
				}
				if(valeur){
					var nbPaquetAnnee = valeur*365;
					$("#nbPaquetAnnee").toggle(true);
					$("#nbPaquetAnnee label").html("<span style='font-weight: bold; color: green;'>"+nbPaquetAnnee+"</span> p/an");
				}else{
					$("#nbPaquetAnnee").toggle(false);
				}
			}).click(function(){
				var valeur = $('#nbPaquetFumeurHV').val();
				if(isNaN(valeur/1) || valeur > 10){
					$('#nbPaquetFumeurHV').val("");
					valeur = null;
				}
				if(valeur){
					var nbPaquetAnnee = valeur*365;
					$("#nbPaquetAnnee").toggle(true);
					$("#nbPaquetAnnee label").html("<span style='font-weight: bold; color: green;'>"+nbPaquetAnnee+"</span> p/an");
				}else{
					$("#nbPaquetAnnee").toggle(false);
				}
			}); 
			
			if($('#DateDebutDroguerHV').val() == '00/00/0000'){ $('#DateDebutDroguerHV').val("");}
			if($('#DateFinDroguerHV').val() == '00/00/0000'){ $('#DateFinDroguerHV').val("");}
			$('#HabitudesDeVie input[name=DroguerHV]').click(function(){
				var boutons = $('#HabitudesDeVie input[name=DroguerHV]');
				if( boutons[1].checked){ $("#dateDebDroguer, #dateFinDroguer").toggle(true); }
				if(!boutons[1].checked){ $("#dateDebDroguer, #dateFinDroguer").toggle(false); }
			});
			
			$('#HabitudesDeVie input[name=AutresHV]').click(function(){
				var boutons = $('#HabitudesDeVie input[name=AutresHV]');
				if( boutons[1].checked){ $("#DivNoteAutresHV").toggle(true); }
				if(!boutons[1].checked){ $("#DivNoteAutresHV").toggle(false); }
			});
	 
	 }
	
	//ANTECEDENTS MEDICAUX TESTER SI C'EST COCHE
	//ANTECEDENTS MEDICAUX TESTER SI C'EST COCHE
	function scriptAntecedentMedicaux(){
		if(temoinDiabeteAM != 1){
			$(".imageValiderDiabeteAM").toggle(false);
		}
		if(temoinhtaAM != 1){
			$(".imageValiderHtaAM").toggle(false);
		}
		if(temoindrepanocytoseAM != 1){
			$(".imageValiderDrepanocytoseAM").toggle(false);
		}
		if(temoindislipidemieAM != 1){
			$(".imageValiderDislipidemieAM").toggle(false);
		}
		if(temoinasthmeAM != 1){
			$(".imageValiderAsthmeAM").toggle(false);
		}
		
		$('#AntecedentMedicaux input[name=DiabeteAM]').click(function(){
			var boutons = $('#AntecedentMedicaux input[name=DiabeteAM]');
			if( boutons[1].checked){ $(".imageValiderDiabeteAM").toggle(true); }
			if(!boutons[1].checked){ $(".imageValiderDiabeteAM").toggle(false); }
		});
		
		$('#AntecedentMedicaux input[name=htaAM]').click(function(){
			var boutons = $('#AntecedentMedicaux input[name=htaAM]');
			if( boutons[1].checked){ $(".imageValiderHtaAM").toggle(true); }
			if(!boutons[1].checked){ $(".imageValiderHtaAM").toggle(false); }
		});
		
		$('#AntecedentMedicaux input[name=drepanocytoseAM]').click(function(){
			var boutons = $('#AntecedentMedicaux input[name=drepanocytoseAM]');
			if( boutons[1].checked){ $(".imageValiderDrepanocytoseAM").toggle(true); }
			if(!boutons[1].checked){ $(".imageValiderDrepanocytoseAM").toggle(false); }
		});
		
		$('#AntecedentMedicaux input[name=dislipidemieAM]').click(function(){
			var boutons = $('#AntecedentMedicaux input[name=dislipidemieAM]');
			if( boutons[1].checked){ $(".imageValiderDislipidemieAM").toggle(true); }
			if(!boutons[1].checked){ $(".imageValiderDislipidemieAM").toggle(false); }
		});
		
		$('#AntecedentMedicaux input[name=asthmeAM]').click(function(){
			var boutons = $('#AntecedentMedicaux input[name=asthmeAM]');
			if( boutons[1].checked){ $(".imageValiderAsthmeAM").toggle(true); }
			if(!boutons[1].checked){ $(".imageValiderAsthmeAM").toggle(false); }
		});
	}

	//GYNECO-OBSTETRIQUE TESTER SI C'EST COCHE
	//GYNECO-OBSTETRIQUE TESTER SI C'EST COCHE
	function scriptGynecoObstetrique(){
		if(temoinMenarcheGO != 1){
			$("#NoteMonarche").toggle(false);
		}
		if(temoinGestiteGO != 1){
			$("#NoteGestite").toggle(false);
		}
		if(temoinPariteGO != 1){
			$("#NoteParite").toggle(false);
		}
		if(temoinCycleGO != 1){
			$("#RegulariteON, #DysmenorrheeON, #DureeGO").toggle(false);
		}
		$("#DivNoteAutresGO").toggle(false);
		
		$('#GynecoObstetrique input[name=MenarcheGO]').click(function(){
			var boutons = $('#GynecoObstetrique input[name=MenarcheGO]');
			if( boutons[1].checked){ $("#NoteMonarche").toggle(true); }
			if(!boutons[1].checked){ $("#NoteMonarche").toggle(false); }
		});
		
		$('#GynecoObstetrique input[name=GestiteGO]').click(function(){
			var boutons = $('#GynecoObstetrique input[name=GestiteGO]');
			if( boutons[1].checked){ $("#NoteGestite").toggle(true); }
			if(!boutons[1].checked){ $("#NoteGestite").toggle(false); }
		});
		
		$('#GynecoObstetrique input[name=PariteGO]').click(function(){
			var boutons = $('#GynecoObstetrique input[name=PariteGO]');
			if( boutons[1].checked){ $("#NoteParite").toggle(true); }
			if(!boutons[1].checked){ $("#NoteParite").toggle(false); }
		});
		
		$('#GynecoObstetrique input[name=CycleGO]').click(function(){
			var boutons = $('#GynecoObstetrique input[name=CycleGO]');
			if( boutons[1].checked){ $("#RegulariteON, #DysmenorrheeON, #DureeGO").toggle(true); }
			if(!boutons[1].checked){ $("#RegulariteON, #DysmenorrheeON, #DureeGO").toggle(false); }
		});
		
		$('#GynecoObstetrique input[name=AutresGO]').click(function(){
			var boutons = $('#GynecoObstetrique input[name=AutresGO]');
			if( boutons[1].checked){ $("#DivNoteAutresGO").toggle(true); }
			if(!boutons[1].checked){ $("#DivNoteAutresGO").toggle(false); }
		});
	}
	
	
	//ANTECEDENTS FAMILIAUX TESTER SI C'EST COCHE
	//ANTECEDENTS FAMILIAUX TESTER SI C'EST COCHE
	function scriptAntecedentsFamiliaux(){
		if(temoinDiabeteAF != 1){
			$("#DivNoteDiabeteAF").toggle(false);
		}
		if(temoinDrepanocytoseAF != 1){
			$("#DivNoteDrepanocytoseAF").toggle(false);
		}
		if(temoinhtaAF != 1){
			$("#DivNoteHtaAF").toggle(false);
		}
		$("#DivNoteAutresAF").toggle(false);
		
		$('#AntecedentsFamiliaux input[name=DiabeteAF]').click(function(){ 
			var boutons = $('#AntecedentsFamiliaux input[name=DiabeteAF]');
			if( boutons[1].checked){ $("#DivNoteDiabeteAF").toggle(true); }
			if(!boutons[1].checked){ $("#DivNoteDiabeteAF").toggle(false); }
		});
		
		$('#AntecedentsFamiliaux input[name=DrepanocytoseAF]').click(function(){ 
			var boutons = $('#AntecedentsFamiliaux input[name=DrepanocytoseAF]');
			if( boutons[1].checked){ $("#DivNoteDrepanocytoseAF").toggle(true); }
			if(!boutons[1].checked){ $("#DivNoteDrepanocytoseAF").toggle(false); }
		});
		
		$('#AntecedentsFamiliaux input[name=htaAF]').click(function(){ 
			var boutons = $('#AntecedentsFamiliaux input[name=htaAF]');
			if( boutons[1].checked){ $("#DivNoteHtaAF").toggle(true); }
			if(!boutons[1].checked){ $("#DivNoteHtaAF").toggle(false); }
		});
		
		$('#AntecedentsFamiliaux input[name=autresAF]').click(function(){ 
			var boutons = $('#AntecedentsFamiliaux input[name=autresAF]');
			if( boutons[1].checked){ $("#DivNoteAutresAF").toggle(true); }
			if(!boutons[1].checked){ $("#DivNoteAutresAF").toggle(false); }
		});
	}

	//===================================================================================================================
  	//===================================================================================================================
  	//===================================================================================================================
  	//===================================================================================================================
  	//===================================================================================================================
  	var itab = 1;
  	var ligne = 0; 
  	var tableau = [];
  	
  	function ajouterToutLabelAntecedentsMedicaux(tableau_){
  		for(var l = 1; l <= ligne; l++){
  			if( l == 1 ){
	  			$("#labelDesAntecedentsMedicaux_"+1).html("").css({'height' : '0px'});
	  			itab = 1;
  			} else {
	  			$("#labelDesAntecedentsMedicaux_"+l).remove();
  			}
  		}
  		
  		var tab = [];
  		var j = 1;
  		
  		for(var i=1 ; i<tableau_.length ; i++){
  			if( tableau_[i] ){
  				tab[j++] = tableau_[i];
  				itab++;
  				ajouterLabelAntecedentsMedicaux(tableau_[i]);
  			}
  		}

  		tableau = tab;
  		itab = j;
  		$('#nbCheckboxAM').val(itab);

  		stopPropagation();
  	}
  	
  	
  	//Ajouter des labels au click sur ajouter
  	//Ajouter des labels au click sur ajouter
  	//Ajouter des labels au click sur ajouter
  	var scriptLabel = "";
  	function ajouterLabelAntecedentsMedicaux(nomLabel){
  		
  		if(!nomLabel){ stopPropagation(); }
  		
  		var reste = ( itab - 1 ) % 5; 
  		var nbElement = parseInt( ( itab - 1 ) / 5 ); 
  		if(reste != 0){ ligne = nbElement + 1; }
  		else { ligne = nbElement; }
  		
  		var i = 0;
  		if(ligne == 1){
	  		i = $("#labelDesAntecedentsMedicaux_"+ligne+" td").length;
  		} else {
  			if(reste == 1){
	  			$("#labelDesAntecedentsMedicaux_"+(ligne-1)).after(
            			"<tr id='labelDesAntecedentsMedicaux_"+ligne+"' style='width:100%; '>"+
            			"</tr>");
  			}
  			i = $("#labelDesAntecedentsMedicaux_"+ligne+" td").length;
  		}
  		
  		scriptLabel = 
				"<td id='BUcheckbox' class='label_"+ligne+"_"+i+"' style='width: 20%; padding-right: 5px;'> "+
            "<div > "+
            " <label style='width: 90%; height:30px; text-align:right; font-family: time new romans; font-size: 18px;'> "+
            "    <span style='padding-left: -10px;'> "+
            "       <a href='javascript:supprimerLabelAM("+ligne+","+i+");' ><img class='imageSupprimerAsthmeAM' style='cursor: pointer; float: right; margin-right: -10px; width:10px; height: 10px;' src='"+tabUrl[0]+"public/images_icons/sup.png' /></a> "+ 
            "       <img class='imageValider_"+ligne+"_"+i+"'  style='cursor: pointer; margin-left: -15px;' src='"+tabUrl[0]+"public/images_icons/tick-icon2.png' /> "+  
            "    </span> "+
            nomLabel +"  <input type='checkbox' checked='${this.checked}' name='champValider_"+ligne+"_"+i+"' id='champValider_"+ligne+"_"+i+"' > "+
            " <input type='hidden'  id='champTitreLabel_"+ligne+"_"+i+"' value='"+nomLabel+"' > "+
            " </label> "+
            "</div> "+
            "</td> "+
            
            "<script>"+
            "$('#champValider_"+ligne+"_"+i+"').click(function(){"+
  			"var boutons = $('#champValider_"+ligne+"_"+i+"');"+
  			"if( boutons[0].checked){ $('.imageValider_"+ligne+"_"+i+"').toggle(true);  }"+
  			"if(!boutons[0].checked){ $('.imageValider_"+ligne+"_"+i+"').toggle(false); }"+
  		    "});"+
  		    "</script>"
            ;
  		
  		if( i == 0 ){
  			//AJOUTER ELEMENT SUIVANT
            $("#labelDesAntecedentsMedicaux_"+ligne).html(scriptLabel);
            $("#labelDesAntecedentsMedicaux_"+ligne).css({'height' : '50px'});
  	    } else if( i < 5 ){
  	    	//AJOUTER ELEMENT SUIVANT
            $("#labelDesAntecedentsMedicaux_"+ligne+" .label_"+ligne+"_"+(i-1)).after(scriptLabel);
  	    }
  		
  	}

  	//Ajouter un label --- Ajouter un label
  	//Ajouter un label --- Ajouter un label
  	//Ajouter un label --- Ajouter un label

  	$(function(){
  		$('#imgIconeAjouterLabel').click(function(){ 
  	  		if(!$('#autresAM').val()){ stopPropagation(); }
  	  		else{
  	  			tableau[itab++] = $('#autresAM').val();
  	  			ajouterLabelAntecedentsMedicaux($('#autresAM').val());
  	  			$('#nbCheckboxAM').val(itab);
  	  			$('#autresAM').val("");
  	  		}
  	  		stopPropagation();
  	  	});
  	});
  	
  	//Supprimer un label ajouter --- Supprimer un label ajouter
  	//Supprimer un label ajouter --- Supprimer un label ajouter
  	//Supprimer un label ajouter --- Supprimer un label ajouter
  	function supprimerLabelAM(ligne, i){
  		
  		var pos = ((ligne - 1)*5)+i;
  		var indiceTableau = pos+1; 
  		tableau[indiceTableau] = "";
  		
  		$("#labelDesAntecedentsMedicaux_"+ligne+" .label_"+ligne+"_"+i).fadeOut(
  			function(){	ajouterToutLabelAntecedentsMedicaux(tableau); }
  		);
	  	
  	}
    
  	//Ajout automatique des antecedents medicaux
  	//Ajout automatique des antecedents medicaux
  	function ajoutAutomatiqueAntecedentsMedicaux(){
  		var donnees = new Array();
  		var $nbCheckboxAM = ($('#nbCheckboxAM').val())+1;
  		var $nbCheck = 0;
  		var $ligne;
  		var $reste = ( $nbCheckboxAM - 1 ) % 5;
  			var $nbElement = parseInt( ( $nbCheckboxAM - 1 ) / 5 ); 
  			if($reste != 0){ $ligne = $nbElement + 1; }
  			else { $ligne = $nbElement; }
  			
  			var k=0;
  			var i;
  		for(var j=1 ; j<=$ligne ; j++){
  			for( i=0 ; i<5 ; i++){
  				var $champValider = $('#champValider_'+j+'_'+i+':checked').val();
  				if($champValider == 'on'){
  					donnees['champValider_'+k] = 1;
  					donnees['champTitreLabel_'+k] = $('#champTitreLabel_'+j+'_'+i).val();
  					k++;
  					$nbCheck++;
  				}
  			}
  			i=0; 
  		}
  		
  		donnees['nbCheckboxAM'] = $nbCheck;
  		
  		
  		//Ajouter les champs dans le formulaire 
  		//Ajouter les champs dans le formulaire
		var listePatientAdmisInfServiceForm = document.getElementById("listePatientAdmisInfServiceForm");
		
	    for( donnee in donnees ){
	        // Ajout dynamique de champs dans le formulaire
	        var champ = document.createElement("input");
	        champ.setAttribute("type", "hidden");
	        champ.setAttribute("name", donnee);
	        champ.setAttribute("value", donnees[donnee]);
	        listePatientAdmisInfServiceForm.appendChild(champ);
	    }
  		
  	}
  	
	//*********************************************
  	//*********************************************
	
	
  	//Ajout de l'auto-completion sur le champ autre
    //Ajout de l'auto-completion sur le champ autre
  	
  	function autocompletionAntecedent(myArrayMedicament){
	  	$( "#imageIconeAjouterLabel label input" ).autocomplete({
		  	  source: myArrayMedicament
		    });
  	}
  	
  	
  	function affichageAntecedentsMedicauxDuPatient(nbElement, tableau_){
  		for(var i=1 ; i<=nbElement ; i++){
  			itab++;
  			ajouterLabelAntecedentsMedicaux(tableau_[i]);
  		}
  		tableau = tableau_;
  	}
  	
    //===================================================================================================================
  	//===================================================================================================================
  	//===================================================================================================================
  	//===================================================================================================================
  	$(function(){
  	  	$('#dateDebAlcoolique input, #dateFinAlcoolique input, #dateDebFumeur input, #dateFinFumeur input, #dateDebDroguer input, #dateFinDroguer input').datepicker(
  				$.datepicker.regional['fr'] = {
  						closeText: 'Fermer',
  						changeYear: true,
  						yearRange: 'c-80:c',
  						prevText: '&#x3c;Préc',
  						nextText: 'Suiv&#x3e;',
  						currentText: 'Courant',
  						monthNames: ['Janvier','Fevrier','Mars','Avril','Mai','Juin',
  						'Juillet','Aout','Septembre','Octobre','Novembre','Decembre'],
  						monthNamesShort: ['Jan','Fev','Mar','Avr','Mai','Jun',
  						'Jul','Aout','Sep','Oct','Nov','Dec'],
  						dayNames: ['Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi'],
  						dayNamesShort: ['Dim','Lun','Mar','Mer','Jeu','Ven','Sam'],
  						dayNamesMin: ['Di','Lu','Ma','Me','Je','Ve','Sa'],
  						weekHeader: 'Sm',
  						dateFormat: 'dd/mm/yy',
  						firstDay: 1,
  						isRTL: false,
  						showMonthAfterYear: false,
  						yearRange: '1990:2025',
  						showAnim : 'bounce',
  						changeMonth: true,
  						changeYear: true,
  						yearSuffix: ''}
  		);
  	});

  	
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
  	
  	
  	function getTraumatismeMecanisme(id){ 
  		if(id){ 
  	  		$("#rpu_traumatisme_mecanismes_precision").attr("readonly", false);
  		}else{
  	  		$("#rpu_traumatisme_mecanismes_precision").attr("readonly", true).val("");
  		}
  	}
  	
  	
  	function getTraumatismeDiagnostic(id){
  		if(id){
  	  		$("#rpu_traumatisme_diagnostic_precision").attr("readonly", false);
  		}else{
  	  		$("#rpu_traumatisme_diagnostic_precision").attr("readonly", true).val("");
  		}
  	}
  	
  	
  	function getTraumatismeIndication(id){
  		if(id){
  	  		$("#rpu_traumatisme_indication_precision").attr("readonly", false);
  		}else{
  	  		$("#rpu_traumatisme_indication_precision").attr("readonly", true).val("");
  		}
  	}
  	
  	
  	//IMPRESSION DU RPU-Hospitalisation
  	//IMPRESSION DU RPU-Hospitalisation
  	function imprimerRpuHospitalisation(){
  		var id_patient = $('#id_patient').val();
    	var id_admission = $('#id_admission').val();
    	
    	var motif_consultation = $('#motif_admission1').val();
    	if($('#motif_admission2').val()){
    		motif_consultation += ' - '+$('#motif_admission2').val();
    	}
    	if($('#motif_admission3').val()){
    		motif_consultation += ' - '+$('#motif_admission3').val();
    	}
    	if($('#motif_admission4').val()){
    		motif_consultation += ' - '+$('#motif_admission4').val();
    	}
    	if($('#motif_admission5').val()){
    		motif_consultation += ' - '+$('#motif_admission5').val();
    	}
    	
    	var salle = $('#salle').val();
    	var lit = $('#lit').val();
    	var couloir = $('#couloir').val();
    	var niveau =  $('#niveauAlerte :radio:checked').val();
    	
    	var resume_syndromique       = $('#resume_syndromique').val();
    	var hypotheses_diagnostiques = $('#hypotheses_diagnostiques').val();
    	var examens_complementaires  = $('#examens_complementaires').val();
    	var traitement               = $('#traitement').val();
    	var resultats_examens_complementaires = $('#resultats_examens_complementaires').val();
    	var mutation                 = $('#mutation').val();
    	var mise_a_jour_1 = $('#mise_a_jour_1').val();
    	var mise_a_jour_2 = $('#mise_a_jour_2').val();
    	var mise_a_jour_3 = $('#mise_a_jour_3').val();
    	
    	var avis_specialiste = $('#avis_specialiste').val();
    	
    	//var vart =  tabUrl[0]+'public/consultation/impression-rpu-hospitalisation';
    	var vart =  tabUrl[0]+'public/consultation/imprimer-rpu-hospitalisation';
		var FormulaireImprimerRPU = document.getElementById("FormulaireImprimerRPU");
		FormulaireImprimerRPU.setAttribute("action", vart);
		FormulaireImprimerRPU.setAttribute("method", "POST");
		FormulaireImprimerRPU.setAttribute("target", "_blank");
		
		var champ = document.createElement("input");
		champ.setAttribute("type", "hidden");
		champ.setAttribute("name", 'id_patient');
		champ.setAttribute("value", id_patient);
		FormulaireImprimerRPU.appendChild(champ);
		
		var champ1 = document.createElement("input");
		champ1.setAttribute("type", "hidden");
		champ1.setAttribute("name", 'id_admission');
		champ1.setAttribute("value", id_admission);
		FormulaireImprimerRPU.appendChild(champ1);
		
		var champ2 = document.createElement("input");
		champ2.setAttribute("type", "hidden");
		champ2.setAttribute("name", 'resume_syndromique');
		champ2.setAttribute("value", resume_syndromique);
		FormulaireImprimerRPU.appendChild(champ2);
		
		var champ3 = document.createElement("input");
		champ3.setAttribute("type", "hidden");
		champ3.setAttribute("name", 'hypotheses_diagnostiques');
		champ3.setAttribute("value", hypotheses_diagnostiques);
		FormulaireImprimerRPU.appendChild(champ3);
		
		var champ4 = document.createElement("input");
		champ4.setAttribute("type", "hidden");
		champ4.setAttribute("name", 'examens_complementaires');
		champ4.setAttribute("value", examens_complementaires);
		FormulaireImprimerRPU.appendChild(champ4);
		
		var champ5 = document.createElement("input");
		champ5.setAttribute("type", "hidden");
		champ5.setAttribute("name", 'traitement');
		champ5.setAttribute("value", traitement);
		FormulaireImprimerRPU.appendChild(champ5);
		
		var champ6 = document.createElement("input");
		champ6.setAttribute("type", "hidden");
		champ6.setAttribute("name", 'resultats_examens_complementaires');
		champ6.setAttribute("value", resultats_examens_complementaires);
		FormulaireImprimerRPU.appendChild(champ6);
		
		var champ7 = document.createElement("input");
		champ7.setAttribute("type", "hidden");
		champ7.setAttribute("name", 'mutation');
		champ7.setAttribute("value", mutation);
		FormulaireImprimerRPU.appendChild(champ7);
		
		var champ8 = document.createElement("input");
		champ8.setAttribute("type", "hidden");
		champ8.setAttribute("name", 'mise_a_jour_1');
		champ8.setAttribute("value", mise_a_jour_1);
		FormulaireImprimerRPU.appendChild(champ8);
		
		var champ9 = document.createElement("input");
		champ9.setAttribute("type", "hidden");
		champ9.setAttribute("name", 'mise_a_jour_2');
		champ9.setAttribute("value", mise_a_jour_2);
		FormulaireImprimerRPU.appendChild(champ9);
		
		var champ10 = document.createElement("input");
		champ10.setAttribute("type", "hidden");
		champ10.setAttribute("name", 'mise_a_jour_3');
		champ10.setAttribute("value", mise_a_jour_3);
		FormulaireImprimerRPU.appendChild(champ10);
		
		var champ11 = document.createElement("input");
		champ11.setAttribute("type", "hidden");
		champ11.setAttribute("name", 'avis_specialiste');
		champ11.setAttribute("value", avis_specialiste);
		FormulaireImprimerRPU.appendChild(champ11);
		
		var champ12 = document.createElement("input");
		champ12.setAttribute("type", "hidden");
		champ12.setAttribute("name", 'salle');
		champ12.setAttribute("value", salle);
		FormulaireImprimerRPU.appendChild(champ12);
		
		var champ13 = document.createElement("input");
		champ13.setAttribute("type", "hidden");
		champ13.setAttribute("name", 'lit');
		champ13.setAttribute("value", lit);
		FormulaireImprimerRPU.appendChild(champ13);
		
		var champ14 = document.createElement("input");
		champ14.setAttribute("type", "hidden");
		champ14.setAttribute("name", 'couloir');
		champ14.setAttribute("value", couloir);
		FormulaireImprimerRPU.appendChild(champ14);
		
		var champ15 = document.createElement("input");
		champ15.setAttribute("type", "hidden");
		champ15.setAttribute("name", 'motif_consultation');
		champ15.setAttribute("value", motif_consultation);
		FormulaireImprimerRPU.appendChild(champ15);
		
		var champ16 = document.createElement("input");
		champ16.setAttribute("type", "hidden");
		champ16.setAttribute("name", 'niveau');
		champ16.setAttribute("value", niveau);
		FormulaireImprimerRPU.appendChild(champ16);
		
		$("#ImpressionRPU").trigger('click');
    	
  	}
  	
  	//IMPRESSION DU Rpu-traumatologie
  	//IMPRESSION DU Rpu-traumatologie
  	function imprimerRpuTraumatologie(){
  		var id_patient = $('#id_patient').val();
    	var id_admission = $('#id_admission').val();
    	
    	var motif_consultation = $('#motif_admission1').val();
    	if($('#motif_admission2').val()){
    		motif_consultation += ' - '+$('#motif_admission2').val();
    	}
    	if($('#motif_admission3').val()){
    		motif_consultation += ' - '+$('#motif_admission3').val();
    	}
    	if($('#motif_admission4').val()){
    		motif_consultation += ' - '+$('#motif_admission4').val();
    	}
    	if($('#motif_admission5').val()){
    		motif_consultation += ' - '+$('#motif_admission5').val();
    	}
    	
    	var salle = $('#salle').val();
    	var lit = $('#lit').val();
    	var couloir = $('#couloir').val();
    	var niveau =  $('#niveauAlerte :radio:checked').val();
    	
    	var cote_dominant                  = $('#rpu_traumatisme_cote_dominant').val();
    	var date_heure                     = $('#rpu_traumatisme_date_heure').val();
    	var circonstances                  = $('#rpu_traumatisme_circonstances').val();
    	var antecedent                     = $('#rpu_traumatisme_antecedent').val();
    	var examen_physique                = $('#rpu_traumatisme_examen_physique').val();
    	var examen_paraclinique            = $('#rpu_traumatisme_examen_paraclinique').val();
    	var resultat_examen_complementaire = $('#rpu_traumatisme_resultat_examen_complementaire').val();
    	var mecanismes                     = $('#rpu_traumatisme_mecanismes').val();
    	var mecanismes_precision           = $('#rpu_traumatisme_mecanismes_precision').val();
    	var indication                     = $('#rpu_traumatisme_indication').val();
    	var indication_precision           = $('#rpu_traumatisme_indication_precision').val();
    	var diagnostic                     = $('#rpu_traumatisme_diagnostic').val();
    	var diagnostic_precision           = $('#rpu_traumatisme_diagnostic_precision').val();
    	var conduite                       = $('#rpu_traumatisme_conduite').val();
    	var motif_sortie                   = $('#rpu_traumatisme_motif_sortie').val();
    	var rendez_vous                    = $('#rpu_traumatisme_rendez_vous').val();
    	var specialiste_trauma             = $('#rpu_traumatisme_avis_specialiste_trauma').val();
    	var conduite_specialiste           = $('#rpu_traumatisme_conduite_specialiste').val();
    	
    	
    	//var vart =  tabUrl[0]+'public/consultation/impression-rpu-traumatologie';
    	var vart =  tabUrl[0]+'public/consultation/imprimer-rpu-traumatologie';
		var FormulaireImprimerRPU = document.getElementById("FormulaireImprimerRPU");
		FormulaireImprimerRPU.setAttribute("action", vart);
		FormulaireImprimerRPU.setAttribute("method", "POST");
		FormulaireImprimerRPU.setAttribute("target", "_blank");
		
		var champ = document.createElement("input");
		champ.setAttribute("type", "hidden");
		champ.setAttribute("name", 'id_patient');
		champ.setAttribute("value", id_patient);
		FormulaireImprimerRPU.appendChild(champ);
		
		var champ1 = document.createElement("input");
		champ1.setAttribute("type", "hidden");
		champ1.setAttribute("name", 'id_admission');
		champ1.setAttribute("value", id_admission);
		FormulaireImprimerRPU.appendChild(champ1);
		
		var champ2 = document.createElement("input");
		champ2.setAttribute("type", "hidden");
		champ2.setAttribute("name", 'cote_dominant');
		champ2.setAttribute("value", cote_dominant);
		FormulaireImprimerRPU.appendChild(champ2);
		
		var champ3 = document.createElement("input");
		champ3.setAttribute("type", "hidden");
		champ3.setAttribute("name", 'date_heure');
		champ3.setAttribute("value", date_heure);
		FormulaireImprimerRPU.appendChild(champ3);
		
		var champ4 = document.createElement("input");
		champ4.setAttribute("type", "hidden");
		champ4.setAttribute("name", 'circonstances');
		champ4.setAttribute("value", circonstances);
		FormulaireImprimerRPU.appendChild(champ4);
		
		var champ5 = document.createElement("input");
		champ5.setAttribute("type", "hidden");
		champ5.setAttribute("name", 'antecedent');
		champ5.setAttribute("value", antecedent);
		FormulaireImprimerRPU.appendChild(champ5);
		
		var champ6 = document.createElement("input");
		champ6.setAttribute("type", "hidden");
		champ6.setAttribute("name", 'examen_physique');
		champ6.setAttribute("value", examen_physique);
		FormulaireImprimerRPU.appendChild(champ6);
		
		var champ7 = document.createElement("input");
		champ7.setAttribute("type", "hidden");
		champ7.setAttribute("name", 'examen_paraclinique');
		champ7.setAttribute("value", examen_paraclinique);
		FormulaireImprimerRPU.appendChild(champ7);
		
		var champ8 = document.createElement("input");
		champ8.setAttribute("type", "hidden");
		champ8.setAttribute("name", 'resultat_examen_complementaire');
		champ8.setAttribute("value", resultat_examen_complementaire);
		FormulaireImprimerRPU.appendChild(champ8);
		
		var champ9 = document.createElement("input");
		champ9.setAttribute("type", "hidden");
		champ9.setAttribute("name", 'mecanismes');
		champ9.setAttribute("value", mecanismes);
		FormulaireImprimerRPU.appendChild(champ9);
		
		var champ10 = document.createElement("input");
		champ10.setAttribute("type", "hidden");
		champ10.setAttribute("name", 'mecanismes_precision');
		champ10.setAttribute("value", mecanismes_precision);
		FormulaireImprimerRPU.appendChild(champ10);
		
		var champ11 = document.createElement("input");
		champ11.setAttribute("type", "hidden");
		champ11.setAttribute("name", 'indication');
		champ11.setAttribute("value", indication);
		FormulaireImprimerRPU.appendChild(champ11);
		
		var champ12 = document.createElement("input");
		champ12.setAttribute("type", "hidden");
		champ12.setAttribute("name", 'indication_precision');
		champ12.setAttribute("value", indication_precision);
		FormulaireImprimerRPU.appendChild(champ12);
		
		var champ13 = document.createElement("input");
		champ13.setAttribute("type", "hidden");
		champ13.setAttribute("name", 'diagnostic');
		champ13.setAttribute("value", diagnostic);
		FormulaireImprimerRPU.appendChild(champ13);
		
		var champ14 = document.createElement("input");
		champ14.setAttribute("type", "hidden");
		champ14.setAttribute("name", 'diagnostic_precision');
		champ14.setAttribute("value", diagnostic_precision);
		FormulaireImprimerRPU.appendChild(champ14);
		
		var champ15 = document.createElement("input");
		champ15.setAttribute("type", "hidden");
		champ15.setAttribute("name", 'conduite');
		champ15.setAttribute("value", conduite);
		FormulaireImprimerRPU.appendChild(champ15);
		
		var champ16 = document.createElement("input");
		champ16.setAttribute("type", "hidden");
		champ16.setAttribute("name", 'motif_sortie');
		champ16.setAttribute("value", motif_sortie);
		FormulaireImprimerRPU.appendChild(champ16);
		
		var champ17 = document.createElement("input");
		champ17.setAttribute("type", "hidden");
		champ17.setAttribute("name", 'rendez_vous');
		champ17.setAttribute("value", rendez_vous);
		FormulaireImprimerRPU.appendChild(champ17);
		
		var champ18 = document.createElement("input");
		champ18.setAttribute("type", "hidden");
		champ18.setAttribute("name", 'specialiste_trauma');
		champ18.setAttribute("value", specialiste_trauma);
		FormulaireImprimerRPU.appendChild(champ18);
		
		var champ19 = document.createElement("input");
		champ19.setAttribute("type", "hidden");
		champ19.setAttribute("name", 'conduite_specialiste');
		champ19.setAttribute("value", conduite_specialiste);
		FormulaireImprimerRPU.appendChild(champ19);
		
		var champ20 = document.createElement("input");
		champ20.setAttribute("type", "hidden");
		champ20.setAttribute("name", 'salle');
		champ20.setAttribute("value", salle);
		FormulaireImprimerRPU.appendChild(champ20);
		
		var champ21 = document.createElement("input");
		champ21.setAttribute("type", "hidden");
		champ21.setAttribute("name", 'lit');
		champ21.setAttribute("value", lit);
		FormulaireImprimerRPU.appendChild(champ21);
		
		var champ22 = document.createElement("input");
		champ22.setAttribute("type", "hidden");
		champ22.setAttribute("name", 'couloir');
		champ22.setAttribute("value", couloir);
		FormulaireImprimerRPU.appendChild(champ22);
		
		var champ23 = document.createElement("input");
		champ23.setAttribute("type", "hidden");
		champ23.setAttribute("name", 'motif_consultation');
		champ23.setAttribute("value", motif_consultation);
		FormulaireImprimerRPU.appendChild(champ23);
		
		var champ24 = document.createElement("input");
		champ24.setAttribute("type", "hidden");
		champ24.setAttribute("name", 'niveau');
		champ24.setAttribute("value", niveau);
		FormulaireImprimerRPU.appendChild(champ24);
		
		$("#ImpressionRPU").trigger('click');
  	}
  	
  	//Impression du Rpu de sortie
  	//Impression du Rpu de sortie
  	function imprimerRpuSortie()
  	{

  		var id_patient = $('#id_patient').val();
    	var id_admission = $('#id_admission').val();
    	
    	var motif_consultation = $('#motif_admission1').val();
    	if($('#motif_admission2').val()){
    		motif_consultation += ' - '+$('#motif_admission2').val();
    	}
    	if($('#motif_admission3').val()){
    		motif_consultation += ' - '+$('#motif_admission3').val();
    	}
    	if($('#motif_admission4').val()){
    		motif_consultation += ' - '+$('#motif_admission4').val();
    	}
    	if($('#motif_admission5').val()){
    		motif_consultation += ' - '+$('#motif_admission5').val();
    	}
    	
    	var salle = $('#salle').val();
    	var lit = $('#lit').val();
    	var couloir = $('#couloir').val();
    	var niveau =  $('#niveauAlerte :radio:checked').val();
    	
    	var diganostic                        = $('#rpu_sortie_diagnostic_principal').val();
    	var diganostic_associe                = $('#rpu_sortie_diagnostic_associe').val();
    	var traitement                        = $('#rpu_sortie_traitement').val();
    	var examens_complementaires_demandes  = $('#rpu_sortie_examens_complementaires_demandes').val();
    	var mode_sortie                      = $('#rpu_sortie_motif_sortie').val();
    	var liste_mutation                    = $('#rpu_sortie_liste_mutation').val();
    	var transfert                         = $('#rpu_sortie_transfert').val();
    	var evacuation                        = $('#rpu_sortie_evacuation').val();
    	
    	
    	//var vart =  tabUrl[0]+'public/consultation/impression-rpu-sortie';
    	var vart =  tabUrl[0]+'public/consultation/imprimer-rpu-sortie';
		var FormulaireImprimerRPU = document.getElementById("FormulaireImprimerRPU");
		FormulaireImprimerRPU.setAttribute("action", vart);
		FormulaireImprimerRPU.setAttribute("method", "POST");
		FormulaireImprimerRPU.setAttribute("target", "_blank");
		
		var champ = document.createElement("input");
		champ.setAttribute("type", "hidden");
		champ.setAttribute("name", 'id_patient');
		champ.setAttribute("value", id_patient);
		FormulaireImprimerRPU.appendChild(champ);
		
		var champ1 = document.createElement("input");
		champ1.setAttribute("type", "hidden");
		champ1.setAttribute("name", 'id_admission');
		champ1.setAttribute("value", id_admission);
		FormulaireImprimerRPU.appendChild(champ1);
		
		var champ2 = document.createElement("input");
		champ2.setAttribute("type", "hidden");
		champ2.setAttribute("name", 'salle');
		champ2.setAttribute("value", salle);
		FormulaireImprimerRPU.appendChild(champ2);
		
		var champ3 = document.createElement("input");
		champ3.setAttribute("type", "hidden");
		champ3.setAttribute("name", 'lit');
		champ3.setAttribute("value", lit);
		FormulaireImprimerRPU.appendChild(champ3);
		
		var champ4 = document.createElement("input");
		champ4.setAttribute("type", "hidden");
		champ4.setAttribute("name", 'couloir');
		champ4.setAttribute("value", couloir);
		FormulaireImprimerRPU.appendChild(champ4);
		
		var champ5 = document.createElement("input");
		champ5.setAttribute("type", "hidden");
		champ5.setAttribute("name", 'motif_consultation');
		champ5.setAttribute("value", motif_consultation);
		FormulaireImprimerRPU.appendChild(champ5);
		
		var champ6 = document.createElement("input");
		champ6.setAttribute("type", "hidden");
		champ6.setAttribute("name", 'diganostic');
		champ6.setAttribute("value", diganostic);
		FormulaireImprimerRPU.appendChild(champ6);
		
		var champ7 = document.createElement("input");
		champ7.setAttribute("type", "hidden");
		champ7.setAttribute("name", 'diganostic_associe');
		champ7.setAttribute("value", diganostic_associe);
		FormulaireImprimerRPU.appendChild(champ7);
		
		var champ8 = document.createElement("input");
		champ8.setAttribute("type", "hidden");
		champ8.setAttribute("name", 'traitement');
		champ8.setAttribute("value", traitement);
		FormulaireImprimerRPU.appendChild(champ8);
		
		var champ9 = document.createElement("input");
		champ9.setAttribute("type", "hidden");
		champ9.setAttribute("name", 'examens_complementaires_demandes');
		champ9.setAttribute("value", examens_complementaires_demandes);
		FormulaireImprimerRPU.appendChild(champ9);
		
		var champ10 = document.createElement("input");
		champ10.setAttribute("type", "hidden");
		champ10.setAttribute("name", 'mode_sortie');
		champ10.setAttribute("value", mode_sortie);
		FormulaireImprimerRPU.appendChild(champ10);
		
		var champ11 = document.createElement("input");
		champ11.setAttribute("type", "hidden");
		champ11.setAttribute("name", 'liste_mutation');
		champ11.setAttribute("value", liste_mutation);
		FormulaireImprimerRPU.appendChild(champ11);
		
		var champ12 = document.createElement("input");
		champ12.setAttribute("type", "hidden");
		champ12.setAttribute("name", 'transfert');
		champ12.setAttribute("value", transfert);
		FormulaireImprimerRPU.appendChild(champ12);
		
		var champ13 = document.createElement("input");
		champ13.setAttribute("type", "hidden");
		champ13.setAttribute("name", 'evacuation');
		champ13.setAttribute("value", evacuation);
		FormulaireImprimerRPU.appendChild(champ13);
		
		var champ14 = document.createElement("input");
		champ14.setAttribute("type", "hidden");
		champ14.setAttribute("name", 'niveau');
		champ14.setAttribute("value", niveau);
		FormulaireImprimerRPU.appendChild(champ14);
		
		$("#ImpressionRPU").trigger('click');
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
  	}