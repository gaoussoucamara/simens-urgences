<?php

namespace Urgence\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Urgence\Form\PatientForm;
use Urgence\Form\AdmissionForm;
use Urgence\View\Helper\DateHelper;
use Zend\Json\Json;
use Urgence\View\Helper\infosStatistiquePdf;
use Urgence\View\Helper\infosRegistrePatientAdmisPdf;

class UrgenceController extends AbstractActionController {
	protected $patientTable;
	protected $formPatient;
	protected $tarifConsultationTable;
	protected $consultationTable;
	protected $serviceTable;
	protected $admissionTable;
	protected $dateHelper;
	protected $motifAdmissionTable;
	protected $pathologieTable;
	protected $typePathologieTable;
	protected $personneListeTable;
	
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
	
	public function getPathologieTable() {
		if (! $this->pathologieTable) {
			$sm = $this->getServiceLocator ();
			$this->pathologieTable = $sm->get ( 'Urgence\Model\PathologieTable' );
		}
		return $this->pathologieTable;
	}
	
	public function getTypePathologieTable() {
		if (! $this->typePathologieTable) {
			$sm = $this->getServiceLocator ();
			$this->typePathologieTable = $sm->get ( 'Urgence\Model\TypePathologieTable' );
		}
		return $this->typePathologieTable;
	}
	
	public function getPersonneListeTable() {
		if (! $this->personneListeTable) {
			$sm = $this->getServiceLocator ();
			$this->personneListeTable = $sm->get ( 'Urgence\Model\PersonneTable' );
		}
		return $this->personneListeTable;
	}
	
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
	/* ----- DOMAINE DE LA CREATION DU DOSSIER PATIENT ------- */
	/* ----- DOMAINE DE LA CREATION DU DOSSIER PATIENT ------- */
	//--------------------------------------------------------------------------------------
	//--------------------------------------------------------------------------------------
	//--------------------------------------------------------------------------------------
	
	
	public function listePatientAction() {
		// $personne = $this->getPatientTable()->miseAJourAgePatient(4);
		$user = $this->layout ()->user;
		$id_employe = $user ['id_personne']; // L'utilisateur connect�
		
		
		/***
		 * TEST DE LA NOUVELLE METHODE POUR L'AFFICHAGE RAPIDE DES LISTES
		 * TEST DE LA NOUVELLE METHODE POUR L'AFFICHAGE RAPIDE DES LISTES
		 */
		
		//$timestart = microtime(true);
		//$output = $this->getPatientTable ()->getListePatient ();
		//var_dump($output); exit();
		/*
		$output = $this->getPersonneListeTable()->fetchAll()->toArray();
		$listeAjax = array(
				'iTotalDisplayRecords' => count($output),
				'aaData' => $output
		);
		*/
		
		//$timeend = microtime(true);
		//$time = $timeend-$timestart;

		//var_dump(number_format($time,3)); exit();
		
		/***
		 * ==============================================================
		 * ==============================================================
		 */
		
		
		
		
		$layout = $this->layout ();
		$layout->setTemplate ( 'layout/urgence' );
		$view = new ViewModel ();
		return $view;
	}
	
	/**
	 * Pour la creation du dossier patient
	 */
	public function enregistrementPatientAction() {
		$user = $this->layout ()->user;
		$id_employe = $user ['id_personne']; // L'utilisateur connect�
		                                    
		// CHARGEMENT DE LA PHOTO ET ENREGISTREMENT DES DONNEES
		if (isset ( $_POST ['terminer'] )) 		// si formulaire soumis
		{
			$Control = new DateHelper ();
			$form = new PatientForm ();
			$Patient = $this->getPatientTable ();
			$today = new \DateTime ( 'now' );
			$nomfile = $today->format ( 'dmy_His' );
			$date_enregistrement = $today->format ( 'Y-m-d H:i:s' );
			$fileBase64 = $this->params ()->fromPost ( 'fichier_tmp' );
			$fileBase64 = substr ( $fileBase64, 23 );
			
			if ($fileBase64) {
				$img = imagecreatefromstring ( base64_decode ( $fileBase64 ) );
			} else {
				$img = false;
			}
			
			$date_naissance = $this->params ()->fromPost ( 'DATE_NAISSANCE' );
			if ($date_naissance) {
				$date_naissance = $Control->convertDateInAnglais ( $this->params ()->fromPost ( 'DATE_NAISSANCE' ) );
			} else {
				$date_naissance = null;
			}
			
			$donnees = array (
					'LIEU_NAISSANCE' => $this->params ()->fromPost ( 'LIEU_NAISSANCE' ),
					'EMAIL' => $this->params ()->fromPost ( 'EMAIL' ),
					'NOM' => $this->params ()->fromPost ( 'NOM' ),
					'TELEPHONE' => $this->params ()->fromPost ( 'TELEPHONE' ),
					'NATIONALITE_ORIGINE' => $this->params ()->fromPost ( 'NATIONALITE_ORIGINE' ),
					'PRENOM' => $this->params ()->fromPost ( 'PRENOM' ),
					'PROFESSION' => $this->params ()->fromPost ( 'PROFESSION' ),
					'NATIONALITE_ACTUELLE' => $this->params ()->fromPost ( 'NATIONALITE_ACTUELLE' ),
					'DATE_NAISSANCE' => $date_naissance,
					'ADRESSE' => $this->params ()->fromPost ( 'ADRESSE' ),
					'SEXE' => $this->params ()->fromPost ( 'SEXE' ),
					'AGE' => $this->params ()->fromPost ( 'AGE' ) 
			);
			
			$sexe = 2;
			if($donnees['SEXE'] == 'Masculin'){ $sexe = 1; }
			
			//var_dump($donnees); exit();
			
			if ($img != false) {
				
				$donnees ['PHOTO'] = $nomfile;
				// ENREGISTREMENT DE LA PHOTO
				imagejpeg ( $img, $this->baseUrlRacine().'public/img/photos_patients/' . $nomfile . '.jpg' );
				// ENREGISTREMENT DES DONNEES
				$Patient->addPatientAvecNumeroDossier ( $donnees, $date_enregistrement, $id_employe, $sexe );
			} else {
				// On enregistre sans la photo
				$Patient->addPatientAvecNumeroDossier ( $donnees, $date_enregistrement, $id_employe, $sexe );
			}
			
			return $this->redirect ()->toRoute ( 'urgence', array (
					'action' => 'admission' 
			) );
		}
	}
	
	public function getForm() {
		if (! $this->formPatient) {
			$this->formPatient = new PatientForm ();
		}
		return $this->formPatient;
	}
	
	public function getPhoto($id) {
		$donneesPatient =  $this->getInfoPatient( $id );
	
		$nom = null;
		if($donneesPatient){$nom = $donneesPatient['PHOTO'];}
		if ($nom) {
			return $nom . '.jpg';
		} else {
			return 'identite.jpg';
		}
	}
	
	public function ajoutPatientAction() {
		$this->layout ()->setTemplate ( 'layout/urgence' );
		$form = $this->getForm ();
		// $form = new PatientForm ();
		$patientTable = $this->getPatientTable ();
		$form->get ( 'NATIONALITE_ORIGINE' )->setvalueOptions ( $patientTable->listeDeTousLesPays () );
		$form->get ( 'NATIONALITE_ACTUELLE' )->setvalueOptions ( $patientTable->listeDeTousLesPays () );
		$data = array (
				'NATIONALITE_ORIGINE' => 'Sénégal',
				'NATIONALITE_ACTUELLE' => 'Sénégal' 
		);
		
		$form->populateValues ( $data );
		
		return new ViewModel ( array (
				'form' => $form 
		) );
	}
	
	public function listePatientAjaxAction() {
		$output = $this->getPatientTable ()->getListePatient ();
		
		/*
		$output = $this->getPersonneListeTable()->fetchAll()->toArray();
		$listeAjax = array(
				'iTotalDisplayRecords' => count($output),
				'aaData' => $output
		);
		*/
		
		return $this->getResponse ()->setContent ( Json::encode ( $output, array (
				'enableJsonExprFinder' => true 
		) ) );
	}
	
	// modification donnees d'un patient
	public function modifierAction() {
		$control = new DateHelper ();
		$this->layout ()->setTemplate ( 'layout/urgence' );
		$id_patient = $this->params ()->fromRoute ( 'id_patient', 0 );
		//var_dump($id_patient);exit();
		$infoPatient = $this->getPatientTable ();
		try {
			$info = $infoPatient->getInfoPatient ( $id_patient );
		} catch ( \Exception $ex ) {
			return $this->redirect ()->toRoute ( 'urgence', array (
					'action' => 'liste-patient' 
			) );
		}
		$form = new PatientForm ();
		$form->get ( 'NATIONALITE_ORIGINE' )->setvalueOptions ( $infoPatient->listeDeTousLesPays () );
		$form->get ( 'NATIONALITE_ACTUELLE' )->setvalueOptions ( $infoPatient->listeDeTousLesPays () );
		
		$date_naissance = $info ['DATE_NAISSANCE'];
		if ($date_naissance) {
			$info ['DATE_NAISSANCE'] = $control->convertDate ( $info ['DATE_NAISSANCE'] );
		} else {
			$info ['DATE_NAISSANCE'] = null;
		}
		
		$form->populateValues ( $info );
		
		if (! $info ['PHOTO']) {
			$info ['PHOTO'] = "identite";
		}
		return array (
				'form' => $form,
				'photo' => $info ['PHOTO'] 
		);
	}
    
