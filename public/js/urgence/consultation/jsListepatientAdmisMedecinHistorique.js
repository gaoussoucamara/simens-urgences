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

    				"sAjaxSource":  tabUrl[0] + "public/consultation/liste-patients-admis-infirmier-service-historique-ajax",
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
    	
    	//$(".termineradmission").html("<button id='termineradmission' style='height:35px;'>Terminer</button>");
    	$(".annuleradmission" ).html("<button id='annuleradmission' style='height:35px;'>Terminer</button>");
    	$("#titre span").html("CONSULTATION DU PATIENT");

    	$('#contenu').fadeOut(function(){
        	$(".chargementPageModification").toggle(true);
    	});
    	
    	
    	//Envoyer le formulaire
    	//Envoyer le formulaire
    	/*
    	$('#termineradmission').click(function(){
    	  	
    		if( $('#poids').val() && $('#taille').val() && $('#temperature').val() 
    		    && $('#tensionmaximale').val() && $('#tensionminimale').val() && $('#pouls').val()
    		    && $('#salle').val()){
    			
    			if($('#listePatientAdmisInfServiceForm')[0].checkValidity() == true){
    				
        			$(this).attr('disabled', true);
        			$('#envoyerDonneesForm').trigger('click');
    			}else{
    				if(
    				   $('#poids'          )[0].checkValidity() == false ||
    				   $('#taille'         )[0].checkValidity() == false ||
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
    			if( !$('#poids').val() || !$('#taille').val() || !$('#temperature').val() ||
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
    	*/
    	
    	
    	var chemin = tabUrl[0] + 'public/consultation/get-infos-modification-admission';
    	$.ajax({
    		type : 'POST',
    		url : chemin,
    		data : {'id_patient' : id_patient, 'id_admission' : id_admission, 'historique' : 1},
    		success : function(data) {
    			var result = jQuery.parseJSON(data);
    			$(".chargementPageModification").fadeOut(function(){
    				$('#admission_urgence').fadeIn();
        				
    				$("#motif_admission_donnees").css({'height':'350px'});
    				$("#constantes_donnees").css({'height':'330px'});
    				$("#orientation_donnees").css({'height':'100px'});
    				$("#rpu_hospitalisation_donnees").css({'height':'390px'});
    				$("#rpu_traumatisme_donnees").css({'height':'340px'});
    				$("#rpu_sortie_donnees").css({'height':'200px'});

    				//Reduction de l'interface
    				$("#accordionsUrgence").css({'min-height':'100px'});
    			});
    				
    			$("#info_patient").html(result);
    			
    			$('#annuleradmission').click(function() {
    	    		$("#titre span").html("LISTE DES PATIENTS ADMIS");
    	    		
    	    		$('#admission_urgence').fadeOut(function(){
    		    		$('#contenu').fadeIn();
    		    		vart=tabUrl[0]+'public/consultation/liste-patients-consultes';
    		    	    $(location).attr("href",vart);
    		    	});
    	    		
    	    		return false;
    	    		
    	    	});
    		}
    	});
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
    
    //TESTER LEQUEL DES CHECKBOX est coché
	//TESTER LEQUEL DES CHECKBOX est coché
	function OptionCochee() {
		$("#labelAlbumine").toggle(false);
		$("#labelSucre").toggle(false);
		$("#labelCorpscetonique").toggle(false);

		//AFFICHER SI C'EST COCHE
		//AFFICHER SI C'EST COCHE
		var boutonsAlbumine = $('#BUcheckbox input[name=albumine]');
		if(boutonsAlbumine[1].checked){ $("#labelAlbumine").toggle(true); }
		
		var boutonsSucre = $('#BUcheckbox input[name=sucre]');
		if(boutonsSucre[1].checked){ $("#labelSucre").toggle(true); }

		var boutonsCorps = $('#BUcheckbox input[name=corpscetonique]');
		if(boutonsCorps[1].checked){ $("#labelCorpscetonique").toggle(true); }

		//AFFICHER AU CLICK SI C'EST COCHE
		//AFFICHER AU CLICK SI C'EST COCHE
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
		albumineOption();
		function albumineOption(){
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
		
		$('#BUcheckbox input[name=croixalbumine]').click(function(){
			albumineOption();
		});

		//========================================================
		$("#ChoixPlus2").toggle(false);
		sucreOption();
		function sucreOption(){
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
		$('#BUcheckbox input[name=croixsucre]').click(function(){
			sucreOption();
		});

		//========================================================
		$("#ChoixPlus3").toggle(false);
		corpscetoniqueOption();
		function corpscetoniqueOption(){
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
		$('#BUcheckbox input[name=croixcorpscetonique]').click(function(){
			corpscetoniqueOption();
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
							prevText: '&#x3c;PrÃ©c',
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
	
	