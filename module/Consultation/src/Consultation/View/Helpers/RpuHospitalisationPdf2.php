<?php
namespace Consultation\View\Helpers;

use ZendPdf;
use ZendPdf\Page;
use ZendPdf\Font;
use Consultation\Model\Consultation;
use Urgence\View\Helper\DateHelper;

class RpuHospitalisationPdf2
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
	protected $_Donnees;
	protected $_policeContenu;
	protected $_newPolice;
	protected $_Service;
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
	protected $_tableauTemoinLimitePage2;
	protected $_suitePageRpu;
	
	
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
		
		$this->getNoteTC();
		$this->getPiedPage();
		
		$this->_page->restoreGS();
	}
	
	public function baseUrl(){
		$baseUrl = $_SERVER['SCRIPT_FILENAME'];
		$tabURI  = explode('public', $baseUrl);
		return $tabURI[0];
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
	
	public function nbAnnees($debut, $fin) {
		//60 secondes X 60 minutes X 24 heures dans une journee
		$nbSecondes = 60*60*24*365;
	
		$debut_ts = strtotime($debut);
		$fin_ts = strtotime($fin);
		$diff = $fin_ts - $debut_ts;
		return (int)($diff / $nbSecondes);
	}
	
	public  function gestionDuTexte($Texte){
		$tableau = array();
		return $tableau;
	}
	
	public  function setNbLigne($nbligne) {
		$this->_nbligne = $nbligne;
	}
	
	public function getNbLigne(){
		return $this->_nbligne;
	}
	
	public function setEntrerResumeSyndromique($entrerResumeSyndromique) {
		$this->_entrerResumeSyndromique = $entrerResumeSyndromique;
	}
	
	public function getEntrerResumeSyndromique(){
		return $this->_entrerResumeSyndromique;
	}
	
	public function setEntrerCOM($entrerCOM) {
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
	
	public  function setNbTotalLigne($nbTotalLigne) {
		$this->_nbTotalLigne = $nbTotalLigne;
	}
	
	public function getNbTotalLigne(){
		return $this->_nbTotalLigne;
	}
	
	public function getTableauTemoinLimitePage2(){
		return $this->_tableauTemoinLimitePage2;
	}
	
	public function setTableauTemoinLimitePage2($tableauTemoinLimitePage2) {
		$this->_tableauTemoinLimitePage2 = $tableauTemoinLimitePage2;
	}
	
	public function setSuitePageRpu($suitePageRpu){
		$this->_suitePageRpu = $suitePageRpu;
	}
	
	public function getSuitePageRpu(){
		return $this->_suitePageRpu;
	}
	
	protected  function getNoteTC(){
		$Control = new DateHelper();
		$noteLineHeight = 22;
		$baseUrl = $_SERVER['SCRIPT_FILENAME'];
		$tabURI  = explode('public', $baseUrl);
	
		$nbLigne = 0;
		$maxLigne = 31;
		
 		$tab_resume_syndromique = $this->getTextResumeSyndromique();
 		$tab_hypotheses_diagnostiques = $this->getTextHypothesesDiagnostics();
 		$tab_examens_complementaires = $this->getTextExamenComplementaire();
 		$tab_traitement = $this->getTextTraitement();
 		$tab_resultats_examens_complementaires = $this->getTextResultatExamenComplementaire();
			
 		$donnee_mutation      = $this->getTextMutation();
 		$donnee_mise_a_jour_1 = $this->getTextMiseAJour1();
 		$donnee_mise_a_jour_2 = $this->getTextMiseAJour2();
 		$donnee_mise_a_jour_3 = $this->getTextMiseAJour3();
 		$tab_avis_specialiste = $this->getTextAvisSpecialiste();
			

 		$tableauTemoinLimite = $this->getTableauTemoinLimite();
 		$tableauTemoinInfos  = $this->getTableauTemoinInfos();
 		
 		$tableauTemoinLimitePdf2 = array();
 		$tableauTemoinInfosPdf2 = array();
 		$suitePageRpu = 0;
 		/**
 		 * **************************************************************************************
 		 * ======================================================================================
 		 * **************************************************************************************
 		 */
 		
 		//************* Gestion des hypotheses diagnostiques ************
 		//************* Gestion des hypotheses diagnostiques ************
 		if($tableauTemoinLimite[2] == 1){
 			
 			if($tab_hypotheses_diagnostiques){
 				$debut = $tableauTemoinInfos[2][0];
 				$fin = $tableauTemoinInfos[2][1];
 					
 				if($debut < $fin){
 					if($debut == 0){
 						if($nbLigne < $maxLigne){
 							if($nbLigne == 30){
 								$nbLigne++;
 							}else{
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
 								$nbLigne ++;
 							}
 						}
 					}
 				
 					$j = $debut;
 					for($i = $debut ; $i < $fin ; $i++){
 						if($nbLigne < $maxLigne){
 							$this->_page->setlineColor(new ZendPdf\Color\Html('#efefef'));
 							$this->_page->setLineWidth(0.5);
 							$this->_page->drawLine($this->_leftMargin,
 									$this->_yPosition,
 									$this->_pageWidth -
 									$this->_leftMargin,
 									$this->_yPosition);
 				
 							$this->_page->setFont($this->_policeContenu, 11);
 							$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $tab_hypotheses_diagnostiques[$i] ),
 									$this->_leftMargin,
 									$this->_yPosition);
 				
 							$this->_yPosition -= $noteLineHeight;
 							$nbLigne ++;
 							$j++;
 						}
 					}
 						
 					if($j == $fin){
 						$tableauTemoinLimitePdf2 [2] [1] = 0;
 						$tableauTemoinLimitePdf2 [2] [2] = 'tout est affiche';
 					}else{
 						$tableauTemoinLimitePdf2 [2] [1] = 1;
 						$tableauTemoinLimitePdf2 [2] [2] = $j;
 						$tableauTemoinLimitePdf2 [2] [3] = $fin;
 						$tableauTemoinLimitePdf2 [2] [4] = 'il reste '.($fin - $j).' ligne(s) a afficher';
 						$suitePageRpu = 1;
 					}
 				}else{
 					$tableauTemoinLimitePdf2 [2] [1] = 0;
 					$tableauTemoinLimitePdf2 [2] [2] = 'donnees deja affichees';
 				}
 			}else{
 				$tableauTemoinLimitePdf2 [2] [1] = 0;
 				$tableauTemoinLimitePdf2 [2] [2] = 'pas de donnees';
 			}
 				
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
 		if($tableauTemoinLimite[3] == 1){
 			if($tab_examens_complementaires){

 				$debut = $tableauTemoinInfos[3][0];
 				$fin = $tableauTemoinInfos[3][1];
 					
 				if($debut < $fin){
 					if($debut == 0){
 						if($nbLigne < $maxLigne){
 							if($nbLigne == 30){
 								$nbLigne++;
 							}else{
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
 								$nbLigne ++;
 							}
 						}
 					}
 					
 					$j = $debut;
 					for($i = $debut ; $i < $fin ; $i++){
 						if($nbLigne < $maxLigne){
 							$this->_page->setlineColor(new ZendPdf\Color\Html('#efefef'));
 							$this->_page->setLineWidth(0.5);
 							$this->_page->drawLine($this->_leftMargin,
 									$this->_yPosition,
 									$this->_pageWidth -
 									$this->_leftMargin,
 									$this->_yPosition);
 				
 							$this->_page->setFont($this->_policeContenu, 11);
 							$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $tab_examens_complementaires[$i] ),
 									$this->_leftMargin,
 									$this->_yPosition);
 				
 							$this->_yPosition -= $noteLineHeight;
 							$nbLigne ++;
 							$j++;
 						}
 					}
 						
 					if($j == $fin){
 						$tableauTemoinLimitePdf2 [3] [1] = 0;
 						$tableauTemoinLimitePdf2 [3] [2] = 'tout est affiche';
 					}else{
 						$tableauTemoinLimitePdf2 [3] [1] = 1;
 						$tableauTemoinLimitePdf2 [3] [2] = $j;
 						$tableauTemoinLimitePdf2 [3] [3] = $fin;
 						$tableauTemoinLimitePdf2 [3] [4] = 'il reste '.($fin - $j).' ligne(s) a afficher';
 						$suitePageRpu = 1;
 					}
 				}else{
 					$tableauTemoinLimitePdf2 [3] [1] = 0;
 					$tableauTemoinLimitePdf2 [3] [2] = 'donnees deja affichees';
 				}
 			}else{
 				$tableauTemoinLimitePdf2 [3] [1] = 0;
 				$tableauTemoinLimitePdf2 [3] [2] = 'pas de donnees';
 			}
 				
 		}
 		//Fin Gestion des examens complémentaires
 		//Fin Gestion des examens complémentaires
 		 	
 		
 		/**
 		 * **************************************************************************************
 		 * ======================================================================================
 		 * **************************************************************************************
 		 */
 			
 		//************* Gestion des traitements ************
 		//************* Gestion des traitements ************
 		if($tableauTemoinLimite[4] == 1){
 		
 			if($tab_traitement){
 				
 				$debut = $tableauTemoinInfos[4][0];
 				$fin = $tableauTemoinInfos[4][1];
 					
 				if($debut < $fin){
 					if($debut == 0){
 						if($nbLigne < $maxLigne){
 							if($nbLigne == 30){
 								$nbLigne++;
 							}else{
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
 								$nbLigne ++;
 							}
 						}
 					}
 						
 					$j = $debut;
 					for($i = $debut ; $i < $fin ; $i++){
 						if($nbLigne < $maxLigne){
 							$this->_page->setlineColor(new ZendPdf\Color\Html('#efefef'));
 							$this->_page->setLineWidth(0.5);
 							$this->_page->drawLine($this->_leftMargin,
 									$this->_yPosition,
 									$this->_pageWidth -
 									$this->_leftMargin,
 									$this->_yPosition);
 				
 							$this->_page->setFont($this->_policeContenu, 11);
 							$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $tab_traitement[$i] ),
 									$this->_leftMargin,
 									$this->_yPosition);
 				
 							$this->_yPosition -= $noteLineHeight;
 							$nbLigne ++;
 							$j++;
 						}
 					}
 						
 					if($j == $fin){
 						$tableauTemoinLimitePdf2 [4] [1] = 0;
 						$tableauTemoinLimitePdf2 [4] [2] = 'tout est affiche';
 					}else{
 						$tableauTemoinLimitePdf2 [4] [1] = 1;
 						$tableauTemoinLimitePdf2 [4] [2] = $j;
 						$tableauTemoinLimitePdf2 [4] [3] = $fin;
 						$tableauTemoinLimitePdf2 [4] [4] = 'il reste '.($fin - $j).' ligne(s) a afficher';
 						$suitePageRpu = 1;
 					}
 				}else{
 					$tableauTemoinLimitePdf2 [4] [1] = 0;
 					$tableauTemoinLimitePdf2 [4] [2] = 'donnees deja affichees';
 				}
 			}else{
 				$tableauTemoinLimitePdf2 [4] [1] = 0;
 				$tableauTemoinLimitePdf2 [4] [2] = 'pas de donnees';
 			}
 			
 		}
 		//Fin Gestion des traitements
 		//Fin Gestion des traitements
 		
 		//var_dump($tableauTemoinLimitePdf2); exit();
 		/**
 		 * **************************************************************************************
 		 * ======================================================================================
 		 * **************************************************************************************
 		 */
 		
 		//************* Gestion des résultats des examens complémentaires ************
 		//************* Gestion des résultats des examens complémentaires ************
 		if($tableauTemoinLimite[5] == 1){
 			
 			if($tab_resultats_examens_complementaires){
 				$debut = $tableauTemoinInfos[5][0];
 				$fin = $tableauTemoinInfos[5][1];
 				
 				if($debut < $fin){
 					if($debut == 0){
 						if($nbLigne < $maxLigne){
 							if($nbLigne == 30){
 								$nbLigne++;
 							}else{
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
 								$nbLigne ++;
 							}
 						}
 					}
 				
 					$j = $debut;
 					for($i = $debut ; $i < $fin ; $i++){
 						if($nbLigne < $maxLigne){
 							$this->_page->setlineColor(new ZendPdf\Color\Html('#efefef'));
 							$this->_page->setLineWidth(0.5);
 							$this->_page->drawLine($this->_leftMargin,
 									$this->_yPosition,
 									$this->_pageWidth -
 									$this->_leftMargin,
 									$this->_yPosition);
 				
 							$this->_page->setFont($this->_policeContenu, 11);
 							$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $tab_resultats_examens_complementaires[$i] ),
 									$this->_leftMargin,
 									$this->_yPosition);
 				
 							$this->_yPosition -= $noteLineHeight;
 							$nbLigne ++;
 							$j++;
 						}
 					}
 						
 					if($j == $fin){
 						$tableauTemoinLimitePdf2 [5] [1] = 0;
 						$tableauTemoinLimitePdf2 [5] [2] = 'tout est affiche';
 					}else{
 						$tableauTemoinLimitePdf2 [5] [1] = 1;
 						$tableauTemoinLimitePdf2 [5] [2] = $j;
 						$tableauTemoinLimitePdf2 [5] [3] = $fin;
 						$tableauTemoinLimitePdf2 [5] [4] = 'il reste '.($fin - $j).' ligne(s) a afficher';
 						$suitePageRpu = 1;
 					}
 				}else{
 					$tableauTemoinLimitePdf2 [5] [1] = 0;
 					$tableauTemoinLimitePdf2 [5] [2] = 'donnees deja affichees';
 				}
 				
 			}else{
 				$tableauTemoinLimitePdf2 [5] [1] = 0;
 				$tableauTemoinLimitePdf2 [5] [2] = 'pas de donnees';
 			}
 				
 		}
 		//Fin Gestion des résultats des examens complémentaires
 		//Fin Gestion des résultats des examens complémentaires
 		
		/**
		 * **************************************************************************************
		 * ======================================================================================
		 * **************************************************************************************
		 */
		//var_dump($tableauTemoinLimite); exit();
		//************* Gestion des mutations ************
		//************* Gestion des mutations ************
		if($tableauTemoinLimite[6] == 1){
		
			if($donnee_mutation != ''){
				if($nbLigne < $maxLigne){
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
					$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $donnee_mutation),
							$this->_leftMargin+83,
							$this->_yPosition);
					
					$this->_yPosition -= $noteLineHeight;
					$nbLigne ++;
					$tableauTemoinLimitePdf2 [6] [1] = 0;
					$tableauTemoinLimitePdf2 [6] [2] = 'donnee deja afficher';
				}else{
 					$tableauTemoinLimitePdf2 [6] [1] = 1;
 					$tableauTemoinLimitePdf2 [6] [2] = 'il y a une ligne a afficher';
 				}
			}else{
 				$tableauTemoinLimitePdf2 [6] [1] = 0;
 				$tableauTemoinLimitePdf2 [6] [2] = 'il y a aucune donnee afficher';
 			}
		
		}
		//Fin Gestion des mutations
		//Fin Gestion des mutations
 		
		/**
		 * **************************************************************************************
		 * ======================================================================================
		 * **************************************************************************************
		 */
		
		//************* Gestion des mise à jours 1 ************
		//************* Gestion des mise à jours 1 ************
		if($tableauTemoinLimite[7] == 1){
		
			if($donnee_mise_a_jour_1 != ''){
				if($nbLigne < $maxLigne){
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
					$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $donnee_mise_a_jour_1),
							$this->_leftMargin+83,
							$this->_yPosition);
					
					$this->_yPosition -= $noteLineHeight;
					$nbLigne ++;
					$tableauTemoinLimitePdf2 [7] [1] = 0;
					$tableauTemoinLimitePdf2 [7] [2] = 'donnee deja afficher';
				}else{
 					$tableauTemoinLimitePdf2 [7] [1] = 1;
 					$tableauTemoinLimitePdf2 [7] [2] = 'il y a une ligne a afficher';
 				}
			}else{
 				$tableauTemoinLimitePdf2 [7] [1] = 0;
 				$tableauTemoinLimitePdf2 [7] [2] = 'il y a aucune donnee afficher';
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
		if($tableauTemoinLimite[8] == 1){
		
			if($donnee_mise_a_jour_2 != ''){
				if($nbLigne < $maxLigne){
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
					$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $donnee_mise_a_jour_2),
							$this->_leftMargin+83,
							$this->_yPosition);
					
					$this->_yPosition -= $noteLineHeight;
					$nbLigne ++;
					$tableauTemoinLimitePdf2 [8] [1] = 0;
					$tableauTemoinLimitePdf2 [8] [2] = 'donnee deja afficher';
				}else{
 					$tableauTemoinLimitePdf2 [8] [1] = 1;
 					$tableauTemoinLimitePdf2 [8] [2] = 'il y a une ligne a afficher';
 				}
			}else{
 				$tableauTemoinLimitePdf2 [8] [1] = 0;
 				$tableauTemoinLimitePdf2 [8] [2] = 'il y a aucune donnee afficher';
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
 		if($tableauTemoinLimite[9] == 1){
 			
 			if($donnee_mise_a_jour_3 != ''){
 				if($nbLigne < $maxLigne){
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
 					$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $donnee_mise_a_jour_3),
 							$this->_leftMargin+83,
 							$this->_yPosition);
 					
 					$this->_yPosition -= $noteLineHeight;
 					$nbLigne ++;
 					$tableauTemoinLimitePdf2 [9] [1] = 0;
 					$tableauTemoinLimitePdf2 [9] [2] = 'donnee deja afficher';
 				}else{
 					$tableauTemoinLimitePdf2 [9] [1] = 1;
 					$tableauTemoinLimitePdf2 [9] [2] = 'il y a une ligne a afficher';
 				}
 			}else{
 				$tableauTemoinLimitePdf2 [9] [1] = 0;
 				$tableauTemoinLimitePdf2 [9] [2] = 'il y a aucune donnee afficher';
 			}				

 		}
 		//var_dump($tableauTemoinLimitePdf2); exit();
 		//Fin Gestion des mise à jour 3
 		//Fin Gestion des mise à jour 3
 		
 		/**
 		 * **************************************************************************************
 		 * ======================================================================================
 		 * **************************************************************************************
 		 */
 		
 		//************* Gestion du texte de l'Avis du spécialiste
 		//************* Gestion du texte de l'Avis du spécialiste
 			
 		if($tableauTemoinLimite[10] == 1){
 			if($tab_avis_specialiste){
 				$debut = $tableauTemoinInfos[10][0];
 				$fin = $tableauTemoinInfos[10][1];
 				
 				if($debut < $fin){
 					if($debut == 0){
 						if($nbLigne < $maxLigne ){
 							if($nbLigne == 30){
 								$nbLigne++;
 							}else{
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
 								$nbLigne ++;
 							}
 						}
 					}
 				
 					$j = $debut;
 					for($i = $debut ; $i < $fin ; $i++){
 						if($nbLigne < $maxLigne){
 							$this->_page->setlineColor(new ZendPdf\Color\Html('#efefef'));
 							$this->_page->setLineWidth(0.5);
 							$this->_page->drawLine($this->_leftMargin,
 									$this->_yPosition,
 									$this->_pageWidth -
 									$this->_leftMargin,
 									$this->_yPosition);
 				
 							$this->_page->setFont($this->_policeContenu, 11);
 							$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $tab_avis_specialiste[$i] ),
 									$this->_leftMargin,
 									$this->_yPosition);
 				
 							$this->_yPosition -= $noteLineHeight;
 							$nbLigne ++;
 							$j++;
 						}
 					}
 					
 					if($j == $fin){
 						$tableauTemoinLimitePdf2 [10] [1] = 0;
 						$tableauTemoinLimitePdf2 [10] [2] = 'tout est affiche';
 					}else{
 						$tableauTemoinLimitePdf2 [10] [1] = 1;
 						$tableauTemoinLimitePdf2 [10] [2] = $j;
 						$tableauTemoinLimitePdf2 [10] [3] = $fin;
 						$tableauTemoinLimitePdf2 [10] [4] = 'il reste '.($fin - $j).' ligne(s) a afficher';
 						$suitePageRpu = 1;
 					}
 					
 				}else{
 					$tableauTemoinLimitePdf2 [10] [1] = 0;
 					$tableauTemoinLimitePdf2 [10] [2] = 'donnees deja affichees';
 				}
 			}else{
 				$tableauTemoinLimitePdf2 [10] [1] = 0;
 				$tableauTemoinLimitePdf2 [10] [2] = 'pas de donnees';
 			}
 			
 		}
 		//Fin Gestion Avis du spécialiste
 		//Fin Gestion Avis du spécialiste
 		
 		
 		$this->setTableauTemoinLimitePage2($tableauTemoinLimitePdf2);
 		
 		
 		$this->setSuitePageRpu($suitePageRpu);
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
		
		$imageHeader = ZendPdf\Image::imageWithPath($tabURI[0].'public/images_icons/number-two_r.png');
		$this->_page->drawImage($imageHeader,
				$this->_leftMargin + 220, //-x1
				47, //-y1
				290, //+x2
				65); //+y2
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
	
	
}