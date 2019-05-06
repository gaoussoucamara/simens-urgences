<?php

namespace Consultation\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Urgence\Form\AdmissionForm;
use Zend\Json\Json;
use Urgence\View\Helper\DateHelper;
use Consultation\View\Helpers\DocumentPdf;
use Consultation\View\Helpers\RpuHospitalisationPdf;
use Consultation\View\Helpers\RpuHospitalisationPdf2;
use Consultation\View\Helpers\RpuHospitalisationPdf3;
use Consultation\View\Helpers\RpuTraumatologiePdf;
use Consultation\View\Helpers\RpuTraumatologiePdf2;
use Consultation\View\Helpers\RpuSortiePdf;
use Consultation\View\Helpers\RpuSortiePdf2;
use Consultation\View\Helpers\fpdf181\PDF;
use Urgence\Form\AdmissionConsultationForm;

class ConsultationController extends AbstractActionController {
	protected $patientTable;
	protected $tarifConsultationTable;
	protected $consultationTable;
	protected $serviceTable;
	protected $admissionTable;
	protected $motifAdmissionTable;
	protected $antecedantPersonnelTable;
	protected $antecedantsFamiliauxTable;
	
	
	public function getPatientTable() {
		if (! $this->patientTable) {
			$sm = $this->getServiceLocator ();
			$this->patientTable = $sm->get ( 'Urgence\Model\PatientTable' );
		}
		return $this->patientTable;
	}
	
	public function getAdmissionTable() {
		if (! $this->admissionTable) {
			$sm = $this->getServiceLocator ();
			$this->admissionTable = $sm->get ( 'Urgence\Model\AdmissionTable' );
		}
		return $this->admissionTable;
	}
	
	public function getTarifConsultationTable() {
		if (! $this->tarifConsultationTable) {
			$sm = $this->getServiceLocator ();
			$this->tarifConsultationTable = $sm->get ( 'Urgence\Model\TarifConsultationTable' );
		}
		return $this->tarifConsultationTable;
	}
	
	public function getConsultationTable() {
		if (! $this->consultationTable) {
			$sm = $this->getServiceLocator ();
			$this->consultationTable = $sm->get ( 'Urgence\Model\ConsultationTable' );
		}
		return $this->consultationTable;
	}
	
	public function getServiceTable() {
		if (! $this->serviceTable) {
			$sm = $this->getServiceLocator ();
			$this->serviceTable = $sm->get ( 'Urgence\Model\ServiceTable' );
		}
		return $this->serviceTable;
	}
	
	public function getMotifAdmissionTable() {
		if (! $this->motifAdmissionTable) {
			$sm = $this->getServiceLocator ();
			$this->motifAdmissionTable = $sm->get ( 'Urgence\Model\MotifAdmissionTable' );
		}
		return $this->motifAdmissionTable;
	}
	
	public function getAntecedantPersonnelTable() {
		if (! $this->antecedantPersonnelTable) {
			$sm = $this->getServiceLocator ();
			$this->antecedantPersonnelTable = $sm->get ( 'Consultation\Model\AntecedentPersonnelTable' );
		}
		return $this->antecedantPersonnelTable;
	}
	
	public function getAntecedantsFamiliauxTable() {
		if (! $this->antecedantsFamiliauxTable) {
			$sm = $this->getServiceLocator ();
			$this->antecedantsFamiliauxTable = $sm->get ( 'Consultation\Model\AntecedentsFamiliauxTable' );
		}
		return $this->antecedantsFamiliauxTable;
	}
	
	//======================================================================================
	//======================================================================================
	//======================================================================================
	//======================================================================================
	
	public function baseUrl() {
		$baseUrl = $_SERVER ['REQUEST_URI'];
		$tabURI = explode ( 'public', $baseUrl );
		return $tabURI [0];
	}
	
	public function baseUrlRacine() {
		$baseUrl = $_SERVER ['SCRIPT_FILENAME'];
		$tabURI = explode ( 'public', $baseUrl );
		return $tabURI[0];
	}
	
	//**************************************************************************************
	//**************************************************************************************
	//**************************************************************************************
	//**************************************************************************************
	
	public function listePatientsAdmisInfirmierServiceAjaxAction() {
		$output = $this->getPatientTable ()->getListePatientAdmisInfirmierServiceVuParMedecin ();
		return $this->getResponse ()->setContent ( Json::encode ( $output, array (
				'enableJsonExprFinder' => true
		) ) );
	}
	