    //Enregistrement modification
	public function enregistrementModificationAction() {
	
		$user = $this->layout()->user;
		$id_employe = $user['id_personne']; //L'utilisateur connect�
	
		if (isset ( $_POST ['terminer'] ))
		{
			$Control = new DateHelper();
			$Patient = $this->getPatientTable ();
			$today = new \DateTime ( 'now' );
			$nomfile = $today->format ( 'dmy_His' );
			$date_modification = $today->format ( 'Y-m-d H:i:s' );
			$fileBase64 = $this->params ()->fromPost ( 'fichier_tmp' );
			$fileBase64 = substr ( $fileBase64, 23 );
	
			if($fileBase64){
				$img = imagecreatefromstring(base64_decode($fileBase64));
			}else {
				$img = false;
			}
	
			$date_naissance = $this->params ()->fromPost ( 'DATE_NAISSANCE' );
			if($date_naissance){ $date_naissance = $Control->convertDateInAnglais($this->params ()->fromPost ( 'DATE_NAISSANCE' )); }else{ $date_naissance = null;}
	
			$donnees = array(
					'LIEU_NAISSANCE' => $this->params ()->fromPost ( 'LIEU_NAISSANCE' ),
					'EMAIL' => $this->params ()->fromPost ( 'EMAIL' ),
					'NOM' => $this->params ()->fromPost ( 'NOM' ),
					'TELEPHONE' => $this->params ()->fromPost ( 'TELEPHONE' ),
					'NATIONALITE_ORIGINE' => $this->params ()->fromPost ( 'NATIONALITE_ORIGINE' ),
					'PRENOM' => $this->params ()->fromPost ( 'PRENOM' ),
					'PROFESSION' => $this->params ()->fromPost ( 'PROFESSION' ),
					'NATIONALITE_ACTUELLE' => $this->params ()->fromPost ( 'NATIONALITE_ACTUELLE' ),
					'DATE_NAISSANCE' => $date_naissance,
					'ADRESSE' => $this->params ()->fromPost ( 'ADRESSE' ),
					'SEXE' => $this->params ()->fromPost ( 'SEXE' ),
					'AGE' => $this->params ()->fromPost ( 'AGE' ),
			);
	
			$id_patient =  $this->params ()->fromPost ( 'ID_PERSONNE' );
			
			$info = $this->getPatientTable ()->getInfoPatient ( $id_patient );
			if($donnees['SEXE'] == 'Masculin'){ $numero_dossier = substr_replace($info['NUMERO_DOSSIER'], 1, 0, 1); }else{  $numero_dossier = substr_replace($info['NUMERO_DOSSIER'], 2, 0, 1);  }
			
			if ($img != false) {
	
				$lePatient = $Patient->getInfoPatient ( $id_patient );
				$ancienneImage = $lePatient['PHOTO'];
	
				if($ancienneImage) {
					unlink ( $this->baseUrlRacine().'public/img/photos_patients/' . $ancienneImage . '.jpg' );
				}
				imagejpeg ( $img, $this->baseUrlRacine().'public/img/photos_patients/' . $nomfile . '.jpg' );
	
				$donnees['PHOTO'] = $nomfile;
				$Patient->updatePatient ( $donnees , $id_patient, $numero_dossier, $date_modification, $id_employe);
					
			} else {
				$Patient->updatePatient($donnees, $id_patient, $numero_dossier, $date_modification, $id_employe);
				
			}
			return $this->redirect ()->toRoute ( 'urgence', array (
					'action' => 'liste-patient'
			) );
		}
	}

    //Afficher Information Patient
	public function infoPatientAction() {
		$this->layout ()->setTemplate ( 'layout/urgence' );
		$id_pat = $this->params ()->fromRoute ( 'id_patient', 0 );
	
		$patient = $this->getPatientTable ();
		$unPatient = $patient->getInfoPatient( $id_pat );
	
		return array (
				'lesdetails' => $unPatient,
				'image' => $patient->getPhoto ( $id_pat ),
				'id_patient' => $unPatient['ID_PERSONNE'],
				'numero_dossier' => $unPatient['NUMERO_DOSSIER'],
				'date_enregistrement' => $unPatient['DATE_ENREGISTREMENT']
		);
	}


	public function ajouterAction() {
		$this->layout ()->setTemplate ( 'layout/urgence' );
		$form = $this->getForm ();
		$patientTable = $this->getPatientTable();
		$form->get('NATIONALITE_ORIGINE')->setvalueOptions($patientTable->listeDeTousLesPays());
		$form->get('NATIONALITE_ACTUELLE')->setvalueOptions($patientTable->listeDeTousLesPays());
		$data = array('NATIONALITE_ORIGINE' => 'Sénégal', 'NATIONALITE_ACTUELLE' => 'Sénégal');
	
		$form->populateValues($data);
	
		return new ViewModel ( array (
				'form' => $form
		) );
	}

