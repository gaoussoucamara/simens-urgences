<?php
namespace Consultation\View\Helpers;

use ZendPdf;
use ZendPdf\Page;
use ZendPdf\Font;
use Consultation\Model\Consultation;
use Urgence\View\Helper\DateHelper;

class RpuTraumatologiePdf2
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
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	protected $_textCoteDominant;
	protected $_entrerCoteDominant;
	protected $_textDateHeure;
	protected $_entrerDateHeure;
	protected $_textCirconstances;
	protected $_entrerCirconstances;
	protected $_listeCirconstances;
	protected $_textAntecedentTrauma;
	protected $_entrerAntecedentTrauma;
	protected $_textExamenPhysique;
	protected $_entrerExamenPhysique;
	protected $_textExamenParaclinique;
	protected $_entrerExamenParaclinique;
	protected $_textMecanismes;
	protected $_entrerMecanismes;
	protected $_textIndication;
	protected $_entrerIndication;
	protected $_listeIndications;
	protected $_textDiagnostic;
	protected $_entrerDiagnostic;
	protected $_textConduiteATenir;
	protected $_entrerConduiteATenir;
	protected $_textModeSortie;
	protected $_entrerModeSortie;
	protected $_textRendezVous;
	protected $_entrerRendezVous;
	protected $_textSpecialiste;
	protected $_entrerSpecialiste;
	protected $_textConduiteATenirSpecialiste;
	protected $_entrerConduiteATenirSpecialiste;
	
	protected $_listeMecanismes;
	protected $_listeModeSortie;
	protected $_listeDiagnostics;
	
	
	public function setTextCoteDominant($textCoteDominant) {
		$this->_textCoteDominant = $textCoteDominant;
	}
	
	public function getTextCoteDominant(){
		return $this->_textCoteDominant;
	}
	
	public function setEntrerCoteDominant($entrerCoteDominant) {
		$this->_entrerCoteDominant = $entrerCoteDominant;
	}
	
	public function getEntrerCoteDominant(){
		return $this->_entrerCoteDominant;
	}
	
	public function setTextDateHeure($textDateHeure) {
		$this->_textDateHeure = $textDateHeure;
	}
	
	public function getTextDateHeure(){
		return $this->_textDateHeure;
	}
	
	public function setEntrerDateHeure($entrerDateHeure) {
		$this->_entrerDateHeure = $entrerDateHeure;
	}
	
	public function getEntrerDateHeure(){
		return $this->_entrerDateHeure;
	}
	
	public function setTextCirconstances($textCirconstances) {
		$this->_textCirconstances = $textCirconstances;
	}
	
	public function getTextCirconstances(){
		return $this->_textCirconstances;
	}
	
	public function setEntrerCirconstances($entrerCirconstances) {
		$this->_entrerCirconstances = $entrerCirconstances;
	}
	
	public function getEntrerCirconstances(){
		return $this->_entrerCirconstances;
	}
	
	public function setListeCirconstances($listeCirconstances){
		$this->_listeCirconstances = $listeCirconstances;
	}
	
	public function getListeCirconstances(){
		return $this->_listeCirconstances;
	}
	
	public function setListeMecanismes($listeMecanismes){
		$this->_listeMecanismes = $listeMecanismes;
	}
	
	public function getListeMecanismes(){
		return $this->_listeMecanismes;
	}
	
	public function setListeIndications($listeIndications){
		$this->_listeIndications = $listeIndications;
	}
	
	public function getListeIndications(){
		return $this->_listeIndications;
	}
	
	public function setListeDiagnostics($listeDiagnostics){
		$this->_listeDiagnostics = $listeDiagnostics;
	}
	
	public function getListeDiagnostics(){
		return $this->_listeDiagnostics;
	}
	
	public function setTextAntecedentTrauma($textAntecedentTrauma) {
		$this->_textAntecedentTrauma = $textAntecedentTrauma;
	}
	
	public function getTextAntecedentTrauma(){
		return $this->_textAntecedentTrauma;
	}
	
	public function setEntrerAntecedentTrauma($entrerAntecedentTrauma){
		$this->_entrerAntecedentTrauma = $entrerAntecedentTrauma;
	}
	
	Public function getEntrerAntecedentTrauma(){
		return $this->_entrerAntecedentTrauma;
	}
	
	public function setTextExamenPhysique($textExamenPhysique) {
		$this->_textExamenPhysique = $textExamenPhysique;
	}
	
	public function getTextExamenPhysique(){
		return $this->_textExamenPhysique;
	}
	
	public function setEntrerExamenPhysique($entrerExamenPhysique){
		$this->_entrerExamenPhysique = $entrerExamenPhysique;
	}
	
	Public function getEntrerExamenPhysique(){
		return $this->_entrerExamenPhysique;
	}
	
	public function setTextExamenParaclinique($textExamenParaclinique) {
		$this->_textExamenParaclinique = $textExamenParaclinique;
	}
	
	public function getTextExamenParaclinique(){
		return $this->_textExamenParaclinique;
	}
	
	public function setEntrerExamenParaclinique($entrerExamenParaclinique){
		$this->_entrerExamenParaclinique = $entrerExamenParaclinique;
	}
	
	Public function getEntrerExamenParaclinique(){
		return $this->_entrerExamenParaclinique;
	}
	
	public function setTextMecanismes($textMecanismes) {
		$this->_textMecanismes = $textMecanismes;
	}
	
	public function getTextMecanismes(){
		return $this->_textMecanismes;
	}
	
	public function setEntrerMecanismes($entrerMecanismes) {
		$this->_entrerMecanismes = $entrerMecanismes;
	}
	
	public function getEntrerMecanismes(){
		return $this->_entrerMecanismes;
	}
	
	public function setTextIndication($textIndication) {
		$this->_textIndication = $textIndication;
	}
	
	public function getTextIndication(){
		return $this->_textIndication;
	}
	
	public function setEntrerIndication($entrerIndication) {
		$this->_entrerIndication = $entrerIndication;
	}
	
	public function getEntrerIndication(){
		return $this->_entrerIndication;
	}
	
	public function setTextDiagnostic($textDiagnostic) {
		$this->_textDiagnostic = $textDiagnostic;
	}
	
	public function getTextDiagnostic(){
		return $this->_textDiagnostic;
	}
	
	public function setEntrerDiagnostic($entrerDiagnostic) {
		$this->_entrerDiagnostic = $entrerDiagnostic;
	}
	
	public function getEntrerDiagnostic(){
		return $this->_entrerDiagnostic;
	}
	
	public function setTextConduiteATenir($textConduiteATenir) {
		$this->_textConduiteATenir = $textConduiteATenir;
	}
	
	public function getTextConduiteATenir(){
		return $this->_textConduiteATenir;
	}
	
	public function setEntrerConduiteATenir($textConduiteATenir) {
		$this->_entrerConduiteATenir = $textConduiteATenir;
	}
	
	public function getEntrerConduiteATenir(){
		return $this->_entrerConduiteATenir;
	}
	
	public function setTextModeSortie($textModeSortie) {
		$this->_textModeSortie = $textModeSortie;
	}
	
	public function getTextModeSortie(){
		return $this->_textModeSortie;
	}
	
	public function setEntrerModeSortie($textModeSortie) {
		$this->_entrerModeSortie = $textModeSortie;
	}
	
	public function getEntrerModeSortie(){
		return $this->_entrerModeSortie;
	}
	
	public function setListeModeSortie($listeModeSortie){
		$this->_listeModeSortie = $listeModeSortie;
	}
	
	public function getListeModeSortie(){
		return $this->_listeModeSortie;
	}
	
	public function setTextRendezVous($textRendezVous) {
		$this->_textRendezVous = $textRendezVous;
	}
	
	public function getTextRendezVous(){
		return $this->_textRendezVous;
	}
	
	public function setEntrerRendezVous($textRendezVous) {
		$this->_entrerRendezVous = $textRendezVous;
	}
	
	public function getEntrerRendezVous(){
		return $this->_entrerRendezVous;
	}
	
	public function setTextSpecialiste($textSpecialiste) {
		$this->_textSpecialiste = $textSpecialiste;
	}
	
	public function getTextSpecialiste(){
		return $this->_textSpecialiste;
	}
	
	public function setEntrerSpecialiste($entrerSpecialiste) {
		$this->_entrerSpecialiste = $entrerSpecialiste;
	}
	
	public function getEntrerSpecialiste(){
		return $this->_entrerSpecialiste;
	}
	
	public function setTextConduiteATenirSpecialiste($textConduiteATenirSpecialiste) {
		$this->_textConduiteATenirSpecialiste = $textConduiteATenirSpecialiste;
	}
	
	public function getTextConduiteATenirSpecialiste(){
		return $this->_textConduiteATenirSpecialiste;
	}
	
	public function setEntrerConduiteATenirSpecialiste($entrerConduiteATenirSpecialiste) {
		$this->_entrerConduiteATenirSpecialiste = $entrerConduiteATenirSpecialiste;
	}
	
	public function getEntrerConduiteATenirSpecialiste(){
		return $this->_entrerConduiteATenirSpecialiste;
	}
	
	
	protected  function getNoteTC(){
		$Control = new DateHelper();
		$noteLineHeight = 22;
		$baseUrl = $_SERVER['SCRIPT_FILENAME'];
		$tabURI  = explode('public', $baseUrl);
	
		$nbLigne = 0;
		$maxLigne = 31;
		
		
 		$cote_dominant            = $this->getTextCoteDominant();
 		$date_heure               = $this->getTextDateHeure();
 		$circonstances            = $this->getTextCirconstances();
 		$tab_antecedents_trauma   = $this->getTextAntecedentTrauma();
 		$tab_examen_physique      = $this->getTextExamenPhysique();
 		$tab_examen_paraclinique  = $this->getTextExamenParaclinique();
 		$tab_resultat_examen_complementaire  = $this->getTextResultatExamenComplementaire();
 		$mecanisme                = $this->getTextMecanismes();
 		$mecanisme_precision      = $this->_DonneesDemande['mecanismes_precision'];
 		$indication               = $this->getTextIndication();
 		$indication_precision     = $this->_DonneesDemande['indication_precision'];
 		$diagnostic               = $this->getTextDiagnostic();
 		$diagnostic_precision     = $this->_DonneesDemande['diagnostic_precision'];
 		$conduite_a_tenir         = $this->getTextConduiteATenir();
 		$mode_sortie              = $this->getTextModeSortie();
 		$rendez_vous_dans         = $this->getTextRendezVous();
 		$avis_specialiste         = $this->getTextAvisSpecialiste();
 		$conduite_tenir_specialiste = $this->getTextConduiteATenirSpecialiste();        
 		

 		
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
 		//************* Gestion des cotés dominants ************
 		//************* Gestion des cotés dominants ************
 		/*1)*/
 		if($tableauTemoinLimite[1] == 1){
 		
 			if($cote_dominant != ''){
 				if($nbLigne < $maxLigne){
 					$this->_page->setlineColor(new ZendPdf\Color\Html('#efefef'));
 					$this->_page->setLineWidth(0.5);
 					$this->_page->drawLine($this->_leftMargin,
 							$this->_yPosition,
 							$this->_pageWidth -
 							$this->_leftMargin,
 							$this->_yPosition);
 						
 					$this->_page->setFont($this->_newTimeGrasNonItalic, 12);
 					$this->_page->drawText('Côté dominant : ',
 							$this->_leftMargin,
 							$this->_yPosition);
 						
 					$this->_page->setlineColor(new ZendPdf\Color\Html('black'));
 					$this->_page->setLineWidth(0.5);
 					$this->_page->drawLine($this->_leftMargin,
 							$this->_yPosition-2,
 							$this->_pageWidth -
 							$this->_leftMargin-412,
 							$this->_yPosition-2);
 						
 					if($cote_dominant == 2){ $cote_dominant = 'Gauche'; }else{ $cote_dominant = 'Droite'; }
  					$this->_page->setFont($this->_policeContenu, 11);
  					$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $cote_dominant),
  							$this->_leftMargin+93,
  							$this->_yPosition);
 						
 					$this->_yPosition -= $noteLineHeight;
 					$nbLigne ++;
 					$tableauTemoinLimitePdf2 [1] [1] = 0;
 					$tableauTemoinLimitePdf2 [1] [2] = 'donnee deja afficher';
 				}else{
 					$tableauTemoinLimitePdf2 [1] [1] = 1;
 					$tableauTemoinLimitePdf2 [1] [2] = 'il y a une ligne a afficher';
 				}
 			}else{
 				$tableauTemoinLimitePdf2 [1] [1] = 0;
 				$tableauTemoinLimitePdf2 [1] [2] = 'il y a aucune donnee afficher';
 			}
 		
 		}
 		//Fin Gestion des cotés dominants
 		//Fin Gestion des cotés dominants
 		/**
 		 * **************************************************************************************
 		 * ======================================================================================
 		 * **************************************************************************************
 		 */
 		
 		//************* Gestion des dates et heure ************
 		//************* Gestion des dates et heure ************
 		/*2)*/
 		if($tableauTemoinLimite[2] == 1){
 				
 			if($date_heure != ''){
 				if($nbLigne < $maxLigne){
 					$this->_page->setlineColor(new ZendPdf\Color\Html('#efefef'));
 					$this->_page->setLineWidth(0.5);
 					$this->_page->drawLine($this->_leftMargin,
 							$this->_yPosition,
 							$this->_pageWidth -
 							$this->_leftMargin,
 							$this->_yPosition);
 						
 					$this->_page->setFont($this->_newTimeGrasNonItalic, 12);
 					$this->_page->drawText('Date & heure : ',
 							$this->_leftMargin,
 							$this->_yPosition);
 						
 					$this->_page->setlineColor(new ZendPdf\Color\Html('black'));
 					$this->_page->setLineWidth(0.5);
 					$this->_page->drawLine($this->_leftMargin,
 							$this->_yPosition-2,
 							$this->_pageWidth -
 							$this->_leftMargin-418,
 							$this->_yPosition-2);
 						
 					$this->_page->setFont($this->_policeContenu, 11);
 					$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $date_heure),
 							$this->_leftMargin+93,
 							$this->_yPosition);
 						
 					$this->_yPosition -= $noteLineHeight;
 					$nbLigne ++;
 					$tableauTemoinLimitePdf2 [2] [1] = 0;
 					$tableauTemoinLimitePdf2 [2] [2] = 'donnee deja afficher';
 				}else{
 					$tableauTemoinLimitePdf2 [2] [1] = 1;
 					$tableauTemoinLimitePdf2 [2] [2] = 'il y a une ligne a afficher';
 				}
 			}else{
 				$tableauTemoinLimitePdf2 [2] [1] = 0;
 				$tableauTemoinLimitePdf2 [2] [2] = 'il y a aucune donnee afficher';
 			}
 				
 		}
 		//Fin Gestion des dates et heure
 		//Fin Gestion des dates et heure
 		/**
 		 * **************************************************************************************
 		 * ======================================================================================
 		 * **************************************************************************************
 		 */
 		
 		//************* Gestion des circonstances ************
 		//************* Gestion des circonstances ************
 		/*3)*/
 		if($tableauTemoinLimite[3] == 1){
 				
 			if($circonstances != ''){
 				if($nbLigne < $maxLigne){
 					$this->_page->setlineColor(new ZendPdf\Color\Html('#efefef'));
 					$this->_page->setLineWidth(0.5);
 					$this->_page->drawLine($this->_leftMargin,
 							$this->_yPosition,
 							$this->_pageWidth -
 							$this->_leftMargin,
 							$this->_yPosition);
 						
 					$this->_page->setFont($this->_newTimeGrasNonItalic, 12);
 					$this->_page->drawText('Circonstances : ',
 							$this->_leftMargin,
 							$this->_yPosition);
 						
 					$this->_page->setlineColor(new ZendPdf\Color\Html('black'));
 					$this->_page->setLineWidth(0.5);
 					$this->_page->drawLine($this->_leftMargin,
 							$this->_yPosition-2,
 							$this->_pageWidth -
 							$this->_leftMargin-418,
 							$this->_yPosition-2);
 						
 					$this->_page->setFont($this->_policeContenu, 11);
 					$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $this->_listeCirconstances[$circonstances]),
 							$this->_leftMargin+93,
 							$this->_yPosition);
 						
 					$this->_yPosition -= $noteLineHeight;
 					$nbLigne ++;
 					$tableauTemoinLimitePdf2 [3] [1] = 0;
 					$tableauTemoinLimitePdf2 [3] [2] = 'donnee deja afficher';
 				}else{
 					$tableauTemoinLimitePdf2 [3] [1] = 1;
 					$tableauTemoinLimitePdf2 [3] [2] = 'il y a une ligne a afficher';
 				}
 			}else{
 				$tableauTemoinLimitePdf2 [3] [1] = 0;
 				$tableauTemoinLimitePdf2 [3] [2] = 'il y a aucune donnee afficher';
 			}
 				
 		}
 		//Fin Gestion des circonstances
 		//Fin Gestion des circonstances
 		
 		/**
 		 * **************************************************************************************
 		 * ======================================================================================
 		 * **************************************************************************************
 		 */
 		
 		//************* Gestion des antécédents traumatisme ************
 		//************* Gestion des antécédents traumatisme ************
 		/*4)*/
 		if($tableauTemoinLimite[4] == 1){
 		
 			if($tab_antecedents_trauma){
 				$debut = $tableauTemoinInfos[4][0];
 				$fin = $tableauTemoinInfos[4][1];
 		
 				if($debut < $fin){
 					if($debut == 0){
 						if($nbLigne < $maxLigne){
 							if($nbLigne == 30){
 								$nbLigne++;
 							}else{
 								$this->_page->setFont($this->_newTimeGrasNonItalic, 12);
 								$this->_page->drawText('Antécédent traumatisme : ',
 										$this->_leftMargin,
 										$this->_yPosition);
 									
 								$this->_page->setlineColor(new ZendPdf\Color\Html('black'));
 								$this->_page->setLineWidth(0.5);
 								$this->_page->drawLine($this->_leftMargin,
 										$this->_yPosition-2,
 										$this->_pageWidth -
 										$this->_leftMargin-363,
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
 							$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $tab_antecedents_trauma[$i] ),
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
 		//Fin Gestion des antécédents traumatisme
 		//Fin Gestion des antécédents traumatisme
 		
 		
 		/**
 		 * **************************************************************************************
 		 * ======================================================================================
 		 * **************************************************************************************
 		 */
 			
 		//************* Gestion des examens physiques ************
 		//************* Gestion des examens physiques ************
 		/*5)*/
 		if($tableauTemoinLimite[5] == 1){
 				
 			if($tab_examen_physique){
 				$debut = $tableauTemoinInfos[5][0];
 				$fin = $tableauTemoinInfos[5][1];
 					
 				if($debut < $fin){
 					if($debut == 0){
 						if($nbLigne < $maxLigne){
 							if($nbLigne == 30){
 								$nbLigne++;
 							}else{
 								$this->_page->setFont($this->_newTimeGrasNonItalic, 12);
 								$this->_page->drawText('Examen physique : ',
 										$this->_leftMargin,
 										$this->_yPosition);
 		
 								$this->_page->setlineColor(new ZendPdf\Color\Html('black'));
 								$this->_page->setLineWidth(0.5);
 								$this->_page->drawLine($this->_leftMargin,
 										$this->_yPosition-2,
 										$this->_pageWidth -
 										$this->_leftMargin-398,
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
 							$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $tab_examen_physique[$i] ),
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
 		//Fin Gestion des examens physiques
 		//Fin Gestion des examens physiques
 		
 		/**
 		 * **************************************************************************************
 		 * ======================================================================================
 		 * **************************************************************************************
 		 */
 		
 		//************* Gestion des examens paracliniques ************
 		//************* Gestion des examens paracliniques ************
 		/*6)*/
 		if($tableauTemoinLimite[6] == 1){
 				
 			if($tab_examen_paraclinique){
 				$debut = $tableauTemoinInfos[6][0];
 				$fin = $tableauTemoinInfos[6][1];
 		
 				if($debut < $fin){
 					if($debut == 0){
 						if($nbLigne < $maxLigne){
 							if($nbLigne == 30){
 								$nbLigne++;
 							}else{
 								$this->_page->setFont($this->_newTimeGrasNonItalic, 12);
 								$this->_page->drawText('Examen paraclinique : ',
 										$this->_leftMargin,
 										$this->_yPosition);
 									
 								$this->_page->setlineColor(new ZendPdf\Color\Html('black'));
 								$this->_page->setLineWidth(0.5);
 								$this->_page->drawLine($this->_leftMargin,
 										$this->_yPosition-2,
 										$this->_pageWidth -
 										$this->_leftMargin-378,
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
 							$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $tab_examen_paraclinique[$i] ),
 									$this->_leftMargin,
 									$this->_yPosition);
 								
 							$this->_yPosition -= $noteLineHeight;
 							$nbLigne ++;
 							$j++;
 						}
 					}
 						
 					if($j == $fin){
 						$tableauTemoinLimitePdf2 [6] [1] = 0;
 						$tableauTemoinLimitePdf2 [6] [2] = 'tout est affiche';
 					}else{
 						$tableauTemoinLimitePdf2 [6] [1] = 1;
 						$tableauTemoinLimitePdf2 [6] [2] = $j;
 						$tableauTemoinLimitePdf2 [6] [3] = $fin;
 						$tableauTemoinLimitePdf2 [6] [4] = 'il reste '.($fin - $j).' ligne(s) a afficher';
 						$suitePageRpu = 1;
 					}
 				}else{
 					$tableauTemoinLimitePdf2 [6] [1] = 0;
 					$tableauTemoinLimitePdf2 [6] [2] = 'donnees deja affichees';
 				}
 			}else{
 				$tableauTemoinLimitePdf2 [6] [1] = 0;
 				$tableauTemoinLimitePdf2 [6] [2] = 'pas de donnees';
 			}
 				
 		}
 		//Fin Gestion des examens paracliniques
 		//Fin Gestion des examens paracliniques
 			
 		/**
 		 * **************************************************************************************
 		 * ======================================================================================
 		 * **************************************************************************************
 		 */
 		
 		//************* Gestion de résultat examen complémentaire ************
 		//************* Gestion de résultat examen complémentaire ************
 		/*7)*/
 		if($tableauTemoinLimite[7] == 1){
 				
 			if($tab_resultat_examen_complementaire){
 				$debut = $tableauTemoinInfos[7][0];
 				$fin = $tableauTemoinInfos[7][1];
 					
 				if($debut < $fin){
 					if($debut == 0){
 						if($nbLigne < $maxLigne){
 							if($nbLigne == 30){
 								$nbLigne++;
 							}else{
 								$this->_page->setFont($this->_newTimeGrasNonItalic, 12);
 								$this->_page->drawText('Résultat examen complémentaire : ',
 										$this->_leftMargin,
 										$this->_yPosition);
 		
 								$this->_page->setlineColor(new ZendPdf\Color\Html('black'));
 								$this->_page->setLineWidth(0.5);
 								$this->_page->drawLine($this->_leftMargin,
 										$this->_yPosition-2,
 										$this->_pageWidth -
 										$this->_leftMargin-318,
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
 							$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $tab_resultat_examen_complementaire[$i] ),
 									$this->_leftMargin,
 									$this->_yPosition);
 								
 							$this->_yPosition -= $noteLineHeight;
 							$nbLigne ++;
 							$j++;
 						}
 					}
 						
 					if($j == $fin){
 						$tableauTemoinLimitePdf2 [7] [1] = 0;
 						$tableauTemoinLimitePdf2 [7] [2] = 'tout est affiche';
 					}else{
 						$tableauTemoinLimitePdf2 [7] [1] = 1;
 						$tableauTemoinLimitePdf2 [7] [2] = $j;
 						$tableauTemoinLimitePdf2 [7] [3] = $fin;
 						$tableauTemoinLimitePdf2 [7] [4] = 'il reste '.($fin - $j).' ligne(s) a afficher';
 						$suitePageRpu = 1;
 					}
 				}else{
 					$tableauTemoinLimitePdf2 [7] [1] = 0;
 					$tableauTemoinLimitePdf2 [7] [2] = 'donnees deja affichees';
 				}
 			}else{
 				$tableauTemoinLimitePdf2 [7] [1] = 0;
 				$tableauTemoinLimitePdf2 [7] [2] = 'pas de donnees';
 			}
 				
 		}
 		//Fin Gestion de résultat examen complémentaire
 		//Fin Gestion de résultat examen complémentaire
 		
 		/**
 		 * **************************************************************************************
 		 * ======================================================================================
 		 * **************************************************************************************
 		 */
 		 	
 		//************* Gestion des mécanismes ************
 		//************* Gestion des mécanismes ************
 		/*8)*/
 		if($tableauTemoinLimite[8] == 1){
 				
 			if($mecanisme != ''){
 				if($nbLigne < $maxLigne){
 					$this->_page->setlineColor(new ZendPdf\Color\Html('#efefef'));
 					$this->_page->setLineWidth(0.5);
 					$this->_page->drawLine($this->_leftMargin,
 							$this->_yPosition,
 							$this->_pageWidth -
 							$this->_leftMargin,
 							$this->_yPosition);
 						
 					$this->_page->setFont($this->_newTimeGrasNonItalic, 12);
 					$this->_page->drawText('Mécanisme : ',
 							$this->_leftMargin,
 							$this->_yPosition);
 						
 					$this->_page->setlineColor(new ZendPdf\Color\Html('black'));
 					$this->_page->setLineWidth(0.5);
 					$this->_page->drawLine($this->_leftMargin,
 							$this->_yPosition-2,
 							$this->_pageWidth -
 							$this->_leftMargin-430,
 							$this->_yPosition-2);
 						
 					$this->_page->setFont($this->_policeContenu, 11);
 					$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $this->_listeMecanismes[$mecanisme].'   '.'  ( '.$mecanisme_precision.' )'),
 							$this->_leftMargin+80,
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
 		//Fin Gestion des mécanismes
 		//Fin Gestion des mécanismes
 			
 		/**
 		 * **************************************************************************************
 		 * ======================================================================================
 		 * **************************************************************************************
 		 */
 		 	
 		//************* Gestion des indications ************
 		//************* Gestion des indications ************
 		/*9)*/
 		if($tableauTemoinLimite[9] == 1){
 				
 			if($indication != ''){
 				if($nbLigne < $maxLigne){
 					$this->_page->setlineColor(new ZendPdf\Color\Html('#efefef'));
 					$this->_page->setLineWidth(0.5);
 					$this->_page->drawLine($this->_leftMargin,
 							$this->_yPosition,
 							$this->_pageWidth -
 							$this->_leftMargin,
 							$this->_yPosition);
 						
 					$this->_page->setFont($this->_newTimeGrasNonItalic, 12);
 					$this->_page->drawText('Indication : ',
 							$this->_leftMargin,
 							$this->_yPosition);
 						
 					$this->_page->setlineColor(new ZendPdf\Color\Html('black'));
 					$this->_page->setLineWidth(0.5);
 					$this->_page->drawLine($this->_leftMargin,
 							$this->_yPosition-2,
 							$this->_pageWidth -
 							$this->_leftMargin-435,
 							$this->_yPosition-2);
 						
 					$this->_page->setFont($this->_policeContenu, 11);
 					$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $this->_listeIndications[$indication].'   '.' ( '.$indication_precision.' )'),
 							$this->_leftMargin+80,
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
 		//Fin Gestion des indications
 		//Fin Gestion des indications
 		
 		/**
 		 * **************************************************************************************
 		 * ======================================================================================
 		 * **************************************************************************************
 		 */
 		 		
 		//************* Gestion des diagnostics ************
 		//************* Gestion des diagnostics ************
 		/*10)*/
 		if($tableauTemoinLimite[10] == 1){
 				
 			if($diagnostic != ''){
 				if($nbLigne < $maxLigne){
 					$this->_page->setlineColor(new ZendPdf\Color\Html('#efefef'));
 					$this->_page->setLineWidth(0.5);
 					$this->_page->drawLine($this->_leftMargin,
 							$this->_yPosition,
 							$this->_pageWidth -
 							$this->_leftMargin,
 							$this->_yPosition);
 						
 					$this->_page->setFont($this->_newTimeGrasNonItalic, 12);
 					$this->_page->drawText('Diagnostic : ',
 							$this->_leftMargin,
 							$this->_yPosition);
 						
 					$this->_page->setlineColor(new ZendPdf\Color\Html('black'));
 					$this->_page->setLineWidth(0.5);
 					$this->_page->drawLine($this->_leftMargin,
 							$this->_yPosition-2,
 							$this->_pageWidth -
 							$this->_leftMargin-435,
 							$this->_yPosition-2);
 						
 					$this->_page->setFont($this->_policeContenu, 11);
 					$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $this->_listeDiagnostics[$diagnostic].'   '.' ( '.$diagnostic_precision.' )'),
 							$this->_leftMargin+80,
 							$this->_yPosition);
 						
 					$this->_yPosition -= $noteLineHeight;
 					$nbLigne ++;
 					$tableauTemoinLimitePdf2 [10] [1] = 0;
 					$tableauTemoinLimitePdf2 [10] [2] = 'donnee deja afficher';
 				}else{
 					$tableauTemoinLimitePdf2 [10] [1] = 1;
 					$tableauTemoinLimitePdf2 [10] [2] = 'il y a une ligne a afficher';
 				}
 			}else{
 				$tableauTemoinLimitePdf2 [10] [1] = 0;
 				$tableauTemoinLimitePdf2 [10] [2] = 'il y a aucune donnee afficher';
 			}
 				
 		}
 		//Fin Gestion des diagnostics
 		//Fin Gestion des diagnostics
 			
 		/**
 		 * **************************************************************************************
 		 * ======================================================================================
 		 * **************************************************************************************
 		 */
 		//************* Gestion de conduite à tenir ************
 		//************* Gestion de conduite à tenir ************
 		/*11)*/
 		if($tableauTemoinLimite[11] == 1){
 				
 			if($conduite_a_tenir){
 				$debut = $tableauTemoinInfos[11][0];
 				$fin = $tableauTemoinInfos[11][1];
 		
 				if($debut < $fin){
 					if($debut == 0){
 						if($nbLigne < $maxLigne){
 							if($nbLigne == 30){
 								$nbLigne++;
 							}else{
 								$this->_page->setFont($this->_newTimeGrasNonItalic, 12);
 								$this->_page->drawText('Conduite à tenir : ',
 										$this->_leftMargin,
 										$this->_yPosition);
 									
 								$this->_page->setlineColor(new ZendPdf\Color\Html('black'));
 								$this->_page->setLineWidth(0.5);
 								$this->_page->drawLine($this->_leftMargin,
 										$this->_yPosition-2,
 										$this->_pageWidth -
 										$this->_leftMargin-405,
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
 							$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $conduite_a_tenir[$i] ),
 									$this->_leftMargin,
 									$this->_yPosition);
 								
 							$this->_yPosition -= $noteLineHeight;
 							$nbLigne ++;
 							$j++;
 						}
 					}
 						
 					if($j == $fin){
 						$tableauTemoinLimitePdf2 [11] [1] = 0;
 						$tableauTemoinLimitePdf2 [11] [2] = 'tout est affiche';
 					}else{
 						$tableauTemoinLimitePdf2 [11] [1] = 1;
 						$tableauTemoinLimitePdf2 [11] [2] = $j;
 						$tableauTemoinLimitePdf2 [11] [3] = $fin;
 						$tableauTemoinLimitePdf2 [11] [4] = 'il reste '.($fin - $j).' ligne(s) a afficher';
 						$suitePageRpu = 1;
 					}
 				}else{
 					$tableauTemoinLimitePdf2 [11] [1] = 0;
 					$tableauTemoinLimitePdf2 [11] [2] = 'donnees deja affichees';
 				}
 			}else{
 				$tableauTemoinLimitePdf2 [11] [1] = 0;
 				$tableauTemoinLimitePdf2 [11] [2] = 'pas de donnees';
 			}
 				
 		}
 		//Fin Gestion de conduite à tenir
 		//Fin Gestion de conduite à tenir
 			
 		/**
 		 * **************************************************************************************
 		 * ======================================================================================
 		 * **************************************************************************************
 		 */
 		
 		//************* Gestion de mode de sortie ************
 		//************* Gestion de mode de sortie ************
 		/*12)*/
 		if($tableauTemoinLimite[12] == 1){
 				
 			if($mode_sortie != ''){
 				if($nbLigne < $maxLigne){
 					$this->_page->setlineColor(new ZendPdf\Color\Html('#efefef'));
 					$this->_page->setLineWidth(0.5);
 					$this->_page->drawLine($this->_leftMargin,
 							$this->_yPosition,
 							$this->_pageWidth -
 							$this->_leftMargin,
 							$this->_yPosition);
 						
 					$this->_page->setFont($this->_newTimeGrasNonItalic, 12);
 					$this->_page->drawText('Mode de sortie : ',
 							$this->_leftMargin,
 							$this->_yPosition);
 						
 					$this->_page->setlineColor(new ZendPdf\Color\Html('black'));
 					$this->_page->setLineWidth(0.5);
 					$this->_page->drawLine($this->_leftMargin,
 							$this->_yPosition-2,
 							$this->_pageWidth -
 							$this->_leftMargin-413,
 							$this->_yPosition-2);
 						
 					$this->_page->setFont($this->_policeContenu, 11);
 					$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $this->_listeModeSortie[$mode_sortie]),
 							$this->_leftMargin+93,
 							$this->_yPosition);
 						
 					$this->_yPosition -= $noteLineHeight;
 					$nbLigne ++;
 					$tableauTemoinLimitePdf2 [12] [1] = 0;
 					$tableauTemoinLimitePdf2 [12] [2] = 'donnee deja afficher';
 				}else{
 					$tableauTemoinLimitePdf2 [12] [1] = 1;
 					$tableauTemoinLimitePdf2 [12] [2] = 'il y a une ligne a afficher';
 				}
 			}else{
 				$tableauTemoinLimitePdf2 [12] [1] = 0;
 				$tableauTemoinLimitePdf2 [12] [2] = 'il y a aucune donnee afficher';
 			}
 				
 		}
 		//Fin Gestion de mode de sortie
 		//Fin Gestion de mode de sortie
 		
 		/**
 		 * **************************************************************************************
 		 * ======================================================================================
 		 * **************************************************************************************
 		 */
 		 	
 		//************* Gestion des rendez-vous ************
 		//************* Gestion des rendez-vous ************
 		/*13)*/
 		if($tableauTemoinLimite[13] == 1){
 				
 			if($rendez_vous_dans != ''){
 				if($nbLigne < $maxLigne){
 					$this->_page->setlineColor(new ZendPdf\Color\Html('#efefef'));
 					$this->_page->setLineWidth(0.5);
 					$this->_page->drawLine($this->_leftMargin,
 							$this->_yPosition,
 							$this->_pageWidth -
 							$this->_leftMargin,
 							$this->_yPosition);
 						
 					$this->_page->setFont($this->_newTimeGrasNonItalic, 12);
 					$this->_page->drawText('Rendez-vous : ',
 							$this->_leftMargin,
 							$this->_yPosition);
 						
 					$this->_page->setlineColor(new ZendPdf\Color\Html('black'));
 					$this->_page->setLineWidth(0.5);
 					$this->_page->drawLine($this->_leftMargin,
 							$this->_yPosition-2,
 							$this->_pageWidth -
 							$this->_leftMargin-423,
 							$this->_yPosition-2);
 						
 					$this->_page->setFont($this->_policeContenu, 11);
 					$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $rendez_vous_dans),
 							$this->_leftMargin+93,
 							$this->_yPosition);
 						
 					$this->_yPosition -= $noteLineHeight;
 					$nbLigne ++;
 					$tableauTemoinLimitePdf2 [13] [1] = 0;
 					$tableauTemoinLimitePdf2 [13] [2] = 'donnee deja afficher';
 				}else{
 					$tableauTemoinLimitePdf2 [13] [1] = 1;
 					$tableauTemoinLimitePdf2 [13] [2] = 'il y a une ligne a afficher';
 				}
 			}else{
 				$tableauTemoinLimitePdf2 [13] [1] = 0;
 				$tableauTemoinLimitePdf2 [13] [2] = 'il y a aucune donnee afficher';
 			}
 				
 		}
 		//Fin Gestion des rendez-vous
 		//Fin Gestion des rendez-vous
 			
 		/**
 		 * **************************************************************************************
 		 * ======================================================================================
 		 * **************************************************************************************
 		 */
 		 		
 		//************* Gestion de l'avis du spécialiste ************
 		//************* Gestion de l'avis du spécialiste ************
 		/*14)*/
 		
 		if($tableauTemoinLimite[14] == 1){
 				
 			if($avis_specialiste){
 				$debut = $tableauTemoinInfos[14][0];
 				$fin = $tableauTemoinInfos[14][1];
 				
 				if($debut < $fin){
 					if($debut == 0){
 						if($nbLigne < $maxLigne){
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
 							$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $avis_specialiste[$i] ),
 									$this->_leftMargin,
 									$this->_yPosition);
 								
 							$this->_yPosition -= $noteLineHeight;
 							$nbLigne ++;
 							$j++;
 						}
 					}
 						
 					if($j == $fin){
 						$tableauTemoinLimitePdf2 [14] [1] = 0;
 						$tableauTemoinLimitePdf2 [14] [2] = 'tout est affiche';
 					}else{
 						$tableauTemoinLimitePdf2 [14] [1] = 1;
 						$tableauTemoinLimitePdf2 [14] [2] = $j;
 						$tableauTemoinLimitePdf2 [14] [3] = $fin;
 						$tableauTemoinLimitePdf2 [14] [4] = 'il reste '.($fin - $j).' ligne(s) a afficher';
 						$suitePageRpu = 1;
 					}
 				}else{
 					$tableauTemoinLimitePdf2 [14] [1] = 0;
 					$tableauTemoinLimitePdf2 [14] [2] = 'donnees deja affichees';
 				}
 			}else{
 				$tableauTemoinLimitePdf2 [14] [1] = 0;
 				$tableauTemoinLimitePdf2 [14] [2] = 'pas de donnees';
 			}
 				
 		}
 		//Fin Gestion de l'avis du spécialiste
 		//Fin Gestion de l'avis du spécialiste
 		
 		/**
 		 * **************************************************************************************
 		 * ======================================================================================
 		 * **************************************************************************************
 		 */
 		
 		//************* Gestion de la conduite à tenir ************
 		//************* Gestion de la conduite à tenir ************
 		/*15)*/
 			
 		if($tableauTemoinLimite[15] == 1){
 				
 			if($conduite_tenir_specialiste){
 				$debut = $tableauTemoinInfos[15][0];
 				$fin = $tableauTemoinInfos[15][1];
 					
 				if($debut < $fin){
 					if($debut == 0){
 						if($nbLigne < $maxLigne){
 							if($nbLigne == 30){
 								$nbLigne++;
 							}else{
 								$this->_page->setFont($this->_newTimeGrasNonItalic, 12);
 								$this->_page->drawText('Conduite à tenir ( spécialiste ) : ',
 										$this->_leftMargin,
 										$this->_yPosition);
 									
 								$this->_page->setlineColor(new ZendPdf\Color\Html('black'));
 								$this->_page->setLineWidth(0.5);
 								$this->_page->drawLine($this->_leftMargin,
 										$this->_yPosition-2,
 										$this->_pageWidth -
 										$this->_leftMargin-335,
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
 							$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $conduite_tenir_specialiste[$i] ),
 									$this->_leftMargin,
 									$this->_yPosition);
 								
 							$this->_yPosition -= $noteLineHeight;
 							$nbLigne ++;
 							$j++;
 						}
 					}
 						
 					if($j == $fin){
 						$tableauTemoinLimitePdf2 [15] [1] = 0;
 						$tableauTemoinLimitePdf2 [15] [2] = 'tout est affiche';
 					}else{
 						$tableauTemoinLimitePdf2 [15] [1] = 1;
 						$tableauTemoinLimitePdf2 [15] [2] = $j;
 						$tableauTemoinLimitePdf2 [15] [3] = $fin;
 						$tableauTemoinLimitePdf2 [15] [4] = 'il reste '.($fin - $j).' ligne(s) a afficher';
 						$suitePageRpu = 1;
 					}
 				}else{
 					$tableauTemoinLimitePdf2 [15] [1] = 0;
 					$tableauTemoinLimitePdf2 [15] [2] = 'donnees deja affichees';
 				}
 			}else{
 				$tableauTemoinLimitePdf2 [15] [1] = 0;
 				$tableauTemoinLimitePdf2 [15] [2] = 'pas de donnees';
 			}
 				
 		}
 		//Fin Gestion de la conduite à tenir
 		//Fin Gestion de la conduite à tenir
 			
 		/**
 		 * **************************************************************************************
 		 * ======================================================================================
 		 * **************************************************************************************
 		 */
 		
 		
 		 	
 		
 		
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