	public function getNbPatientAdmisNonVuAction(){
		$nbPatientAdmisInfirmierServ = $this->getPatientTable ()->nbPatientAdmisParInfirmierService();
	
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ( $nbPatientAdmisInfirmierServ ) );
	}
	
	public function listePatientsAdmisAction() {
		$this->layout ()->setTemplate ( 'layout/consultation' );
	
		//INSTANCIATION DU FORMULAIRE D'ADMISSION
		$formAdmission = new AdmissionForm ();
		$listeSalles = $this->getPatientTable ()->listeSalles();
		$formAdmission->get ( 'salle' )->setValueOptions ($listeSalles);
		
		$listeServices = $this->getPatientTable ()->listeService();
		
		$listeServiceMutation = $this->getPatientTable ()->listeServicesMutation();
		$formAdmission->get ( 'mutation' )->setValueOptions ($listeServiceMutation);
		$formAdmission->get ( 'rpu_sortie_liste_mutation' )->setValueOptions ($listeServiceMutation);
		
		$listeCircontances = $this->getPatientTable ()->listeCirconstances();
		$formAdmission->get ( 'rpu_traumatisme_circonstances' )->setValueOptions ($listeCircontances);
	
		$listeCircontances = $this->getPatientTable ()->listeMecanismes();
		$formAdmission->get ( 'rpu_traumatisme_mecanismes' )->setValueOptions ($listeCircontances);
		
		$listeDiagnostics = $this->getPatientTable ()->listeDiagnostic();
		$formAdmission->get ( 'rpu_traumatisme_diagnostic' )->setValueOptions ($listeDiagnostics);
		
		$listeIndications = $this->getPatientTable ()->listeIndications();
		$formAdmission->get ( 'rpu_traumatisme_indication' )->setValueOptions ($listeIndications);
		
		$listeMotifSortieRpuTraumato = $this->getPatientTable ()->listeMotifsSortieRpuTraumato();
		$formAdmission->get ( 'rpu_traumatisme_motif_sortie' )->setValueOptions ($listeMotifSortieRpuTraumato);
		
		$listeMotifSortieRpuSortie = $this->getPatientTable ()->listeMotifsSortieRpuSortie();
		$formAdmission->get ( 'rpu_sortie_motif_sortie' )->setValueOptions ($listeMotifSortieRpuSortie);
		
		$listeModeTransport = $this->getPatientTable ()->listeModeTransport();
		$formAdmission->get ( 'mode_transport' )->setValueOptions ($listeModeTransport);
		
		$nbPatientAdmisInfimierService = $this->getPatientTable ()->nbPatientAdmisParInfirmierService();
		/*
		$listeMotifs = $this->getPatientTable ()->listeMotifsAdmission();
		$formAdmission->get ( 'motif_admission1' )->setValueOptions ($listeMotifs);
		$formAdmission->get ( 'motif_admission2' )->setValueOptions ($listeMotifs);
		$formAdmission->get ( 'motif_admission3' )->setValueOptions ($listeMotifs);
		$formAdmission->get ( 'motif_admission4' )->setValueOptions ($listeMotifs);
		$formAdmission->get ( 'motif_admission5' )->setValueOptions ($listeMotifs);
		*/
		return array (
				'form' => $formAdmission,
				'temoin' => 0,
				'nbPatientAdmisInfimierService' => $nbPatientAdmisInfimierService,
		);
	}
	
	public function listeLitsAction()
	{
		$id_salle = (int)$this->params()->fromPost ('id_salle');
		$liste_select = "";
		foreach($this->getPatientTable()->getListeLitsPourSalle($id_salle) as $listeLits){
			$liste_select.= "<option value=".$listeLits['Id_lit'].">".$listeLits['Numero_lit']."</option>";
		}
	
		$this->getResponse()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html' );
		return $this->getResponse ()->setContent(Json::encode ( $liste_select ));
	}
	
	public function getListeDesLits($id_salle)
	{
		$liste_select = "";
		foreach($this->getPatientTable()->getListeLitsPourSalle($id_salle) as $listeLits){
			$liste_select.= "<option value=".$listeLits['Id_lit'].">".$listeLits['Numero_lit']."</option>";
		}
	
		return $liste_select;
	}
	
	public function getInfosModificationAdmissionAction() {
	
		$user = $this->layout()->user;
		$role = $user['role'];
	
		$today = new \DateTime ();
		$dateAujourdhui = $today->format( 'Y-m-d' );
	
		$id_patient = ( int ) $this->params ()->fromPost ( 'id_patient', 0 );
		$id_admission = ( int ) $this->params ()->fromPost ( 'id_admission', 0 );
		$historique = ( int ) $this->params ()->fromPost ( 'historique', 0 );
	
		//MISE A JOUR DE L'AGE DU PATIENT
		//MISE A JOUR DE L'AGE DU PATIENT
		//MISE A JOUR DE L'AGE DU PATIENT
		//$this->getPatientTable()->miseAJourAgePatient($id_patient);
		//*******************************
		//*******************************
		//*******************************
	
		$pat = $this->getPatientTable ();
	
		$unPatient = $pat->getInfoPatient( $id_patient );
	
		$photo = $pat->getPhoto ( $id_patient );
	
	
		$date = $unPatient['DATE_NAISSANCE'];
		if($date){ $date = (new DateHelper())->convertDate( $unPatient['DATE_NAISSANCE'] ); }else{ $date = null;}
	
		$html  = "<div style='width:100%; height: 190px;'>";
	
		$html .= "<div style='width: 18%; height: 190px; float:left;'>";
		$html .= "<div id='photo' style='float:left; margin-left:40px; margin-top:10px; margin-right:30px;'> <img style='width:105px; height:105px;' src='".$this->baseUrl()."public/img/photos_patients/" . $photo . "' ></div>";
		$html .= "<div style='margin-left:60px; margin-top: 150px;'> <div style='text-decoration:none; font-size:14px; float:left; padding-right: 7px; '>Age:</div>  <div style='font-weight:bold; font-size:19px; font-family: time new romans; color: green; font-weight: bold;'>" . $unPatient['AGE'] . " ans</div></div>";
		$html .= "</div>";
	
		$html .= "<div id='vuePatientAdmission' style='width: 70%; height: 190px; float:left;'>";
		$html .= "<table style='margin-top:0px; float:left; width: 100%;'>";
	
		$html .= "<tr style='width: 100%;'>";
		$html .= "<td style='width: 24%; vertical-align: top;'><a style='text-decoration:underline; font-size:12px;'>Nom:</a><br><div style='width: 150px; max-width: 160px; height:40px; overflow:auto; margin-bottom: 3px;'><p style='font-weight:bold; font-size:19px;'>" . $unPatient['NOM'] . "</p></div></td>";
		$html .= "<td style='width: 24%; vertical-align: top;'><a style='text-decoration:underline; font-size:12px;'>Date de naissance:</a><br><div style='width: 95%; max-width: 250px; height:40px; overflow:auto; margin-bottom: 3px;'><p style=' font-weight:bold; font-size:19px;'>" . $date . "</p></div></td>";
		$html .= "<td style='width: 23%; vertical-align: top;'><a style='text-decoration:underline; font-size:12px;'>T&eacute;l&eacute;phone:</a><br><div style='width: 95%; '><p style=' font-weight:bold; font-size:19px;'>" . $unPatient['TELEPHONE'] . "</p></div></td>";
		$html .= "<td style='width: 29%; '></td>";
	
		$html .= "</tr><tr style='width: 100%;'>";
		$html .= "<td style='vertical-align: top;'><a style='text-decoration:underline; font-size:12px;'>Pr&eacute;nom:</a><br><div style='width: 95%; max-width: 180px; height:40px; overflow:auto; margin-bottom: 3px;'><p style=' font-weight:bold; font-size:19px;'>" . $unPatient['PRENOM'] . " </p></div></td>";
		$html .= "<td style='vertical-align: top;'><a style='text-decoration:underline; font-size:12px;'>Lieu de naissance:</a><br><div style='width: 95%; max-width: 250px; height:40px; overflow:auto; margin-bottom: 3px;'><p style=' font-weight:bold; font-size:19px;'>" . $unPatient['LIEU_NAISSANCE'] . "</p></div></td>";
		$html .= "<td style='vertical-align: top;'><a style='text-decoration:underline; font-size:12px;'>Nationalit&eacute; actuelle:</a><br><div style='width: 95%; max-width: 135px; overflow:auto; '><p style=' font-weight:bold; font-size:19px;'>" . $unPatient['NATIONALITE_ACTUELLE']. "</p></td>";
		$html .= "<td style='vertical-align: top;'><a style='text-decoration:underline; font-size:12px;'>Email:</a><br><div style='width: 100%; max-width: 235px; height:40px; overflow:auto;'><p style='font-weight:bold; font-size:19px;'>" . $unPatient['EMAIL'] . "</p></div></td>";
	
		$html .= "</tr><tr style='width: 100%;'>";
		$html .= "<td style='vertical-align: top;'><a style='text-decoration:underline; font-size:12px;'>Sexe:</a><br><div style='width: 95%; max-width: 130px; height:40px; overflow:auto; margin-bottom: 3px;'><p style=' font-weight:bold; font-size:19px;'>" . $unPatient['SEXE'] . "</p></div></td>";
		$html .= "<td style='vertical-align: top;'><a style='text-decoration:underline; font-size:12px;'>Adresse:</a><br><div style='width: 97%; max-width: 250px; height:50px; overflow:auto; margin-bottom: 3px;'><p style=' font-weight:bold; font-size:19px;'>" . $unPatient['ADRESSE'] . "</p></div></td>";
		$html .= "<td style='vertical-align: top;'><a style='text-decoration:underline; font-size:12px;'>Profession:</a><br><div style='width: 95%; max-width: 235px; height:40px; overflow:auto; '><p style=' font-weight:bold; font-size:19px;'>" .  $unPatient['PROFESSION'] . "</p></div></td>";
	
		$html .= "<td style='width: 30%; height: 50px;'>";
		$html .= "</td>";
		$html .= "</tr>";
		$html .= "</table>";
		$html .= "</div>";
	
		$html .= "<div style='width: 12%; height: 190px; float:left;'>";
		$html .= "<div id='' style='color: white; opacity: 0.09; float:left; margin-right:10px; margin-left:5px; margin-top:5px;'> <img style='width:105px; height:105px;' src='".$this->baseUrl()."public/img/photos_patients/" . $photo . "'></div>";
		$html .= "<div style='margin-left: 5px; margin-top: 10px; margin-right:10px;'>  <div style='font-size:19px; font-family: time new romans; color: green; float:left; margin-top: 10px;'>" . $unPatient['NUMERO_DOSSIER'] . " </div></div>";
		$html .= "</div>";
	
		$html .= "</div>";
	
		$admission = $this->getAdmissionTable()->getPatientAdmis($id_admission);
	
		if($admission){
			$niveau = (int)$admission->niveau;
				
			if($niveau == 4){
				$html .= "<script> setTimeout(function(){ $('#blanc' ).trigger('click'); $('#blanc' ).trigger('click'); });</script>";
			}else if($niveau == 3){
				$html .= "<script> setTimeout(function(){ $('#jaune' ).trigger('click'); $('#jaune' ).trigger('click'); });</script>";
			}else if($niveau == 2){
				$html .= "<script> setTimeout(function(){ $('#orange').trigger('click'); $('#orange').trigger('click'); });</script>";
			}else if($niveau == 1){
				$html .= "<script> setTimeout(function(){ $('#rouge' ).trigger('click'); $('#rouge' ).trigger('click');}); </script>";
			}
	
			if($role == "medecin" || $role == 'specialiste'){
				if($admission->couloir == 1){
					$html .="<script> setTimeout(function(){ $('#couloir').trigger('click'); }); </script>";
				}else{
					$html .="<script> setTimeout(function(){ $('#salle').val('".$admission->salle."'); $('#lit').html('".$this->getListeDesLits($admission->salle)."'); }); </script>";
					$html .="<script> setTimeout(function(){ $('#lit').val('".$admission->lit."'); }); </script>";
				}
	
			}
		}
			
		//Recuperation du mode d'entree et du mode de transport
		//Recuperation du mode d'entree et du mode de transport
		$mode_entree = $this->getAdmissionTable()->getModeEntreeModeTransport($id_admission);
		if($mode_entree['mode_entree']){
			$html .="<script> $('#mode_entree').val('".$mode_entree['mode_entree']."'); </script>";
			$html .="<script> if(".$mode_entree['mode_entree']." == 1 ){ getModeEntre(1); } </script>";
		}
		$html .="<script> $('#precision_provenance').val('".str_replace("'", "\'", $mode_entree['precision_provenance'])."'); </script>";
		$html .="<script> $('#mode_transport').val('".$mode_entree['mode_transport']."'); </script>";
			
		
		//Recuperation de l'admission et de l'id du patient
		//Recuperation de l'admission et de l'id du patient
		$html .="<script> $('#id_patient').val('".$id_patient."'); </script>";
		$html .="<script> $('#id_admission').val('".$id_admission."'); </script>";
	
	
		//Récupération des motifs des consultations
		//Récupération des motifs des consultations
		$motif_admission = $this->getMotifAdmissionTable()->getMotifAdmissionUrgence($id_admission);
		$nbMotif = $motif_admission->count(); $i=1;
		if($nbMotif > 1){ $html .="<script> afficherMotif(".$nbMotif."); $('#bouton_motif_valider').trigger('click'); </script>"; }
		else{
			if($nbMotif == 1){ $html .="<script> $('#bouton_motif_valider').trigger('click'); </script>"; }
			$html .="<script> afficherMotif(1); </script>";
		}
		foreach ($motif_admission as $motif){
			$html .= "<script> setTimeout(function(){ $('#motif_admission".$i++."').val('".str_replace("'", "\'",$motif->libelle_motif)."'); });</script>";
		}
	
		//Récupération des constantes
		//Récupération des constantes
		$constantes = $this->getConsultationTable()->getConsultationParIdAdmission($id_admission);
		if($constantes){
			$tensions = explode(' ', $constantes['PRESSION_ARTERIELLE']);
			if($tensions && count($tensions) == 2){
				$html .="<script> $('#tensionmaximale').val('".$tensions[0]."'); </script>";
				$html .="<script> $('#tensionminimale').val('".$tensions[1]."'); </script>";
			}
			if($constantes['TEMPERATURE'] ){ $html .="<script> $('#temperature').val('".$constantes['TEMPERATURE']."'); </script>"; }
			if($constantes['POIDS']       ){ $html .="<script> $('#poids').val('".$constantes['POIDS']."'); </script>";             }
			if($constantes['TAILLE']      ){ $html .="<script> $('#taille').val('".$constantes['TAILLE']."'); </script>";           }
			if($constantes['POULS']       ){ $html .="<script> $('#pouls').val('".$constantes['POULS']."'); </script>";              }
			if($constantes['FREQUENCE_RESPIRATOIRE'] ){ $html .="<script> $('#frequence_respiratoire').val('".$constantes['FREQUENCE_RESPIRATOIRE']."'); </script>"; }
			if($constantes['GLYCEMIE_CAPILLAIRE']    ){ $html .="<script> $('#glycemie_capillaire').val('".$constantes['GLYCEMIE_CAPILLAIRE']."'); </script>";       }
			$html .="<script>setTimeout(function(){ $('#bouton_constantes_valider').trigger('click'); }); </script>";
		
			//GESTION DES BANDELETTES URINAIRE
			$bandelettes = $this->getConsultationTable ()->getBandelette($constantes['ID_CONS']);
			
			if($bandelettes['temoin'] == 1){
				if($bandelettes['albumine'] == 1){
					$html .="<script> setTimeout(function(){ $('#BUcheckbox input[name=albumine][value=".$bandelettes['albumine']."]').attr('checked', true); $('#BUcheckbox input[name=croixalbumine][value=".$bandelettes['croixalbumine']."]').attr('checked', true); albumineOption(); }, 1000); </script>";
				}
			
				if($bandelettes['sucre'] == 1){
					$html .="<script> setTimeout(function(){ $('#BUcheckbox input[name=sucre][value=".$bandelettes['sucre']."]').attr('checked', true); $('#BUcheckbox input[name=croixsucre][value=".$bandelettes['croixsucre']."]').attr('checked', true); sucreOption(); }, 1000); </script>";
				}
			
				if($bandelettes['corpscetonique'] == 1){
					$html .="<script> setTimeout(function(){ $('#BUcheckbox input[name=corpscetonique][value=".$bandelettes['corpscetonique']."]').attr('checked', true); $('#BUcheckbox input[name=croixcorpscetonique][value=".$bandelettes['croixcorpscetonique']."]').attr('checked', true); corpscetoniqueOption(); }, 1000); </script>";
				}
				 
				$html .="<script> $('#depliantBandelette').trigger('click'); </script>";
			}
		
		}
	
		if($role == "medecin"){
			$html .="<script> setTimeout(function(){ $('#bouton_motif_modifier, #bouton_constantes_modifier').trigger('click'); }, 500); </script>";
		}
		
		
		//Gestion des examens complémentaires
		//Gestion des examens complémentaires
		$listeExamensDemandes = $this->getMotifAdmissionTable ()->getListeExamensDemandes($id_admission);
		$infosExamenComp = "";
		for($iec = 0 ; $iec < count($listeExamensDemandes) ; $iec++){
			$infosExamenComp .= ($iec+1).') '.$listeExamensDemandes[$iec][0].' ( '.$listeExamensDemandes[$iec][1].' ) \n';
		}
		
		
		//Récupération du rpu_hospitalisation
		//Récupération du rpu_hospitalisation
		$rpu_hospitalisation = $this->getConsultationTable ()->getRpuHopitalisation($id_admission);
		$html .="<script> var rpu_hospitalisation = 0;  </script>";
		if($rpu_hospitalisation){
			if($rpu_hospitalisation['resume_syndromique'] ){ $html .="<script> $('#resume_syndromique').val('".preg_replace("/(\r\n|\n|\r)/", "\\n",str_replace("'", "\'",$rpu_hospitalisation['resume_syndromique']))."'); </script>"; }
			if($rpu_hospitalisation['hypotheses_diagnostiques'] ){ $html .="<script> $('#hypotheses_diagnostiques').val('".preg_replace("/(\r\n|\n|\r)/", "\\n",str_replace("'", "\'",$rpu_hospitalisation['hypotheses_diagnostiques']))."'); </script>"; }
			if($listeExamensDemandes){ $html .="<script> $('#examens_complementaires').val('".preg_replace("/(\r\n|\n|\r)/", "\\n",str_replace("'", "\'",$infosExamenComp))."'); </script>"; }
			if($rpu_hospitalisation['traitement'] ){ $html .="<script> $('#traitement').val('".preg_replace("/(\r\n|\n|\r)/", "\\n",str_replace("'", "\'",$rpu_hospitalisation['traitement']))."'); </script>"; }
			if($rpu_hospitalisation['resultats_examens_complementaires'] ){ $html .="<script> $('#resultats_examens_complementaires').val('".preg_replace("/(\r\n|\n|\r)/", "\\n",str_replace("'", "\'",$rpu_hospitalisation['resultats_examens_complementaires']))."'); </script>"; }
			if($rpu_hospitalisation['mise_a_jour_1'] ){ $html .="<script> $('#mise_a_jour_1').val('".preg_replace("/(\r\n|\n|\r)/", "\\n",str_replace("'", "\'",$rpu_hospitalisation['mise_a_jour_1']))."'); </script>"; }
			if($rpu_hospitalisation['mise_a_jour_2'] ){ $html .="<script> $('#mise_a_jour_2').val('".preg_replace("/(\r\n|\n|\r)/", "\\n",str_replace("'", "\'",$rpu_hospitalisation['mise_a_jour_2']))."'); </script>"; }
			if($rpu_hospitalisation['mise_a_jour_3'] ){ $html .="<script> $('#mise_a_jour_3').val('".preg_replace("/(\r\n|\n|\r)/", "\\n",str_replace("'", "\'",$rpu_hospitalisation['mise_a_jour_3']))."'); </script>"; }
			if($rpu_hospitalisation['avis_specialiste'] ){ $html .="<script> $('#avis_specialiste').val('".preg_replace("/(\r\n|\n|\r)/", "\\n",str_replace("'", "\'",$rpu_hospitalisation['avis_specialiste']))."'); </script>"; }
			if($rpu_hospitalisation['mutation'] ){ $html .="<script> $('#mutation').val('".preg_replace("/(\r\n|\n|\r)/", "\\n",str_replace("'", "\'",$rpu_hospitalisation['mutation']))."'); </script>"; }
		
			$html .="<script> var rpu_hospitalisation = 1;  </script>";
		}else{
			if($listeExamensDemandes){ $html .="<script> $('#examens_complementaires').val('".preg_replace("/(\r\n|\n|\r)/", "\\n",str_replace("'", "\'",$infosExamenComp))."'); </script>"; }
		}
	
		//Récupération du rpu_traumatisme
		//Récupération du rpu_traumatisme
		$rpu_traumatisme = $this->getConsultationTable ()->getRpuTraumatisme($id_admission);
		$html .="<script> var rpu_traumatisme = 0;  </script>";
		if($rpu_traumatisme){
			if($rpu_traumatisme['cote_dominant'] ){ $html .="<script> $('#rpu_traumatisme_cote_dominant').val('".preg_replace("/(\r\n|\n|\r)/", "\\n",str_replace("'", "\'",$rpu_traumatisme['cote_dominant']))."'); </script>"; }
			if($rpu_traumatisme['date_histoire_maladie'] ){ $html .="<script> $('#rpu_traumatisme_date_heure').val('".preg_replace("/(\r\n|\n|\r)/", "\\n",str_replace("'", "\'",(new DateHelper())->convertDateTimeHm ($rpu_traumatisme['date_histoire_maladie'])))."'); </script>"; }
			if($rpu_traumatisme['circonstances_maladie'] ){ $html .="<script> $('#rpu_traumatisme_circonstances').val('".preg_replace("/(\r\n|\n|\r)/", "\\n",str_replace("'", "\'",$rpu_traumatisme['circonstances_maladie']))."'); </script>"; }
			
			if($rpu_traumatisme['antecedent'] ){ $html .="<script> $('#rpu_traumatisme_antecedent').val('".preg_replace("/(\r\n|\n|\r)/", "\\n",str_replace("'", "\'",$rpu_traumatisme['antecedent']))."'); </script>"; }
			if($rpu_traumatisme['examen_physique'] ){ $html .="<script> $('#rpu_traumatisme_examen_physique').val('".preg_replace("/(\r\n|\n|\r)/", "\\n",str_replace("'", "\'",$rpu_traumatisme['examen_physique']))."'); </script>"; }
			if($rpu_traumatisme['examen_paraclinique'] ){ $html .="<script> $('#rpu_traumatisme_examen_paraclinique').val('".preg_replace("/(\r\n|\n|\r)/", "\\n",str_replace("'", "\'",$rpu_traumatisme['examen_paraclinique']))."'); </script>"; }
			if($rpu_traumatisme['resultat_examen_complementaire'] ){ $html .="<script> $('#rpu_traumatisme_resultat_examen_complementaire').val('".preg_replace("/(\r\n|\n|\r)/", "\\n",str_replace("'", "\'",$rpu_traumatisme['resultat_examen_complementaire']))."'); </script>"; }
			
			if($rpu_traumatisme['mecanismes_maladie'] ){ $html .="<script> $('#rpu_traumatisme_mecanismes').val('".preg_replace("/(\r\n|\n|\r)/", "\\n",str_replace("'", "\'",$rpu_traumatisme['mecanismes_maladie']))."'); </script>"; }
			if($rpu_traumatisme['mecanismes_maladie_precision'] ){ $html .="<script> $('#rpu_traumatisme_mecanismes_precision').val('".preg_replace("/(\r\n|\n|\r)/", "\\n",str_replace("'", "\'",$rpu_traumatisme['mecanismes_maladie_precision']))."').attr('readonly', false); </script>"; }
			if($rpu_traumatisme['diagnostic'] ){ $html .="<script> $('#rpu_traumatisme_diagnostic').val('".preg_replace("/(\r\n|\n|\r)/", "\\n",str_replace("'", "\'",$rpu_traumatisme['diagnostic']))."'); </script>"; }
			if($rpu_traumatisme['diagnostic_precision'] ){ $html .="<script> $('#rpu_traumatisme_diagnostic_precision').val('".preg_replace("/(\r\n|\n|\r)/", "\\n",str_replace("'", "\'",$rpu_traumatisme['diagnostic_precision']))."').attr('readonly', false); </script>"; }
			if($rpu_traumatisme['indication'] ){ $html .="<script> $('#rpu_traumatisme_indication').val('".preg_replace("/(\r\n|\n|\r)/", "\\n",str_replace("'", "\'",$rpu_traumatisme['indication']))."'); </script>"; }
			if($rpu_traumatisme['indication_precision'] ){ $html .="<script> $('#rpu_traumatisme_indication_precision').val('".preg_replace("/(\r\n|\n|\r)/", "\\n",str_replace("'", "\'",$rpu_traumatisme['indication_precision']))."').attr('readonly', false); </script>"; }
			
			if($rpu_traumatisme['conduite_a_tenir'] ){ $html .="<script> $('#rpu_traumatisme_conduite').val('".preg_replace("/(\r\n|\n|\r)/", "\\n",str_replace("'", "\'",$rpu_traumatisme['conduite_a_tenir']))."'); </script>"; }
			if($rpu_traumatisme['mode_sortie'] ){ $html .="<script> $('#rpu_traumatisme_motif_sortie').val('".preg_replace("/(\r\n|\n|\r)/", "\\n",str_replace("'", "\'",$rpu_traumatisme['mode_sortie']))."'); </script>"; }
			if($rpu_traumatisme['rendez_vous'] ){ $html .="<script> $('#rpu_traumatisme_rendez_vous').val('".preg_replace("/(\r\n|\n|\r)/", "\\n",str_replace("'", "\'",$rpu_traumatisme['rendez_vous']))."'); </script>"; }
			
			if($rpu_traumatisme['avis_specialiste'] ){ $html .="<script> $('#rpu_traumatisme_avis_specialiste_trauma').val('".preg_replace("/(\r\n|\n|\r)/", "\\n",str_replace("'", "\'",$rpu_traumatisme['avis_specialiste']))."'); </script>"; }
			if($rpu_traumatisme['conduite_a_tenir_specialiste'] ){ $html .="<script> $('#rpu_traumatisme_conduite_specialiste').val('".preg_replace("/(\r\n|\n|\r)/", "\\n",str_replace("'", "\'",$rpu_traumatisme['conduite_a_tenir_specialiste']))."'); </script>"; }
		
			$html .="<script> var rpu_traumatisme = 1;  </script>";
		}
		
		//Récupération du rpu_sortie
		//Récupération du rpu_sortie
		$rpu_sortie = $this->getConsultationTable ()->getRpuSortie($id_admission);
		$html .="<script> var rpu_sortie = 0;  </script>";
		if($rpu_sortie){
			if($rpu_sortie['diagnostic_principal'] ){ $html .="<script> $('#rpu_sortie_diagnostic_principal').val('".preg_replace("/(\r\n|\n|\r)/", "\\n",str_replace("'", "\'",$rpu_sortie['diagnostic_principal']))."'); </script>"; }
			if($rpu_sortie['diagnostic_associe'] ){ $html .="<script> $('#rpu_sortie_diagnostic_associe').val('".preg_replace("/(\r\n|\n|\r)/", "\\n",str_replace("'", "\'",$rpu_sortie['diagnostic_associe']))."'); </script>"; }
			if($rpu_sortie['traitement_sortie'] ){ $html .="<script> $('#rpu_sortie_traitement').val('".preg_replace("/(\r\n|\n|\r)/", "\\n",str_replace("'", "\'",$rpu_sortie['traitement_sortie']))."'); </script>"; }
			if($rpu_sortie['examens_complementaires_demandes'] ){ $html .="<script> $('#rpu_sortie_examens_complementaires_demandes').val('".preg_replace("/(\r\n|\n|\r)/", "\\n",str_replace("'", "\'",$rpu_sortie['examens_complementaires_demandes']))."'); </script>"; }
			if($rpu_sortie['mode_sortie'] ){ $html .="<script> setTimeout(function(){ getChoixMotif(".$rpu_sortie['mode_sortie']."); }); $('#rpu_sortie_motif_sortie').val('".preg_replace("/(\r\n|\n|\r)/", "\\n",str_replace("'", "\'",$rpu_sortie['mode_sortie']))."'); </script>"; }
		
		
			if( $rpu_sortie['mode_sortie']  == 4 ){
				$html .="<script> $('#rpu_sortie_liste_mutation').val('".$rpu_sortie['type_mode_sortie']."'); </script>";
			}else if($rpu_sortie['mode_sortie']  == 5 ){
				$html .="<script> $('#rpu_sortie_transfert').val('".$rpu_sortie['type_mode_sortie']."'); </script>";
			}else if($rpu_sortie['mode_sortie']  == 6 ){
				$html .="<script> $('#rpu_sortie_evacuation').val('".$rpu_sortie['type_mode_sortie']."'); </script>";
			}
			
			$html .="<script> var rpu_sortie = 1;  </script>";
		}
		
		
		if($role == 'specialiste'){
			$html .="<script> if(rpu_hospitalisation == 0){ $('.rpu_hospitalisation_donnees_onglet').toggle(false); } </script>";
			$html .="<script> if(rpu_traumatisme == 0){ $('.rpu_traumatisme_donnees_onglet').toggle(false); }  </script>";
			$html .="<script> if(rpu_sortie == 0){ $('.rpu_sortie_donnees_onglet').toggle(false); }  </script>";
		}
		
		
		if($historique == 1){
			if($role == 'medecin'){
				$html .="<script> if(rpu_hospitalisation == 0){ $('.rpu_hospitalisation_donnees_onglet').toggle(false); } </script>";
				$html .="<script> if(rpu_traumatisme == 0){ $('.rpu_traumatisme_donnees_onglet').toggle(false); }  </script>";
				$html .="<script> if(rpu_sortie == 0){ $('.rpu_sortie_donnees_onglet').toggle(false); }  </script>";
			}
			
			$heure = "";
			if($constantes){ $heure = " - ".$constantes['HEURECONS'];}
			$html .="<script> $('#infoDateConsultation').html(' {<span style=\'font-weight: normal;\'> date de consultation :</span> ".(new DateHelper())->convertDate($admission->date).$heure." }'); </script>";
		}
		
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ( $html ) );
	}
	
	//Verifier si un tableau est vide ou pas
	function array_empty($array) {
		$is_empty = true;
		foreach($array as $k) {
			$is_empty = $is_empty && empty($k);
		}
		return $is_empty;
	}
	
	public function enregistrementDonneesConsultationAction() {
	
		$this->layout ()->setTemplate ( 'layout/urgence' );
		$user = $this->layout()->user;
		$role = $user['role'];
	
		$today = new \DateTime ();
		$date = $today->format( 'Y-m-d' );
		$heure = $today->format( 'H:i:s' );
	
		$form = new AdmissionForm();
		$formData = $this->getRequest ()->getPost ();
		$form->setData ( $formData );
	
		$id_cons = $form->get ( "id_cons" )->getValue ();
		$id_patient = $this->params ()->fromPost( "id_patient" );
		$id_medecin = $user['id_employe'];
	
		$id_admission = $this->params ()->fromPost( "id_admission" );
		$niveau = $this->params ()->fromPost( "niveau" );
	
		//Recuperation des donnees des motifs d'admission
		$donneesMotifAdmission	 = array(
				'motif_admission1' => trim($form->get ( 'motif_admission1' )->getValue ()),
				'motif_admission2' => trim($form->get ( 'motif_admission2' )->getValue ()),
				'motif_admission3' => trim($form->get ( 'motif_admission3' )->getValue ()),
				'motif_admission4' => trim($form->get ( 'motif_admission4' )->getValue ()),
				'motif_admission5' => trim($form->get ( 'motif_admission5' )->getValue ()),
		);
	
		//Recuperation des donnees des constantes
		$donneesConstantes = array(
				'POIDS' => (float)trim($form->get ( "poids" )->getValue ()),
				'TAILLE' => (float)trim($form->get ( "taille" )->getValue ()),
				'TEMPERATURE' => (float)trim($form->get ( "temperature" )->getValue ()),
				'PRESSION_ARTERIELLE' => trim( trim($form->get ( "tensionmaximale" )->getValue ()).' '.trim($form->get ( "tensionminimale" )->getValue ()) ),
				'POULS' => (float)trim($form->get ( "pouls" )->getValue ()),
				'FREQUENCE_RESPIRATOIRE' => (float)trim($form->get ( "frequence_respiratoire" )->getValue ()),
				'GLYCEMIE_CAPILLAIRE' => (float)trim($form->get ( "glycemie_capillaire" )->getValue ()),
		);
		
		//Recuperer les donnees sur les bandelettes urinaires
		//Recuperer les donnees sur les bandelettes urinaires
		$bandelettes = array(
				'albumine' => $this->params()->fromPost('albumine'),
				'sucre' => $this->params()->fromPost('sucre'),
				'corpscetonique' => $this->params()->fromPost('corpscetonique'),
				'croixalbumine' => $this->params()->fromPost('croixalbumine'),
				'croixsucre' => $this->params()->fromPost('croixsucre'),
				'croixcorpscetonique' => $this->params()->fromPost('croixcorpscetonique'),
		);
		
		//Recuperation des donnees sur le mode d'entree et le mode de transport
		//Recuperation des donnees sur le mode d'entree et le mode de transport
		$mode_entree = (int)$this->params()->fromPost('mode_entree');
		$mode_entree == 0 ? $mode_entree=null : $mode_entree;
		$mode_transport = $this->params()->fromPost('mode_transport');
		$mode_transport == 0 ? $mode_transport=null :  $mode_transport;
		$precicion_provenance = $this->params()->fromPost('precision_provenance');
		if($mode_entree == 2){ $precicion_provenance = ''; }
		$mode_entree_et_mode_transport = array(
				'mode_entree' => $mode_entree,
				'precision_provenance' => $precicion_provenance,
				'mode_transport' => $mode_transport,
		);
		
		
		$dateHeureRpuTrauma = $this->params ()->fromPost ( 'rpu_traumatisme_date_heure' );
		if($dateHeureRpuTrauma){
			$dateRpuTrauma = (new DateHelper())->convertDateInAnglais($dateHeureRpuTrauma);
			$timeRpuTrauma = (new DateHelper())->getTimeInDateTimeDHM($dateHeureRpuTrauma);
			$dateHeureRpuTraumaConvert = $dateRpuTrauma.' '.$timeRpuTrauma;
		}else{
			$dateHeureRpuTraumaConvert = '';
		}

		//POUR LES ANTECEDENTS ANTECEDENTS ANTECEDENTS
		//POUR LES ANTECEDENTS ANTECEDENTS ANTECEDENTS
		//POUR LES ANTECEDENTS ANTECEDENTS ANTECEDENTS
		$donneesDesAntecedents = array(
				//**=== ANTECEDENTS PERSONNELS
				//**=== ANTECEDENTS PERSONNELS
				//LES HABITUDES DE VIE DU PATIENTS
				/*Alcoolique*/
				'AlcooliqueHV' => $this->params()->fromPost('AlcooliqueHV'),
				'DateDebutAlcooliqueHV' => $this->params()->fromPost('DateDebutAlcooliqueHV'),
				'DateFinAlcooliqueHV' => $this->params()->fromPost('DateFinAlcooliqueHV'),
				/*Fumeur*/
				'FumeurHV' => $this->params()->fromPost('FumeurHV'),
				'DateDebutFumeurHV' => $this->params()->fromPost('DateDebutFumeurHV'),
				'DateFinFumeurHV' => $this->params()->fromPost('DateFinFumeurHV'),
				'nbPaquetFumeurHV' => $this->params()->fromPost('nbPaquetFumeurHV'),
				/*Droguer*/
				'DroguerHV' => $this->params()->fromPost('DroguerHV'),
				'DateDebutDroguerHV' => $this->params()->fromPost('DateDebutDroguerHV'),
				'DateFinDroguerHV' => $this->params()->fromPost('DateFinDroguerHV'),
					
				//LES ANTECEDENTS MEDICAUX
		        'DiabeteAM' => $this->params()->fromPost('DiabeteAM'),
		        'htaAM' => $this->params()->fromPost('htaAM'),
		        'drepanocytoseAM' => $this->params()->fromPost('drepanocytoseAM'),
		        'dislipidemieAM' => $this->params()->fromPost('dislipidemieAM'),
		        'asthmeAM' => $this->params()->fromPost('asthmeAM'),
			
        		//GYNECO-OBSTETRIQUE
		        /*Menarche*/
	        	'MenarcheGO' => $this->params()->fromPost('MenarcheGO'),
        		'NoteMenarcheGO' => $this->params()->fromPost('NoteMenarcheGO'),
	        	/*Gestite*/
	         	'GestiteGO' => $this->params()->fromPost('GestiteGO'),
        		'NoteGestiteGO' => $this->params()->fromPost('NoteGestiteGO'),
		        /*Parite*/
		        'PariteGO' => $this->params()->fromPost('PariteGO'),
		        'NotePariteGO' => $this->params()->fromPost('NotePariteGO'),
		        /*Cycle*/
		        'CycleGO' => $this->params()->fromPost('CycleGO'),
		        'DureeCycleGO' => $this->params()->fromPost('DureeCycleGO'),
		        'RegulariteCycleGO' => $this->params()->fromPost('RegulariteCycleGO'),
		        'DysmenorrheeCycleGO' => $this->params()->fromPost('DysmenorrheeCycleGO'),
			
	        	//**=== ANTECEDENTS FAMILIAUX
		        //**=== ANTECEDENTS FAMILIAUX
		        'DiabeteAF' => $this->params()->fromPost('DiabeteAF'),
		        'NoteDiabeteAF' => $this->params()->fromPost('NoteDiabeteAF'),
		        'DrepanocytoseAF' => $this->params()->fromPost('DrepanocytoseAF'),
		        'NoteDrepanocytoseAF' => $this->params()->fromPost('NoteDrepanocytoseAF'),
		        'htaAF' => $this->params()->fromPost('htaAF'),
		        'NoteHtaAF' => $this->params()->fromPost('NoteHtaAF'),
		);
		
		$this->getAntecedantPersonnelTable()->addAntecedentsPersonnels($donneesDesAntecedents, $id_patient, $id_medecin);
		$this->getAntecedantsFamiliauxTable()->addAntecedentsFamiliaux($donneesDesAntecedents, $id_patient, $id_medecin);
		
		
		//les antecedents medicaux du patient a ajouter
		$this->getConsultationTable()->addAntecedentMedicaux($formData, $id_medecin);
		$this->getConsultationTable()->addAntecedentMedicauxPersonne($formData, $id_medecin);
		
		
		$id_admission = $this->params ()->fromPost( "id_admission" );
		if($id_admission){
			
			//**** MISE A JOUR DES DONNNEES SUR L'ADMISSION ****
			//**** MISE A JOUR DES DONNNEES SUR L'ADMISSION ****
			//**** MISE A JOUR DES DONNNEES SUR L'ADMISSION ****
			
				
			//Mise à jour de l'admission 
			//Mise à jour de l'admission 
			$donneesAdmission = array(
					'niveau' => $niveau,
					'salle'  => trim($form->get ( "salle" )->getValue ()),
					'lit'    => trim($form->get ( "lit" )->getValue ()),
					'couloir' => trim($form->get ( "couloir" )->getValue ()),
			);
			$this->getAdmissionTable()->updateAdmission($donneesAdmission, $id_admission);
			
			
			//Insertion des donnees sur le mode d'entrée et le mode de transport
			//Insertion des donnees sur le mode d'entrée et le mode de transport
			$mode_entree = $this->getAdmissionTable()->getModeEntreeModeTransport($id_admission);
			if($mode_entree){
				$this->getAdmissionTable()->updateModeEntreeModeTransport($mode_entree_et_mode_transport, $id_admission);
			}else{
				$mode_entree_et_mode_transport['id_admission'] = $id_admission;
				$this->getAdmissionTable()->addModeEntreeModeTransport($mode_entree_et_mode_transport);
			}
			
			
			//Insertion des motifs de l'admission s'il y'en a
			//Insertion des motifs de l'admission s'il y'en a
			$this->getMotifAdmissionTable ()->deleteMotifAdmission($id_admission);
			if(!$this->array_empty($donneesMotifAdmission)){
				$this->getMotifAdmissionTable ()->addMotifAdmission ( $form , $id_admission);
			}
			
			//**** Donnees du RPU-Hospitalisation ****
			//**** Donnees du RPU-Hospitalisation ****
			$mutation = (int)$form->get ( "mutation" )->getValue ();
			$mutation == 0 ? $mutation=null :  $mutation;
			$donneesRpuHospitalisation = array(
					'resume_syndromique'  => $form->get ( "resume_syndromique" )->getValue (),
					'hypotheses_diagnostiques' => $form->get ( "hypotheses_diagnostiques" )->getValue (),
					'examens_complementaires' => $form->get ( "examens_complementaires" )->getValue (),
					'traitement' => $form->get ( "traitement" )->getValue (),
					'resultats_examens_complementaires' => $form->get ( "resultats_examens_complementaires" )->getValue (),
					'mutation' => $mutation,
					'mise_a_jour_1' => $form->get ( "mise_a_jour_1" )->getValue (),
					'mise_a_jour_2' => $form->get ( "mise_a_jour_2" )->getValue (),
					'mise_a_jour_3' => $form->get ( "mise_a_jour_3" )->getValue (),
					
					'id_employe' => $id_medecin,
			);
			//var_dump($donneesRpuHospitalisation); exit();
			//**** Donnees du RPU-Traumatisme ****
			//**** Donnees du RPU-Traumatisme ****
			$donneesRpuTraumatisme = array(
					'cote_dominant' => $this->params ()->fromPost ( 'rpu_traumatisme_cote_dominant' ),
					'date_histoire_maladie' => $dateHeureRpuTraumaConvert,
					'circonstances_maladie' => $this->params ()->fromPost ( 'rpu_traumatisme_circonstances' ),
					
					'antecedent' => $this->params ()->fromPost ( 'rpu_traumatisme_antecedent' ),
					'examen_physique' => $this->params ()->fromPost ( 'rpu_traumatisme_examen_physique' ),
					'examen_paraclinique' => $this->params ()->fromPost ( 'rpu_traumatisme_examen_paraclinique' ),
					'resultat_examen_complementaire' => $this->params ()->fromPost ( 'rpu_traumatisme_resultat_examen_complementaire' ),
					
					'mecanismes_maladie'    => $this->params ()->fromPost ( 'rpu_traumatisme_mecanismes' ),
					'mecanismes_maladie_precision'    => $this->params ()->fromPost ( 'rpu_traumatisme_mecanismes_precision' ),
					
					'indication' => $form->get ( "rpu_traumatisme_indication" )->getValue (),
					'indication_precision' => $form->get ( "rpu_traumatisme_indication_precision" )->getValue (),
					
					'diagnostic' => $form->get ( "rpu_traumatisme_diagnostic" )->getValue (),
					'diagnostic_precision' => $form->get ( "rpu_traumatisme_diagnostic_precision" )->getValue (),
					
					'mode_sortie' => $form->get ( "rpu_traumatisme_motif_sortie" )->getValue (),
					'rendez_vous' => $form->get ( "rpu_traumatisme_rendez_vous" )->getValue (),
					'conduite_a_tenir' => $form->get ( "rpu_traumatisme_conduite" )->getValue (),
					
					'id_employe' => $id_medecin,
			);
			
			//**** Donnees du RPU-Sortie ****
			//**** Donnees du RPU-Sortie ****
			$donneesRpuSortie = array(
					'diagnostic_principal' => $form->get ( "rpu_sortie_diagnostic_principal" )->getValue (),
					'diagnostic_associe' => $form->get ( "rpu_sortie_diagnostic_associe" )->getValue (),
					'traitement_sortie' => $form->get ( "rpu_sortie_traitement" )->getValue (),
					'examens_complementaires_demandes' => $form->get ( "rpu_sortie_examens_complementaires_demandes" )->getValue (),
					'mode_sortie' => $form->get ( "rpu_sortie_motif_sortie" )->getValue (),
			);
			
			if( $donneesRpuSortie['mode_sortie'] == 4 ){
				$donneesRpuSortie['type_mode_sortie'] = $form->get ( "rpu_sortie_liste_mutation" )->getValue ();
			}else if($donneesRpuSortie['mode_sortie'] == 5 ){
				$donneesRpuSortie['type_mode_sortie'] = $form->get ( "rpu_sortie_transfert" )->getValue ();
			}else if($donneesRpuSortie['mode_sortie'] == 6 ){
				$donneesRpuSortie['type_mode_sortie'] = $form->get ( "rpu_sortie_evacuation" )->getValue ();
			}else{
				$donneesRpuSortie['type_mode_sortie'] = 0;
			}
			
			$donneesRpuSortie['id_employe'] = $id_medecin;
			
			
			//Modification des constantes
			//Modification des constantes
			$consultation_urgence = $this->getConsultationTable ()->getConsultationUrgence($id_admission);
			if($consultation_urgence){
				$donneesConstantes['ID_MEDECIN'] = (int)$id_medecin;
				if($donneesRpuHospitalisation['resume_syndromique'] || $donneesRpuHospitalisation['hypotheses_diagnostiques'] ||
				   $donneesRpuTraumatisme['diagnostic'] ||  $donneesRpuTraumatisme['circonstances_maladie'] ||
				   $donneesRpuSortie['diagnostic_principal'] || $donneesRpuSortie['traitement_sortie'] 
			       ){
					
					$donneesConstantes['CONSPRISE']  = 1;
				}
				$this->getConsultationTable ()->updateConsultationUrgence($donneesConstantes, $consultation_urgence['id_cons']);
				
				//mettre à jour les bandelettes urinaires
				$bandelettes['id_cons'] = $consultation_urgence['id_cons'];
				$this->getConsultationTable ()->deleteBandelette($consultation_urgence['id_cons']);
				$this->getConsultationTable ()->addBandelette($bandelettes);
			}
			
			//**** ENREGISTREMENT DES DONNEES DE CONSULTATION DU MEDECIN
			//**** ENREGISTREMENT DES DONNEES DE CONSULTATION DU MEDECIN
			//**** ENREGISTREMENT DES DONNEES DE CONSULTATION DU MEDECIN
			//**** RPU -- HOSPITALISATION 
			//**** RPU -- HOSPITALISATION
			$rpu_hopitalisation = $this->getConsultationTable ()->getRpuHopitalisation($id_admission);
			if($rpu_hopitalisation){
				$donneesRpuHospi = $donneesRpuHospitalisation;
				$data = array_splice($donneesRpuHospitalisation, 0, -1);
				
				if(!$this->array_empty($data)){
					$this->getConsultationTable ()->updateRpuHopitalisation($donneesRpuHospi, $id_admission);
				}else{
					$this->getConsultationTable ()->deleteRpuHospitalisation($id_admission);
				}

			}else{
				$donneesRpuHospitalisation ['id_admission_urgence'] = $id_admission;
				$donneesRpuHospitalisation ['date_enregistrement']  = $date.' '.$heure;
				
				if($donneesRpuHospitalisation['resume_syndromique'] || $donneesRpuHospitalisation['hypotheses_diagnostiques']){
					$this->getConsultationTable ()->addRpuHopitalisation($donneesRpuHospitalisation);
				}
			}
			
			//**** RPU -- TRAUMATISME 
			//**** RPU -- TRAUMATISME
			$rpu_traumatisme = $this->getConsultationTable ()->getRpuTraumatisme($id_admission);
			if($rpu_traumatisme){
				$donneesRpuTrauma = $donneesRpuTraumatisme;
				$data = array_splice($donneesRpuTraumatisme, 0, -1);
				
				if(!$this->array_empty($data)){
					$this->getConsultationTable ()->updateRpuTraumatisme($donneesRpuTrauma, $id_admission);
				}else{
					$this->getConsultationTable ()->deleteRpuTraumatisme($id_admission);
				}
				
			}else{
				$donneesRpuTraumatisme ['id_admission_urgence'] = $id_admission;
				$donneesRpuTraumatisme ['date_enregistrement']  = $date.' '.$heure;
				
				if($donneesRpuTraumatisme['cote_dominant'] || $donneesRpuTraumatisme['diagnostic'] || $donneesRpuTraumatisme['conduite_a_tenir'] ){
					$this->getConsultationTable ()->addRpuTraumatisme($donneesRpuTraumatisme);
				}
			}
			
			//**** RPU -- SORTIE
			//**** RPU -- SORTIE
			$rpu_sortie = $this->getConsultationTable ()->getRpuSortie($id_admission);
			if($rpu_sortie){
				$donneesRpuSort = $donneesRpuSortie;
				$data = array_splice($donneesRpuSortie, 0, -1);
				
				if(!$this->array_empty($data)){
					$this->getConsultationTable ()->updateRpuSortie($donneesRpuSort, $id_admission);
				}else{
					$this->getConsultationTable ()->deleteRpuSortie($id_admission);
				}

			}else{
				$donneesRpuSortie ['id_admission_urgence'] = $id_admission;
				$donneesRpuSortie ['date_enregistrement']  = $date.' '.$heure;
			
				if($donneesRpuSortie['diagnostic_principal'] || $donneesRpuSortie['traitement_sortie']){
					$this->getConsultationTable ()->addRpuSortie($donneesRpuSortie);
				}
			}
			
		}
		
		return $this->redirect ()->toRoute ('consultation', array ('action' => 'liste-patients-admis' ));
	}
	

	public function enregistrementDonneesConsultationSpecialisteAction() {
		$user = $this->layout()->user;
		$role = $user['role'];
		
		$today = new \DateTime ();
		$date = $today->format( 'Y-m-d' );
		$heure = $today->format( 'H:i:s' );
		
		$form = new AdmissionForm();
		$formData = $this->getRequest ()->getPost ();
		$form->setData ( $formData );
		
		$id_cons = $form->get ( "id_cons" )->getValue ();
		$id_patient = $this->params ()->fromPost( "id_patient" );
		$id_medecin = $user['id_employe'];
		
		$id_admission = $this->params ()->fromPost( "id_admission" );
		
		//**** Donnees du RPU-Hospitalisation ****
		//**** Donnees du RPU-Hospitalisation ****
		$donneesRpuHospitalisation = array(
				'avis_specialiste' => $form->get ( "avis_specialiste" )->getValue (),
				'specialiste' => $id_medecin,
		);
		$rpu_hopitalisation = $this->getConsultationTable ()->getRpuHopitalisation($id_admission);
		
		if($rpu_hopitalisation){
			$this->getConsultationTable ()->updateRpuHopitalisation($donneesRpuHospitalisation, $id_admission);
		}
		
		//**** Donnees du RPU-Traumatisme ****
		//**** Donnees du RPU-Traumatisme ****
		$donneesRpuTraumatisme = array(
				'avis_specialiste' => $form->get ( "rpu_traumatisme_avis_specialiste_trauma" )->getValue (),
				'conduite_a_tenir_specialiste' => $form->get ( "rpu_traumatisme_conduite_specialiste" )->getValue (),
				'specialiste' => $id_medecin,
		);
		
		$rpu_traumatisme = $this->getConsultationTable ()->getRpuTraumatisme($id_admission);
		if($rpu_traumatisme){
			$this->getConsultationTable ()->updateRpuTraumatisme($donneesRpuTraumatisme, $id_admission);
		}
			
		//**** Donnees du RPU-Sortie ****
		//**** Donnees du RPU-Sortie ****
		//$donneesRpuSortie = array(
		//		'specialiste' => $id_medecin,
		//);
		
		
		return $this->redirect ()->toRoute ('consultation', array ('action' => 'liste-patients-admis' ));
	}
	
	
	public function getHistoriquesTerrainParticulierAction() {
		
		$dateConvert = new DateHelper();
		$id_patient = $this->params ()->fromPost( "id_patient" );
		$infosPatient = $this->getPatientTable()->getInfoPatient($id_patient);
		
		//Enregistrement et mise à jour des données sur l'admission et la consultation
		//Enregistrement et mise à jour des données sur l'admission et la consultation
		$donneesAntecedentsPersonnels = $this->getAntecedantPersonnelTable()->getTableauAntecedentsPersonnels($id_patient);
		$donneesAntecedentsFamiliaux = $this->getAntecedantsFamiliauxTable()->getTableauAntecedentsFamiliaux($id_patient);
		
		$html  ="<script>
				   var temoinAlcoolique = ".$donneesAntecedentsPersonnels['AlcooliqueHV']."; 
				   if(temoinAlcoolique == 1){ 
				   		$('#AlcooliqueHV').trigger('click');";
		
		         if($donneesAntecedentsPersonnels['AlcooliqueHV'] != 0){
		         	$html .=" 
		         			  $('#DateDebutAlcooliqueHV').val('".$donneesAntecedentsPersonnels['DateDebutAlcooliqueHV']."');
		         	          $('#DateFinAlcooliqueHV').val('".$donneesAntecedentsPersonnels['DateFinAlcooliqueHV']."');
		         	        ";
		         }

	    $html .=" }  </script>";
		
	    $html .="<script>
				   var temoinFumeurHV  = ".$donneesAntecedentsPersonnels['FumeurHV'].";
				   if(temoinFumeurHV == 1){
				   		$('#FumeurHV').trigger('click');";
	    
	             if($donneesAntecedentsPersonnels['FumeurHV'] != 0){
	    	        $html .="
	    	        		 $('#DateDebutFumeurHV').val('".$donneesAntecedentsPersonnels['DateDebutFumeurHV']."');
                             $('#DateFinFumeurHV').val('".$donneesAntecedentsPersonnels['DateFinFumeurHV']."');
                             var nbPaquetFumeurHV = ".$donneesAntecedentsPersonnels['nbPaquetFumeurHV'].";
                             $('#nbPaquetFumeurHV').val('".$donneesAntecedentsPersonnels['nbPaquetFumeurHV']."');
		         	        ";
	             }
				 
	    $html .=" }  </script>";
		
	    $html .="<script>
				   var temoinDroguerHV = ".$donneesAntecedentsPersonnels['DroguerHV'].";
				   if(temoinDroguerHV == 1){
				   		$('#DroguerHV').trigger('click');";
	     
	               if($donneesAntecedentsPersonnels['DroguerHV'] != 0){
	    	          $html .="
	    	        		   $('#DroguerHV').trigger('click'); 
                   		       $('#DateDebutDroguerHV').val('".$donneesAntecedentsPersonnels['DateDebutDroguerHV']."');
                   		       $('#DateFinDroguerHV').val('".$donneesAntecedentsPersonnels['DateFinDroguerHV']."');
	                          ";
	               }
	    	
	    $html .=" }  </script>";
	    
	    $html .="<script>
	    		   var temoinDiabeteAM = ".$donneesAntecedentsPersonnels['DiabeteAM'].";
                   if(temoinDiabeteAM == 1){ $('#DiabeteAM').trigger('click'); }
                   var temoindrepanocytoseAM =  ".$donneesAntecedentsPersonnels['drepanocytoseAM'].";	
                   if(temoindrepanocytoseAM == 1){ $('#drepanocytoseAM').trigger('click'); }
                   var temoinhtaAM = ".$donneesAntecedentsPersonnels['htaAM'].";
                   if(temoinhtaAM == 1){ $('#htaAM').trigger('click'); }
                   var temoindislipidemieAM = ".$donneesAntecedentsPersonnels['dislipidemieAM'].";
                   if(temoindislipidemieAM == 1){ $('#dislipidemieAM').trigger('click'); }
                   var temoinasthmeAM = ".$donneesAntecedentsPersonnels['asthmeAM'].";
                   if(temoinasthmeAM == 1){ $('#asthmeAM').trigger('click'); }
                 </script>
	    		"; 
	    
	    $html .="<script>
				   var temoinMenarcheGO = ".$donneesAntecedentsPersonnels['MenarcheGO'].";
				   if(temoinMenarcheGO == 1){ ";
	    
	               if($donneesAntecedentsPersonnels['MenarcheGO'] != 0){
	    	          $html .="
	    	        		    $('#MenarcheGO').trigger('click');
	    	          		    $('#NoteMenarcheGO').val('".$donneesAntecedentsPersonnels['NoteMenarcheGO']."');
	                          ";
	               }
	    
	    $html .="  }  </script>";
	    
	    $html .="<script>
				   var temoinGestiteGO = ".$donneesAntecedentsPersonnels['GestiteGO'].";
				   if(temoinGestiteGO == 1){ ";
	     
	               if($donneesAntecedentsPersonnels['GestiteGO'] != 0){
	    	          $html .="
	    	        		    $('#GestiteGO').trigger('click'); 
                   		        $('#NoteGestiteGO').val('".$donneesAntecedentsPersonnels['NoteGestiteGO']."');
	                          ";
	               }
	     
	    $html .="  }  </script>";
	    
	    $html .="<script>
				   var temoinPariteGO = ".$donneesAntecedentsPersonnels['PariteGO'].";
				   if(temoinPariteGO == 1){ ";
	    
	               if($donneesAntecedentsPersonnels['PariteGO'] != 0){
	    	          $html .="
	    	        		    $('#PariteGO').trigger('click'); 
	    	          		    $('#NotePariteGO').val('".$donneesAntecedentsPersonnels['NotePariteGO']."');
	                          ";
	               }
	    
	    $html .="  }  </script>";
	    
	    $html .="<script>
				   var temoinCycleGO = ".$donneesAntecedentsPersonnels['CycleGO'].";
				   if(temoinCycleGO == 1){ ";
	     
	               if($donneesAntecedentsPersonnels['CycleGO'] != 0){
	    	          $html .="
	    	        		    $('#CycleGO').trigger('click');
                                $('#DureeCycleGO').val('".$donneesAntecedentsPersonnels['DureeCycleGO']."');
                   		        $('#RegulariteCycleGO').val('".$donneesAntecedentsPersonnels['RegulariteCycleGO']."');
                   		        $('#DysmenorrheeCycleGO').val('".$donneesAntecedentsPersonnels['DysmenorrheeCycleGO']."');
	                          ";
	               }
	    
	    $html .="  }  </script>";
	    
	    $html .="<script>
				   var temoinDiabeteAF = ".$donneesAntecedentsFamiliaux['DiabeteAF'].";
				   if(temoinDiabeteAF == 1){ ";
	    
	               if($donneesAntecedentsFamiliaux['DiabeteAF'] != 0){
	    	          $html .="
	    	          		    $('#DiabeteAF').trigger('click'); 		
                   		        $('#NoteDiabeteAF').val('".$donneesAntecedentsFamiliaux['NoteDiabeteAF']."');
	                          ";
	               }
	     
	    $html .="  }  </script>";
	    
	    $html .="<script>
				   var temoinDrepanocytoseAF = 	".$donneesAntecedentsFamiliaux['DrepanocytoseAF'].";
				   if(temoinDrepanocytoseAF == 1){ ";
	     
	               if($donneesAntecedentsFamiliaux['DrepanocytoseAF'] != 0){
	    	          $html .="
	    	          		    $('#DrepanocytoseAF').trigger('click'); 		
                   		        $('#NoteDrepanocytoseAF').val('".$donneesAntecedentsFamiliaux['NoteDrepanocytoseAF']."');
	                          ";
	               }
	    
	    $html .="  }  </script>";
	    
	    $html .="<script>
				   var temoinhtaAF = ".$donneesAntecedentsFamiliaux['htaAF'].";
				   if(temoinhtaAF == 1){ ";
	    
	               if($donneesAntecedentsFamiliaux['htaAF'] != 0){
	    	          $html .="
	    	          		    $('#htaAF').trigger('click'); 		
                   		        $('#NoteHtaAF').val('".$donneesAntecedentsFamiliaux['NoteHtaAF']."');
	                          ";
	               }
	     
	    $html .="  }  </script>";
	     
	    
		$html .="<script> setTimeout(function(){ scritpHabitudeDeVie();  scriptAntecedentMedicaux();  scriptGynecoObstetrique(); scriptAntecedentsFamiliaux(); }); </script>";
		
		//Recuperer les antecedents medicaux ajouter pour le patient
		//Recuperer les antecedents medicaux ajouter pour le patient
		$antMedPat = $this->getConsultationTable()->getAntecedentMedicauxPersonneParIdPatient($id_patient);
		
		//Recuperer les antecedents medicaux
		//Recuperer les antecedents medicaux
		$listeAntMed = $this->getConsultationTable()->getAntecedentsMedicaux();
		
		$html .="<script> var myArrayAntMedPat = ['']; var i = 1; </script>";
		
		foreach ($antMedPat as $antMed){
			$html .="<script> myArrayAntMedPat[i++] = '".$antMed['libelle']."'; </script> ";
		}
		$html .="<script>
				  $('#nbCheckboxAM').val('".$antMedPat->count()."'); 
				  setTimeout(function(){
   	                affichageAntecedentsMedicauxDuPatient(".$antMedPat->count()." , myArrayAntMedPat);
	              }, 500);
				</script> ";
		
		
		$html .="<script> var myArrayAntMed = ['']; var i = 1; </script>";
		
		foreach ($listeAntMed as $antMed){
			$html .="<script> myArrayAntMed[i++] = '".$antMed['libelle']."'; </script> ";
		}
		
		$html .="<script> autocompletionAntecedent(myArrayAntMed); </script>";
		
		if($infosPatient['SEXE'] == 'Masculin'){
			$html .="<script> $('.image4_AP').toggle(false); </script>";
		}
		
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ( $html ) );
		
	}
	
	public function historiquesDesConsultationsPatientAjaxAction() {
		$id_patient = $this->params ()->fromRoute ( 'val', 0 );
		$output = $this->getPatientTable ()->getHistoriqueDesConsultationsDuPatient($id_patient);
		return $this->getResponse ()->setContent ( Json::encode ( $output, array (
				'enableJsonExprFinder' => true
		) ) );
	}
	
	
	public function visualisationHistoriqueConsultationPatientAction() {
		$this->layout ()->setTemplate ( 'layout/consultation' );
	
		$id_admission = $this->params ()->fromRoute ( 'val', 0 ); 
		$InfoAdmission = $this->getAdmissionTable()->getAdmissionUrgence($id_admission);
		$id_patient = $InfoAdmission->id_patient;
		
		//INSTANCIATION DU FORMULAIRE D'ADMISSION
		$formAdmission = new AdmissionForm ();
		$listeSalles = $this->getPatientTable ()->listeSalles();
		$formAdmission->get ( 'salle' )->setValueOptions ($listeSalles);
	
		$listeServices = $this->getPatientTable ()->listeService();
	
		$listeServiceMutation = $this->getPatientTable ()->listeServicesMutation();
		$formAdmission->get ( 'mutation' )->setValueOptions ($listeServiceMutation);
		$formAdmission->get ( 'rpu_sortie_liste_mutation' )->setValueOptions ($listeServiceMutation);
	
		$listeCircontances = $this->getPatientTable ()->listeCirconstances();
		$formAdmission->get ( 'rpu_traumatisme_circonstances' )->setValueOptions ($listeCircontances);
	
		$listeCircontances = $this->getPatientTable ()->listeMecanismes();
		$formAdmission->get ( 'rpu_traumatisme_mecanismes' )->setValueOptions ($listeCircontances);
	
		$listeDiagnostics = $this->getPatientTable ()->listeDiagnostic();
		$formAdmission->get ( 'rpu_traumatisme_diagnostic' )->setValueOptions ($listeDiagnostics);
	
		$listeIndications = $this->getPatientTable ()->listeIndications();
		$formAdmission->get ( 'rpu_traumatisme_indication' )->setValueOptions ($listeIndications);
	
		$listeMotifSortieRpuTraumato = $this->getPatientTable ()->listeMotifsSortieRpuTraumato();
		$formAdmission->get ( 'rpu_traumatisme_motif_sortie' )->setValueOptions ($listeMotifSortieRpuTraumato);
	
		$listeMotifSortieRpuSortie = $this->getPatientTable ()->listeMotifsSortieRpuSortie();
		$formAdmission->get ( 'rpu_sortie_motif_sortie' )->setValueOptions ($listeMotifSortieRpuSortie);
	
		$listeModeTransport = $this->getPatientTable ()->listeModeTransport();
		$formAdmission->get ( 'mode_transport' )->setValueOptions ($listeModeTransport);
	
		$nbPatientAdmisInfimierService = $this->getPatientTable ()->nbPatientAdmisParInfirmierService();
	
		return array (
				'form' => $formAdmission,
				'temoin' => 0,
				'id_admission' => $id_admission,
				'id_patient' => $id_patient,
				'nbPatientAdmisInfimierService' => $nbPatientAdmisInfimierService,
		);
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	public function listePatientsAdmisInfirmierServiceHistoriqueAjaxAction() {
		$output = $this->getPatientTable ()->getListePatientAdmisInfirmierServiceVuParMedecinHistorique ();
		return $this->getResponse ()->setContent ( Json::encode ( $output, array (
				'enableJsonExprFinder' => true
		) ) );
	}
	
	public function listePatientsConsultesAction() {
		$this->layout ()->setTemplate ( 'layout/consultation' );
	
		//var_dump($this->getPatientTable()->getListeAdmissionPatient(399)['date']); exit();
		//INSTANCIATION DU FORMULAIRE D'ADMISSION
		$formAdmission = new AdmissionForm ();
		$listeSalles = $this->getPatientTable ()->listeSalles();
		$formAdmission->get ( 'salle' )->setValueOptions ($listeSalles);
	
		$listeServices = $this->getPatientTable ()->listeService();
		$formAdmission->get ( 'mutation' )->setValueOptions ($listeServices);
		
		return array (
				'form' => $formAdmission,
				'temoin' => 0,
		);
	}
	
	public function historiquesDesConsultationsPatientDansListeHistoriqueAjaxAction() {
		$id_patient = $this->params ()->fromRoute ( 'val', 0 );
		$output = $this->getPatientTable ()->getHistoriqueDesConsultationsDuPatientDansListeHistoriques($id_patient);
		return $this->getResponse ()->setContent ( Json::encode ( $output, array (
				'enableJsonExprFinder' => true
		) ) );
	}
	
	public function historiquePatientsConsultesAction() {
		$this->layout ()->setTemplate ( 'layout/consultation' );
		
		//var_dump($this->getPatientTable()->getListeAdmissionPatient(399)); exit();
		//INSTANCIATION DU FORMULAIRE D'ADMISSION
		$formAdmission = new AdmissionForm ();
		//$formAdmission = new AdmissionConsultationForm ();
		
		$listeSalles = $this->getPatientTable ()->listeSalles();
		$formAdmission->get ( 'salle' )->setValueOptions ($listeSalles);
	
		$listeServices = $this->getPatientTable ()->listeService();
	
		$listeServiceMutation = $this->getPatientTable ()->listeServicesMutation();
		$formAdmission->get ( 'mutation' )->setValueOptions ($listeServiceMutation);
		$formAdmission->get ( 'rpu_sortie_liste_mutation' )->setValueOptions ($listeServiceMutation);
	
		$listeCircontances = $this->getPatientTable ()->listeCirconstances();
		$formAdmission->get ( 'rpu_traumatisme_circonstances' )->setValueOptions ($listeCircontances);
	
		$listeCircontances = $this->getPatientTable ()->listeMecanismes();
		$formAdmission->get ( 'rpu_traumatisme_mecanismes' )->setValueOptions ($listeCircontances);
	
		$listeDiagnostics = $this->getPatientTable ()->listeDiagnostic();
		$formAdmission->get ( 'rpu_traumatisme_diagnostic' )->setValueOptions ($listeDiagnostics);
	
		$listeIndications = $this->getPatientTable ()->listeIndications();
		$formAdmission->get ( 'rpu_traumatisme_indication' )->setValueOptions ($listeIndications);
	
		$listeMotifSortieRpuTraumato = $this->getPatientTable ()->listeMotifsSortieRpuTraumato();
		$formAdmission->get ( 'rpu_traumatisme_motif_sortie' )->setValueOptions ($listeMotifSortieRpuTraumato);
	
		$listeMotifSortieRpuSortie = $this->getPatientTable ()->listeMotifsSortieRpuSortie();
		$formAdmission->get ( 'rpu_sortie_motif_sortie' )->setValueOptions ($listeMotifSortieRpuSortie);
	
		$listeModeTransport = $this->getPatientTable ()->listeModeTransport();
		$formAdmission->get ( 'mode_transport' )->setValueOptions ($listeModeTransport);
	
		$nbPatientAdmisInfimierService = $this->getPatientTable ()->nbPatientAdmisParInfirmierService();
	
		/*
		$listeMotifs = $this->getPatientTable ()->listeMotifsAdmission();
		$formAdmission->get ( 'motif_admission1' )->setValueOptions ($listeMotifs);
		$formAdmission->get ( 'motif_admission2' )->setValueOptions ($listeMotifs);
		$formAdmission->get ( 'motif_admission3' )->setValueOptions ($listeMotifs);
		$formAdmission->get ( 'motif_admission4' )->setValueOptions ($listeMotifs);
		$formAdmission->get ( 'motif_admission5' )->setValueOptions ($listeMotifs);
		*/
		
		return array (
				'form' => $formAdmission,
				'temoin' => 0,
				'nbPatientAdmisInfimierService' => $nbPatientAdmisInfimierService,
		);
	}
	
	
	
	public function impressionRpuHospitalisationAction(){
	
		$control = new DateHelper();
	
		$user = $this->layout()->user;
		$serviceMedecin = $user['NomService'];
		$id_medecin = $user['id_employe'];
	
		$nomMedecin = $user['Nom'];
		$prenomMedecin = $user['Prenom'];
		$donneesMedecin = array('nomMedecin' => $nomMedecin, 'prenomMedecin' => $prenomMedecin);
	
		$id_patient = $this->params ()->fromPost ( 'id_patient', 0 );
		$donneesPatient = $this->getConsultationTable()->getInfoPatient($id_patient);
	
		$id_admission = $this->params ()->fromPost ( 'id_admission', 0 );
		$InfoAdmission = $this->getAdmissionTable()->getAdmissionUrgence($id_admission);
		
		$donnees = array();
		//Récupération des données
		$donnees['motif_consultation'] = $this->params ()->fromPost (  'motif_consultation' );
		
		$donnees['salle'] = $this->params ()->fromPost (  'salle' );
		$donnees['lit'] = $this->params ()->fromPost (  'lit' );
		$donnees['couloir'] = $this->params ()->fromPost (  'couloir' );
		
		$donnees['resume_syndromique'] = str_replace("â€™", "'", $this->params ()->fromPost (  'resume_syndromique' ));
		$donnees['resume_syndromique'] = str_replace("œ", "oe" ,$donnees['resume_syndromique']); 
		
		$donnees['hypotheses_diagnostiques'] = str_replace("â€™", "'", $this->params()->fromPost('hypotheses_diagnostiques'));
		$donnees['hypotheses_diagnostiques'] = str_replace("œ", "oe" ,$donnees['hypotheses_diagnostiques']); 
		
		$donnees['examens_complementaires'] = str_replace("â€™", "'", $this->params()->fromPost('examens_complementaires'));
		$donnees['examens_complementaires'] = str_replace("œ", "oe" ,$donnees['examens_complementaires']); 
		
		$donnees['traitement'] = str_replace("â€™", "'", $this->params()->fromPost('traitement'));
		$donnees['traitement'] = str_replace("œ", "oe" ,$donnees['traitement']);
		
		$donnees['resultats_examens_complementaires'] = str_replace("â€™", "'", $this->params()->fromPost('resultats_examens_complementaires'));
		$donnees['resultats_examens_complementaires'] = str_replace("œ", "oe" ,$donnees['resultats_examens_complementaires']);
		
		$donnees['mutation'] = str_replace("â€™", "'", $this->params()->fromPost('mutation'));
		$donnees['mutation'] = str_replace("œ", "oe" ,$donnees['mutation']);
		
		$donnees['mise_a_jour_1'] = str_replace("â€™", "'", $this->params()->fromPost('mise_a_jour_1'));
		$donnees['mise_a_jour_1'] = str_replace("œ", "oe" ,$donnees['mise_a_jour_1']);
		
		$donnees['mise_a_jour_2'] = str_replace("â€™", "'", $this->params()->fromPost('mise_a_jour_2'));
		$donnees['mise_a_jour_2'] = str_replace("œ", "oe" ,$donnees['mise_a_jour_2']);
		
		$donnees['mise_a_jour_3'] = str_replace("â€™", "'", $this->params()->fromPost('mise_a_jour_3'));
		$donnees['mise_a_jour_3'] = str_replace("œ", "oe" ,$donnees['mise_a_jour_3']);
		
		$donnees['avis_specialiste'] = str_replace("â€™", "'", $this->params()->fromPost('avis_specialiste'));
		$donnees['avis_specialiste'] = str_replace("œ", "oe" ,$donnees['avis_specialiste']);

		//Recuperer les informations sur les infirmiers
		//Recuperer les informations sur les infirmiers
		$infosInfirmiers = array();
		if($InfoAdmission){
			$id_infirmier_tri = $InfoAdmission->id_infirmier_tri;
			$id_infirmier_service = $InfoAdmission->id_infirmier_service;
			if($id_infirmier_tri){
				$infos =  $this->getConsultationTable()->getInfosInfirmier($id_infirmier_tri);
				$infosInfirmiers[$id_infirmier_tri] = $infos['PRENOM'].' '.$infos['NOM'];
			}
			if($id_infirmier_service){
				if($id_medecin != $id_infirmier_service){
					$infos =  $this->getConsultationTable()->getInfosInfirmier($id_infirmier_service);
					$infosInfirmiers[$id_infirmier_service] = $infos['PRENOM'].' '.$infos['NOM'];
				}else{
					$infosInfirmiers[$id_infirmier_service] = "";
				}
			}
		}
		
		//Recuperation des informations sur la consultation
		//Recuperation des informations sur la consultation
		$constantes = $this->getConsultationTable()->getConsultationParIdAdmission($id_admission);
		if($constantes){
			$donnees['dateConsultation'] = $control->convertDate($constantes['DATEONLY']);
		}else{
			$today = new \DateTime ();
			$date = $today->format( 'd/m/Y' );
			$donnees['dateConsultation'] = $date;
		}
		
		$listeSalles = $this->getPatientTable()->listeSalles();
		$listeLits = $this->getPatientTable()->listeLits();
		$listeServiceMutation = $this->getPatientTable ()->listeServicesMutation();
		
		//CREATION DU DOCUMENT PDF
		//Créer le document
		$DocPdf = new DocumentPdf();
	
		//Créer la page 1
		$page1 = new RpuHospitalisationPdf();
	
		$page1->setService($serviceMedecin);
		$page1->setInfoAdmission($InfoAdmission);
		
		//Envoi des données du patient
		$page1->setDonneesPatientTC($donneesPatient);
		//Envoi des données du medecin
		$page1->setDonneesMedecinTC($donneesMedecin);
		//Envoi des données des infirmiers
		$page1->setDonneesInfosInfirmiers($infosInfirmiers);
		//Envoi de la liste des salles et des lits et service de mutation
		$page1->setListeSalles($listeSalles);
		$page1->setListeLits($listeLits);
		$page1->setListeServiceMutation($listeServiceMutation);
		
		//Envoi les données de la demande
		$page1->setDonneesDemandeTC($donnees);
	
		//Ajouter les donnees a la page
		$page1->addNoteTC();
		//Ajouter la page au document
		$DocPdf->addPage($page1->getPage());
	
 		$entrerRS    = $page1->getEntrerResumeSyndromique(); 
 		$entrerHD    = $page1->getEntrerHypothesesDiagnostics();
 		$entrerEC    = $page1->getEntrerExamenComplementaire();
 		$entrerT     = $page1->getEntrerTraitement();
 		$entrerREC   = $page1->getEntrerResultatExamenComplementaire();
 		$entrerAS    = $page1->getEntrerAvisSpecialiste();
 		
 		
 		$textRS   = $page1->getTextResumeSyndromique();
 		$textHD   = $page1->getTextHypothesesDiagnostics();
 		$textEC   = $page1->getTextExamenComplementaire();
 		$textT    = $page1->getTextTraitement();
 		$textREC  = $page1->getTextResultatExamenComplementaire();
 		$textM    = $page1->getTextMutation();
 		$textM1   = $page1->getTextMiseAJour1();
 		$textM2   = $page1->getTextMiseAJour2();
 		$textM3   = $page1->getTextMiseAJour3();
 		$textAS   = $page1->getTextAvisSpecialiste();
 		
 		
 		$nbLigne   = $page1->getNbLigne();
 		$nbTotalLigne = $page1->getNbTotalLigne();
 		$tableauTemoinLimite = $page1->getTableauTemoinLimite();
 		$tableauTemoinInfos  = $page1->getTableauTemoinInfos();
 		
		if($nbTotalLigne > 18 && $nbLigne < $nbTotalLigne){
			
			//Créer la page 2
			$page2 = new RpuHospitalisationPdf2();
			
 			$page2->setEntrerResumeSyndromique($entrerRS);
 			$page2->setEntrerHypothesesDiagnostics($entrerHD);
 			$page2->setEntrerExamenComplementaire($entrerEC);
 			$page2->setEntrerTraitement($entrerT);
 			$page2->setEntrerResultatExamenComplementaire($entrerREC);
 			$page2->setEntrerAvisSpecialiste($entrerAS);
 			
				
 			$page2->setTextResumeSyndromique($textRS);
 			$page2->setTextHypothesesDiagnostics($textHD);
 			$page2->setTextExamenComplementaire($textEC);
 			$page2->setTextTraitement($textT);
 			$page2->setTextResultatExamenComplementaire($textREC);
 			$page2->setTextMutation($textM);
 			$page2->setTextMiseAJour1($textM1);
 			$page2->setTextMiseAJour2($textM2);
 			$page2->setTextMiseAJour3($textM3);
 			$page2->setTextAvisSpecialiste($textAS);
 			
 			$page2->setTableauTemoinInfos($tableauTemoinInfos);
 			$page2->setTableauTemoinLimite($tableauTemoinLimite);
 			
 			$page2->addNoteTC();
 			$DocPdf->addPage($page2->getPage());
 			
 			
 			$suitePageRpu = $page2->getSuitePageRpu();
 			
 			
 			if($suitePageRpu == 1){
 				//Créer la page 3
 				$page3 = new RpuHospitalisationPdf3();
 				
 				$page3->setEntrerResumeSyndromique($entrerRS);
 				$page3->setEntrerHypothesesDiagnostics($entrerHD);
 				$page3->setEntrerExamenComplementaire($entrerEC);
 				$page3->setEntrerTraitement($entrerT);
 				$page3->setEntrerResultatExamenComplementaire($entrerREC);
 				$page3->setEntrerAvisSpecialiste($entrerAS);
 				
 				
 				$page3->setTextResumeSyndromique($textRS);
 				$page3->setTextHypothesesDiagnostics($textHD);
 				$page3->setTextExamenComplementaire($textEC);
 				$page3->setTextTraitement($textT);
 				$page3->setTextResultatExamenComplementaire($textREC);
 				$page3->setTextMutation($textM);
 				$page3->setTextMiseAJour1($textM1);
 				$page3->setTextMiseAJour2($textM2);
 				$page3->setTextMiseAJour3($textM3);
 				$page3->setTextAvisSpecialiste($textAS);
 				
 				$page3->setTableauTemoinLimite($page2->getTableauTemoinLimitePage2());
 				
 				$page3->addNoteTC();
 				$DocPdf->addPage($page3->getPage());
 			}
 			
		}
		
		
		//Afficher le document contenant la page
		$DocPdf->getDocument();
	}
	
	
	public function impressionRpuTraumatologieAction(){
	
		$control = new DateHelper();
	
		$user = $this->layout()->user;
		$serviceMedecin = $user['NomService'];
		$id_medecin = $user['id_employe'];
	
		$nomMedecin = $user['Nom'];
		$prenomMedecin = $user['Prenom'];
		$donneesMedecin = array('nomMedecin' => $nomMedecin, 'prenomMedecin' => $prenomMedecin);
	
		$id_patient = $this->params ()->fromPost ( 'id_patient', 0 );
		$donneesPatient = $this->getConsultationTable()->getInfoPatient($id_patient);
	
		$id_admission = $this->params ()->fromPost ( 'id_admission', 0 );
		$InfoAdmission = $this->getAdmissionTable()->getAdmissionUrgence($id_admission);
	
		$donnees = array();
		//Récupération des données
		$donnees['motif_consultation'] = $this->params ()->fromPost (  'motif_consultation' );
	
		$donnees['salle'] = $this->params ()->fromPost (  'salle' );
		$donnees['lit'] = $this->params ()->fromPost (  'lit' );
		$donnees['couloir'] = $this->params ()->fromPost (  'couloir' );
	
		$donnees['cote_dominant'] = str_replace("â€™", "'", $this->params ()->fromPost (  'cote_dominant' ));
		$donnees['cote_dominant'] = str_replace("œ", "oe" ,$donnees['cote_dominant']);
		
		$donnees['date_heure'] = str_replace("â€™", "'", $this->params ()->fromPost (  'date_heure' ));
		$donnees['date_heure'] = str_replace("œ", "oe" ,$donnees['date_heure']);
		
		$donnees['circonstances'] = str_replace("â€™", "'", $this->params ()->fromPost (  'circonstances' ));
		$donnees['circonstances'] = str_replace("œ", "oe" ,$donnees['circonstances']);
		
		$donnees['antecedent'] = str_replace("â€™", "'", $this->params ()->fromPost (  'antecedent' ));
		$donnees['antecedent'] = str_replace("œ", "oe" ,$donnees['antecedent']);
		
		$donnees['examen_physique'] = str_replace("â€™", "'", $this->params ()->fromPost (  'examen_physique' ));
		$donnees['examen_physique'] = str_replace("œ", "oe" ,$donnees['examen_physique']);
		
		$donnees['examen_paraclinique'] = str_replace("â€™", "'", $this->params ()->fromPost (  'examen_paraclinique' ));
		$donnees['examen_paraclinique'] = str_replace("œ", "oe" ,$donnees['examen_paraclinique']);
		
		$donnees['resultat_examen_complementaire'] = str_replace("â€™", "'", $this->params ()->fromPost (  'resultat_examen_complementaire' ));
		$donnees['resultat_examen_complementaire'] = str_replace("œ", "oe" ,$donnees['resultat_examen_complementaire']);
		
		$donnees['mecanismes'] = str_replace("â€™", "'", $this->params ()->fromPost (  'mecanismes' ));
		$donnees['mecanismes'] = str_replace("œ", "oe" ,$donnees['mecanismes']);
		
		$donnees['mecanismes_precision'] = str_replace("â€™", "'", $this->params ()->fromPost (  'mecanismes_precision' ));
		$donnees['mecanismes_precision'] = str_replace("œ", "oe" ,$donnees['mecanismes_precision']);
		
		$donnees['indication'] = str_replace("â€™", "'", $this->params ()->fromPost (  'indication' ));
		$donnees['indication'] = str_replace("œ", "oe" ,$donnees['indication']);
		
		$donnees['indication_precision'] = str_replace("â€™", "'", $this->params ()->fromPost (  'indication_precision' ));
		$donnees['indication_precision'] = str_replace("œ", "oe" ,$donnees['indication_precision']);
		
		$donnees['diagnostic'] = str_replace("â€™", "'", $this->params ()->fromPost (  'diagnostic' ));
		$donnees['diagnostic'] = str_replace("œ", "oe" ,$donnees['diagnostic']);
		
		$donnees['diagnostic_precision'] = str_replace("â€™", "'", $this->params ()->fromPost (  'diagnostic_precision' ));
		$donnees['diagnostic_precision'] = str_replace("œ", "oe" ,$donnees['diagnostic_precision']);
		
		$donnees['conduite'] = str_replace("â€™", "'", $this->params ()->fromPost (  'conduite' ));
		$donnees['conduite'] = str_replace("œ", "oe" ,$donnees['conduite']);
		
		$donnees['motif_sortie'] = str_replace("â€™", "'", $this->params ()->fromPost (  'motif_sortie' ));
		$donnees['motif_sortie'] = str_replace("œ", "oe" ,$donnees['motif_sortie']);
		
		$donnees['rendez_vous'] = str_replace("â€™", "'", $this->params ()->fromPost (  'rendez_vous' ));
		$donnees['rendez_vous'] = str_replace("œ", "oe" ,$donnees['rendez_vous']);
		
		$donnees['specialiste_trauma'] = str_replace("â€™", "'", $this->params ()->fromPost (  'specialiste_trauma' ));
		$donnees['specialiste_trauma'] = str_replace("œ", "oe" ,$donnees['specialiste_trauma']);
		
		$donnees['conduite_specialiste'] = str_replace("â€™", "'", $this->params ()->fromPost (  'conduite_specialiste' ));
		$donnees['conduite_specialiste'] = str_replace("œ", "oe" ,$donnees['conduite_specialiste']);
		
		/*=====================================================================*/
		/*=====================================================================*/
		$donnees['resume_syndromique'] = str_replace("â€™", "'", $this->params ()->fromPost (  'resume_syndromique' ));
		$donnees['resume_syndromique'] = str_replace("œ", "oe" ,$donnees['resume_syndromique']);
	
		$donnees['hypotheses_diagnostiques'] = str_replace("â€™", "'", $this->params()->fromPost('hypotheses_diagnostiques'));
		$donnees['hypotheses_diagnostiques'] = str_replace("œ", "oe" ,$donnees['hypotheses_diagnostiques']);
	
		$donnees['examens_complementaires'] = str_replace("â€™", "'", $this->params()->fromPost('examens_complementaires'));
		$donnees['examens_complementaires'] = str_replace("œ", "oe" ,$donnees['examens_complementaires']);
	
		$donnees['traitement'] = str_replace("â€™", "'", $this->params()->fromPost('traitement'));
		$donnees['traitement'] = str_replace("œ", "oe" ,$donnees['traitement']);
	
		$donnees['resultats_examens_complementaires'] = str_replace("â€™", "'", $this->params()->fromPost('resultats_examens_complementaires'));
		$donnees['resultats_examens_complementaires'] = str_replace("œ", "oe" ,$donnees['resultats_examens_complementaires']);
	
		$donnees['mutation'] = str_replace("â€™", "'", $this->params()->fromPost('mutation'));
		$donnees['mutation'] = str_replace("œ", "oe" ,$donnees['mutation']);
	
		$donnees['mise_a_jour_1'] = str_replace("â€™", "'", $this->params()->fromPost('mise_a_jour_1'));
		$donnees['mise_a_jour_1'] = str_replace("œ", "oe" ,$donnees['mise_a_jour_1']);
	
		$donnees['mise_a_jour_2'] = str_replace("â€™", "'", $this->params()->fromPost('mise_a_jour_2'));
		$donnees['mise_a_jour_2'] = str_replace("œ", "oe" ,$donnees['mise_a_jour_2']);
	
		$donnees['mise_a_jour_3'] = str_replace("â€™", "'", $this->params()->fromPost('mise_a_jour_3'));
		$donnees['mise_a_jour_3'] = str_replace("œ", "oe" ,$donnees['mise_a_jour_3']);
	
		$donnees['avis_specialiste'] = str_replace("â€™", "'", $this->params()->fromPost('avis_specialiste'));
		$donnees['avis_specialiste'] = str_replace("œ", "oe" ,$donnees['avis_specialiste']);
	
		
		
		
		
		//Recuperer les informations sur les infirmiers
		//Recuperer les informations sur les infirmiers
		$infosInfirmiers = array();
		if($InfoAdmission){
			$id_infirmier_tri = $InfoAdmission->id_infirmier_tri;
			$id_infirmier_service = $InfoAdmission->id_infirmier_service;
			if($id_infirmier_tri){
				$infos =  $this->getConsultationTable()->getInfosInfirmier($id_infirmier_tri);
				$infosInfirmiers[$id_infirmier_tri] = $infos['PRENOM'].' '.$infos['NOM'];
			}
			if($id_infirmier_service){
				if($id_medecin != $id_infirmier_service){
					$infos =  $this->getConsultationTable()->getInfosInfirmier($id_infirmier_service);
					$infosInfirmiers[$id_infirmier_service] = $infos['PRENOM'].' '.$infos['NOM'];
				}else{
					$infosInfirmiers[$id_infirmier_service] = "";
				}
			}
		}
	
		//Recuperation des informations sur la consultation
		//Recuperation des informations sur la consultation
		$constantes = $this->getConsultationTable()->getConsultationParIdAdmission($id_admission);
		if($constantes){
			$donnees['dateConsultation'] = $control->convertDate($constantes['DATEONLY']);
		}else{
			$today = new \DateTime ();
			$date = $today->format( 'd/m/Y' );
			$donnees['dateConsultation'] = $date;
		}
	
		$listeSalles = $this->getPatientTable()->listeSalles();
		$listeLits = $this->getPatientTable()->listeLits();
		$listeServiceMutation = $this->getPatientTable ()->listeServicesMutation();
		$listeCirconstances = $this->getPatientTable ()->listeCirconstances();
		$listeMecanismes = $this->getPatientTable ()->listeMecanismes();
		$listeIndications = $this->getPatientTable ()->listeIndications();
		$listeDiagnostics = $this->getPatientTable ()->listeDiagnostic();
		$listeModeSortie = $this->getPatientTable ()->listeMotifsSortieRpuTraumato();
		
		//CREATION DU DOCUMENT PDF
		//Créer le document
		$DocPdf = new DocumentPdf();
	
		//Créer la page 1
		$page1 = new RpuTraumatologiePdf();
		
		$page1->setService($serviceMedecin);
		$page1->setInfoAdmission($InfoAdmission);
	
		//Envoi des données du patient
		$page1->setDonneesPatientTC($donneesPatient);
		//Envoi des données du medecin
		$page1->setDonneesMedecinTC($donneesMedecin);
		//Envoi des données des infirmiers
		$page1->setDonneesInfosInfirmiers($infosInfirmiers);
		//Envoi de la liste des salles et des lits et service de mutation
		$page1->setListeSalles($listeSalles);
		$page1->setListeLits($listeLits);
		$page1->setListeServiceMutation($listeServiceMutation);
		$page1->setListeCirconstances($listeCirconstances);
		$page1->setListeMecanismes($listeMecanismes);
		$page1->setListeIndications($listeIndications);
		$page1->setListeDiagnostics($listeDiagnostics);
		$page1->setListeModeSortie($listeModeSortie);

		
		//Envoi les données de la demande
		$page1->setDonneesDemandeTC($donnees);
	
		//Ajouter les donnees a la page
		$page1->addNoteTC();
		//Ajouter la page au document
		$DocPdf->addPage($page1->getPage());
	
		$entrerCD    = $page1->getEntrerCoteDominant();
		$entrerDH    = $page1->getEntrerDateHeure();
		$entrerC     = $page1->getEntrerCirconstances();
		$entrerAT    = $page1->getEntrerAntecedentTrauma();
		$entrerEP    = $page1->getEntrerExamenPhysique();
		$entrerEPa   = $page1->getEntrerExamenParaclinique();
		$entrerREC   = $page1->getEntrerResultatExamenComplementaire();
		$entrerM     = $page1->getEntrerMecanismes();
		$entrerI     = $page1->getEntrerIndication();
		$entrerD     = $page1->getEntrerDiagnostic();
		$entrerCT    = $page1->getEntrerConduiteATenir();
		$entrerMS    = $page1->getEntrerModeSortie();
		$entrerRV    = $page1->getEntrerRendezVous();
		$entrerAS    = $page1->getEntrerAvisSpecialiste();
		$entrerCTS   = $page1->getEntrerConduiteATenirSpecialiste();
		
		$textCD   = $page1->getTextCoteDominant();
		$textDH   = $page1->getTextDateHeure();
		$textC    = $page1->getTextCirconstances();
		$textAT   = $page1->getTextAntecedentTrauma();
		$textEP   = $page1->getTextExamenPhysique();
		$textEPa  = $page1->getTextExamenParaclinique();
		$textREC  = $page1->getTextResultatExamenComplementaire();
		$textM    = $page1->getTextMecanismes();
		$textI    = $page1->getTextIndication();
		$textD    = $page1->getTextDiagnostic();
		$textCT   = $page1->getTextConduiteATenir();
		$textMS   = $page1->getTextModeSortie();
		$textRV   = $page1->getTextRendezVous();
		$textAS   = $page1->getTextAvisSpecialiste();
		$textCTS  = $page1->getTextConduiteATenirSpecialiste();
		
			
		$nbLigne   = $page1->getNbLigne();
		$nbTotalLigne = $page1->getNbTotalLigne();
		$tableauTemoinLimite = $page1->getTableauTemoinLimite();
		$tableauTemoinInfos  = $page1->getTableauTemoinInfos();

		
		if($nbTotalLigne > 18 && $nbLigne < $nbTotalLigne){
				
			//Créer la page 2
			$page2 = new RpuTraumatologiePdf2();
			$page2->setDonneesDemandeTC($donnees);
			
			$page2->setListeServiceMutation($listeServiceMutation);
			$page2->setListeCirconstances($listeCirconstances);
			$page2->setListeMecanismes($listeMecanismes);
			$page2->setListeIndications($listeIndications);
			$page2->setListeDiagnostics($listeDiagnostics);
			$page2->setListeModeSortie($listeModeSortie);
			//========================================================
			//========================================================
			$page2->setEntrerCoteDominant($entrerCD);
			$page2->setEntrerDateHeure($entrerDH);
			$page2->setEntrerCirconstances($entrerC);
			$page2->setEntrerAntecedentTrauma($entrerAT);
			$page2->setEntrerExamenPhysique($entrerEP);
			$page2->setEntrerExamenParaclinique($entrerEPa);
			$page2->setEntrerResultatExamenComplementaire($entrerREC);
			$page2->setEntrerMecanismes($entrerM);
			$page2->setEntrerIndication($entrerI);
			$page2->setEntrerDiagnostic($entrerD);
			$page2->setEntrerConduiteATenir($entrerCT);
			$page2->setEntrerModeSortie($entrerMS);
			$page2->setEntrerRendezVous($entrerRV);
			$page2->setEntrerAvisSpecialiste($entrerAS);
			$page2->setEntrerConduiteATenirSpecialiste($entrerCTS);
			//========================================================
			//========================================================
			$page2->setTextCoteDominant($textCD);
			$page2->setTextDateHeure($textDH);
			$page2->setTextCirconstances($textC);
			$page2->setTextAntecedentTrauma($textAT);
			$page2->setTextExamenPhysique($textEP);
			$page2->setTextExamenParaclinique($textEPa);
			$page2->setTextResultatExamenComplementaire($textREC);
			$page2->setTextMecanismes($textM);
			$page2->setTextIndication($textI);
			$page2->setTextDiagnostic($textD);
			$page2->setTextConduiteATenir($textCT);
			$page2->setTextModeSortie($textMS);
			$page2->setTextRendezVous($textRV);
			$page2->setTextAvisSpecialiste($textAS);
			$page2->setTextConduiteATenirSpecialiste($textCTS);
			//========================================================
			//========================================================
			$page2->setTableauTemoinInfos($tableauTemoinInfos);
			$page2->setTableauTemoinLimite($tableauTemoinLimite);
	
			$page2->addNoteTC();
			$DocPdf->addPage($page2->getPage());
	
	
			$suitePageRpu = $page2->getSuitePageRpu();
	    
	        /*
			if($suitePageRpu == 1){
				//Créer la page 3
				$page3 = new RpuHospitalisationPdf3();
					
				//$page3->setEntrerResumeSyndromique($entrerRS);
				$page3->setEntrerHypothesesDiagnostics($entrerHD);
				$page3->setEntrerExamenComplementaire($entrerEC);
				$page3->setEntrerTraitement($entrerT);
				$page3->setEntrerResultatExamenComplementaire($entrerREC);
				$page3->setEntrerAvisSpecialiste($entrerAS);
					
					
				//$page3->setTextResumeSyndromique($textRS);
				$page3->setTextHypothesesDiagnostics($textHD);
				$page3->setTextExamenComplementaire($textEC);
				$page3->setTextTraitement($textT);
				$page3->setTextResultatExamenComplementaire($textREC);
				$page3->setTextMutation($textM);
				$page3->setTextMiseAJour1($textM1);
				$page3->setTextMiseAJour2($textM2);
				$page3->setTextMiseAJour3($textM3);
				$page3->setTextAvisSpecialiste($textAS);
					
				$page3->setTableauTemoinLimite($page2->getTableauTemoinLimitePage2());
					
				$page3->addNoteTC();
				$DocPdf->addPage($page3->getPage());
			}
			*/
	
		}
	
	
		//Afficher le document contenant les pages
		$DocPdf->getDocument();
	}
	
	
	public function impressionRpuSortieAction(){
	
		$control = new DateHelper();
	
		$user = $this->layout()->user;
		$serviceMedecin = $user['NomService'];
		$id_medecin = $user['id_employe'];
	
		$nomMedecin = $user['Nom'];
		$prenomMedecin = $user['Prenom'];
		$donneesMedecin = array('nomMedecin' => $nomMedecin, 'prenomMedecin' => $prenomMedecin);
	
		$id_patient = $this->params ()->fromPost ( 'id_patient', 0 );
		$donneesPatient = $this->getConsultationTable()->getInfoPatient($id_patient);
	
		$id_admission = $this->params ()->fromPost ( 'id_admission', 0 );
		$InfoAdmission = $this->getAdmissionTable()->getAdmissionUrgence($id_admission);
	
		$donnees = array();
		//Récupération des données
		$donnees['motif_consultation'] = $this->params ()->fromPost (  'motif_consultation' );
	
		$donnees['salle'] = $this->params ()->fromPost (  'salle' );
		$donnees['lit'] = $this->params ()->fromPost (  'lit' );
		$donnees['couloir'] = $this->params ()->fromPost (  'couloir' );
	
		/*=====================================================================*/
		/*=====================================================================*/
		
		$donnees['diganostic'] = str_replace("â€™", "'", $this->params ()->fromPost (  'diganostic' ));
		$donnees['diganostic'] = str_replace("œ", "oe" ,$donnees['diganostic']);
		
		$donnees['diganostic_associe'] = str_replace("â€™", "'", $this->params ()->fromPost (  'diganostic_associe' ));
		$donnees['diganostic_associe'] = str_replace("œ", "oe" ,$donnees['diganostic_associe']);
		
		$donnees['traitement'] = str_replace("â€™", "'", $this->params ()->fromPost (  'traitement' ));
		$donnees['traitement'] = str_replace("œ", "oe" ,$donnees['traitement']);
		
		$donnees['examens_complementaires_demandes'] = str_replace("â€™", "'", $this->params ()->fromPost (  'examens_complementaires_demandes' ));
		$donnees['examens_complementaires_demandes'] = str_replace("œ", "oe" ,$donnees['examens_complementaires_demandes']);
		
		$donnees['mode_sortie'] = str_replace("â€™", "'", $this->params ()->fromPost (  'mode_sortie' ));
		$donnees['mode_sortie'] = str_replace("œ", "oe" ,$donnees['mode_sortie']);
		
		$donnees['liste_mutation'] = str_replace("â€™", "'", $this->params ()->fromPost (  'liste_mutation' ));
		$donnees['liste_mutation'] = str_replace("œ", "oe" ,$donnees['liste_mutation']);
		
		$donnees['transfert'] = str_replace("â€™", "'", $this->params ()->fromPost (  'transfert' ));
		$donnees['transfert'] = str_replace("œ", "oe" ,$donnees['transfert']);
		
		$donnees['evacuation'] = str_replace("â€™", "'", $this->params ()->fromPost (  'evacuation' ));
		$donnees['evacuation'] = str_replace("œ", "oe" ,$donnees['evacuation']);
		

		//Recuperer les informations sur les infirmiers
		//Recuperer les informations sur les infirmiers
		$infosInfirmiers = array();
		if($InfoAdmission){
			$id_infirmier_tri = $InfoAdmission->id_infirmier_tri;
			$id_infirmier_service = $InfoAdmission->id_infirmier_service;
			if($id_infirmier_tri){
				$infos =  $this->getConsultationTable()->getInfosInfirmier($id_infirmier_tri);
				$infosInfirmiers[$id_infirmier_tri] = $infos['PRENOM'].' '.$infos['NOM'];
			}
			if($id_infirmier_service){
				if($id_medecin != $id_infirmier_service){
					$infos =  $this->getConsultationTable()->getInfosInfirmier($id_infirmier_service);
					$infosInfirmiers[$id_infirmier_service] = $infos['PRENOM'].' '.$infos['NOM'];
				}else{
					$infosInfirmiers[$id_infirmier_service] = "";
				}
			}
		}
	
		//Recuperation des informations sur la consultation
		//Recuperation des informations sur la consultation
		$constantes = $this->getConsultationTable()->getConsultationParIdAdmission($id_admission);
		if($constantes){
			$donnees['dateConsultation'] = $control->convertDate($constantes['DATEONLY']);
		}else{
			$today = new \DateTime ();
			$date = $today->format( 'd/m/Y' );
			$donnees['dateConsultation'] = $date;
		}
	
		$listeSalles = $this->getPatientTable()->listeSalles();
		$listeLits = $this->getPatientTable()->listeLits();
		$listeServiceMutation = $this->getPatientTable ()->listeServicesMutation();
		$listeCirconstances = $this->getPatientTable ()->listeCirconstances();
		$listeMecanismes = $this->getPatientTable ()->listeMecanismes();
		$listeIndications = $this->getPatientTable ()->listeIndications();
		$listeDiagnostics = $this->getPatientTable ()->listeDiagnostic();
		$listeModeSortie = $this->getPatientTable ()->listeMotifsSortieRpuSortie();
	
		//CREATION DU DOCUMENT PDF
		//Créer le document
		$DocPdf = new DocumentPdf();
	
		//Créer la page 1
		$page1 = new RpuSortiePdf();
	
		$page1->setService($serviceMedecin);
		$page1->setInfoAdmission($InfoAdmission);
	
		//Envoi des données du patient
		$page1->setDonneesPatientTC($donneesPatient);
		//Envoi des données du medecin
		$page1->setDonneesMedecinTC($donneesMedecin);
		//Envoi des données des infirmiers
		$page1->setDonneesInfosInfirmiers($infosInfirmiers);
		//Envoi de la liste des salles et des lits et service de mutation
		$page1->setListeSalles($listeSalles);
		$page1->setListeLits($listeLits);
		$page1->setListeServiceMutation($listeServiceMutation);
		$page1->setListeCirconstances($listeCirconstances);
		$page1->setListeMecanismes($listeMecanismes);
		$page1->setListeIndications($listeIndications);
		$page1->setListeDiagnostics($listeDiagnostics);
		$page1->setListeModeSortie($listeModeSortie);
	
	
		//Envoi les données de la demande
		$page1->setDonneesDemandeTC($donnees);
	
		//Ajouter les donnees a la page
		$page1->addNoteTC();
		//Ajouter la page au document
		$DocPdf->addPage($page1->getPage());
	
 		$textInfos1 = $page1->getTextInfos1();
 		$textInfos2 = $page1->getTextInfos2();
 		$textInfos3 = $page1->getTextInfos3();
 		$textInfos4 = $page1->getTextInfos4();
 		$textInfos5 = $page1->getTextInfos5();
 		$textInfos6 = $page1->getTextInfos6();
 		$textInfos7 = $page1->getTextInfos7();
 		$textInfos8 = $page1->getTextInfos8();
			
		$nbLigne   = $page1->getNbLigne();
		$nbTotalLigne = $page1->getNbTotalLigne();
		$tableauTemoinLimite = $page1->getTableauTemoinLimite();
		$tableauTemoinInfos  = $page1->getTableauTemoinInfos();
	
	
		if($nbTotalLigne > 18 && $nbLigne < $nbTotalLigne){
	
			//Créer la page 2
			$page2 = new RpuSortiePdf2();
			$page2->setDonneesDemandeTC($donnees);
				
			$page2->setListeServiceMutation($listeServiceMutation);
			$page2->setListeCirconstances($listeCirconstances);
			$page2->setListeMecanismes($listeMecanismes);
			$page2->setListeIndications($listeIndications);
			$page2->setListeDiagnostics($listeDiagnostics);
			$page2->setListeModeSortie($listeModeSortie);
			//========================================================
			//========================================================
 			$page2->setTextInfos1($textInfos1);
 			$page2->setTextInfos2($textInfos2);
 			$page2->setTextInfos3($textInfos3);
 			$page2->setTextInfos4($textInfos4);
 			$page2->setTextInfos5($textInfos5);
 			$page2->setTextInfos6($textInfos6);
 			$page2->setTextInfos7($textInfos7);
 			$page2->setTextInfos8($textInfos8);

			
			//========================================================
			//========================================================
			$page2->setTableauTemoinInfos($tableauTemoinInfos);
			$page2->setTableauTemoinLimite($tableauTemoinLimite);
	
			$page2->addNoteTC();
			$DocPdf->addPage($page2->getPage());
	
			//var_dump($tableauTemoinLimite); exit();
	
			//$suitePageRpu = $page2->getSuitePageRpu();
		  
			/*
				if($suitePageRpu == 1){
			//Créer la page 3
			$page3 = new RpuHospitalisationPdf3();
				
			//$page3->setEntrerResumeSyndromique($entrerRS);
			$page3->setEntrerHypothesesDiagnostics($entrerHD);
			$page3->setEntrerExamenComplementaire($entrerEC);
			$page3->setEntrerTraitement($entrerT);
			$page3->setEntrerResultatExamenComplementaire($entrerREC);
			$page3->setEntrerAvisSpecialiste($entrerAS);
				
				
			//$page3->setTextResumeSyndromique($textRS);
			$page3->setTextHypothesesDiagnostics($textHD);
			$page3->setTextExamenComplementaire($textEC);
			$page3->setTextTraitement($textT);
			$page3->setTextResultatExamenComplementaire($textREC);
			$page3->setTextMutation($textM);
			$page3->setTextMiseAJour1($textM1);
			$page3->setTextMiseAJour2($textM2);
			$page3->setTextMiseAJour3($textM3);
			$page3->setTextAvisSpecialiste($textAS);
				
			$page3->setTableauTemoinLimite($page2->getTableauTemoinLimitePage2());
				
			$page3->addNoteTC();
			$DocPdf->addPage($page3->getPage());
			}
			*/
	
		}
	
	
		//Afficher le document contenant les pages
		$DocPdf->getDocument();
	}
	
	
	/**
	 * Autres fonctions d'impression
	 * Autres fonctions d'impression
	 */
	//Impression des rpu-hospitalisations  
	//Impression des rpu-hospitalisations
	public function imprimerRpuHospitalisationAction()
	{
		$control = new DateHelper();
		
		$user = $this->layout()->user;
		$nomService = $user['NomService'];
		$id_medecin = $user['id_employe'];
		
		$nomMedecin = $user['Nom'];
		$prenomMedecin = $user['Prenom'];
		$infosMedecin = array('nomMedecin' => $nomMedecin, 'prenomMedecin' => $prenomMedecin);
		
		$id_patient = $this->params ()->fromPost ( 'id_patient', 0 );
		$infosPatients = $this->getConsultationTable()->getInfoPatient($id_patient);

		$id_admission = $this->params ()->fromPost ( 'id_admission', 0 );
		$infosAdmission = $this->getAdmissionTable()->getAdmissionUrgence($id_admission);
		
		$listeSalles = $this->getPatientTable()->listeSalles();
		$listeLits = $this->getPatientTable()->listeLits();
		$listeServiceMutation = $this->getPatientTable ()->listeServicesMutation();
		
		$infosComp = array();
		$infosComp['salle'] = $this->params ()->fromPost (  'salle' );
		$infosComp['lit'] = $this->params ()->fromPost (  'lit' );
		$infosComp['couloir'] = $this->params ()->fromPost (  'couloir' );
		$infosComp['niveau'] = $this->params ()->fromPost (  'niveau' );
		
		//Récupération des données
		//Récupération des données
		$tabInformations = array();
		
		$tabInformations[0]['titre'] = 'Motif de la consultation';
		$tabInformations[0]['type' ] = 1;
		$tabInformations[0]['texte'] = iconv ('UTF-8' , 'windows-1252', $this->params ()->fromPost (  'motif_consultation' ));
		
		$tabInformations[1]['titre'] = 'Résumé syndrômique';
		$tabInformations[1]['type' ] = 1;
		$tabInformations[1]['texte'] = iconv ('UTF-8' , 'windows-1252', $this->params ()->fromPost (  'resume_syndromique' ));
		
		$tabInformations[2]['titre'] = 'Hypothèses diagnostiques';
		$tabInformations[2]['type' ] = 1;
		$tabInformations[2]['texte'] = iconv ('UTF-8' , 'windows-1252', $this->params ()->fromPost (  'hypotheses_diagnostiques' ));
		
		$tabInformations[3]['titre'] = 'Notes sur les examens complémentaires';
		$tabInformations[3]['type' ] = 1;
		$tabInformations[3]['texte'] = '';//iconv ('UTF-8' , 'windows-1252', $this->params ()->fromPost (  'examens_complementaires' ));
		
		$tabInformations[4]['titre'] = 'Traitement';
		$tabInformations[4]['type' ] = 1;
		$tabInformations[4]['texte'] = iconv ('UTF-8' , 'windows-1252', $this->params ()->fromPost (  'traitement' ));
		
		$tabInformations[5]['titre'] = 'Résultats des examens complémentaires';
		$tabInformations[5]['type' ] = 1;
		$tabInformations[5]['texte'] = iconv ('UTF-8' , 'windows-1252', $this->params ()->fromPost (  'resultats_examens_complementaires' ));
		
		$tabInformations[6]['titre'] = 'Mutation';
		$tabInformations[6]['type' ] = 2;
		$mutation = $this->params ()->fromPost (  'mutation' );
		if($mutation){
			$tabInformations[6]['texte'] = iconv ('UTF-8' , 'windows-1252', $listeServiceMutation[$mutation]);
		}else{
			$tabInformations[6]['texte'] = null;
		}
		
		$tabInformations[7]['titre'] = 'Mise à jour 1';
		$tabInformations[7]['type' ] = 2;
		$tabInformations[7]['texte'] = iconv ('UTF-8' , 'windows-1252', $this->params ()->fromPost (  'mise_a_jour_1' ));
		
		$tabInformations[8]['titre'] = 'Mise à jour 2';
		$tabInformations[8]['type' ] = 2;
		$tabInformations[8]['texte'] = iconv ('UTF-8' , 'windows-1252', $this->params ()->fromPost (  'mise_a_jour_2' ));
		
		$tabInformations[9]['titre'] = 'Mise à jour 3';
		$tabInformations[9]['type' ] = 2;
		$tabInformations[9]['texte'] = iconv ('UTF-8' , 'windows-1252', $this->params ()->fromPost (  'mise_a_jour_3' ));
		
		$tabInformations[10]['titre'] = 'Avis du spécialiste';
		$tabInformations[10]['type' ] = 1;
		$tabInformations[10]['texte'] = iconv ('UTF-8' , 'windows-1252', $this->params ()->fromPost (  'avis_specialiste' ));
		
		
		//Récupération des actes et examens complémentaires
		//Récupération des actes et examens complémentaires
		$tabInfosActesExamens = array();
		
		$listeActesDemandes = $this->getMotifAdmissionTable ()->getListeActesDemandes($id_admission);
		
		$tabInfosActesExamens[0]['titre'] = 'Les actes';
		$tabInfosActesExamens[0]['type' ] = 1;
		$tabInfosActesExamens[0]['texte'] = iconv ('UTF-8' , 'windows-1252', $listeActesDemandes);
		
		$listeExamensDemandes = $this->getMotifAdmissionTable ()->getListeExamensDemandes($id_admission);
		
		$tabInfosActesExamens[1]['titre'] = 'Les examens complémentaires';
		$tabInfosActesExamens[1]['type' ] = 1;
		$tabInfosActesExamens[1]['tableau'] = $listeExamensDemandes;
		
		//var_dump($listeExamensDemandes); exit();
		
		
		//Recuperer les infos du medecin si c'est le specialiste qui est connecte
		//Recuperer les infos du medecin si c'est le specialiste qui est connecte
		if($user['role'] == 'specialiste'){
			$constantes = $this->getConsultationTable()->getConsultationParIdAdmission($id_admission);
			$id_medecin = $constantes['ID_MEDECIN'];
			$infosEmploye = $this->getConsultationTable()->getInfosEmploye($id_medecin);
			$infosMedecin = array('nomMedecin' => $infosEmploye['NOM'], 'prenomMedecin' => $infosEmploye['PRENOM']);
		}
		
		//Recuperer les informations sur les infirmiers
		//Recuperer les informations sur les infirmiers
		$infosInfirmiers = array();
		if($infosAdmission){
			$id_infirmier_tri = $infosAdmission->id_infirmier_tri;
			$id_infirmier_service = $infosAdmission->id_infirmier_service;
			if($id_infirmier_tri){
				$infos =  $this->getConsultationTable()->getInfosInfirmier($id_infirmier_tri);
				$infosInfirmiers[$id_infirmier_tri] = iconv ('UTF-8' , 'windows-1252', $infos['PRENOM']).' '.iconv ('UTF-8' , 'windows-1252',$infos['NOM']);
			}
			
			if($id_infirmier_service){
				if($id_medecin != $id_infirmier_service){
					$infos =  $this->getConsultationTable()->getInfosInfirmier($id_infirmier_service);
					$infosInfirmiers[$id_infirmier_service] = iconv ('UTF-8' , 'windows-1252', $infos['PRENOM']).' '.iconv ('UTF-8' , 'windows-1252', $infos['NOM']);
				}else{
					$infosInfirmiers[$id_infirmier_service] = "";
				}
			}
		}
		
		//Recuperation des informations sur la consultation
		//Recuperation des informations sur la consultation
		$constantes = $this->getConsultationTable()->getConsultationParIdAdmission($id_admission);
		if($constantes){
			$infosComp['dateConsultation'] = $control->convertDate($constantes['DATEONLY']);
		}else{
			$today = new \DateTime ();
			$date = $today->format( 'd/m/Y' );
			$infosComp['dateConsultation'] = $date;
		}
		
		$pdf = new PDF();
		$pdf->SetMargins(13.5,13.5,13.5);
		
		$pdf->setNbInformations(count($tabInformations));
		$pdf->setTabInformations($tabInformations);
		$pdf->setTabInfosActesExamens($tabInfosActesExamens);
		$pdf->setNomService($nomService);
		$pdf->setInfosMedecin($infosMedecin);
		$pdf->setInfosInfirmiers($infosInfirmiers);
		$pdf->setInfosPatients($infosPatients);
		$pdf->setInfosAdmission($infosAdmission);
		$pdf->setInfosComp($infosComp);
		$pdf->setListeLits($listeLits);
		$pdf->setListeSalles($listeSalles);
		
		$pdf->ImpressionRpuHospitalisation();
		$pdf->Output('I');
		
	}
	
	//Impression des rpu-traumatologie
	//Impression des rpu-traumatologie
	public function imprimerRpuTraumatologieAction()
	{
		$control = new DateHelper();
		
		$user = $this->layout()->user;
		$nomService = $user['NomService'];
		$id_medecin = $user['id_employe'];
		
		$nomMedecin = $user['Nom'];
		$prenomMedecin = $user['Prenom'];
		$infosMedecin = array('nomMedecin' => $nomMedecin, 'prenomMedecin' => $prenomMedecin);
		
		$id_patient = $this->params ()->fromPost ( 'id_patient', 0 );
		$infosPatients = $this->getConsultationTable()->getInfoPatient($id_patient);
		
		$id_admission = $this->params ()->fromPost ( 'id_admission', 0 );
		$infosAdmission = $this->getAdmissionTable()->getAdmissionUrgence($id_admission);
		
		$listeSalles = $this->getPatientTable()->listeSalles();
		$listeLits = $this->getPatientTable()->listeLits();
		$listeServiceMutation = $this->getPatientTable ()->listeServicesMutation();
		$listeCirconstances = $this->getPatientTable ()->listeCirconstances();
		$listeMecanismes = $this->getPatientTable ()->listeMecanismes();
		$listeIndications = $this->getPatientTable ()->listeIndications();
		$listeDiagnostics = $this->getPatientTable ()->listeDiagnostic();
		$listeModeSortie = $this->getPatientTable ()->listeMotifsSortieRpuTraumato();
		

		$infosComp = array();
		$infosComp['salle'] = $this->params ()->fromPost (  'salle' );
		$infosComp['lit'] = $this->params ()->fromPost (  'lit' );
		$infosComp['couloir'] = $this->params ()->fromPost (  'couloir' );
		$infosComp['niveau'] = $this->params ()->fromPost (  'niveau' );
		
		//Récupération des données
		//Récupération des données
		$tabInformations = array();
		
		$tabInformations[0]['titre'] = 'Côté dominant';
		$tabInformations[0]['type' ] = 2;
		$cote_dominant = $this->params ()->fromPost (  'cote_dominant' );
		if($cote_dominant){
			if($cote_dominant == 1){	$tabInformations[0]['texte'] = 'Droite';}
			else{ 	$tabInformations[0]['texte'] = 'Gauche'; }
		}else{
			$tabInformations[0]['texte'] = null;
		}
		
		$tabInformations[1]['titre'] = 'Date & heure';
		$tabInformations[1]['type' ] = 2;
		$tabInformations[1]['texte'] = $this->params ()->fromPost (  'date_heure' );
		
		$tabInformations[2]['titre'] = 'Circonstances';
		$tabInformations[2]['type' ] = 2;
		$circonstances = $this->params ()->fromPost (  'circonstances' );
		if($circonstances){
			$tabInformations[2]['texte'] = iconv ('UTF-8' , 'windows-1252', $listeCirconstances[$circonstances]);
		}else{
			$tabInformations[2]['texte'] = null;
		}
		
		
		$tabInformations[3]['titre'] = 'Antécédent';
		$tabInformations[3]['type' ] = 1;
		$tabInformations[3]['texte'] = iconv ('UTF-8' , 'windows-1252', $this->params ()->fromPost (  'antecedent' ));
		
		$tabInformations[4]['titre'] = 'Examen physique';
		$tabInformations[4]['type' ] = 1;
		$tabInformations[4]['texte'] = iconv ('UTF-8' , 'windows-1252', $this->params ()->fromPost (  'examen_physique' ));
		
		$tabInformations[5]['titre'] = 'Examen paraclinique';
		$tabInformations[5]['type' ] = 1;
		$tabInformations[5]['texte'] = iconv ('UTF-8' , 'windows-1252', $this->params ()->fromPost (  'examen_paraclinique' ));
		
		$tabInformations[6]['titre'] = 'Résultat des examens complémentaires';
		$tabInformations[6]['type' ] = 1;
		$tabInformations[6]['texte'] = iconv ('UTF-8' , 'windows-1252', $this->params ()->fromPost (  'resultat_examen_complementaire' ));
		
		$tabInformations[7]['titre'] = 'Mécanismes';
		$tabInformations[7]['type' ] = 2;
		$mecanismes = $this->params ()->fromPost (  'mecanismes' );
		if($mecanismes){ $tabInformations[7]['texte'] = iconv ('UTF-8' , 'windows-1252', $listeMecanismes[$mecanismes]); }
		else{ $tabInformations[7]['texte'] = null; }
		
		$tabInformations[8]['titre'] = 'Mécanismes précision';
		$tabInformations[8]['type' ] = 2;
		$tabInformations[8]['texte'] = iconv ('UTF-8' , 'windows-1252', $this->params ()->fromPost (  'mecanismes_precision' ));
		
		$tabInformations[9]['titre'] = 'Indication';
		$tabInformations[9]['type' ] = 2;
		$indication = $this->params ()->fromPost (  'indication' );
		if($indication){ $tabInformations[9]['texte'] = iconv ('UTF-8' , 'windows-1252', $listeIndications[$indication]); }
		else{ $tabInformations[9]['texte'] = null; }
		
		$tabInformations[10]['titre'] = 'Indication précision';
		$tabInformations[10]['type' ] = 2;
		$tabInformations[10]['texte'] = iconv ('UTF-8' , 'windows-1252', $this->params ()->fromPost (  'indication_precision' ));
		
		$tabInformations[11]['titre'] = 'Diagnostic';
		$tabInformations[11]['type' ] = 2;
		$diagnostic = $this->params ()->fromPost (  'diagnostic' );
		if($diagnostic){ $tabInformations[11]['texte'] = iconv ('UTF-8' , 'windows-1252', $listeDiagnostics[$diagnostic]); }
		else{ $tabInformations[11]['texte'] = null; }
		
		$tabInformations[12]['titre'] = 'Diagnostic précision';
		$tabInformations[12]['type' ] = 2;
		$tabInformations[12]['texte'] = iconv ('UTF-8' , 'windows-1252', $this->params ()->fromPost (  'diagnostic_precision' ));
		
		$tabInformations[13]['titre'] = 'Conduite à tenir';
		$tabInformations[13]['type' ] = 1;
		$tabInformations[13]['texte'] = iconv ('UTF-8' , 'windows-1252', $this->params ()->fromPost (  'conduite' ));
		
		$tabInformations[14]['titre'] = 'Mode de sortie';
		$tabInformations[14]['type' ] = 2;
		$motif_sortie = $this->params ()->fromPost (  'motif_sortie' );
		if($diagnostic){ $tabInformations[14]['texte'] = iconv ('UTF-8' , 'windows-1252', $listeModeSortie[$motif_sortie]); }
		else{ $tabInformations[14]['texte'] = null; }
		
		
		$tabInformations[15]['titre'] = 'Rendez-vous';
		$tabInformations[15]['type' ] = 2;
		$tabInformations[15]['texte'] = iconv ('UTF-8' , 'windows-1252', $this->params ()->fromPost (  'rendez_vous' ));
		
		$tabInformations[16]['titre'] = 'Avis du spécialiste';
		$tabInformations[16]['type' ] = 1;
		$tabInformations[16]['texte'] = iconv ('UTF-8' , 'windows-1252', $this->params ()->fromPost (  'specialiste_trauma' ));
		
		$tabInformations[17]['titre'] = 'Conduite à tenir (par le spécialiste)';
		$tabInformations[17]['type' ] = 1;
		$tabInformations[17]['texte'] = iconv ('UTF-8' , 'windows-1252', $this->params ()->fromPost (  'conduite_specialiste' ));
		
		//Recuperer les infos du medecin si c'est le specialiste qui est connecte
		//Recuperer les infos du medecin si c'est le specialiste qui est connecte
		if($user['role'] == 'specialiste'){
			$constantes = $this->getConsultationTable()->getConsultationParIdAdmission($id_admission);
			$id_medecin = $constantes['ID_MEDECIN'];
			$infosEmploye = $this->getConsultationTable()->getInfosEmploye($id_medecin);
			$infosMedecin = array('nomMedecin' => $infosEmploye['NOM'], 'prenomMedecin' => $infosEmploye['PRENOM']);
		}
		
		//Recuperer les informations sur les infirmiers
		//Recuperer les informations sur les infirmiers
		$infosInfirmiers = array();
		if($infosAdmission){
			$id_infirmier_tri = $infosAdmission->id_infirmier_tri;
			$id_infirmier_service = $infosAdmission->id_infirmier_service;
			if($id_infirmier_tri){
				$infos =  $this->getConsultationTable()->getInfosInfirmier($id_infirmier_tri);
				$infosInfirmiers[$id_infirmier_tri] = iconv ('UTF-8' , 'windows-1252', $infos['PRENOM']).' '.iconv ('UTF-8' , 'windows-1252',$infos['NOM']);
			}
				
			if($id_infirmier_service){
				if($id_medecin != $id_infirmier_service){
					$infos =  $this->getConsultationTable()->getInfosInfirmier($id_infirmier_service);
					$infosInfirmiers[$id_infirmier_service] = iconv ('UTF-8' , 'windows-1252', $infos['PRENOM']).' '.iconv ('UTF-8' , 'windows-1252', $infos['NOM']);
				}else{
					$infosInfirmiers[$id_infirmier_service] = "";
				}
			}
		}
		
		//Recuperation des informations sur la consultation
		//Recuperation des informations sur la consultation
		$constantes = $this->getConsultationTable()->getConsultationParIdAdmission($id_admission);
		if($constantes){
			$infosComp['dateConsultation'] = $control->convertDate($constantes['DATEONLY']);
		}else{
			$today = new \DateTime ();
			$date = $today->format( 'd/m/Y' );
			$infosComp['dateConsultation'] = $date;
		}
		
		
		
		//Récupération des actes et examens complémentaires
		//Récupération des actes et examens complémentaires
		$tabInfosActesExamens = array();
		
		$listeActesDemandes = $this->getMotifAdmissionTable ()->getListeActesDemandes($id_admission);
		
		$tabInfosActesExamens[0]['titre'] = 'Les actes';
		$tabInfosActesExamens[0]['type' ] = 1;
		$tabInfosActesExamens[0]['texte'] = iconv ('UTF-8' , 'windows-1252', $listeActesDemandes);
		
		$listeExamensDemandes = $this->getMotifAdmissionTable ()->getListeExamensDemandes($id_admission);
		
		$tabInfosActesExamens[1]['titre'] = 'Les examens complémentaires';
		$tabInfosActesExamens[1]['type' ] = 1;
		$tabInfosActesExamens[1]['tableau'] = $listeExamensDemandes;
		
		
		
		$pdf = new PDF();
		$pdf->SetMargins(13.5,13.5,13.5);
		
		$pdf->setNbInformations(count($tabInformations));
		$pdf->setTabInformations($tabInformations);
		$pdf->setTabInfosActesExamens($tabInfosActesExamens);
		$pdf->setNomService($nomService);
		$pdf->setInfosMedecin($infosMedecin);
		$pdf->setInfosInfirmiers($infosInfirmiers);
		$pdf->setInfosPatients($infosPatients);
		$pdf->setInfosAdmission($infosAdmission);
		$pdf->setInfosComp($infosComp);
		$pdf->setListeLits($listeLits);
		$pdf->setListeSalles($listeSalles);
		
		$pdf->ImpressionRpuTraumatologie();
		$pdf->Output('I');
	}
	
	public function imprimerRpuSortieAction(){

		$control = new DateHelper();
		
		$user = $this->layout()->user;
		$nomService = $user['NomService'];
		$id_medecin = $user['id_employe'];
		
		$nomMedecin = $user['Nom'];
		$prenomMedecin = $user['Prenom'];
		$infosMedecin = array('nomMedecin' => $nomMedecin, 'prenomMedecin' => $prenomMedecin);
		
		$id_patient = $this->params ()->fromPost ( 'id_patient', 0 );
		$infosPatients = $this->getConsultationTable()->getInfoPatient($id_patient);
		
		$id_admission = $this->params ()->fromPost ( 'id_admission', 0 );
		$infosAdmission = $this->getAdmissionTable()->getAdmissionUrgence($id_admission);
		
		$listeSalles = $this->getPatientTable()->listeSalles();
		$listeLits = $this->getPatientTable()->listeLits();
		$listeServiceMutation = $this->getPatientTable ()->listeServicesMutation();
		$listeCirconstances = $this->getPatientTable ()->listeCirconstances();
		$listeMecanismes = $this->getPatientTable ()->listeMecanismes();
		$listeIndications = $this->getPatientTable ()->listeIndications();
		$listeDiagnostics = $this->getPatientTable ()->listeDiagnostic();
		$listeModeSortie = $this->getPatientTable ()->listeMotifsSortieRpuSortie();
		
		
		$infosComp = array();
		$infosComp['salle'] = $this->params ()->fromPost (  'salle' );
		$infosComp['lit'] = $this->params ()->fromPost (  'lit' );
		$infosComp['couloir'] = $this->params ()->fromPost (  'couloir' );
		$infosComp['niveau'] = $this->params ()->fromPost (  'niveau' );
		
		//Récupération des données
		//Récupération des données
		$tabInformations = array();
		
		$tabInformations[0]['titre'] = 'Diagnostic principal';
		$tabInformations[0]['type' ] = 1;
		$tabInformations[0]['texte'] = iconv ('UTF-8' , 'windows-1252', $this->params ()->fromPost (  'diganostic' ));
		
		$tabInformations[1]['titre'] = 'Diagnostic associé';
		$tabInformations[1]['type' ] = 1;
		$tabInformations[1]['texte'] = iconv ('UTF-8' , 'windows-1252', $this->params ()->fromPost (  'diganostic_associe' ));
		
		$tabInformations[2]['titre'] = 'Traitement';
		$tabInformations[2]['type' ] = 1;
		$tabInformations[2]['texte'] = iconv ('UTF-8' , 'windows-1252', $this->params ()->fromPost (  'traitement' ));
		
		$tabInformations[3]['titre'] = 'Examens complémentaires demandés';
		$tabInformations[3]['type' ] = 1;
		$tabInformations[3]['texte'] = iconv ('UTF-8' , 'windows-1252', $this->params ()->fromPost (  'examens_complementaires_demandes' ));
		
		$tabInformations[4]['titre'] = 'Mode de sortie';
		$tabInformations[4]['type' ] = 2;
		$mode_sortie = $this->params ()->fromPost (  'mode_sortie' );
		if($mode_sortie){
			$tabInformations[4]['texte'] = iconv ('UTF-8' , 'windows-1252', $listeModeSortie[$mode_sortie]);
		}else{
			$tabInformations[4]['texte'] = null;
		}
		
		$tabInformations[5]['titre'] = 'Muter vers';
		$tabInformations[5]['type' ] = 2;
		$liste_mutation = $this->params ()->fromPost (  'liste_mutation' );
		if($liste_mutation){
			$tabInformations[5]['texte'] = iconv ('UTF-8' , 'windows-1252', $listeServiceMutation[$liste_mutation]);
		}else{
			$tabInformations[5]['texte'] = null;
		}
		
		$tabInformations[6]['titre'] = 'Transférer vers';
		$tabInformations[6]['type' ] = 2;
		$tabInformations[6]['texte'] = iconv ('UTF-8' , 'windows-1252',  $this->params ()->fromPost (  'transfert' ));
		
		$tabInformations[7]['titre'] = 'Evacuer vers';
		$tabInformations[7]['type' ] = 2;
		$tabInformations[7]['texte'] = iconv ('UTF-8' , 'windows-1252',  $this->params ()->fromPost (  'evacuation' ));

		//Recuperer les infos du medecin si c'est le specialiste qui est connecte 
		//Recuperer les infos du medecin si c'est le specialiste qui est connecte
		if($user['role'] == 'specialiste'){
			$constantes = $this->getConsultationTable()->getConsultationParIdAdmission($id_admission);
			$id_medecin = $constantes['ID_MEDECIN'];
			$infosEmploye = $this->getConsultationTable()->getInfosEmploye($id_medecin);
			$infosMedecin = array('nomMedecin' => $infosEmploye['NOM'], 'prenomMedecin' => $infosEmploye['PRENOM']);
		}
		
		//Recuperer les informations sur les infirmiers
		//Recuperer les informations sur les infirmiers
		$infosInfirmiers = array();
		if($infosAdmission){
			$id_infirmier_tri = $infosAdmission->id_infirmier_tri;
			$id_infirmier_service = $infosAdmission->id_infirmier_service;
			if($id_infirmier_tri){
				$infos =  $this->getConsultationTable()->getInfosInfirmier($id_infirmier_tri);
				$infosInfirmiers[$id_infirmier_tri] = iconv ('UTF-8' , 'windows-1252', $infos['PRENOM']).' '.iconv ('UTF-8' , 'windows-1252',$infos['NOM']);
			}
		
			if($id_infirmier_service){
				if($id_medecin != $id_infirmier_service){
					$infos =  $this->getConsultationTable()->getInfosInfirmier($id_infirmier_service);
					$infosInfirmiers[$id_infirmier_service] = iconv ('UTF-8' , 'windows-1252', $infos['PRENOM']).' '.iconv ('UTF-8' , 'windows-1252', $infos['NOM']);
				}else{
					$infosInfirmiers[$id_infirmier_service] = "";
				}
			}
		}
		
		//Recuperation des informations sur la consultation
		//Recuperation des informations sur la consultation
		$constantes = $this->getConsultationTable()->getConsultationParIdAdmission($id_admission);
		if($constantes){
			$infosComp['dateConsultation'] = $control->convertDate($constantes['DATEONLY']);
		}else{
			$today = new \DateTime ();
			$date = $today->format( 'd/m/Y' );
			$infosComp['dateConsultation'] = $date;
		}
		
		$pdf = new PDF();
		$pdf->SetMargins(13.5,13.5,13.5);
		
		$pdf->setNbInformations(count($tabInformations));
		$pdf->setTabInformations($tabInformations);
		$pdf->setNomService($nomService);
		$pdf->setInfosMedecin($infosMedecin);
		$pdf->setInfosInfirmiers($infosInfirmiers);
		$pdf->setInfosPatients($infosPatients);
		$pdf->setInfosAdmission($infosAdmission);
		$pdf->setInfosComp($infosComp);
		$pdf->setListeLits($listeLits);
		$pdf->setListeSalles($listeSalles);
		
		$pdf->ImpressionRpuSortie();
		$pdf->Output('I');
	}
	
	
}