	public function supprimerAction() {
		$id = ( int ) $this->params ()->fromPost ( 'id', 0 );
	    $this->getPatientTable ()->deletePersonne ( $id );
		
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ( ) );
	}
	
	public function getInfosVuePatientAction() {
		$id = ( int ) $this->params ()->fromPost ( 'id', 0 );
		//MISE A JOUR DE L'AGE DU PATIENT
		//MISE A JOUR DE L'AGE DU PATIENT
		//MISE A JOUR DE L'AGE DU PATIENT
		  //$personne = $this->getPatientTable()->miseAJourAgePatient($id);
		//*******************************
		//*******************************
		//*******************************
		$pat = $this->getPatientTable ();
		$unPatient = $pat->getInfoPatient ( $id );
		$photo = $pat->getPhoto ( $id );
		
		$date = $unPatient['DATE_NAISSANCE'];
		if($date){ $date = (new DateHelper())->convertDate ($date); }else{ $date = null;}
		
		$html = "<div style='float:left;' ><div id='photo' style='float:left; margin-right:20px; margin-bottom: 10px;'> <img  src='".$this->baseUrl()."public/img/photos_patients/" . $photo . "'  style='width:105px; height:105px;'></div>";
		$html .= "<div style='margin-left:6px;'> <div style='text-decoration:none; font-size:14px; float:left; padding-right: 7px; '>Age:</div>  <div style='font-weight:bold; font-size:19px; font-family: time new romans; color: green; font-weight: bold;'>" . $unPatient['AGE'] . " ans</div></div></div>";
		
		
		$html .= "<table>";
		
		$html .= "<tr>";
		$html .= "<td><a style='text-decoration:underline; font-size:12px;'>Nom:</a><br><p style='width:280px; font-weight:bold; font-size:17px;'>" . $unPatient['NOM'] . "</p></td>";
		$html .= "</tr><tr>";
		$html .= "<td><a style='text-decoration:underline; font-size:12px;'>Pr&eacute;nom:</a><br><p style='width:280px; font-weight:bold; font-size:17px;'>" . $unPatient['PRENOM'] . "</p></td>";
		$html .= "</tr><tr>";
		$html .= "<td><a style='text-decoration:underline; font-size:12px;'>Date de naissance:</a><br><p style='width:280px; font-weight:bold; font-size:17px;'>" . $date . "</p></td>";
		$html .= "</tr><tr>";
		$html .= "<td><a style='text-decoration:underline; font-size:12px;'>Adresse:</a><br><p style='width:280px; font-weight:bold; font-size:17px;'>" . $unPatient['ADRESSE'] . "</p></td>";
		$html .= "</tr><tr>";
		$html .= "<td><a style='text-decoration:underline; font-size:12px;'>T&eacute;l&eacute;phone:</a><br><p style='width:280px; font-weight:bold; font-size:17px;'>" . $unPatient['TELEPHONE'] . "</p></td>";
		$html .= "</tr>";
		
		$html .= "</table>";
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ( $html ) );
		
	}
	
	//**************************************************************************************
	//**************************************************************************************
	//**************************************************************************************
	//**************************************************************************************
	   /* ----- DOMAINE DE LA GESTION DES ADMISSIONS DES PATIENTS ------- */
	   /* ----- DOMAINE DE LA GESTION DES ADMISSIONS DES PATIENTS ------- */
	//--------------------------------------------------------------------------------------
	//--------------------------------------------------------------------------------------
	//--------------------------------------------------------------------------------------
	public function listeAdmissionAjaxAction() {
		$output = $this->getPatientTable ()->laListePatientsAjax();
		return $this->getResponse ()->setContent ( Json::encode ( $output, array (
				'enableJsonExprFinder' => true
		) ) );
	}
	
	public function listeAdmissionInfirmierTriAjaxAction() {
		$output = $this->getPatientTable ()->laListePatientsAdmisParInfimierTriAjax();
		return $this->getResponse ()->setContent ( Json::encode ( $output, array (
				'enableJsonExprFinder' => true
		) ) );
	}
	
	public function getNbPatientAdmisNonVuAction(){
		$nbPatientAdmisInfTriNonVu = $this->getPatientTable ()->nbPatientAdmisParInfirmierTri();
		
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ( $nbPatientAdmisInfTriNonVu ) );
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
	
    //Admission d' un patient aux urgences
	public function admissionAction() {
	
		if ($this->getRequest ()->isPost ()) {
				
			$id = ( int ) $this->params ()->fromPost ( 'id', 0 );
				
			//MISE A JOUR DE L'AGE DU PATIENT
			//MISE A JOUR DE L'AGE DU PATIENT
			//MISE A JOUR DE L'AGE DU PATIENT
			         //$personne = $this->getPatientTable()->miseAJourAgePatient($id);
			//*******************************
			//*******************************
			//*******************************
				
			$unPatient = $this->getPatientTable ()->getInfoPatient( $id );
			$photo = $this->getPatientTable ()->getPhoto ( $id );
				
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
			$html .= "<div style='color: white; opacity: 0.09; float:left; margin-right:10px; margin-left:5px; margin-top:5px;'> <img style='width:105px; height:105px;' src='".$this->baseUrl()."public/img/photos_patients/" . $photo . "'></div>";
			$html .= "<div style='margin-left: 5px; margin-top: 10px; margin-right:10px;'>  <div style='font-size:19px; font-family: time new romans; color: green; float:left; margin-top: 10px;'>" . $unPatient['NUMERO_DOSSIER'] . " </div></div>";
			$html .= "</div>";
				
			$html .= "</div>";
			
			//Liste des actes et des examens compl�mentaires
			$listeActes = $this->getAdmissionTable()->getListeActes();
			$listeExamenComp = $this->getAdmissionTable()->getListeExamenComp();
			
			$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
			return $this->getResponse ()->setContent ( Json::encode ( array($html, $listeActes, $listeExamenComp) ) );
		
		}else{
			
			//$layout = $this->layout ();
			$this->layout ()->setTemplate ( 'layout/urgence' );
			
			//INSTANCIATION DU FORMULAIRE D'ADMISSION
			$formAdmission = new AdmissionForm ();
			
			$listeModeTransport = $this->getPatientTable ()->listeModeTransport();
			$formAdmission->get ( 'mode_transport' )->setValueOptions ($listeModeTransport);
			
			$listeSalles = $this->getPatientTable ()->listeSalles();
			$formAdmission->get ( 'salle' )->setValueOptions ($listeSalles);
			
			$nbPatientAdmisInfTriNonVu = $this->getPatientTable ()->nbPatientAdmisParInfirmierTri();
			
			/*
			$listeMotifs = $this->getPatientTable ()->listeMotifsAdmission();
			$formAdmission->get ( 'motif_admission1' )->setValueOptions ($listeMotifs);
			$formAdmission->get ( 'motif_admission2' )->setValueOptions ($listeMotifs);
			$formAdmission->get ( 'motif_admission3' )->setValueOptions ($listeMotifs);
			$formAdmission->get ( 'motif_admission4' )->setValueOptions ($listeMotifs);
			$formAdmission->get ( 'motif_admission5' )->setValueOptions ($listeMotifs);
			
			//var_dump($listeMotifs); exit();
			*/
			//A REVOIR DANS LA PARTIE AMELIORATION
			//A REVOIR DANS LA PARTIE AMELIORATION
			// 		$listeLitsParSalles = $this->getPatientTable ()->listeLitsParSalle();
			// 		$liste_select = "";
			// 		for($tS = 0 ; $tS < count($listeLitsParSalles[0]) ; $tS++){
			// 			var_dump($listeLitsParSalles[1][$listeLitsParSalles[0][$tS]]); exit();
			// 			for($i = 0 ; $i < count($listeLitsParSalles[1][$listeLitsParSalles[0][$tS]]) ; $i++){
			// 				$liste_select.= "<option value=".$listeServices['Id_service'].">".$listeServices['Nom_service']."</option>";
			// 			}
			// 		}
			// 		var_dump($listeLitsParSalles); exit();
			//		var_dump($this->getPatientTable()->getListeLitsPourSalle(2)->current()); exit();
			
			//Fin --- A REVOIR DANS LA PARTI AMELIORATION
			//Fin --- A REVOIR DANS LA PARTI AMELIORATION
			
		}
		
		return array (
				'form' => $formAdmission,
				'nbPatientAdmisInfTriNonVu' => $nbPatientAdmisInfTriNonVu,
		);
	}
	
	//Verifier si un tableau est vide ou pas 
    function array_empty($array) {
    	$is_empty = true;
        foreach($array as $k) {
        	$is_empty = $is_empty && empty($k);
        }
        return $is_empty;
    }
    
	/**
	 * Admission du patient par l'infirmier de tri et l'infirmier de service
	 */
	public function enregistrementAdmissionPatientAction() {
	
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
		$id_Infirmier = $user['id_employe'];
		
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
		
		//Insertion des donnees de l'infirmier de tri
		//Insertion des donnees de l'infirmier de tri
		//Insertion des donnees de l'infirmier de tri
		if($role == "infirmier-tri"){
			
			//Insertion de l'admission pour admettre le patient au niveau de l'infirmier de service
			//Insertion de l'admission pour admettre le patient au niveau de l'infirmier de service
			$donneesAdmission = array(
					'id_patient' => $id_patient,
					'id_infirmier_tri' => $id_Infirmier,
					'heure_infirmier_tri' =>  $heure,
					'date' => $date,
					'niveau' => $niveau,
			);
			$id_admission = $this->getAdmissionTable()->addAdmission($donneesAdmission);
			
			//Insertion des donnees sur le mode d'entr�e et le mode de transport
			//Insertion des donnees sur le mode d'entr�e et le mode de transport
			$mode_entree_et_mode_transport['id_admission'] = $id_admission;
			$this->getAdmissionTable()->addModeEntreeModeTransport($mode_entree_et_mode_transport);
			
			
			//Insertion des motifs de l'admission s'il y'en a
			//Insertion des motifs de l'admission s'il y'en a
			if(!$this->array_empty($donneesMotifAdmission)){
				$this->getMotifAdmissionTable ()->addMotifAdmission ( $form , $id_admission);
			}
			
			//Insertion des constantes s'il y'en a
			//Insertion des constantes s'il y'en a
			if(!$this->array_empty($donneesConstantes) || !$this->array_empty($bandelettes)){ 
				$donneesConstantes['ID_CONS']    = $id_cons;
				$donneesConstantes['ID_PATIENT'] = (int)$id_patient;
				$donneesConstantes['DATEONLY']   = $form->get ( "dateonly" )->getValue ();
				$donneesConstantes['HEURECONS']  = $form->get ( "heure_cons" )->getValue ();
				
				$this->getConsultationTable ()->addConsultation ($donneesConstantes); 
				$this->getConsultationTable ()->addConsultationUrgence($id_cons, $id_admission, $id_Infirmier);
				
				//mettre � jour les bandelettes urinaires
				$bandelettes['id_cons'] = $id_cons;
				$this->getConsultationTable ()->deleteBandelette($id_cons);
				$this->getConsultationTable ()->addBandelette($bandelettes);
			}
	
		}
	
		if($role == "infirmier-service" || $role == "medecin"){
			$tabDonnees = $this->params ()->fromPost();
			//Les actes
			$tabActesDemandes = explode(',', $tabDonnees['tabActesDemandes']);
			$tabNotesActes = explode(':,,;', $tabDonnees['tabNotesActes']);
			//Les examens compl�mentaires
			$tabTypesExamensDemandes = explode(',', $tabDonnees['tabTypeExamenDemandesEC']);
			$tabExamensDemandes = explode(',', $tabDonnees['tabExamenDemandesEC']);
			
			$id_admission = $this->params ()->fromPost( "id_admission" );
			//Si c'est un patient d�j� admis par l'infirmier de tri
			//Si c'est un patient d�j� admis par l'infirmier de tri
			if($id_admission){
				
				//Validation de l'admission par l'infirmier de service
				//Validation de l'admission par l'infirmier de service
				$donneesAdmission = array(
						'id_infirmier_service' => $id_Infirmier,
						'heure_infirmier_service' =>  $heure,
						'niveau' => $niveau,
						'salle'  => trim($form->get ( "salle" )->getValue ()),
						'lit'    => trim($form->get ( "lit" )->getValue ()),
						'couloir' => trim($form->get ( "couloir" )->getValue ()),
				);
				
				$this->getAdmissionTable()->updateAdmission($donneesAdmission, $id_admission);
				
				
				//Insertion des donnees sur le mode d'entr�e et le mode de transport
				//Insertion des donnees sur le mode d'entr�e et le mode de transport
				$this->getAdmissionTable()->updateModeEntreeModeTransport($mode_entree_et_mode_transport, $id_admission);
				
				
				//Insertion des motifs de l'admission s'il y'en a
				//Insertion des motifs de l'admission s'il y'en a
				$this->getMotifAdmissionTable ()->deleteMotifAdmission($id_admission);
				if(!$this->array_empty($donneesMotifAdmission)){
					$this->getMotifAdmissionTable ()->addMotifAdmission ( $form , $id_admission);
				}
					
				//Insertion des constantes s'il y'en a (est pass� par l'infirmier de tri)
				//Insertion des constantes s'il y'en a (est pass� par l'infirmier de tri)
				$consultation_urgence = $this->getConsultationTable ()->getConsultationUrgence($id_admission);
				if($consultation_urgence){
					$this->getConsultationTable ()->updateConsultationUrgence($donneesConstantes, $consultation_urgence['id_cons']);
					$this->getConsultationTable ()->miseajourConsultationUrgence($id_Infirmier, $consultation_urgence['id_cons']);
					
					//mettre � jour les bandelettes urinaires
					$bandelettes['id_cons'] = $consultation_urgence['id_cons'];
					$this->getConsultationTable ()->deleteBandelette($consultation_urgence['id_cons']);
					$this->getConsultationTable ()->addBandelette($bandelettes);
				}else{
					//Insertion des constantes s'il y'en a
					//Insertion des constantes s'il y'en a
					if(!$this->array_empty($donneesConstantes) || !$this->array_empty($bandelettes)){
						$donneesConstantes['ID_CONS']    = $id_cons;
						$donneesConstantes['ID_PATIENT'] = (int)$id_patient;
						$donneesConstantes['DATEONLY']   = $form->get ( "dateonly" )->getValue ();
						$donneesConstantes['HEURECONS']  = $form->get ( "heure_cons" )->getValue ();
							
						$this->getConsultationTable ()->addConsultation ($donneesConstantes);
						$this->getConsultationTable ()->addConsultationUrgenceInfirmierService ($id_cons, $id_admission, $id_Infirmier);
					
						//mettre � jour les bandelettes urinaires
						$bandelettes['id_cons'] = $id_cons;
						$this->getConsultationTable ()->deleteBandelette($id_cons);
						$this->getConsultationTable ()->addBandelette($bandelettes);
					}
				}
				
			}else{

				$donneesAdmission = array(
						'id_patient' => $id_patient,
						'id_infirmier_service' => $id_Infirmier,
						'heure_infirmier_service' =>  $heure,
						'date' => $date,
						'niveau' => $niveau,
						'salle'  => trim($form->get ( "salle" )->getValue ()),
						'lit'    => trim($form->get ( "lit" )->getValue ()),
						'couloir'    => trim($form->get ( "couloir" )->getValue ()),
				);
				
				$id_admission = $this->getAdmissionTable()->addAdmission($donneesAdmission);
					
				//Insertion des donnees sur le mode d'entr�e et le mode de transport
				//Insertion des donnees sur le mode d'entr�e et le mode de transport
				$mode_entree_et_mode_transport['id_admission'] = $id_admission;
				$this->getAdmissionTable()->addModeEntreeModeTransport($mode_entree_et_mode_transport);
					
				//Insertion des motifs de l'admission s'il y'en a
				//Insertion des motifs de l'admission s'il y'en a
				if(!$this->array_empty($donneesMotifAdmission)){
					$this->getMotifAdmissionTable ()->addMotifAdmission ( $form , $id_admission);
				}
					
				//Insertion des constantes s'il y'en a
				//Insertion des constantes s'il y'en a
				if(!$this->array_empty($donneesConstantes) || !$this->array_empty($bandelettes)){
					$donneesConstantes['ID_CONS']    = $id_cons;
					$donneesConstantes['ID_PATIENT'] = (int)$id_patient;
					$donneesConstantes['DATEONLY']   = $form->get ( "dateonly" )->getValue ();
					$donneesConstantes['HEURECONS']  = $form->get ( "heure_cons" )->getValue ();
				
					$this->getConsultationTable ()->addConsultation ($donneesConstantes);
					$this->getConsultationTable ()->addConsultationUrgenceInfirmierService($id_cons, $id_admission, $id_Infirmier);
				
					//mettre � jour les bandelettes urinaires
					$bandelettes['id_cons'] = $id_cons;
					$this->getConsultationTable ()->deleteBandelette($id_cons);
					$this->getConsultationTable ()->addBandelette($bandelettes);
				}
				
				//Insertion des demandes d'actes et examens compl�mentaires
				//Insertion des demandes d'actes et examens compl�mentaires
				$this->getMotifAdmissionTable ()->addDemandesActes($id_admission, $tabActesDemandes, $tabNotesActes, $id_Infirmier);
				$this->getMotifAdmissionTable ()->addDemandesExamensComplementaire($id_admission, $id_Infirmier, $tabTypesExamensDemandes, $tabExamensDemandes);
			}

		}
	
		return $this->redirect ()->toRoute ('urgence', array ('action' => 'liste-patients-admis' ));
	
	}
	
	public function listePatientsAdmisAjaxAction() {
		$output = $this->getPatientTable ()->getListePatientAdmis ();
		return $this->getResponse ()->setContent ( Json::encode ( $output, array (
				'enableJsonExprFinder' => true
		) ) );
	}
	
	public function listePatientsAdmisAction() {
		$this->layout ()->setTemplate ( 'layout/urgence' );
		
		//INSTANCIATION DU FORMULAIRE D'ADMISSION
		$formAdmission = new AdmissionForm ();
		
		$listeModeTransport = $this->getPatientTable ()->listeModeTransport();
		$formAdmission->get ( 'mode_transport' )->setValueOptions ($listeModeTransport);
		
		$listeSalles = $this->getPatientTable ()->listeSalles();
		$formAdmission->get ( 'salle' )->setValueOptions ($listeSalles);
		
		/*
		$listeMotifs = $this->getPatientTable ()->listeMotifsAdmission();
		$formAdmission->get ( 'motif_admission1' )->setValueOptions ($listeMotifs);
		$formAdmission->get ( 'motif_admission2' )->setValueOptions ($listeMotifs);
		$formAdmission->get ( 'motif_admission3' )->setValueOptions ($listeMotifs);
		$formAdmission->get ( 'motif_admission4' )->setValueOptions ($listeMotifs);
		$formAdmission->get ( 'motif_admission5' )->setValueOptions ($listeMotifs);
		*/
		
		
		
		//$fusion_tab = array_unique(array_merge($listeActesExamensComp, $listeExamensComp));
		//rsort($fusion_tab);
		//var_dump($fusion_tab); exit();

		
		/***
		 * TEST DE LA NOUVELLE METHODE POUR L'AFFICHAGE RAPIDE DES LISTES
		* TEST DE LA NOUVELLE METHODE POUR L'AFFICHAGE RAPIDE DES LISTES
		*/
		
		//$timestart = microtime(true);
		//$output = $this->getPatientTable ()->getListePatient ();
		//var_dump($output); exit();
		//UTILISER LA TABLE 'ListePatientsAdmisTable()' 
		/*
		$output = $this->getPersonneListeTable()->fetchAll()->toArray();
		$listeAjax = array(
				'iTotalDisplayRecords' => count($output),
				'aaData' => $output
		);
		
		var_dump($listeAjax); exit();
		*/
		//$timeend = microtime(true);
		//$time = $timeend-$timestart;
		
		//var_dump(number_format($time,3)); exit();
		
		/***
		 * ==============================================================
		* ==============================================================
		*/
		
		
		return array (
				'form' => $formAdmission
		);
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

			if($role == "infirmier-service" || $role == "medecin"){
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
		
		
		//R�cup�ration des motifs des consultations
		//R�cup�ration des motifs des consultations
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
		
		//R�cup�ration des constantes
		//R�cup�ration des constantes 
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

        if($role == "infirmier-service"|| $role == "medecin"){
        	$html .="<script> setTimeout(function(){ $('#bouton_motif_modifier, #bouton_constantes_modifier').trigger('click'); }, 500); </script>";
        }
        

        /**
         * Liste des actes et des examens compl�mentaires
         */
        //Liste des actes
        $listeActes = $this->getAdmissionTable()->getListeActes();
        
        //Liste des actes pour l'admission
        $listeActesDemandes = $this->getMotifAdmissionTable ()->getDemandesActes($id_admission);
        $nbListeActesDemandes = $listeActesDemandes->count();
        $scriptDonnees = "";
        $cmpti = 1;
        foreach ($listeActesDemandes as $listeActesDem){
        	$scriptDonnees .="<script> $('#type_analyse_name_".$cmpti."').val('".$listeActesDem['id_acte']."'); </script>";
        	$scriptDonnees .="<script> $('#analyse_name_".$cmpti."').val('".str_replace("'", "\'",$listeActesDem['note'])."'); </script>";
        	$cmpti++;
        }
        
        //Liste des examens compl�mentaires
        $listeExamenComp = $this->getAdmissionTable()->getListeExamenComp();
        
        $listeExamensDemandes = $this->getMotifAdmissionTable ()->getDemandesExamenComplementaire($id_admission);
        $nbListeExamensDemandes = $listeExamensDemandes->count();
        $scriptDonneesEC = "";
        $cmptiec = 1;
        foreach ($listeExamensDemandes as $listeExamenCompDem){
        	$scriptDonneesEC .="<script> $('#type_analyse_name_ec_".$cmptiec."').val('".$listeExamenCompDem['type']."'); </script>";
        	$scriptDonneesEC .="<script> $('#analyse_name_ec_".$cmptiec."').html('".$this->getListeExamensComplementairesParType($listeExamenCompDem['type'])."'); </script>";
        	$scriptDonneesEC .="<script> $('#analyse_name_ec_".$cmptiec."').val('".$listeExamenCompDem['id_examen']."'); </script>";
        	$cmptiec++;
        }
        
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ( array($html, $listeActes, $nbListeActesDemandes, $scriptDonnees, $listeExamenComp, $nbListeExamensDemandes, $scriptDonneesEC) ) );
	}
	
	public function getListeExamensComplementairesParType($id)
	{
		$liste_select = "";
	
		foreach($this->getMotifAdmissionTable ()->getLiteExamensComplementairesParType($id) as $listeExamensCompl){
			$liste_select.= "<option value=".$listeExamensCompl['id'].">".$listeExamensCompl['libelle']."</option>";
		}
	
		return $liste_select;
	}
	
	public function enregistrementModificationAdmissionAction() {

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
		$id_Infirmier = $user['id_employe'];
		
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
		
		//Insertion des donnees de l'infirmier de tri
		//Insertion des donnees de l'infirmier de tri
		//Insertion des donnees de l'infirmier de tri
		if($role == "infirmier-tri"){
				
			//Insertion de l'admission pour admettre le patient au niveau de l'infirmier de service
			//Insertion de l'admission pour admettre le patient au niveau de l'infirmier de service
			$donneesAdmission = array(
					'id_infirmier_tri' => $id_Infirmier,
					'niveau' => $niveau,
			);
			$this->getAdmissionTable()->updateAdmission($donneesAdmission, $id_admission);
				
			//Insertion des donnees sur le mode d'entr�e et le mode de transport
			//Insertion des donnees sur le mode d'entr�e et le mode de transport
			$this->getAdmissionTable()->updateModeEntreeModeTransport($mode_entree_et_mode_transport, $id_admission);
			
			
			//Insertion des motifs de l'admission s'il y'en a
			//Insertion des motifs de l'admission s'il y'en a
			$this->getMotifAdmissionTable ()->deleteMotifAdmission($id_admission);
			if(!$this->array_empty($donneesMotifAdmission)){
				$this->getMotifAdmissionTable ()->addMotifAdmission ( $form , $id_admission);
			}
				
			//Insertion des constantes s'il y'en a
			//Insertion des constantes s'il y'en a
			$consultation_urgence = $this->getConsultationTable ()->getConsultationUrgence($id_admission);
			if($consultation_urgence){
				$this->getConsultationTable ()->updateConsultationUrgence($donneesConstantes, $consultation_urgence['id_cons']);
				
				//mettre � jour les bandelettes urinaires
				$bandelettes['id_cons'] = $consultation_urgence['id_cons'];
				$this->getConsultationTable ()->deleteBandelette($consultation_urgence['id_cons']);
				$this->getConsultationTable ()->addBandelette($bandelettes);
			}else{
				//Insertion des constantes s'il y'en a
				//Insertion des constantes s'il y'en a
				if(!$this->array_empty($donneesConstantes) || !$this->array_empty($bandelettes)){
					$donneesConstantes['ID_CONS']    = $id_cons;
					$donneesConstantes['ID_PATIENT'] = (int)$id_patient;
					$donneesConstantes['DATEONLY']   = $form->get ( "dateonly" )->getValue ();
					$donneesConstantes['HEURECONS']  = $form->get ( "heure_cons" )->getValue ();
				
					$this->getConsultationTable ()->addConsultation ($donneesConstantes);
					$this->getConsultationTable ()->addConsultationUrgence ($id_cons, $id_admission, $id_Infirmier);
					
					//mettre � jour les bandelettes urinaires
					$bandelettes['id_cons'] = $id_cons;
					$this->getConsultationTable ()->deleteBandelette($id_cons);
					$this->getConsultationTable ()->addBandelette($bandelettes);
				}
				
			}
			
		}
		
		if($role == "infirmier-service" || $role == "medecin"){
			$id_admission = $this->params ()->fromPost( "id_admission" );
			
			$tabDonnees = $this->params ()->fromPost();

			//Insertion des demandes d'actes
			//Insertion des demandes d'actes
			$tabActesDemandes = explode(',', $tabDonnees['tabActesDemandes']);
			$tabNotesActes = explode(':,,;', $tabDonnees['tabNotesActes']);
			
			$this->getMotifAdmissionTable ()->deleteDemandesActes($id_admission);
			$this->getMotifAdmissionTable ()->addDemandesActes($id_admission, $tabActesDemandes, $tabNotesActes, $id_Infirmier);
			
			//Insertion des demandes d'examens compl�mentaires
			//Insertion des demandes d'examens compl�mentaires
			$tabTypesExamensDemandes = explode(',', $tabDonnees['tabTypeExamenDemandesEC']);
			$tabExamensDemandes = explode(',', $tabDonnees['tabExamenDemandesEC']);
				
			$this->getMotifAdmissionTable ()->deleteDemandesExamenComplementaire($id_admission);
			$this->getMotifAdmissionTable ()->addDemandesExamensComplementaire($id_admission, $id_Infirmier, $tabTypesExamensDemandes, $tabExamensDemandes);

			
			if($id_admission){
			
				//Validation de l'admission par l'infirmier de service
				//Validation de l'admission par l'infirmier de service
				$donneesAdmission = array(
						'id_infirmier_service' => $id_Infirmier,
						'niveau' => $niveau,
						'salle'  => trim($form->get ( "salle" )->getValue ()),
						'lit'    => trim($form->get ( "lit" )->getValue ()),
						'couloir' => trim($form->get ( "couloir" )->getValue ()),
				);
				$this->getAdmissionTable()->updateAdmission($donneesAdmission, $id_admission);
			
				//Insertion des donnees sur le mode d'entr�e et le mode de transport
				//Insertion des donnees sur le mode d'entr�e et le mode de transport
				$this->getAdmissionTable()->updateModeEntreeModeTransport($mode_entree_et_mode_transport, $id_admission);
					
				//Insertion des motifs de l'admission s'il y'en a
				//Insertion des motifs de l'admission s'il y'en a
				$this->getMotifAdmissionTable ()->deleteMotifAdmission($id_admission);
				if(!$this->array_empty($donneesMotifAdmission)){
					$this->getMotifAdmissionTable ()->addMotifAdmission ( $form , $id_admission);
				}
					
				//Insertion des constantes s'il y'en a (est pass� par l'infirmier de tri)
				//Insertion des constantes s'il y'en a (est pass� par l'infirmier de tri)
				$consultation_urgence = $this->getConsultationTable ()->getConsultationUrgence($id_admission);
				if($consultation_urgence){
					$this->getConsultationTable ()->updateConsultationUrgence($donneesConstantes, $consultation_urgence['id_cons']);
					$this->getConsultationTable ()->miseajourConsultationUrgence($id_Infirmier, $consultation_urgence['id_cons']);
					
					//mettre � jour les bandelettes urinaires
					$bandelettes['id_cons'] = $consultation_urgence['id_cons'];
					$this->getConsultationTable ()->deleteBandelette($consultation_urgence['id_cons']);
					$this->getConsultationTable ()->addBandelette($bandelettes);
				}else{
					//Insertion des constantes s'il y'en a
					//Insertion des constantes s'il y'en a
					if(!$this->array_empty($donneesConstantes) ||  !$this->array_empty($bandelettes)){
						$donneesConstantes['ID_CONS']    = $id_cons;
						$donneesConstantes['ID_PATIENT'] = (int)$id_patient;
						$donneesConstantes['DATEONLY']   = $form->get ( "dateonly" )->getValue ();
						$donneesConstantes['HEURECONS']  = $form->get ( "heure_cons" )->getValue ();
							
						$this->getConsultationTable ()->addConsultation ($donneesConstantes);
						$this->getConsultationTable ()->addConsultationUrgenceInfirmierService ($id_cons, $id_admission, $id_Infirmier);
						
						//mettre � jour les bandelettes urinaires
						$bandelettes['id_cons'] = $id_cons;
						$this->getConsultationTable ()->deleteBandelette($id_cons);
						$this->getConsultationTable ()->addBandelette($bandelettes);
					}
				}
			
			}
		
		}
		
		return $this->redirect ()->toRoute ('urgence', array ('action' => 'liste-patients-admis' ));
		
	}
	
	public function getListeExamensComplementairesAction()
	{
		$id = (int)$this->params()->fromPost ('id');
		$liste_select = "";
		
		foreach($this->getMotifAdmissionTable ()->getLiteExamensComplementairesParType($id) as $listeExamensCompl){
			$liste_select.= "<option value=".$listeExamensCompl['id'].">".$listeExamensCompl['libelle']."</option>";
		}
	
		$this->getResponse()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html' );
		return $this->getResponse ()->setContent(Json::encode ( $liste_select));
	}
	
	public function suppressionAdmissionParInfirmiertriAction(){
		
		$id_patient = ( int ) $this->params ()->fromPost ( 'id_patient', 0 );
		$id_admission = ( int ) $this->params ()->fromPost ( 'id_admission', 0 );
		$reponse = 1;
		$admission = $this->getAdmissionTable()->getAdmissionParInfirmierTri($id_admission);
		if($admission){
			$consultation_urgence = $this->getConsultationTable ()->getConsultationUrgence($id_admission);
			$this->getConsultationTable()->deleteConsultationUrgence($consultation_urgence['id_cons']);
			$this->getAdmissionTable()->deleteAdmission($id_admission);
		}else{
			$reponse = 0;
		}
		
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ( $reponse ) );
	}
	
	
	//DOMAINE DE LA GESTION DES INTERFACE DE L'INFIRMIER DE SERVICE
	//DOMAINE DE LA GESTION DES INTERFACE DE L'INFIRMIER DE SERVICE
	//DOMAINE DE LA GESTION DES INTERFACE DE L'INFIRMIER DE SERVICE
	/**
	 * Afficher les infos sur l'admission d'un patient par l'infir�ier de tri 
	 */
	public function getInfosAdmissionParInfirmierTriAction() {
	
		$today = new \DateTime ();
		$dateAujourdhui = $today->format( 'Y-m-d' );
	
		$id_patient = ( int ) $this->params ()->fromPost ( 'id_patient', 0 );
		$id_admission = ( int ) $this->params ()->fromPost ( 'id_admission', 0 );
	
		
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
	
		}
			
		//Recuperation de l'admission et de l'id du patient
		//Recuperation de l'admission et de l'id du patient
		$html .="<script> $('#id_patient').val('".$id_patient."'); </script>";
		$html .="<script> $('#id_admission').val('".$id_admission."'); </script>";
	
	
		//Recuperation du mode d'entree et du mode de transport
		//Recuperation du mode d'entree et du mode de transport
		$mode_entree = $this->getAdmissionTable()->getModeEntreeModeTransport($id_admission);
		if($mode_entree['mode_entree']){
			$html .="<script> $('#mode_entree').val('".$mode_entree['mode_entree']."'); </script>";
			$html .="<script> if(".$mode_entree['mode_entree']." == 1 ){ getModeEntre(1); } </script>";
		}
		$html .="<script> $('#precision_provenance').val('".str_replace("'", "\'", $mode_entree['precision_provenance'])."'); </script>";
		$html .="<script> $('#mode_transport').val('".$mode_entree['mode_transport']."'); </script>";
		
		
		//R�cup�ration des motifs des consultations
		//R�cup�ration des motifs des consultations
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
	
		//R�cup�ration des constantes
		//R�cup�ration des constantes
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
			$html .="<script>setTimeout(function(){ /* $('#bouton_constantes_valider').trigger('click'); */ }); </script>";
		
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
	
	
		//Liste des actes et des examens compl�mentaires
		$listeActes = $this->getAdmissionTable()->getListeActes();
		$listeExamenComp = $this->getAdmissionTable()->getListeExamenComp();
		
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ( array($html, $listeActes, $listeExamenComp) ) );
	}
	
	public function listePatientsAdmisInfirmierServiceAjaxAction() {
		$output = $this->getPatientTable ()->getListePatientAdmisInfirmierService ();
		return $this->getResponse ()->setContent ( Json::encode ( $output, array (
				'enableJsonExprFinder' => true
		) ) );
	}
	
	public function listePathologiesAction()
	{
		$script  = "<script>";
		$script .="var arrayPathologies = new Array();";
		$listePathologies = $this->getPathologieTable()->getListePathologie();
		for($i = 0 ; $i <  count($listePathologies); $i++){
			$script .="arrayPathologies[".$i."] = '".str_replace("'", "\'", $listePathologies[$i]['libelle_pathologie'])."';";
		}
		$script .="</script>";
	
		$this->getResponse()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html' );
		return $this->getResponse ()->setContent(Json::encode ( $script ));
	}
	
	public function listePathologiesSelectOptionAction()
	{
		$html  = "<select onchange='appelScriptAuto();' ><option value=''> *** Choisir un motif *** </option>";
		$listePathologies = $this->getPathologieTable()->getListePathologie();
		for($i = 0 ; $i <  count($listePathologies); $i++){
			$html .='<option value="'.$listePathologies[$i]['id'].",".$listePathologies[$i]['libelle_pathologie'].'" >'.$listePathologies[$i]['libelle_pathologie'].'</option>';
		}
		$html .="</select>";
	
		$this->getResponse()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html' );
		return $this->getResponse ()->setContent(Json::encode ( $html ));
	}
	
	
	
	public function listeMotifsAdmissionAction()
	{
		$script  = "<script>";
		$script .="var arrayMotifAdmission = new Array();";
		$listeMotifsAdmission = $this->getMotifAdmissionTable()->getListeMotifsAdmissionUrgence();
		for($i = 0 ; $i <  count($listeMotifsAdmission); $i++){
			$script .="arrayMotifAdmission[".$i."] = '".str_replace("'", "\'", $listeMotifsAdmission[$i]['libelle_motif'])."';";
		}
		$script .="</script>";
		
		$this->getResponse()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html' );
		return $this->getResponse ()->setContent(Json::encode ( $script ));
	}
	
	
	public function listeMotifsAdmissionMultiSelectAction()
	{
		$html  = "<select id='listeMotifsAdmission' class='listeMotifsAdmissionMultiSelect' multiple='multiple'>";
		$listeMotifsAdmission = $this->getMotifAdmissionTable()->getListeMotifsAdmissionUrgence();
		for($i = 0 ; $i <  count($listeMotifsAdmission); $i++){
			$html .="<option>".$listeMotifsAdmission[$i]['libelle_motif']."</option>";
		}
		$html .="</select>";
	
		$this->getResponse()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html' );
		return $this->getResponse ()->setContent(Json::encode ( $html ));
	}
	
	
	public function modificationListeMotifsAdmissionSelectAction()
	{
		$tabMotisSelectionnes = $this->params ()->fromPost ( 'tabMotisSelectionnes', 0 );
		$valeurDeCorrection = $this->params ()->fromPost ( 'valeurDeCorrection', 0 );

		$laPathologie = $this->getPathologieTable()->getLaPathologie($valeurDeCorrection);
		
		$this->getMotifAdmissionTable()->modificationListeMotifsAdmissionUrgence($tabMotisSelectionnes, $laPathologie);
	
		$this->getResponse()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html' );
		return $this->getResponse ()->setContent(Json::encode ());
	}
	
	public function listeTypesPathologiesAction()
	{
		$html  = "";
		$listeTypesPathologies = $this->getTypePathologieTable()->getListeTypePathologieOrdreDecroissant();
		for($i = 0 ; $i <  count($listeTypesPathologies); $i++){
			if($i == 0){
				$html .="<tr><td class='LTPE1  iconeIndicateurChoix_".$listeTypesPathologies[$i]['id']."'><a href='javascript:afficherListePathologieDuType(".$listeTypesPathologies[$i]['id'].");'><img src='../images_icons/greenarrowright.png'></a></td> <td class='LTPE2  LTPE2_".$listeTypesPathologies[$i]['id']."' ><span>".str_replace("'", "'", $listeTypesPathologies[$i]['libelle_type_pathologie'])."</span><img onclick='modifierInfosTypePathologie(".$listeTypesPathologies[$i]['id'].");' class='imgLTPE2' src='../img/light/pencil.png'> </td></tr>";
			}else{
				$html .="<tr><td class='LTPE1  iconeIndicateurChoix_".$listeTypesPathologies[$i]['id']."'><a href='javascript:afficherListePathologieDuType(".$listeTypesPathologies[$i]['id'].");'><img src='../img/light/triangle_right.png'></a></td> <td class='LTPE2  LTPE2_".$listeTypesPathologies[$i]['id']."' ><span>".str_replace("'", "'", $listeTypesPathologies[$i]['libelle_type_pathologie'])."</span><img onclick='modifierInfosTypePathologie(".$listeTypesPathologies[$i]['id'].");' class='imgLTPE2' src='../img/light/pencil.png'> </td></tr>";
			}

		}
		
		$this->getResponse()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html' );
		return $this->getResponse ()->setContent(Json::encode ( $html ));
	}
	
	/**
	 * Liste des pathologies pour un type donn�
	 */
	public function listePathologiesPourInterfaceAjoutAction()
	{
		$idTypePathologie = ( int ) $this->params ()->fromPost ( 'id', 0 );
		
		if($idTypePathologie == 0){
			$lastTypePathologie = $this->getTypePathologieTable()->getListeTypePathologieOrdreDecroissant();
			$idTypePathologie = $lastTypePathologie[0]['id'];
		}
		
		$listePathologies = $this->getPathologieTable()->getListePathologieOrdreDecroissant($idTypePathologie);

		$html  = "";
		for($i = 0 ; $i <  count($listePathologies); $i++){
			$html .="<tr><!-- td class='LPE1  iconeIndPathologieChoix_".$listePathologies[$i]['id']."'> </td--> <td class='LPE2'>".str_replace("'", "'", $listePathologies[$i]['libelle_pathologie'])."</td></tr>";
		}
	
		$this->getResponse()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html' );
		return $this->getResponse ()->setContent(Json::encode ( $html ));
	}
	
	
	public function listeTypesPathologiesSelectAction()
	{
		$html  = "";
		$listeTypesPathologies = $this->getTypePathologieTable()->getListeTypePathologieOrdreDecroissant();
		for($i = 0 ; $i <  count($listeTypesPathologies); $i++){
			$html .="<option  value='".$listeTypesPathologies[$i]['id']."'>".str_replace("'", "'", $listeTypesPathologies[$i]['libelle_type_pathologie'])."</option>";
		}
		
		$this->getResponse()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html' );
		return $this->getResponse ()->setContent(Json::encode ( $html ));
	}
	
	
	public function enregistrementTypePathologieAction()
	{
		$user = $this->layout()->user;
		$id_employe = $user['id_personne'];
		$tabTypePathologie = $this->params ()->fromPost ( 'tabTypePathologie' );

		$this->getTypePathologieTable()->insertTypePathologie($tabTypePathologie, $id_employe);
	
		$this->getResponse()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html' );
		return $this->getResponse ()->setContent(Json::encode ( ));
	}
	
	public function modifierTypePathologieAction()
	{
		$user = $this->layout()->user;
		$id_employe = $user['id_personne'];
		$idType = $this->params ()->fromPost ( 'idType' );
		$libelleTypePathologie = $this->params ()->fromPost ( 'libelleTypePathologie' );
	
		$this->getTypePathologieTable()->updateTypePathologie($idType, $libelleTypePathologie, $id_employe);
	
		$this->getResponse()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html' );
		return $this->getResponse ()->setContent(Json::encode ( $libelleTypePathologie ));
	}
	
	public function enregistrementPathologieAction()
	{
		$user = $this->layout()->user;
		$id_employe = $user['id_personne'];
		$tabTypePathologie = $this->params ()->fromPost ( 'tabTypePathologie' );
		$tabPathologie = $this->params ()->fromPost ( 'tabPathologie' );

		$this->getPathologieTable()->insertPathologie($tabTypePathologie, $tabPathologie, $id_employe);
	
		$this->getResponse()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html' );
		return $this->getResponse ()->setContent(Json::encode ( 1 ));
	}
	
	
	
	public function infosStatistiquesParDefautAction()
	{
		$listeDesMotifsAdmission = $this->getMotifAdmissionTable()->getListeDesMotifsAdmissionUrgence();
		
		$touteTypePathologie = array();
		$diffTypePathologie = array();
		$diffLibelleTypePathologie = array();
		
		$toutePathologie = array();
		$diffPathologie = array();
		$diffPathologieVerifType = array();
		
		for($i=0 ; $i < count($listeDesMotifsAdmission) ; $i++){
			
			$typePathologie = $listeDesMotifsAdmission[$i]['type_pathologie'];
			$libelleTypePathologie = $listeDesMotifsAdmission[$i]['libelle_type_pathologie'];
				
			$touteTypePathologie[] = $typePathologie;
			if(!in_array($typePathologie, $diffTypePathologie)){
				$diffTypePathologie[] = $typePathologie;
				$diffLibelleTypePathologie[] = $libelleTypePathologie;			
			}
					
			$pathologie = $listeDesMotifsAdmission[$i]['libelle_motif'];
			$toutePathologie[] = $pathologie;

			if(!in_array($pathologie, $diffPathologie)){
				$diffPathologie[] = $pathologie;
				$diffPathologieVerifType[] = array($pathologie, $typePathologie);
			}
		
		}
		
		

		$touteTypePathologieNbVal = array_count_values($touteTypePathologie);
		$toutePathologieNbVal = array_count_values($toutePathologie);
		
		$html ='<table class="titreTableauInfosStatistiques">
				  <tr class="ligneTitreTableauInfos">
				    <td rowspan="2" style="width: 35%; height: 40px;">Types de pathologie</td>
                    <td style="width: 50%; height: 40px;">Motifs</td>
                    <td style="width: 15%; height: 40px;">Nombre</td>
                  </tr>
				</table>';
		
		$html .="<div id='listeTableauInfosStatistiques'>";
		
		for($i=0 ; $i<count($diffTypePathologie) ; $i++){
				
			$prem = 1;
			$html .="<table class='tableauInfosStatistiques'>";
				
			for($j=0 ; $j<count($diffPathologieVerifType) ; $j++){
				if($diffTypePathologie[$i] == $diffPathologieVerifType[$j][1]){
						
					if($prem == 1){
		
						$html .='<tr style="width: 100%; ">
						           <td rowspan="'.count($diffPathologieVerifType).'" style="width: 35%; height: 40px; background: re; text-align: center;">'.$diffLibelleTypePathologie[$i].' </br>(Nombre = '.$touteTypePathologieNbVal[$diffTypePathologie[$i]].')</td>
						           <td class="infosPath" style="width: 50%; height: 40px; background: yello;">'.$diffPathologieVerifType[$j][0].'</td>
						           <td class="infosPath" style="width: 15%; height: 40px; background: gree;">'.$toutePathologieNbVal[$diffPathologieVerifType[$j][0]].'</td>
						         </tr>';
						$prem++;
					}else{
						$html .='<tr style="width: 100%; ">
                                   <td class="infosPath" style="width: 50%; height: 40px; background: orang;">'.$diffPathologieVerifType[$j][0].'</td>
                                   <td class="infosPath" style="width: 15%; height: 40px; background: brow;">'.$toutePathologieNbVal[$diffPathologieVerifType[$j][0]].'</td>
                                 </tr>';
					}
						
				}
			}
				
			$html .="</table>";
		}
		
		$html .="</div>";
		
		$this->getResponse()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html' );
		return $this->getResponse ()->setContent(Json::encode ( $html ));
	}
	
	
	public function listeTypePathologieDesMotifsSelectAction()
	{
		$html  = "<select onchange='recupererTypePathologie(this.value);' ><option value=''> *** Choisir un type de pathologie *** </option>";
		$listeTypePathologies = $this->getMotifAdmissionTable()->getListeTypeDesMotifsAdmissionUrgence();
		for($i = 0 ; $i <  count($listeTypePathologies); $i++){
			$html .='<option value="'.$listeTypePathologies[$i]['type_pathologie'].'" >'.$listeTypePathologies[$i]['libelle_type_pathologie'].'</option>';
		}
		$html .="</select>";
	
		$this->getResponse()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html' );
		return $this->getResponse ()->setContent(Json::encode ( $html ));
	}
	
	
	public function infosStatistiquesOptionnellesAction()
	{
		$id_type_pathologie = ( int ) $this->params ()->fromPost ( 'id_type_pathologie', 0 );
		
		if($id_type_pathologie != 0){
			$listeDesMotifsAdmission = $this->getMotifAdmissionTable()->getListeDesMotifsAdmissionUrgencePourUnType($id_type_pathologie);
		}else{
			$listeDesMotifsAdmission = $this->getMotifAdmissionTable()->getListeDesMotifsAdmissionUrgence();
		}
	
		$touteTypePathologie = array();
		$diffTypePathologie = array();
		$diffLibelleTypePathologie = array();
	
		$toutePathologie = array();
		$diffPathologie = array();
		$diffPathologieVerifType = array();
	
		for($i=0 ; $i < count($listeDesMotifsAdmission) ; $i++){
				
			$typePathologie = $listeDesMotifsAdmission[$i]['type_pathologie'];
			$libelleTypePathologie = $listeDesMotifsAdmission[$i]['libelle_type_pathologie'];
	
			$touteTypePathologie[] = $typePathologie;
			if(!in_array($typePathologie, $diffTypePathologie)){
				$diffTypePathologie[] = $typePathologie;
				$diffLibelleTypePathologie[] = $libelleTypePathologie;
			}
				
			$pathologie = $listeDesMotifsAdmission[$i]['libelle_motif'];
			$toutePathologie[] = $pathologie;
	
			if(!in_array($pathologie, $diffPathologie)){
				$diffPathologie[] = $pathologie;
				$diffPathologieVerifType[] = array($pathologie, $typePathologie);
			}
	
		}
	
	
	
		$touteTypePathologieNbVal = array_count_values($touteTypePathologie);
		$toutePathologieNbVal = array_count_values($toutePathologie);
	
		$html ='<table class="titreTableauInfosStatistiques">
				  <tr class="ligneTitreTableauInfos">
				    <td rowspan="2" style="width: 35%; height: 40px;">Types de pathologie</td>
                    <td style="width: 50%; height: 40px;">Motifs</td>
                    <td style="width: 15%; height: 40px;">Nombre</td>
                  </tr>
				</table>';
	
		$html .="<div id='listeTableauInfosStatistiques'>";
		
		for($i=0 ; $i<count($diffTypePathologie) ; $i++){
	
			$prem = 1;
			$html .="<table class='tableauInfosStatistiques'>";
	
			for($j=0 ; $j<count($diffPathologieVerifType) ; $j++){
				if($diffTypePathologie[$i] == $diffPathologieVerifType[$j][1]){
	
					if($prem == 1){
	
						$html .='<tr style="width: 100%; ">
						           <td rowspan="'.count($diffPathologieVerifType).'" style="width: 35%; height: 40px; background: re; text-align: center;">'.$diffLibelleTypePathologie[$i].' </br>(Nombre = '.$touteTypePathologieNbVal[$diffTypePathologie[$i]].')</td>
						           <td class="infosPath" style="width: 50%; height: 40px; background: yello;">'.$diffPathologieVerifType[$j][0].'</td>
						           <td class="infosPath" style="width: 15%; height: 40px; background: gree;">'.$toutePathologieNbVal[$diffPathologieVerifType[$j][0]].'</td>
						         </tr>';
						$prem++;
					}else{
						$html .='<tr style="width: 100%; ">
                                   <td class="infosPath" style="width: 50%; height: 40px; background: orang;">'.$diffPathologieVerifType[$j][0].'</td>
                                   <td class="infosPath" style="width: 15%; height: 40px; background: brow;">'.$toutePathologieNbVal[$diffPathologieVerifType[$j][0]].'</td>
                                 </tr>';
					}
	
				}
			}
	
			$html .="</table>";
		}
	
		$html .="</div>";
	
		$this->getResponse()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html' );
		return $this->getResponse ()->setContent(Json::encode ( $html ));
	}
	
	
	public function infosStatistiquesOptionnellesPeriodeAction()
	{
		$id_type_pathologie = ( int ) $this->params ()->fromPost ( 'id_type_pathologie', 0 );
		$date_debut = $this->params ()->fromPost ( 'date_debut', 0 );
		$date_fin = $this->params ()->fromPost ( 'date_fin', 0 );
		
		if($id_type_pathologie != 0){
			$listeDesMotifsAdmission = $this->getMotifAdmissionTable()->getListeDesMotifsAdmissionUrgencePourUnTypeEtUnePeriode($id_type_pathologie, $date_debut, $date_fin);
		}else{
			$listeDesMotifsAdmission = $this->getMotifAdmissionTable()->getListeDesMotifsAdmissionUrgencePourUnePeriode($date_debut, $date_fin);
		}
	
		$touteTypePathologie = array();
		$diffTypePathologie = array();
		$diffLibelleTypePathologie = array();
	
		$toutePathologie = array();
		$diffPathologie = array();
		$diffPathologieVerifType = array();
	
		for($i=0 ; $i < count($listeDesMotifsAdmission) ; $i++){
	
			$typePathologie = $listeDesMotifsAdmission[$i]['type_pathologie'];
			$libelleTypePathologie = $listeDesMotifsAdmission[$i]['libelle_type_pathologie'];
	
			$touteTypePathologie[] = $typePathologie;
			if(!in_array($typePathologie, $diffTypePathologie)){
				$diffTypePathologie[] = $typePathologie;
				$diffLibelleTypePathologie[] = $libelleTypePathologie;
			}
	
			$pathologie = $listeDesMotifsAdmission[$i]['libelle_motif'];
			$toutePathologie[] = $pathologie;
	
			if(!in_array($pathologie, $diffPathologie)){
				$diffPathologie[] = $pathologie;
				$diffPathologieVerifType[] = array($pathologie, $typePathologie);
			}
	
		}
	
	
	
		$touteTypePathologieNbVal = array_count_values($touteTypePathologie);
		$toutePathologieNbVal = array_count_values($toutePathologie);
	
		$html ='<table class="titreTableauInfosStatistiques">
				  <tr class="ligneTitreTableauInfos">
				    <td rowspan="2" style="width: 35%; height: 40px;">Types de pathologie</td>
                    <td style="width: 50%; height: 40px;">Motifs</td>
                    <td style="width: 15%; height: 40px;">Nombre</td>
                  </tr>
				</table>';
	
		$html .="<div id='listeTableauInfosStatistiques'>";
	
		for($i=0 ; $i<count($diffTypePathologie) ; $i++){
	
			$prem = 1;
			$html .="<table class='tableauInfosStatistiques'>";
	
			for($j=0 ; $j<count($diffPathologieVerifType) ; $j++){
				if($diffTypePathologie[$i] == $diffPathologieVerifType[$j][1]){
	
					if($prem == 1){
	
						$html .='<tr style="width: 100%; ">
						           <td rowspan="'.count($diffPathologieVerifType).'" style="width: 35%; height: 40px; background: re; text-align: center;">'.$diffLibelleTypePathologie[$i].' </br>(Nombre = '.$touteTypePathologieNbVal[$diffTypePathologie[$i]].')</td>
						           <td class="infosPath" style="width: 50%; height: 40px; background: yello;">'.$diffPathologieVerifType[$j][0].'</td>
						           <td class="infosPath" style="width: 15%; height: 40px; background: gree;">'.$toutePathologieNbVal[$diffPathologieVerifType[$j][0]].'</td>
						         </tr>';
						$prem++;
					}else{
						$html .='<tr style="width: 100%; ">
                                   <td class="infosPath" style="width: 50%; height: 40px; background: orang;">'.$diffPathologieVerifType[$j][0].'</td>
                                   <td class="infosPath" style="width: 15%; height: 40px; background: brow;">'.$toutePathologieNbVal[$diffPathologieVerifType[$j][0]].'</td>
                                 </tr>';
					}
	
				}
			}
	
			$html .="</table>";
		}
	
		$html .="</div>";
	
		$this->getResponse()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html' );
		return $this->getResponse ()->setContent(Json::encode ( $html ));
	}
	
	//**************************************************************************************
	//**************************************************************************************
	//**************************************************************************************
	//**************************************************************************************
	/* ----- DOMAINE DE LA GESTION DES ACTES ET EXAMENS COMPLEMENTAIRES ------- */
	/* ----- DOMAINE DE LA GESTION DES ACTES ET EXAMENS COMPLEMENTAIRES ------- */
	//--------------------------------------------------------------------------------------
	//--------------------------------------------------------------------------------------
	//--------------------------------------------------------------------------------------
	public function listeActesExamensComplementairesAjaxAction() {
		$output = $this->getPatientTable ()->laListePatientsActesExamensAjax();
		return $this->getResponse ()->setContent ( Json::encode ( $output, array (
				'enableJsonExprFinder' => true
		) ) );
	}

    public function getInfosActesExamensPatientAction() {
    	
    	$id_patient = ( int ) $this->params ()->fromPost ( 'id_patient', 0 );
    	
    	$Control = new DateHelper ();
    	
    	$unPatient = $this->getPatientTable ()->getInfoPatient( $id_patient );
    	
    	$photo = $this->getPatientTable ()->getPhoto ( $id_patient );
    	
    	
    	$date = $unPatient['DATE_NAISSANCE'];
    	if($date){ $date = (new DateHelper())->convertDate( $unPatient['DATE_NAISSANCE'] ); }else{ $date = null;}
    	
    	$html  = "<div style='width:100%; height: 190px;'>";
    	
    	$html .= "<div style='width: 18%; height: 190px; float:left;'>";
    	$html .= "<div id='photo' style='float:left; margin-top:10px; margin-right:30px;'> <img style='width:105px; height:105px;' src='".$this->baseUrl()."public/img/photos_patients/" . $photo . "' ></div>";
    	$html .= "<div style='margin-left:20px; margin-top: 150px;'> <div style='text-decoration:none; font-size:14px; float:left; padding-right: 7px; '>Age:</div>  <div style='font-weight:bold; font-size:19px; font-family: time new romans; color: green; font-weight: bold;'>" . $unPatient['AGE'] . " ans</div></div>";
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
    	$html .= "<div style='margin-left: 5px; margin-top: 10px; margin-right:10px;'>  <div style='font-size:17px; font-family: time new romans; color: green; float:left; margin-top: 10px;'>" . $unPatient['NUMERO_DOSSIER'] . " </div></div>";
    	$html .= "</div>";
    	
    	$html .= "</div>";
    	
    	$html .= "<div id='titre_info_actes_examens'>Actes et examens compl&eacute;mentaires </div>
		          <div id='barre_actes_examens'></div>";
    	
    	//LES ACTES ET EXAMENS COMPLEMENTAIRES DU PATIENT DONNES
    	//LES ACTES ET EXAMENS COMPLEMENTAIRES DU PATIENT DONNES
    	
    	$html .="<div style='width: 100%; margin-right: 10px; margin-top: 5px; max-height: 280px; overflow: auto;'>";

    	$listeActesExamensComp = $this->getAdmissionTable()->getListeDesDatesDesActesDuPatient($id_patient);
    	$listeExamensComp = $this->getAdmissionTable()->getListeDesDatesDesExamensComplementairesDuPatient($id_patient);
    	$listeDatesDesActesDesExamens = array_unique(array_merge($listeActesExamensComp, $listeExamensComp));
    	rsort($listeDatesDesActesDesExamens);
    	
    	for($iae = 0 ; $iae < count($listeDatesDesActesDesExamens) ; $iae++){
    		$date_admission = $listeDatesDesActesDesExamens[$iae];
    		
    		$html .="<span id='labelHeureLABEL' style='padding-left: 5px;'> ".$Control->convertDate($date_admission)." </span>
    		         <p id='zoneChampInfo1' style='background:#f8faf8; padding-left: 15px; padding-right: 25px; margin-right: 35px; padding-top: 5px; font-size:19px; width: 95%;'>";
    			
    		//LISTE DES ACTES
    		$listeActesDemandes = $this->getAdmissionTable()->getListeDesActesDuPatient($id_patient, $date_admission);
    		for($iacte = 0 ; $iacte < count($listeActesDemandes) ; $iacte++){
    			if($iacte == 0){
    				$html .="&#9883; <span style='font-weight: bold; text-decoration: underline; line-height: 30px;'> Les actes :</span><br>
    			                 <span style='color: black; margin-left: 20px;'>";
    			}
    			$html .="<span style='margin-right: 20px'> <span style='font-size: 13px;'>&#10148;</span> <span style='font-size: 16px;'>".$listeActesDemandes[$iacte]."</span></span>";
    		}
    		
    		$html .="</span>";
    		
    		if(count($listeActesDemandes)){
    			$html .="<br>";
    		}
    		
    		//LISTE DES EXAMENS
    		$listeExamensDemandes = $this->getAdmissionTable()->getListeDesExamensComplementairesDuPatient($id_patient, $date_admission);
    		for($iexam = 0 ; $iexam < count($listeExamensDemandes[1]) ; $iexam++){
    				
    			if($iexam == 0){
    				$html .="&#9883; <span style='font-weight: bold;  text-decoration: underline; line-height: 30px;'> Les examens compl&eacute;mentaires :</span><br>
    			            <span style='color: black; margin-left: 20px;'>";
    			}
    				
    			$html .="<span style='margin-right: 20px'> <span style='font-size: 13px;'>&#10148;</span> <i style='font-size: 13px;'>".$listeExamensDemandes[0][$iexam]."</i> <span style='font-size: 14px; font-weight: bold;'>".$listeExamensDemandes[1][$iexam]."</span></span>";
    		}
    		
    		$html .="</span>
    			      </br>
    			     </p>";
    	}
    	
    	
    			
    	$html .="</div>";
    		
    	
    	
    	
    	$this->getResponse()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html' );
    	return $this->getResponse ()->setContent(Json::encode ( $html ));
    }
    
    public function listePatientsAdmisRegistreAjaxAction() {
    	
    	//id_patient est utiliser pour recuperer la date selectionn�e
    	$date_select = $this->params ()->fromRoute ( 'id_patient', null );
    	
    	if($date_select != null){
    		$dateHelper = new DateHelper();
    		$date_select = $dateHelper->convertChaineInDateAnglais($date_select);
    	}
    	
    	$output = $this->getPatientTable ()->laListePatientsAdmisRegistreAjax($date_select);
    	return $this->getResponse ()->setContent ( Json::encode ( $output, array (
    			'enableJsonExprFinder' => true
    	) ) );
    }
    
    public function impressionRegistrePatientsAdmisAction() {
    	
    	$user = $this->layout()->user;
    	$nomService = $user['NomService'];
    	$aujourdhui = (new \DateTime ())->format( 'd/m/Y' );
    	
    	$date_selectionnee = $this->params ()->fromPost (  'date_select' );
    	$listePatientsAdmis = $this->getPatientTable ()->getListePatientsAdmisRegistre($date_selectionnee);
    	//var_dump($listePatientsAdmis['aaData'][0][6]); exit();
    	//******************************************************
    	//******************************************************
    	
    	$pdf = new infosRegistrePatientAdmisPdf('L','mm','A4');
    	$pdf->setNomService($nomService);
    	$pdf->SetMargins(13.5,13.5,13.5);
    	$pdf->setDateAdmission($date_selectionnee);
    	$pdf->setListePatientsAdmis($listePatientsAdmis);
    	
    	$pdf->ImpressionInfosStatistiques();
    	$pdf->Output('I');
    	
    	
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
	public function infosStatistiquesParametreesAction($id_type_pathologie=null, $date_debut=null, $date_fin=null)
	{
	
		if($id_type_pathologie && $date_debut && $date_fin){
			$listeDesMotifsAdmission = $this->getMotifAdmissionTable()->getListeDesMotifsAdmissionUrgencePourUnTypeEtUnePeriode($id_type_pathologie, $date_debut, $date_fin);
		}else
		if(!$id_type_pathologie && $date_debut && $date_fin){
			$listeDesMotifsAdmission = $this->getMotifAdmissionTable()->getListeDesMotifsAdmissionUrgencePourUnePeriode($date_debut, $date_fin);
		}else
		if($id_type_pathologie && !$date_debut && !$date_fin){
			$listeDesMotifsAdmission = $this->getMotifAdmissionTable()->getListeDesMotifsAdmissionUrgencePourUnType($id_type_pathologie);
		}else{
			$listeDesMotifsAdmission = $this->getMotifAdmissionTable()->getListeDesMotifsAdmissionUrgence();
		}

		$touteTypePathologie = array();
		$diffTypePathologie = array();
		$diffLibelleTypePathologie = array();
	
		$toutePathologie = array();
		$diffPathologie = array();
		$diffPathologieVerifType = array();
	
		for($i=0 ; $i < count($listeDesMotifsAdmission) ; $i++){
	
			$typePathologie = $listeDesMotifsAdmission[$i]['type_pathologie'];
			$libelleTypePathologie = $listeDesMotifsAdmission[$i]['libelle_type_pathologie'];
	
			$touteTypePathologie[] = $typePathologie;
			if(!in_array($typePathologie, $diffTypePathologie)){
				$diffTypePathologie[] = $typePathologie;
				$diffLibelleTypePathologie[] = $libelleTypePathologie;
			}
	
			$pathologie = $listeDesMotifsAdmission[$i]['libelle_motif'];
			$toutePathologie[] = $pathologie;
	
			if(!in_array($pathologie, $diffPathologie)){
				$diffPathologie[] = $pathologie;
				$diffPathologieVerifType[] = array($pathologie, $typePathologie);
			}
	
		}
	
	
	
		$touteTypePathologieNbVal = array_count_values($touteTypePathologie);
		$toutePathologieNbVal = array_count_values($toutePathologie);
		
		
		return array($diffTypePathologie, $diffPathologieVerifType, $diffLibelleTypePathologie, $touteTypePathologieNbVal, $toutePathologieNbVal);
	}
	
    //impression des informations statistiques 
	//impression des informations statistiques
 	public function imprimerInformationsStatistiquesAction(){
	
		$user = $this->layout()->user;
		$nomService = $user['NomService'];
		$infosComp['dateConsultation'] = (new \DateTime ())->format( 'd/m/Y' );
		
		$date_debut = $this->params ()->fromPost (  'date_debut' );
		$date_fin = $this->params ()->fromPost (  'date_fin' );
		$id_type_pathologie = $this->params ()->fromPost (  'id_type_pathologie' );
		$periodeConsultation = array();
		$infosStatistique = array();
		
		//******************************************************
		//******************************************************
		if($id_type_pathologie && $date_debut && $date_fin){
			$infosStatistique = $this->infosStatistiquesParametreesAction($id_type_pathologie, $date_debut, $date_fin);
			$periodeConsultation[] = $date_debut;
			$periodeConsultation[] = $date_fin;
		}else
		if(!$id_type_pathologie && $date_debut && $date_fin){
			$infosStatistique = $this->infosStatistiquesParametreesAction(null, $date_debut, $date_fin);
			$periodeConsultation[] = $date_debut;
			$periodeConsultation[] = $date_fin;
		}else
		if($id_type_pathologie && !$date_debut && !$date_fin){
			$infosStatistique = $this->infosStatistiquesParametreesAction($id_type_pathologie);
		}else
		if(!$id_type_pathologie && !$date_debut && !$date_fin){
			$infosStatistique = $this->infosStatistiquesParametreesAction();
		}
		//******************************************************
		//******************************************************
		
		$pdf = new infosStatistiquePdf();
		$pdf->SetMargins(13.5,13.5,13.5);
		$pdf->setTabInformations($infosStatistique);
		
		$pdf->setNomService($nomService);
		$pdf->setInfosComp($infosComp);
		$pdf->setPeriodeConsultation($periodeConsultation);
		
		$pdf->ImpressionInfosStatistiques();
		$pdf->Output('I');
	
	}

}