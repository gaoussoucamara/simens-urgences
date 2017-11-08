<?php
namespace Consultation\View\Helpers;

use ZendPdf;
use ZendPdf\Page;
use ZendPdf\Font;
use Consultation\Model\Consultation;
use Urgence\View\Helper\DateHelper; 


class RpuHospitalisationPdf
{
	protected $_page;
	protected $_yPosition;
	protected $_leftMargin;
	protected $_pageWidth;
	protected $_pageHeight;
	protected $_normalFont;
	protected $_boldFont;
	protected $_newTime;
	protected $_newTimeGras;
	protected $_newTimeGrasNonItalic;
	protected $_year;
	protected $_headTitle;
	protected $_introText;
	protected $_graphData;
	protected $_patient;
	protected $_id_cons;
	protected $_date;
	protected $_note;
	protected $_idPersonne;
	protected $_Medicaments;
	protected $_DonneesPatient;
	protected $_DonneesMedecin;
	protected $_DonneesDemande;
	protected $_policeContenu;
	protected $_newPolice;
	protected $_Service;
	protected $_infoAdmission;
	protected $_nbligne;
	protected $_nbTotalLigne;
	protected $_entrerResumeSyndromique;
	protected $_entrerCOM;
	protected $_entrerHypothesesDiagnostics;
	protected $_textResumeSyndromique;
	protected $_textCOM;
	protected $_textHypothesesDiagnostics;
	protected $_textExamenComplementaire;
	protected $_textTraitement;
	protected $_textResultatExamenComplementaire;
	protected $_infosInfirmiers;
	protected $_listeSalles;
	protected $_listeLits;
	protected $_entrerExamenComplementaire;
	protected $_entrerResultatExamenComplementaire;
	protected $_entrerTraitement;
	protected $_textMutation;
	protected $_textMiseAJour1;
	protected $_textMiseAJour2;
	protected $_textMiseAJour3;
	protected $_entrerMutation;
	protected $_listeServiceMutation;
	protected $_textAvisSpecialiste;
	protected $_entrerAvisSpecialiste;
	
	protected $_tableauTemoinLimite;
	protected $_tableauTemoinInfos;
	
	
	public function __construct()
	{
		$this->_page = new Page(Page::SIZE_A4);
		
 		$this->_yPosition = 750;
 		$this->_leftMargin = 50;
 		$this->_pageHeight = $this->_page->getHeight();
 		$this->_pageWidth = $this->_page->getWidth();
 		/**
 		 * Pas encore utilisé
 		 */
 		$this->_normalFont = Font::fontWithName( ZendPdf\Font::FONT_HELVETICA);
 		$this->_boldFont = Font::fontWithName( ZendPdf\Font::FONT_HELVETICA_BOLD);
 		/**
 		 ***************** 
 		 */
 		$this->_newTime = Font::fontWithName(ZendPdf\Font::FONT_TIMES_ROMAN);
 		$this->_newTimeGras = Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD_ITALIC);
 		$this->_newTimeGrasNonItalic = Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD);
 		$this->_policeContenu = ZendPdf\Font::fontWithName(ZendPdf\Font::FONT_TIMES);
 		$this->_newPolice = ZendPdf\Font::fontWithName(ZendPdf\Font::FONT_TIMES);
 		
	}
	
	public function getPage(){
		return $this->_page;
	}
	
	public function addNoteTC(){
		$this->_page->saveGS();
		
		$this->setEnTete();
		$this->getNoteTC();
		$this->getPiedPage();
		
		$this->_page->restoreGS();
	}
	
	public function baseUrl(){
		$baseUrl = $_SERVER['SCRIPT_FILENAME'];
		$tabURI  = explode('public', $baseUrl);
		return $tabURI[0];
	}
	
	public function setEnTete(){
		$baseUrl = $_SERVER['SCRIPT_FILENAME'];
		$tabURI  = explode('public', $baseUrl);
		
		$imageHeader = ZendPdf\Image::imageWithPath($tabURI[0].'public/images_icons/hrsl.png');
		$this->_page->drawImage($imageHeader, 50, //-x
				$this->_pageHeight - 190, //-y
				155, //+x
				702); //+y
		
		$salle = "Couloir";
		if($this->_DonneesDemande['salle']){
				$salle = $this->_listeSalles[$this->_DonneesDemande['salle']];
		}
		
		$this->_page->setFont($this->_newTimeGras, 10);
		$this->_page->drawText('Salle :',
				$this->_leftMargin + 275,
				$this->_pageHeight - 65);
		$this->_page->setFont($this->_newTime, 10);
		$this->_page->drawText($salle,
				$this->_leftMargin + 310,
				$this->_pageHeight - 65);
		
		if($salle != "Couloir" && $salle != "TRAUMA"){
			$this->_page->setFont($this->_newTimeGras, 10);
			$this->_page->drawText('Lit :',
					$this->_leftMargin + 350,
					$this->_pageHeight - 65);
			$this->_page->setFont($this->_newTime, 10);
			$this->_page->drawText($this->_listeLits[$this->_DonneesDemande['lit']],
					$this->_leftMargin + 375,
					$this->_pageHeight - 65);
		}
		
		
		$this->_page->setFont($this->_newTimeGras, 10);
		$this->_page->drawText('Médecin :',
				$this->_leftMargin + 275,
				$this->_pageHeight - 80);
		$this->_page->setFont($this->_newTime, 10);
		$this->_page->drawText($this->_DonneesMedecin['prenomMedecin'].' '.$this->_DonneesMedecin['nomMedecin'],
				$this->_leftMargin + 320,
				$this->_pageHeight - 80);
		

		if($this->_infoAdmission){
			if($this->_infoAdmission->id_infirmier_service && $this->_infosInfirmiers[$this->_infoAdmission->id_infirmier_service]){
				$this->_page->setFont($this->_newTimeGras, 10);
				$this->_page->drawText('Infirmier de service :',
						$this->_leftMargin + 275,
						$this->_pageHeight - 95);
				$this->_page->setFont($this->_newTime, 10);
				$this->_page->drawText(iconv ('UTF-8' ,'ISO-8859-1' , $this->_infosInfirmiers[$this->_infoAdmission->id_infirmier_service] ),
						$this->_leftMargin + 365,
						$this->_pageHeight - 95);
			}
		}
		
		if($this->_infoAdmission){
			if($this->_infoAdmission->id_infirmier_tri){
				$this->_page->setFont($this->_newTimeGras, 10);
				$this->_page->drawText('Infirmier de tri :',
						$this->_leftMargin + 275,
						$this->_pageHeight - 110);
				$this->_page->setFont($this->_newTime, 10);
				$this->_page->drawText(iconv ('UTF-8' ,'ISO-8859-1' , $this->_infosInfirmiers[$this->_infoAdmission->id_infirmier_tri] ),
						$this->_leftMargin + 350,
						$this->_pageHeight - 110);
			}
		}
		
		
		
		$this->_page->setFont($this->_newTime, 10);
		$this->_page->drawText('République du Sénégal',
				$this->_leftMargin,
				$this->_pageHeight - 50);
		$this->_page->setFont($this->_newTime, 10);
		$this->_page->drawText('Ministère de la santé et de l\'action sociale',
				$this->_leftMargin,
				$this->_pageHeight - 65);
		$this->_page->setFont($this->_newTime, 10);
		$this->_page->drawText('C.H.R de Saint-Louis',
				$this->_leftMargin,
				$this->_pageHeight - 80);
		
		$this->_page->setFont($this->_newTimeGras, 10);
		$this->_page->drawText('Service : ',
				$this->_leftMargin,
				$this->_pageHeight - 95);
		$this->_page->setFont($this->_newTime, 10);
		$this->_page->drawText(iconv ('UTF-8' ,'ISO-8859-1' ,$this->_Service),
				$this->_leftMargin + 40,
				$this->_pageHeight - 95);
		
		$font = ZendPdf\Font::fontWithName(ZendPdf\Font::FONT_TIMES_ROMAN);
		$this->_page->setFont($font, 8);
		$this->_page->drawText('Saint-Louis, le ' . $this->_DonneesDemande['dateConsultation'],
				450,
				$this->_pageHeight - 50);
	}
	
	public function setListeSalles($listeSalles){
		$this->_listeSalles = $listeSalles;
	}
	
	public function setListeLits($listeLits){
		$this->_listeLits = $listeLits;
	}
	
	public function setListeServiceMutation($listeServiceMutation){
		$this->_listeServiceMutation = $listeServiceMutation;
	}
	
	public function getListeServiceMutation(){
		return $this->_listeServiceMutation;
	}
	
	public function setDonneesInfosInfirmiers($infosInfirmiers){
		$this->_infosInfirmiers = $infosInfirmiers;
	}
	
	public function setDonneesPatientTC($donneesPatient){
		$this->_DonneesPatient = $donneesPatient;
	}
	
	public function setDonneesMedecinTC($donneesMedecin){
		$this->_DonneesMedecin = $donneesMedecin;
	}
	
	public function setDonneesDemandeTC($donneesDemande){
		$this->_DonneesDemande = $donneesDemande;
	}

	public function setService($service){
		$this->_Service = $service;
	}
	
	public function setInfoAdmission($infoAdmission){
		$this->_infoAdmission = $infoAdmission;
	}
	
	public function getNewItalique(){
		$font = ZendPdf\Font::fontWithName(ZendPdf\Font::FONT_HELVETICA_OBLIQUE);
		$this->_page->setFont($font, 12);
	}
	
	public function getNewTime(){
		$font = ZendPdf\Font::fontWithName(ZendPdf\Font::FONT_TIMES_ROMAN);
		$this->_page->setFont($font, 12);
	}
	
	protected function nbAnnees($debut, $fin) {
		//60 secondes X 60 minutes X 24 heures dans une journee
		$nbSecondes = 60*60*24*365;
	
		$debut_ts = strtotime($debut);
		$fin_ts = strtotime($fin);
		$diff = $fin_ts - $debut_ts;
		return (int)($diff / $nbSecondes);
	}
	
	protected function gestionDuTexte($Texte){
	    $tableau = array();
	    return $tableau;
	}
	
	protected function setNbLigne($nbligne) {
	    $this->_nbligne = $nbligne;
	}
	
	public function getNbLigne(){
	    return $this->_nbligne; 
	}
	
	protected function setEntrerResumeSyndromique($entrerResumeSyndromique) {
	    $this->_entrerResumeSyndromique = $entrerResumeSyndromique;
	}
	
	public function getEntrerResumeSyndromique(){
	    return $this->_entrerResumeSyndromique;
	}
	
	protected function setEntrerCOM($entrerCOM) {
	    $this->_entrerCOM = $entrerCOM;
	}
	
	public function getEntrerCOM(){
	    return $this->_entrerCOM;
	}
	
	public function setEntrerHypothesesDiagnostics($entrerHypothesesDiagnostics) {
	    $this->_entrerHypothesesDiagnostics = $entrerHypothesesDiagnostics;
	}
	
	public function getEntrerHypothesesDiagnostics(){
	    return $this->_entrerHypothesesDiagnostics;
	}
	
	public function setTextResumeSyndromique($textResumeSyndromique) {
	    $this->_textResumeSyndromique = $textResumeSyndromique;
	}
	
	public function getTextResumeSyndromique(){
	    return $this->_textResumeSyndromique;
	}
	
	public function setTextCOM($textCOM) {
	    $this->_textCOM = $textCOM;
	}
	
	public function getTextCOM(){
	    return $this->_textCOM;
	}
	
	public function setTextHypothesesDiagnostics($TextHypothesesDiagnostics) {
	    $this->_textHypothesesDiagnostics = $TextHypothesesDiagnostics;
	}
	
	public function getTextHypothesesDiagnostics(){
	    return $this->_textHypothesesDiagnostics;
	}
	
	public function setTextExamenComplementaire($TextExamenComplementaire) {
		$this->_textExamenComplementaire = $TextExamenComplementaire;
	}
	
	public function getTextExamenComplementaire(){
		return $this->_textExamenComplementaire;
	}
	
	public function setEntrerExamenComplementaire($entrerExamenComplementaire) {
		$this->_entrerExamenComplementaire = $entrerExamenComplementaire;
	}
	
	public function getEntrerExamenComplementaire(){
		return $this->_entrerExamenComplementaire;
	}
	
	public function getTextTraitement(){
		return $this->_textTraitement;
	}
	
	public function setTextTraitement($TextTraitement) {
		$this->_textTraitement = $TextTraitement;
	}
	
	public function getEntrerTraitement(){
		return $this->_entrerTraitement;
	}
	
	public function setEntrerTraitement($EntrerTraitement) {
		$this->_entrerTraitement = $EntrerTraitement;
	}
	
	public function setTextResultatExamenComplementaire($textResultatExamenComplementaire) {
		$this->_textResultatExamenComplementaire = $textResultatExamenComplementaire;
	}
	
	public function getTextResultatExamenComplementaire(){
		return $this->_textResultatExamenComplementaire;
	}
	
	public function setEntrerResultatExamenComplementaire($entrerResultatExamenComplementaire) {
		$this->_entrerResultatExamenComplementaire = $entrerResultatExamenComplementaire;
	}
	
	public function getEntrerResultatExamenComplementaire(){
		return $this->_entrerResultatExamenComplementaire;
	}
	
	public function setTextMutation($textMutation) {
		$this->_textMutation = $textMutation;
	}
	
	public function getTextMutation(){
		return $this->_textMutation;
	}
	
	public function setTextMiseAJour1($textMiseAJour) {
		$this->_textMiseAJour1 = $textMiseAJour;
	}
	
	public function getTextMiseAJour1(){
		return $this->_textMiseAJour1;
	}
	
	public function setTextMiseAJour2($textMiseAJour) {
		$this->_textMiseAJour2 = $textMiseAJour;
	}
	
	public function getTextMiseAJour2(){
		return $this->_textMiseAJour2;
	}
	
	public function setTextMiseAJour3($textMiseAJour) {
		$this->_textMiseAJour3 = $textMiseAJour;
	}
	
	public function getTextMiseAJour3(){
		return $this->_textMiseAJour3;
	}
	
	public function setTextAvisSpecialiste($textAvisSpecialiste) {
		$this->_textAvisSpecialiste = $textAvisSpecialiste;
	}
	
	public function getTextAvisSpecialiste(){
		return $this->_textAvisSpecialiste;
	}
	
	public function setEntrerAvisSpecialiste($entrerAvisSpecialiste) {
		$this->_entrerAvisSpecialiste = $entrerAvisSpecialiste;
	}
	
	public function getEntrerAvisSpecialiste(){
		return $this->_entrerAvisSpecialiste;
	}
	
	public function getTableauTemoinLimite(){
		return $this->_tableauTemoinLimite;
	}
	
	public function setTableauTemoinLimite($tableauTemoinLimite) {
		$this->_tableauTemoinLimite = $tableauTemoinLimite;
	}
	
	public function getTableauTemoinInfos(){
		return $this->_tableauTemoinInfos;
	}
	
	public function setTableauTemoinInfos($tableauTemoinInfos) {
		$this->_tableauTemoinInfos = $tableauTemoinInfos;
	}
	
	protected function setNbTotalLigne($nbTotalLigne) {
		$this->_nbTotalLigne = $nbTotalLigne;
	}
	
	public function getNbTotalLigne(){
		return $this->_nbTotalLigne;
	}
	
	
	protected  function getNoteTC(){
		$Control = new DateHelper();
		
		$baseUrl = $_SERVER['SCRIPT_FILENAME'];
		$tabURI  = explode('public', $baseUrl);
		
		$this->_yPosition -= 35;
		$this->_page->setFont($this->_newTime, 12);
		$this->_page->setFillColor(new ZendPdf\Color\Html('green'));
		$this->_page->drawText('RESUME DU PASSAGE AUX URGENCES',
				$this->_leftMargin+140,
				$this->_yPosition);
		$this->_yPosition -= 5;
		$this->_page->setlineColor(new ZendPdf\Color\Html('green'));
		$this->_page->drawLine($this->_leftMargin,
				$this->_yPosition,
				$this->_pageWidth -
				$this->_leftMargin,
				$this->_yPosition);
		$noteLineHeight = 22;
		$this->_yPosition -= 15;
		
		$this->_page->setFillColor(new ZendPdf\Color\Html('black')); 
		
		$this->_page->setLineColor(new ZendPdf\Color\Html('#999999')); 
		
		$today = new \DateTime();
		$date_actu = $today->format('Y-m-d');

			//-----------------------------------------------
			$this->_page->setFont($this->_newTimeGras, 9);
			$this->_page->drawText('PRENOM & NOM :',
					$this->_leftMargin+157,
					$this->_yPosition);
			$this->_page->setFont($this->_newTime, 11);
			$this->_page->drawText(iconv ('UTF-8' ,'ISO-8859-1', $this->_DonneesPatient['PRENOM'].' '.$this->_DonneesPatient['NOM']),
					$this->_leftMargin+240,
					$this->_yPosition);
			//-----------------------------------------------
			$this->_yPosition -= 15;// allez a ligne suivante
			//----- -----------------------------------------
			$this->_page->setFont($this->_newTimeGras, 9);
			$this->_page->drawText('SEXE :',
					$this->_leftMargin+205,
					$this->_yPosition);
			$this->_page->setFont($this->_newTime, 11);
			$this->_page->drawText(iconv ('UTF-8' ,'ISO-8859-1' ,$this->_DonneesPatient['SEXE']),
					$this->_leftMargin+240,
					$this->_yPosition);
			
 			//-----------------------------------------------
			$this->_yPosition -= 15;// allez a ligne suivante
			//----- -----------------------------------------
			$date_naissance = $this->_DonneesPatient['DATE_NAISSANCE'];
			if($date_naissance){ $date_naissance = $Control->convertDate($date_naissance); } else {$date_naissance = null; }
				
 			if($date_naissance){
				$this->_page->setFont($this->_newTimeGras, 9);
				$this->_page->drawText('DATE DE NAISSANCE :',
						$this->_leftMargin+135,
						$this->_yPosition);
				$this->_page->setFont($this->_newTime, 10);
			
				$this->_page->drawText($date_naissance."  (".$this->_DonneesPatient['AGE']." ans)",
						$this->_leftMargin+240,
						$this->_yPosition);
			}else {
				$this->_page->setFont($this->_newTimeGras, 9);
				$this->_page->drawText('AGE :',
						$this->_leftMargin+209,
						$this->_yPosition);
				$this->_page->setFont($this->_newTime, 10);
			
				$this->_page->drawText($this->_DonneesPatient['AGE']." ans",
						$this->_leftMargin+240,
						$this->_yPosition);
			}
			
			
			//-----------------------------------------------
			$this->_yPosition -= 15;// allez a ligne suivante
			//----- -----------------------------------------
			$this->_page->setFont($this->_newTimeGras, 9);
			$this->_page->drawText('TELEPHONE :',
					$this->_leftMargin+173,
					$this->_yPosition);
			$this->_page->setFont($this->_newTime, 11);
			$this->_page->drawText(iconv ('UTF-8' ,'ISO-8859-1' , $this->_DonneesPatient['TELEPHONE']),
					$this->_leftMargin+240,
					$this->_yPosition);
			
			$niveauUrgence = "";
			if($this->_infoAdmission){
				if($this->_infoAdmission->niveau){
					$niveauUrgence = $this->_infoAdmission->niveau;
				}
			}
			$this->_page->setFont($this->_newTime, 8);
			$this->_page->drawText('N.U : ',
					$this->_leftMargin+460,
					$this->_yPosition);
			$this->_page->setFont($this->_newTimeGrasNonItalic, 12);
			$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $niveauUrgence),
					$this->_leftMargin+480,
					$this->_yPosition);
			
			
			$this->_page->setlineColor(new ZendPdf\Color\Html('green'));
			$this->_page->setLineWidth(1);
			$this->_page->drawLine($this->_leftMargin,
					$this->_yPosition-5,
					$this->_pageWidth -
					$this->_leftMargin,
					$this->_yPosition-5);
			
		$this->_yPosition -= $noteLineHeight+10; //aller a la ligne suivante
		
		//======================================
		//======================================

		
 		$motif_consultation = wordwrap($this->_DonneesDemande['motif_consultation'], 83, "\n", false); 
 		$motif_consultation = explode( "\n" ,$motif_consultation);
 		
 		$motif_consultation_comp = str_replace($motif_consultation[0], "", $this->_DonneesDemande['motif_consultation']);
 		$motif_consultation_comp = wordwrap($motif_consultation_comp, 108, "\n", false);
 		$motif_consultation_comp = explode( "\n" ,$motif_consultation_comp);
 		

 		$resume_syndromique = $this->_DonneesDemande['resume_syndromique'];
 		$hypotheses_diagnostiques = $this->_DonneesDemande['hypotheses_diagnostiques'];
 		$examens_complementaires = $this->_DonneesDemande['examens_complementaires'];
 		$traitement = $this->_DonneesDemande['traitement'];
 		$resultats_examens_complementaires = $this->_DonneesDemande['resultats_examens_complementaires'];
 		
 		$mutation = (int)$this->_DonneesDemande['mutation'];
 		$mise_a_jour_1 = $this->_DonneesDemande['mise_a_jour_1'];
 		$mise_a_jour_2 = $this->_DonneesDemande['mise_a_jour_2'];
 		$mise_a_jour_3 = $this->_DonneesDemande['mise_a_jour_3'];
 		$avis_specialiste = $this->_DonneesDemande['avis_specialiste'];
 		
 		
 		$TableauRS  = explode( "\n" , $resume_syndromique);
 		$TableauHD  = explode( "\n" , $hypotheses_diagnostiques);
 		$TableauEC  = explode( "\n" , $examens_complementaires );
 		$TableauTr  = explode( "\n" , $traitement );
 		$TableauREC = explode( "\n" , $resultats_examens_complementaires );
 		$TableauAS  = explode( "\n" , $avis_specialiste );
		
 		$tableauTemoinLimite = array(); for ($i = 1; $i < 11; $i++ ){ $tableauTemoinLimite[$i] = 1; }
 		$tableauTemoinInfos = array();
 		$NbTotaleLigne = 0;
 		/**
 		 * **************************************************************************************
 		 * ======================================================================================
 		 * **************************************************************************************
 		 */
 		$temoinMotifCons2 = 0;
 		for($i = 1 ; $i < 3 ; $i++){
 			
 			if($motif_consultation){

 				if($i == 1 && $motif_consultation[0]){
 					$this->_page->setlineColor(new ZendPdf\Color\Html('#efefef'));
 					$this->_page->setLineWidth(0.5);
 					$this->_page->drawLine($this->_leftMargin,
 							$this->_yPosition,
 							$this->_pageWidth -
 							$this->_leftMargin,
 							$this->_yPosition);
 				
 					$this->_page->setFont($this->_newTimeGrasNonItalic, 12);
 					$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1','Motif de consultation : '),
 							$this->_leftMargin,
 							$this->_yPosition);
 				
 					$this->_page->setlineColor(new ZendPdf\Color\Html('black'));
 					$this->_page->setLineWidth(0.5);
 					$this->_page->drawLine($this->_leftMargin,
 							$this->_yPosition-2,
 							$this->_pageWidth -
 							$this->_leftMargin-379,
 							$this->_yPosition-2);
 				
 					$this->_page->setFont($this->_policeContenu, 11);
 					$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1',  $motif_consultation[0]),
 							$this->_leftMargin+120,
 							$this->_yPosition);
 				
 					$this->_yPosition -= $noteLineHeight;
 				}
 					
 				else
 				if($i == 2 && $motif_consultation_comp[0]){
 					$this->_page->setlineColor(new ZendPdf\Color\Html('#efefef'));
 					$this->_page->setLineWidth(0.5);
 					$this->_page->drawLine($this->_leftMargin,
 							$this->_yPosition,
 							$this->_pageWidth -
 							$this->_leftMargin,
 							$this->_yPosition);
 						
 					$this->_page->setFont($this->_policeContenu, 11);
 					$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $motif_consultation_comp[0] ),
 							$this->_leftMargin,
 							$this->_yPosition);
 					$temoinMotifCons2 = 1;
 					$this->_yPosition -= $noteLineHeight;
 					$NbTotaleLigne++;
 				}	
 				
 			}
							
 		}
 		
 		$nbLigne = 0;
 		
 		/**
 		 * **************************************************************************************
 		 * ======================================================================================
 		 * **************************************************************************************
 		 */
 		
 		//************* Gestion du résume syndromique ****************
 		//************* Gestion du résume syndromique ****************
 		
 		//Préparation du texte pour le résume syndromique
 		//Préparation du texte pour le résume syndromique
 		$TextResumeSyndromique = array();
 		
 		for($i = 0 ; $i < count($TableauRS) ; $i++){
 		
 		    if( strlen($TableauRS[$i]) > 106 ){
 		        $textDecouper = wordwrap($TableauRS[$i], 106, "\n", false); // On découpe le texte
 		        $textDecouperTab = explode( "\n" ,$textDecouper); // On le place dans un tableau
 		
 		        for($j = 0 ; $j < count($textDecouperTab) ; $j++){
 		        	if(rtrim($textDecouperTab[$j]) != ''){
 		        		$TextResumeSyndromique[] = $textDecouperTab[$j];
 		        	}
 		        }
 		
 		    }else{
 		    	if(rtrim($TableauRS[$i]) != ''){
 		    		$TextResumeSyndromique[] = $TableauRS[$i];
 		    	}
 		    }
 		     
 		}
 		
 		$TextResumeSyndromique = $this->Justifier($TextResumeSyndromique, 110);
 		$this->setTextResumeSyndromique($TextResumeSyndromique);
 		$NbTotaleLigne += count($TextResumeSyndromique);
 		
 		$maxLigne = 23;
 		if($temoinMotifCons2 == 1){ $maxLigne--; }
 		//Fin préparation du texte du résume syndromique
 		//Fin préparation du texte du résume syndromique
 		/*1)*/

 		if($TextResumeSyndromique){

 			if($TextResumeSyndromique[0]){
 			
 				$tableauTemoinLimite [1] = 1;
 			
 				$this->_page->setFont($this->_newTimeGrasNonItalic, 12);
 				$this->_page->drawText('Résumé syndromique :  ',
 						$this->_leftMargin,
 						$this->_yPosition);
 					
 				$this->_page->setlineColor(new ZendPdf\Color\Html('black'));
 				$this->_page->setLineWidth(0.5);
 				$this->_page->drawLine($this->_leftMargin,
 						$this->_yPosition-2,
 						$this->_pageWidth -
 						$this->_leftMargin-377,
 						$this->_yPosition-2);
 					
 				$this->_yPosition -= $noteLineHeight;
 					
 				$actuLigne = $nbLigne;
 				for($i = 0 ; $i < ($maxLigne-$actuLigne) && $i < count($TextResumeSyndromique); $i++){
 					$this->_page->setlineColor(new ZendPdf\Color\Html('#efefef'));
 					$this->_page->setLineWidth(0.5);
 					$this->_page->drawLine($this->_leftMargin,
 							$this->_yPosition,
 							$this->_pageWidth -
 							$this->_leftMargin,
 							$this->_yPosition);
 			
 					$this->_page->setFont($this->_policeContenu, 11);
 					$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $TextResumeSyndromique[$i] ),
 							$this->_leftMargin,
 							$this->_yPosition);
 			
 					$this->_yPosition -= $noteLineHeight;
 					$nbLigne++;
 				}
 				$tableauTemoinInfos [1][] = $i;
 				$tableauTemoinInfos [1][] = count($TextResumeSyndromique);
 			}
 			else{
 				$tableauTemoinLimite [1] = 0;
 			}
 				
 			$this->setEntrerResumeSyndromique($i);
 		}else{
 			$this->setEntrerResumeSyndromique(-1);
 		}
 		
 		//Fin de la gestion du résume syndromique
 		//Fin de la gestion du résume syndromique
 		
 		/**
 		 * **************************************************************************************
 		 * ======================================================================================
 		 * **************************************************************************************
 		 */
 		
 		
 		//************* Gestion des hypotheses diagnostiques ************
 		//************* Gestion des hypotheses diagnostiques ************
 		
 		//Préparation du texte des hypotheses diagnostiques
 		//Préparation du texte des hypotheses diagnostiques

 		$TextHypothesesDiagnostics = array();
 		for($i = 0 ; $i < count($TableauHD) ; $i++){
 		     
 		    if( strlen($TableauHD[$i]) > 106 ){
 		        $textDecouper = wordwrap($TableauHD[$i], 106, "\n", false); // On découpe le texte
 		        $textDecouperTab = explode( "\n" ,$textDecouper); // On le place dans un tableau
 		         
 		        for($j = 0 ; $j < count($textDecouperTab) ; $j++){
 		        	if(rtrim($textDecouperTab[$j]) != ''){
 		        		$TextHypothesesDiagnostics[] = $textDecouperTab[$j];
 		        	}
 		        }
 		         
 		    }else{
 		    	if(rtrim($TableauHD[$i]) != ''){
 		    		$TextHypothesesDiagnostics[] = $TableauHD[$i];
 		    	}
 		    }
 		
 		}
 		$TextHypothesesDiagnostics = $this->Justifier($TextHypothesesDiagnostics, 110);
 		$this->setTextHypothesesDiagnostics($TextHypothesesDiagnostics);
 		$NbTotaleLigne += count($TextHypothesesDiagnostics);
 		
 		//Fin préparation du texte des hypotheses diagnostiques
 		//Fin préparation du texte des hypotheses diagnostiques
 		/*2)*/
 		if($nbLigne < $maxLigne){
 			
 			$TextHD = count($TextHypothesesDiagnostics);
 			
 			if($TextHypothesesDiagnostics){
 				if($TextHypothesesDiagnostics [0]){
 					$maxLigne--;
 						
 					if($nbLigne < $maxLigne){
 				
 						$tableauTemoinLimite [1] = 0;
 						$tableauTemoinLimite [2] = 1;
 				
 						$this->_page->setFont($this->_newTimeGrasNonItalic, 12);
 						$this->_page->drawText('Hypothèses diagnostiques : ',
 								$this->_leftMargin,
 								$this->_yPosition);
 							
 						$this->_page->setlineColor(new ZendPdf\Color\Html('black'));
 						$this->_page->setLineWidth(0.5);
 						$this->_page->drawLine($this->_leftMargin,
 								$this->_yPosition-2,
 								$this->_pageWidth -
 								$this->_leftMargin-357,
 								$this->_yPosition-2);
 				
 						$this->_yPosition -= $noteLineHeight;
 				
 						$actuLigne = $nbLigne;
 						for($i = 0 ; $i < ($maxLigne-$actuLigne) && $i < $TextHD; $i++){
 							$this->_page->setlineColor(new ZendPdf\Color\Html('#efefef'));
 							$this->_page->setLineWidth(0.5);
 							$this->_page->drawLine($this->_leftMargin,
 									$this->_yPosition,
 									$this->_pageWidth -
 									$this->_leftMargin,
 									$this->_yPosition);
 				
 				
 							$this->_page->setFont($this->_policeContenu, 11);
 							$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $TextHypothesesDiagnostics[$i] ),
 									$this->_leftMargin,
 									$this->_yPosition);
 				
 							$this->_yPosition -= $noteLineHeight;
 							$nbLigne++;
 						}
 						$tableauTemoinInfos [2][] = $i;
 						$tableauTemoinInfos [2][] = count($TextHypothesesDiagnostics);
 					}
 					else{
 						$tableauTemoinLimite [1] = 0;
 						$tableauTemoinLimite [2] = 1;
 						$tableauTemoinInfos [2][] = 0;
 						$tableauTemoinInfos [2][] = count($TextHypothesesDiagnostics);
 					}
 						
 				}else{
 					$tableauTemoinLimite [1] = 0;
 					$tableauTemoinLimite [2] = 0;
 				}
 				
 				$this->setEntrerHypothesesDiagnostics($i);
 				
 			}else{
 				$this->setEntrerHypothesesDiagnostics(-1);
 			}
 			
 			
 		}else{
  			$tableauTemoinInfos [2][] = 0;
  			$tableauTemoinInfos [2][] = count($TextHypothesesDiagnostics);
  		}
 		//Fin Gestion des hypotheses diagnostiques
 		//Fin Gestion des hypotheses diagnostiques
 		 
 		/**
 		 * **************************************************************************************
 		 * ======================================================================================
 		 * **************************************************************************************
 		 */
 		

 		//************* Gestion des examens complémentaires ************
 		//************* Gestion des examens complémentaires ************
 			
 		//Préparation du texte des examens complémentaires
 		//Préparation du texte des examens complémentaires
 		
 		$TextExamensComplementaires = array();
 		for($i = 0 ; $i < count($TableauEC) ; $i++){
 		
 			if( strlen($TableauEC[$i]) > 106 ){
 				$textDecouper = wordwrap($TableauEC[$i], 106, "\n", false); // On découpe le texte
 				$textDecouperTab = explode( "\n" ,$textDecouper); // On le place dans un tableau
 		
 				for($j = 0 ; $j < count($textDecouperTab) ; $j++){
 					if(rtrim($textDecouperTab[$j]) != ''){
 						$TextExamensComplementaires[] = $textDecouperTab[$j];
 					}
 				}
 		
 			}else{
 				if(rtrim($TableauEC[$i]) != ''){
 					$TextExamensComplementaires[] = $TableauEC[$i];
 				}
 			}
 				
 		}
 		
 		$TextExamensComplementaires = $this->Justifier($TextExamensComplementaires, 108);
 		$this->setTextExamenComplementaire($TextExamensComplementaires);
 		$NbTotaleLigne += count($TextExamensComplementaires);
 		
 		//Fin préparation du texte des examens complémentaires
 		//Fin préparation du texte des examens complémentaires
 		/*3)*/
 		if($nbLigne < $maxLigne){
 			
 			
 			if($TextExamensComplementaires){
 				if($TextExamensComplementaires[0]){
 					$maxLigne--;
 						
 					if($nbLigne < $maxLigne){
 				
 						$tableauTemoinLimite [2] = 0;
 						$tableauTemoinLimite [3] = 1;
 				
 						$this->_page->setFont($this->_newTimeGrasNonItalic, 12);
 						$this->_page->drawText('Examen complémentaire : ',
 								$this->_leftMargin,
 								$this->_yPosition);
 				
 						$this->_page->setlineColor(new ZendPdf\Color\Html('black'));
 						$this->_page->setLineWidth(0.5);
 						$this->_page->drawLine($this->_leftMargin,
 								$this->_yPosition-2,
 								$this->_pageWidth -
 								$this->_leftMargin-360,
 								$this->_yPosition-2);
 				
 						$this->_yPosition -= $noteLineHeight;
 				
 						$actuLigne = $nbLigne;
 						for($i = 0 ; $i < ($maxLigne-$actuLigne) && $i < count($TextExamensComplementaires); $i++){
 							$this->_page->setlineColor(new ZendPdf\Color\Html('#efefef'));
 							$this->_page->setLineWidth(0.5);
 							$this->_page->drawLine($this->_leftMargin,
 									$this->_yPosition,
 									$this->_pageWidth -
 									$this->_leftMargin,
 									$this->_yPosition);
 				
 				
 							$this->_page->setFont($this->_policeContenu, 11);
 							$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $TextExamensComplementaires[$i] ),
 									$this->_leftMargin,
 									$this->_yPosition);
 				
 							$this->_yPosition -= $noteLineHeight;
 							$nbLigne++;
 						}
 				
 						$tableauTemoinInfos [3][] = $i;
 						$tableauTemoinInfos [3][] = count($TextExamensComplementaires);
 					}
 					else{
 						$tableauTemoinLimite [2] = 0;
 						$tableauTemoinLimite [3] = 1;
 						$tableauTemoinInfos [3][] = 0;
 						$tableauTemoinInfos [3][] = count($TextExamensComplementaires);
 					}
 				
 						
 				}else{
 					$tableauTemoinLimite [2] = 0;
 					$tableauTemoinLimite [3] = 0;
 				}
 				
 					
 				$this->setEntrerExamenComplementaire($i);
 				
 			}else{
 				$this->setEntrerExamenComplementaire(-1);
 			}
 			
 			
 			
 		}else{
 			$tableauTemoinInfos [3][] = 0;
 			$tableauTemoinInfos [3][] = count($TextExamensComplementaires);
  		}
 		//Fin Gestion des examens complémentaires
 		//Fin Gestion des examens complémentaires
 		
 		
 		/**
 		 * **************************************************************************************
 		 * ======================================================================================
 		 * **************************************************************************************
 		 */
 		
 		
 		/**
 		 * **************************************************************************************
 		 * ======================================================================================
 		 * **************************************************************************************
 		 */
 			
 		
 		//************* Gestion des traitements ************
 		//************* Gestion des traitements ************
 		
 		//Préparation du texte des traitements
 		//Préparation du texte des traitements
 			
 		$TextTraitement = array();
 		for($i = 0 ; $i < count($TableauTr) ; $i++){
 				
 			if( strlen($TableauTr[$i]) > 106 ){
 				$textDecouper = wordwrap($TableauTr[$i], 106, "\n", false); // On découpe le texte
 				$textDecouperTab = explode( "\n" ,$textDecouper); // On le place dans un tableau
 					
 				for($j = 0 ; $j < count($textDecouperTab) ; $j++){
 					if(rtrim($textDecouperTab[$j]) != ''){
 						$TextTraitement[] = $textDecouperTab[$j];
 					}
 				}
 					
 			}else{
 				if(rtrim($TableauTr[$i]) != ''){
 					$TextTraitement[] = $TableauTr[$i];
 				}
 			}
 				
 		}
 			
 		$TextTraitement = $this->Justifier($TextTraitement, 110);
 		$this->setTextTraitement($TextTraitement);
 		$NbTotaleLigne += count($TextTraitement);
 		
 		//Fin préparation du texte des traitements
 		//Fin préparation du texte des traitements
 		/*4)*/
 		if($nbLigne < $maxLigne){
 				
 			if($TextTraitement){
 				if($TextTraitement[0]){
 					$maxLigne--;
 					if($nbLigne < $maxLigne){
 				
 						$tableauTemoinLimite [3] = 0;
 						$tableauTemoinLimite [4] = 1;
 				
 						$this->_page->setFont($this->_newTimeGrasNonItalic, 12);
 						$this->_page->drawText('Traitement : ',
 								$this->_leftMargin,
 								$this->_yPosition);
 							
 						$this->_page->setlineColor(new ZendPdf\Color\Html('black'));
 						$this->_page->setLineWidth(0.5);
 						$this->_page->drawLine($this->_leftMargin,
 								$this->_yPosition-2,
 								$this->_pageWidth -
 								$this->_leftMargin-430,
 								$this->_yPosition-2);
 							
 						$this->_yPosition -= $noteLineHeight;
 							
 						$actuLigne = $nbLigne;
 						for($i = 0 ; $i < ($maxLigne-$actuLigne) && $i < count($TextTraitement); $i++){
 							$this->_page->setlineColor(new ZendPdf\Color\Html('#efefef'));
 							$this->_page->setLineWidth(0.5);
 							$this->_page->drawLine($this->_leftMargin,
 									$this->_yPosition,
 									$this->_pageWidth -
 									$this->_leftMargin,
 									$this->_yPosition);
 				
 				
 							$this->_page->setFont($this->_policeContenu, 11);
 							$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $TextTraitement[$i] ),
 									$this->_leftMargin,
 									$this->_yPosition);
 				
 							$this->_yPosition -= $noteLineHeight;
 							$nbLigne++;
 						}
 				
 						$tableauTemoinInfos [4][] = $i;
 						$tableauTemoinInfos [4][] = count($TextTraitement);
 					}
 					else{
 						$tableauTemoinLimite [3] = 0;
 						$tableauTemoinLimite [4] = 1;
 						$tableauTemoinInfos [4][] = 0;
 						$tableauTemoinInfos [4][] = count($TextTraitement);
 					}
 						
 				}else{
 					$tableauTemoinLimite [3] = 0;
 					$tableauTemoinLimite [4] = 0;
 				}
 					
 					
 				$this->setEntrerTraitement($i);
 				
 			}else {
 				$this->setEntrerTraitement(-1);
 			}
 			
 			
 		}else{
 			$tableauTemoinInfos [4][] = 0;
 			$tableauTemoinInfos [4][] = count($TextTraitement);
 		}
 		//Fin Gestion des traitements
 		//Fin Gestion des traitements
 		
 		
 		
 		
 		/**
 		 * **************************************************************************************
 		 * ======================================================================================
 		 * **************************************************************************************
 		 */
 		
 			
 		//************* Gestion des résultats des examens complémentaires ************
 		//************* Gestion des résultats des examens complémentaires ************
 			
 		//Préparation du texte des résultats des examens complémentaires
 		//Préparation du texte des résultats des examens complémentaires
 		
 		$TextResultatsExamensComp = array();
 		for($i = 0 ; $i < count($TableauREC) ; $i++){
 				
 			if( strlen($TableauREC[$i]) > 106 ){
 				$textDecouper = wordwrap($TableauREC[$i], 106, "\n", false); // On découpe le texte
 				$textDecouperTab = explode( "\n" ,$textDecouper); // On le place dans un tableau
 		
 				for($j = 0 ; $j < count($textDecouperTab) ; $j++){
 					if(rtrim($textDecouperTab[$j]) != ''){
 						$TextResultatsExamensComp[] = $textDecouperTab[$j];
 					}
 				}
 		
 			}else{
 				if(rtrim($TableauREC[$i]) != ''){
 					$TextResultatsExamensComp[] = $TableauREC[$i];
 				}
 			}
 				
 		}
 		
 		$TextResultatsExamensComp = $this->Justifier($TextResultatsExamensComp, 110);
 		$this->setTextResultatExamenComplementaire($TextResultatsExamensComp);
 		$NbTotaleLigne += count($TextResultatsExamensComp);
 		

 		//Fin préparation du texte des résultats des examens complémentaires
 		//Fin préparation du texte des résultats des examens complémentaires
 		/*5)*/
 		if($nbLigne < $maxLigne){
 				
 			if($TextResultatsExamensComp){
 				if($TextResultatsExamensComp[0]){
 					$maxLigne--;
 						
 					if($nbLigne < $maxLigne){
 				
 						$tableauTemoinLimite [4] = 0;
 						$tableauTemoinLimite [5] = 1;
 				
 						$this->_page->setFont($this->_newTimeGrasNonItalic, 12);
 						$this->_page->drawText('Résultats examens complémentaires : ',
 								$this->_leftMargin,
 								$this->_yPosition);
 							
 						$this->_page->setlineColor(new ZendPdf\Color\Html('black'));
 						$this->_page->setLineWidth(0.5);
 						$this->_page->drawLine($this->_leftMargin,
 								$this->_yPosition-2,
 								$this->_pageWidth -
 								$this->_leftMargin-304,
 								$this->_yPosition-2);
 							
 						$this->_yPosition -= $noteLineHeight;
 							
 						$actuLigne = $nbLigne;
 						for($i = 0 ; $i < ($maxLigne-$actuLigne) && $i < count($TextResultatsExamensComp); $i++){
 							$this->_page->setlineColor(new ZendPdf\Color\Html('#efefef'));
 							$this->_page->setLineWidth(0.5);
 							$this->_page->drawLine($this->_leftMargin,
 									$this->_yPosition,
 									$this->_pageWidth -
 									$this->_leftMargin,
 									$this->_yPosition);
 								
 								
 							$this->_page->setFont($this->_policeContenu, 11);
 							$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $TextResultatsExamensComp[$i] ),
 									$this->_leftMargin,
 									$this->_yPosition);
 								
 							$this->_yPosition -= $noteLineHeight;
 							$nbLigne++;
 						}
 							
 						$tableauTemoinInfos [5][] = $i;
 						$tableauTemoinInfos [5][] = count($TextResultatsExamensComp);
 					}
 					else{ //var_dump('dernier'); exit();
 						$tableauTemoinLimite [4] = 0;
 						$tableauTemoinLimite [5] = 1;
 						$tableauTemoinInfos [5][] = 0;
 						$tableauTemoinInfos [5][] = count($TextResultatsExamensComp);
 					}
 				
 				
 				}else{
 					$tableauTemoinLimite [4] = 0;
 					$tableauTemoinLimite [5] = 0;
 				}
 					
 					
 				$this->setEntrerResultatExamenComplementaire($i);
 				
 			}else{
 				$this->setEntrerResultatExamenComplementaire(-1);
 			}
 			
 			
 			
 		}else{
 			$tableauTemoinInfos [5][] = 0;
  			$tableauTemoinInfos [5][] = count($TextResultatsExamensComp);
  		}
 		//Fin Gestion des résultats des examens complémentaires
 		//Fin Gestion des résultats des examens complémentaires
 		
 		

 		/**
 		 * **************************************************************************************
 		 * ======================================================================================
 		 * **************************************************************************************
 		 */
 			
 		
 		//************* Gestion des mutations ************
 		//************* Gestion des mutations ************
 			
 		$this->setTextMutation($this->getListeServiceMutation()[$mutation]);
 		if($mutation != 0){ $NbTotaleLigne ++; }
 		
 		
 		/*6)*/
  		if($nbLigne < $maxLigne){
 				
  			if($mutation != 0){ 
 					
  				if($nbLigne < 19){
  					
  					$tableauTemoinLimite [5] = 0;
  					$tableauTemoinLimite [6] = 0;
  					
  					$this->_page->setlineColor(new ZendPdf\Color\Html('#efefef'));
  					$this->_page->setLineWidth(0.5);
  					$this->_page->drawLine($this->_leftMargin,
  							$this->_yPosition,
  							$this->_pageWidth -
  							$this->_leftMargin,
  							$this->_yPosition);
  					
 					$this->_page->setFont($this->_newTimeGrasNonItalic, 12);
 					$this->_page->drawText('Mutation : ',
 							$this->_leftMargin,
 							$this->_yPosition);
 						
 					$this->_page->setlineColor(new ZendPdf\Color\Html('black'));
 					$this->_page->setLineWidth(0.5);
 					$this->_page->drawLine($this->_leftMargin,
 							$this->_yPosition-2,
 							$this->_pageWidth -
 							$this->_leftMargin-440,
 							$this->_yPosition-2);
 					
 					$this->_page->setFont($this->_policeContenu, 11);
 					$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $this->getListeServiceMutation()[$mutation]),
 							$this->_leftMargin+70,
 							$this->_yPosition);
 						
 					$this->_yPosition -= $noteLineHeight;
 					$nbLigne++;
 					
 					$tableauTemoinInfos [6][] = 1;
 					$tableauTemoinInfos [6][] = 1;
  				}
 		
  			}
  			else{
  				if($nbLigne < 19){
  					$tableauTemoinLimite [5] = 0;
  					$tableauTemoinLimite [6] = 0;
  				}
  			
  			}
  			
  			
  		}
 				//var_dump($tableauTemoinLimite); exit();
 		//Fin Gestion des mutations
 		//Fin Gestion des mutations
 		
  		
  		/**
  		 * **************************************************************************************
  		 * ======================================================================================
  		 * **************************************************************************************
  		 */
  		
  			
  		//************* Gestion des mise à jours 1 ************
  		//************* Gestion des mise à jours 1 ************
  		
  		$this->setTextMiseAJour1($mise_a_jour_1);
  		if($mise_a_jour_1 != ''){ $NbTotaleLigne ++; }
  		
  		/*7)*/
  		if($nbLigne < $maxLigne){ 
  			
  			if($mise_a_jour_1 != ''){

  				if($nbLigne < 19){
  					
  					$tableauTemoinLimite [6] = 0;
  					$tableauTemoinLimite [7] = 0;
  					
  					$this->_page->setlineColor(new ZendPdf\Color\Html('#efefef'));
  					$this->_page->setLineWidth(0.5);
  					$this->_page->drawLine($this->_leftMargin,
  							$this->_yPosition,
  							$this->_pageWidth -
  							$this->_leftMargin,
  							$this->_yPosition);
  					
  					$this->_page->setFont($this->_newTimeGrasNonItalic, 12);
  					$this->_page->drawText('Mise à jour 1 : ',
  							$this->_leftMargin,
  							$this->_yPosition);
  						
  					$this->_page->setlineColor(new ZendPdf\Color\Html('black'));
  					$this->_page->setLineWidth(0.5);
  					$this->_page->drawLine($this->_leftMargin,
  							$this->_yPosition-2,
  							$this->_pageWidth -
  							$this->_leftMargin-420,
  							$this->_yPosition-2);
  		
  					$this->_page->setFont($this->_policeContenu, 11);
  					$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $mise_a_jour_1),
  							$this->_leftMargin+83,
  							$this->_yPosition);
  						
  					$this->_yPosition -= $noteLineHeight;
  					$nbLigne++;
  					
  					$tableauTemoinInfos [7][] = 1;
  					$tableauTemoinInfos [7][] = 1;
  				}
  					
  			}
  			else{
  				if($nbLigne < 19){
  					$tableauTemoinLimite [6] = 0;
  					$tableauTemoinLimite [7] = 0;
  				}
  			
  			}
  				
  		}
  			
  		//Fin Gestion des mise à jour 1
  		//Fin Gestion des mise à jour 1
  		
  		/**
  		 * **************************************************************************************
  		 * ======================================================================================
  		 * **************************************************************************************
  		 */
  		
  			
  		//************* Gestion des mise à jours 2 ************
  		//************* Gestion des mise à jours 2 ************
  		$this->setTextMiseAJour2($mise_a_jour_2);
  		if($mise_a_jour_2 != ''){ $NbTotaleLigne ++; }
  		
  		/*8)*/
  		if($nbLigne < $maxLigne){
  				
  			if($mise_a_jour_2 != ''){
  				
  				if($nbLigne < 19){
  					
  					$tableauTemoinLimite [7] = 0;
  					$tableauTemoinLimite [8] = 0;
  					
  					$this->_page->setlineColor(new ZendPdf\Color\Html('#efefef'));
  					$this->_page->setLineWidth(0.5);
  					$this->_page->drawLine($this->_leftMargin,
  							$this->_yPosition,
  							$this->_pageWidth -
  							$this->_leftMargin,
  							$this->_yPosition);
  					
  					$this->_page->setFont($this->_newTimeGrasNonItalic, 12);
  					$this->_page->drawText('Mise à jour 2 : ',
  							$this->_leftMargin,
  							$this->_yPosition);
  		
  					$this->_page->setlineColor(new ZendPdf\Color\Html('black'));
  					$this->_page->setLineWidth(0.5);
  					$this->_page->drawLine($this->_leftMargin,
  							$this->_yPosition-2,
  							$this->_pageWidth -
  							$this->_leftMargin-420,
  							$this->_yPosition-2);
  		
  					$this->_page->setFont($this->_policeContenu, 11);
  					$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $mise_a_jour_2),
  							$this->_leftMargin+83,
  							$this->_yPosition);
  		
  					$this->_yPosition -= $noteLineHeight;
  					$nbLigne++;
  					
  					$tableauTemoinInfos [8][] = 1;
  					$tableauTemoinInfos [8][] = 1;
  				}
  				
  			}
  			else{
  				if($nbLigne < 19){
  					$tableauTemoinLimite [7] = 0;
  					$tableauTemoinLimite [8] = 0;
  				}
  			
  			}
  		
  		}
  			
  		//Fin Gestion des mise à jour 2
  		//Fin Gestion des mise à jour 2
  		
  		
  		/**
  		 * **************************************************************************************
  		 * ======================================================================================
  		 * **************************************************************************************
  		 */
  		
  			
  		//************* Gestion des mise à jours 3 ************
  		//************* Gestion des mise à jours 3 ************
  		
  		$this->setTextMiseAJour3($mise_a_jour_3);
  		if($mise_a_jour_3 != ''){ $NbTotaleLigne ++; }
  		
  		/*9)*/
  		if($nbLigne < $maxLigne){
  				
  			if($mise_a_jour_3 != ''){
  		
  				if($nbLigne < 19){
  					
  					$tableauTemoinLimite [8] = 0;
  					$tableauTemoinLimite [9] = 0;
  					
  					$this->_page->setlineColor(new ZendPdf\Color\Html('#efefef'));
  					$this->_page->setLineWidth(0.5);
  					$this->_page->drawLine($this->_leftMargin,
  							$this->_yPosition,
  							$this->_pageWidth -
  							$this->_leftMargin,
  							$this->_yPosition);
  					
  					$this->_page->setFont($this->_newTimeGrasNonItalic, 12);
  					$this->_page->drawText('Mise à jour 3 : ',
  							$this->_leftMargin,
  							$this->_yPosition);
  		
  					$this->_page->setlineColor(new ZendPdf\Color\Html('black'));
  					$this->_page->setLineWidth(0.5);
  					$this->_page->drawLine($this->_leftMargin,
  							$this->_yPosition-2,
  							$this->_pageWidth -
  							$this->_leftMargin-420,
  							$this->_yPosition-2);
  		
  					$this->_page->setFont($this->_policeContenu, 11);
  					$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $mise_a_jour_3),
  							$this->_leftMargin+83,
  							$this->_yPosition);
  		
  					$this->_yPosition -= $noteLineHeight;
  					$nbLigne++;
  					
  					$tableauTemoinInfos [9][] = 1;
  					$tableauTemoinInfos [9][] = 1;
  				}
  					
  			}
  			else{
  				if($nbLigne < 19){
  					$tableauTemoinLimite [8] = 0;
  					$tableauTemoinLimite [9] = 0;
  				}
  				
  			}
  		
  		}else{
  			//var_dump('test 9'); exit();
  		}
  			
  		//Fin Gestion des mise à jour 3
  		//Fin Gestion des mise à jour 3
 		 	
  		
  		/**
  		 * **************************************************************************************
  		 * ======================================================================================
  		 * **************************************************************************************
  		 */
  			
  		
  		//************* Gestion de l'Avis du spécialiste ************
  		//************* Gestion de l'Avis du spécialiste ************
  		
  		//Préparation du texte de l'Avis du spécialiste
  		//Préparation du texte de l'Avis du spécialiste
  			
  		$TextAvisSpecialiste = array();
  		for($i = 0 ; $i < count($TableauAS) ; $i++){
  				
  			if( strlen($TableauAS[$i]) > 106 ){
  				$textDecouper = wordwrap($TableauAS[$i], 106, "\n", false); // On découpe le texte
  				$textDecouperTab = explode( "\n" ,$textDecouper); // On le place dans un tableau
  					
  				for($j = 0 ; $j < count($textDecouperTab) ; $j++){
  					if(rtrim($textDecouperTab[$j]) != ''){
  						$TextAvisSpecialiste[] = $textDecouperTab[$j];
  					}
  				}
  					
  			}else{
  				if(rtrim($TableauAS[$i]) != ''){
  					$TextAvisSpecialiste[] = $TableauAS[$i];
  				}
  			}
  				
  		}
  			
  		$TextAvisSpecialiste = $this->Justifier($TextAvisSpecialiste, 110);
  		$this->setTextAvisSpecialiste($TextAvisSpecialiste);
  		$NbTotaleLigne += count($TextAvisSpecialiste);
  		
  		
  		//Fin préparation du texte de l'Avis du spécialiste
  		//Fin préparation du texte de l'Avis du spécialiste
  		/*10)*/
  		if($nbLigne < $maxLigne){ 
  				
  			if($TextAvisSpecialiste){
  				if($TextAvisSpecialiste[0]){
  					$maxLigne--;
  						
  					if($nbLigne < $maxLigne){
  							
  						$tableauTemoinLimite [9] = 0;
  						$tableauTemoinLimite [10] = 1;
  							
  						$this->_page->setFont($this->_newTimeGrasNonItalic, 12);
  						$this->_page->drawText('Avis du spécialiste : ',
  								$this->_leftMargin,
  								$this->_yPosition);
  				
  						$this->_page->setlineColor(new ZendPdf\Color\Html('black'));
  						$this->_page->setLineWidth(0.5);
  						$this->_page->drawLine($this->_leftMargin,
  								$this->_yPosition-2,
  								$this->_pageWidth -
  								$this->_leftMargin-395,
  								$this->_yPosition-2);
  				
  						$this->_yPosition -= $noteLineHeight;
  				
  						$actuLigne = $nbLigne;
  						for($i = 0 ; $i < ($maxLigne-$actuLigne) && $i < count($TextAvisSpecialiste); $i++){
  							$this->_page->setlineColor(new ZendPdf\Color\Html('#efefef'));
  							$this->_page->setLineWidth(0.5);
  							$this->_page->drawLine($this->_leftMargin,
  									$this->_yPosition,
  									$this->_pageWidth -
  									$this->_leftMargin,
  									$this->_yPosition);
  								
  								
  							$this->_page->setFont($this->_policeContenu, 11);
  							$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $TextAvisSpecialiste[$i] ),
  									$this->_leftMargin,
  									$this->_yPosition);
  								
  							$this->_yPosition -= $noteLineHeight;
  							$nbLigne++;
  				
  						}
  				
  						$tableauTemoinInfos [10][] = $i;
  						$tableauTemoinInfos [10][] = count($TextAvisSpecialiste);
  					}else{
  						$tableauTemoinLimite [9]  = 0;
  						$tableauTemoinLimite [10] = 1;
  						$tableauTemoinInfos  [10][] = 0;
  						$tableauTemoinInfos  [10][] = count($TextAvisSpecialiste);
  					}
  				
  				
  				}else{
  					$tableauTemoinLimite [9]  = 0;
  					$tableauTemoinLimite [10] = 0;
  				}
  				
  				$this->setEntrerAvisSpecialiste($i);
  				
  			}else{
  				$this->setEntrerAvisSpecialiste(-1);
  			}
  			
  			
  		}else{
  			$tableauTemoinInfos  [10][] = 0;
  			$tableauTemoinInfos  [10][] = count($TextAvisSpecialiste);
  		}
  		//Fin Gestion Avis du spécialiste
  		//Fin Gestion Avis du spécialiste
  		 	
 		
  		$this->setTableauTemoinInfos($tableauTemoinInfos);
  		$this->setTableauTemoinLimite($tableauTemoinLimite);
  		$this->setNbTotalLigne($NbTotaleLigne);
 		$this->setNbLigne($nbLigne);
 		
	}
	
	public function getPiedPage(){
		$this->_page->setlineColor(new ZendPdf\Color\Html('green'));
		$this->_page->setLineWidth(1.5);
		$this->_page->drawLine($this->_leftMargin,
				70,
				$this->_pageWidth -
				$this->_leftMargin,
				70);
		
		$this->_page->setFont($this->_newTime, 10);
		$this->_page->drawText('Téléphone: 33 961 00 21',
				$this->_leftMargin,
				$this->_pageWidth - ( 105 + 435));
		
		$this->_page->setFont($this->_newTime, 10);
		$this->_page->drawText('SIMENS+: ',
				$this->_leftMargin + 355,
				$this->_pageWidth - ( 105 + 435));
		$this->_page->setFont($this->_newTimeGras, 11);
		$this->_page->drawText('www.simens.sn',
				$this->_leftMargin + 405,
				$this->_pageWidth - ( 105 + 435));
		
		$baseUrl = $_SERVER['SCRIPT_FILENAME'];
		$tabURI  = explode('public', $baseUrl);
		
		if($this->getNbTotalLigne() > 18 && $this->getNbLigne() < $this->getNbTotalLigne()){
			$imageHeader = ZendPdf\Image::imageWithPath($tabURI[0].'public/images_icons/number-one_r.png');
			$this->_page->drawImage($imageHeader,
					$this->_leftMargin + 220, //-x1
					47, //-y1
					290, //+x2
					65); //+y2
		}

		
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function justify($str, $maxlen) {
		$str = trim($str);
	
		$strlen = strlen($str);
		if ($strlen >= $maxlen) {
			$str = wordwrap($str, $maxlen);
			$str = explode("\n", $str);
			$str = $str[0];
			$strlen = strlen($str);
		}
	
		$space_count = substr_count($str, ' ');
		if ($space_count === 0) {
			return str_pad($str, $maxlen, ' ', STR_PAD_BOTH);
		}
	
		$extra_spaces_needed = $maxlen - $strlen;
		$total_spaces = $extra_spaces_needed + $space_count;
	
		$space_string_avg_length = $total_spaces / $space_count;
		$short_string_multiplier = floor($space_string_avg_length);
		$long_string_multiplier = ceil($space_string_avg_length);
	
		$short_fill_string = str_repeat(' ', $short_string_multiplier);
		$long_fill_string = str_repeat(' ', $long_string_multiplier);
	
		$limit = ($space_string_avg_length - $short_string_multiplier) * $space_count;
		$words_split_by_long = explode(' ', $str, $limit+1);
		$words_split_by_short = $words_split_by_long[$limit];
		$words_split_by_short = str_replace(' ', $short_fill_string, $words_split_by_short);
		$words_split_by_long[$limit] = $words_split_by_short;
		
		$result = implode($long_fill_string, $words_split_by_long);
		
		return $result;
		
	}
	
	public function justifier($str, $maxlen){
		
		$resulDonnees = array();
		
		foreach ($str as $test) {
			$len_before = strlen($test);
			
			if($len_before > 85 ){
				$processed = str_replace(' ', ' ', $this->justify($test, $maxlen));
				$len_after = strlen($processed);
				$resulDonnees [] = $processed;
			}else{
				$resulDonnees [] = $test;
			}
		}
		
		return $resulDonnees;
	}
	
	
	
	
	//var_dump($this->_leftMargin); exit();
	/*$imageHeader = ZendPdf\Image::imageWithPath($tabURI[0].'public/images_icons/text_padding_right.png');
	 $this->_page->drawImage($imageHeader,
	 		$this->_leftMargin - 2, //-x1
	 		$this->_pageHeight - 215, //-y1
	 		63, //+x2
	 		$this->_yPosition - 5); //+y2
	*/
	
}