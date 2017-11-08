<?php
namespace Consultation\View\Helpers;

use ZendPdf;
use ZendPdf\Page;
use ZendPdf\Font;
use Consultation\Model\Consultation;
use Urgence\View\Helper\DateHelper; 


class RpuTraumatologiePdf
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
	protected $_listeMecanismes;
	
	
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
	protected $_listeDiagnostics;
	protected $_textConduiteATenir;
	protected $_entrerConduiteATenir;
	protected $_textModeSortie;
	protected $_entrerModeSortie;
	protected $_listeModeSortie;
	protected $_textRendezVous;
	protected $_entrerRendezVous;
	protected $_textSpecialiste;
	protected $_entrerSpecialiste;
	protected $_textConduiteATenirSpecialiste;
	protected $_entrerConduiteATenirSpecialiste;

	
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
 		

 		//******
 		$resume_syndromique = $this->_DonneesDemande['resume_syndromique'];
 		//******
 		$cote_dominant        = $this->_DonneesDemande['cote_dominant'];
 		$date_heure           = $this->_DonneesDemande['date_heure'];
 		$circonstances        = $this->_DonneesDemande['circonstances'];
 		$antecedent           = $this->_DonneesDemande['antecedent'];
 		$examen_physique      = $this->_DonneesDemande['examen_physique'];
 		$examen_paraclinique  = $this->_DonneesDemande['examen_paraclinique'];
 		$resultat_examen_complementaire    = $this->_DonneesDemande['resultat_examen_complementaire'];
 		$mecanismes           = $this->_DonneesDemande['mecanismes'];
 		$mecanismes_precision = $this->_DonneesDemande['mecanismes_precision'];
 		$indication           = $this->_DonneesDemande['indication'];
 		$indication_precision = $this->_DonneesDemande['indication_precision'];
 		$diagnostic           = $this->_DonneesDemande['diagnostic'];
 		$diagnostic_precision = $this->_DonneesDemande['diagnostic_precision'];
 		$conduite             = $this->_DonneesDemande['conduite'];
 		$mode_sortie         = $this->_DonneesDemande['motif_sortie'];
 		$rendez_vous          = $this->_DonneesDemande['rendez_vous'];
 		$specialiste_trauma   = $this->_DonneesDemande['specialiste_trauma'];
 		$conduite_specialiste = $this->_DonneesDemande['conduite_specialiste'];
 		
 		
 		
 		$TableauAntecedent            = explode( "\n" , $antecedent);
 		$TableauExamensPhysiques      = explode( "\n" , $examen_physique);
 		$TableauExamenParaclinique    = explode( "\n" , $examen_paraclinique);
 		$TableauResultatExamenComplementaire  = explode( "\n" , $resultat_examen_complementaire);
 		$TableauConduiteATenir  = explode( "\n" , $conduite);
 		$TableauSpecialiste     = explode( "\n" , $specialiste_trauma);
 		$TableauConduiteATenirSpecialiste  = explode( "\n" , $conduite_specialiste);
 		
 		
 		$tableauTemoinLimite = array(); for ($i = 1; $i < 16; $i++ ){ $tableauTemoinLimite[$i] = 1; }
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
 		
  		/**
  		 * **************************************************************************************
  		 * ======================================================================================
  		 * **************************************************************************************
  		 */
 		$maxLigne = 23;
 		if($temoinMotifCons2 == 1){ $maxLigne--; }
  			
  		//************* Gestion du coté dominant ************
  		//************* Gestion du coté dominant ************
  		
  		$this->setTextCoteDominant($cote_dominant);
  		if($cote_dominant != ''){ $NbTotaleLigne ++; }
  		
  		/*1)*/
  		if($nbLigne < $maxLigne){
  				
  			if($cote_dominant != ''){
  		
  				if($nbLigne < 19){
  					
  					$tableauTemoinLimite [1] = 0;
  					
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
  					$nbLigne++;
  					
  					$tableauTemoinInfos [1][] = 1;
  					$tableauTemoinInfos [1][] = 1;
  				}
  					
  			}
  			else{
  				if($nbLigne < 19){
  					$tableauTemoinLimite [1] = 0;
  				}
  				
  			}
  		
  		}else{
  			//var_dump('test 1'); exit();
  		}
  			
  		//Fin Gestion du coté dominant
  		//Fin Gestion du coté dominant
  		
  		/**
  		 * **************************************************************************************
  		 * ======================================================================================
  		 * **************************************************************************************
  		 */
  		
  			
  		//************* Gestion des date et heure ************
  		//************* Gestion des date et heure ************
  		
  		$this->setTextDateHeure($date_heure);
  		if($date_heure != ''){ $NbTotaleLigne ++; }
  		
  		/*2)*/
  		if($nbLigne < $maxLigne){
  		
  			if($cote_dominant != ''){
  		
  				if($nbLigne < 19){
  					$tableauTemoinLimite [1] = 0;
  					$tableauTemoinLimite [2] = 0;
  						
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
  					$nbLigne++;
  						
  					$tableauTemoinInfos [2][] = 1;
  					$tableauTemoinInfos [2][] = 1;
  				}
  					
  			}
  			else{
  				if($nbLigne < 19){
  					$tableauTemoinLimite [1] = 0;
  					$tableauTemoinLimite [2] = 0;
  				}
  		
  			}
  		
  		}else{
  			//var_dump('test 1'); exit();
  		}
  			
  		//Fin Gestion des date et heure
  		//Fin Gestion des date et heure
 		 	
  		
  		/**
  		 * **************************************************************************************
  		 * ======================================================================================
  		 * **************************************************************************************
  		 */
  		
  			
  		//************* Gestion des circonstances ************
  		//************* Gestion des circonstances ************
  		
  		$this->setTextCirconstances($circonstances);
  		if($circonstances != ''){ $NbTotaleLigne ++; }
  		
  		/*3)*/
  		if($nbLigne < $maxLigne){
  		
  			if($circonstances != ''){
  		
  				if($nbLigne < 19){
  					$tableauTemoinLimite [2] = 0;
  					$tableauTemoinLimite [3] = 0;
  		
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
  					$nbLigne++;
  		
  					$tableauTemoinInfos [3][] = 1;
  					$tableauTemoinInfos [3][] = 1;
  				}
  					
  			}
  			else{
  				if($nbLigne < 19){
  					$tableauTemoinLimite [2] = 0;
  					$tableauTemoinLimite [3] = 0;
  				}
  		
  			}
  		
  		}else{
  			//var_dump('test 1'); exit();
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
  		
  		//Préparation du texte des antécédents traumatisme
  		//Préparation du texte des antécédents traumatisme
  		
  		 $TextAntecedentsTrauma = array();
  		for($i = 0 ; $i < count($TableauAntecedent) ; $i++){

  			if( strlen($TableauAntecedent[$i]) > 106 ){
  			
  				$textDecouper = wordwrap($TableauAntecedent[$i], 106, "\n", false); // On découpe le texte
  				$textDecouperTab = explode( "\n" ,$textDecouper); // On le place dans un tableau
  			
  				for($j = 0 ; $j < count($textDecouperTab) ; $j++){
  					if(rtrim($textDecouperTab[$j]) != ''){
  						$TextAntecedentsTrauma[] = $textDecouperTab[$j];
  					}
  				}
  			
  			}else{
  			
  				if(rtrim($TableauAntecedent[$i]) != ''){
  					$TextAntecedentsTrauma[] = $TableauAntecedent[$i];
  				}
  					
  			}
  			
  			
  		}
  			
  		$TextAntecedentsTrauma = $this->Justifier($TextAntecedentsTrauma, 110);
  		$this->setTextAntecedentTrauma($TextAntecedentsTrauma);
  		$NbTotaleLigne += count($TextAntecedentsTrauma);
  		
  		//Fin préparation du texte des antécédents traumatisme
  		//Fin préparation du texte des antécédents traumatisme
  		
  		/*4)*/
  		if($nbLigne < $maxLigne){
  		
  			if($TextAntecedentsTrauma){
  				if($TextAntecedentsTrauma[0]){
  					$maxLigne--;
  		
  					if($nbLigne < $maxLigne){
  							
  						$tableauTemoinLimite [3] = 0;
  						$tableauTemoinLimite [4] = 1;
  							
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
  		
  						$actuLigne = $nbLigne;
  						for($i = 0 ; $i < ($maxLigne-$actuLigne) && $i < count($TextAntecedentsTrauma); $i++){
  							$this->_page->setlineColor(new ZendPdf\Color\Html('#efefef'));
  							$this->_page->setLineWidth(0.5);
  							$this->_page->drawLine($this->_leftMargin,
  									$this->_yPosition,
  									$this->_pageWidth -
  									$this->_leftMargin,
  									$this->_yPosition);
  		
  		
  							$this->_page->setFont($this->_policeContenu, 11);
  							$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $TextAntecedentsTrauma[$i] ),
  									$this->_leftMargin,
  									$this->_yPosition);
  		
  							$this->_yPosition -= $noteLineHeight;
  							$nbLigne++;
  		
  						}
  		
  						$tableauTemoinInfos [4][] = $i;
  						$tableauTemoinInfos [4][] = count($TextAntecedentsTrauma);
  					}else{
  						$tableauTemoinLimite [3]  = 0;
  						$tableauTemoinLimite [4]  = 1;
  						$tableauTemoinInfos  [4][] = 0;
  						$tableauTemoinInfos  [4][] = count($TextAntecedentsTrauma);
  					}
  		
  		
  				}else{
  					$tableauTemoinLimite [3]  = 0;
  					$tableauTemoinLimite [4] = 0;
  				}
  		
  				$this->setEntrerAntecedentTrauma($i);
  		
  			}else{
  				$this->setEntrerAntecedentTrauma(-1);
  			}
  				
  				
  		}else{
  			$tableauTemoinInfos  [4][] = 0;
  			$tableauTemoinInfos  [4][] = count($TextAntecedentsTrauma);
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
  		
  		//Préparation du texte des examens physiques
  		//Préparation du texte des examens physiques
  		
  		$TextExamensPhysiques = array();
  		for($i = 0 ; $i < count($TableauExamensPhysiques) ; $i++){
  		
  			if( strlen($TableauExamensPhysiques[$i]) > 106 ){
  					
  				$textDecouper = wordwrap($TableauExamensPhysiques[$i], 106, "\n", false); // On découpe le texte
  				$textDecouperTab = explode( "\n" ,$textDecouper); // On le place dans un tableau
  					
  				for($j = 0 ; $j < count($textDecouperTab) ; $j++){
  					if(rtrim($textDecouperTab[$j]) != ''){
  						$TextExamensPhysiques[] = $textDecouperTab[$j];
  					}
  				}
  					
  			}else{
  					
  				if(rtrim($TableauExamensPhysiques[$i]) != ''){
  					$TextExamensPhysiques[] = $TableauExamensPhysiques[$i];
  				}
  					
  			}
  				
  				
  		}
  			
  		$TextExamensPhysiques = $this->Justifier($TextExamensPhysiques, 110);
  		$this->setTextExamenPhysique($TextExamensPhysiques);
  		$NbTotaleLigne += count($TextExamensPhysiques);
  		
  		//Fin préparation du texte des examens physiques
  		//Fin préparation du texte des examens physiques
  		/*5)*/
  		if($nbLigne < $maxLigne){
  		
  			if($TextExamensPhysiques){
  				if($TextExamensPhysiques[0]){
  					$maxLigne--;
  		
  					if($nbLigne < $maxLigne){
  							
  						$tableauTemoinLimite [4] = 0;
  						$tableauTemoinLimite [5] = 1;
  							
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
  		
  						$actuLigne = $nbLigne;
  						for($i = 0 ; $i < ($maxLigne-$actuLigne) && $i < count($TextExamensPhysiques); $i++){
  							$this->_page->setlineColor(new ZendPdf\Color\Html('#efefef'));
  							$this->_page->setLineWidth(0.5);
  							$this->_page->drawLine($this->_leftMargin,
  									$this->_yPosition,
  									$this->_pageWidth -
  									$this->_leftMargin,
  									$this->_yPosition);
  		
  		
  							$this->_page->setFont($this->_policeContenu, 11);
  							$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $TextExamensPhysiques[$i] ),
  									$this->_leftMargin,
  									$this->_yPosition);
  		
  							$this->_yPosition -= $noteLineHeight;
  							$nbLigne++;
  		
  						}
  		
  						$tableauTemoinInfos [5][] = $i;
  						$tableauTemoinInfos [5][] = count($TextExamensPhysiques);
  					}else{
  						$tableauTemoinLimite [4]  = 0;
  						$tableauTemoinLimite [5]  = 1;
  						$tableauTemoinInfos  [5][] = 0;
  						$tableauTemoinInfos  [5][] = count($TextExamensPhysiques);
  					}
  		
  		
  				}else{
  					$tableauTemoinLimite [4]  = 0;
  					$tableauTemoinLimite [5] = 0;
  				}
  		
  				$this->setEntrerExamenPhysique($i);
  		
  			}else{
  				$this->setEntrerExamenPhysique(-1);
  			}
  		
  		
  		}else{
  			$tableauTemoinInfos  [5][] = 0;
  			$tableauTemoinInfos  [5][] = count($TextExamensPhysiques);
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
  		
  		//Préparation du texte des examens paracliniques
  		//Préparation du texte des examens paracliniques
  		
  		$TextExamensParacliniques = array();
  		for($i = 0 ; $i < count($TableauExamenParaclinique) ; $i++){
  		
  			if( strlen($TableauExamenParaclinique[$i]) > 106 ){
  					
  				$textDecouper = wordwrap($TableauExamenParaclinique[$i], 106, "\n", false); // On découpe le texte
  				$textDecouperTab = explode( "\n" ,$textDecouper); // On le place dans un tableau
  					
  				for($j = 0 ; $j < count($textDecouperTab) ; $j++){
  					if(rtrim($textDecouperTab[$j]) != ''){
  						$TextExamensParacliniques[] = $textDecouperTab[$j];
  					}
  				}
  					
  			}else{
  					
  				if(rtrim($TableauExamenParaclinique[$i]) != ''){
  					$TextExamensParacliniques[] = $TableauExamenParaclinique[$i];
  				}
  					
  			}
  		
  		
  		}
  			
  		$TextExamensParacliniques = $this->Justifier($TextExamensParacliniques, 110);
  		$this->setTextExamenParaclinique($TextExamensParacliniques);
  		$NbTotaleLigne += count($TextExamensParacliniques);
  		
  		//Fin préparation du texte des examens paracliniques
  		//Fin préparation du texte des examens paracliniques
  		
  		/*6)*/
  		if($nbLigne < $maxLigne){
  		
  			if($TextExamensParacliniques){
  				if($TextExamensParacliniques[0]){
  					$maxLigne--;
  		
  					if($nbLigne < $maxLigne){
  							
  						$tableauTemoinLimite [5] = 0;
  						$tableauTemoinLimite [6] = 1;
  							
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
  		
  						$actuLigne = $nbLigne;
  						for($i = 0 ; $i < ($maxLigne-$actuLigne) && $i < count($TextExamensParacliniques); $i++){
  							$this->_page->setlineColor(new ZendPdf\Color\Html('#efefef'));
  							$this->_page->setLineWidth(0.5);
  							$this->_page->drawLine($this->_leftMargin,
  									$this->_yPosition,
  									$this->_pageWidth -
  									$this->_leftMargin,
  									$this->_yPosition);
  		
  		
  							$this->_page->setFont($this->_policeContenu, 11);
  							$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $TextExamensParacliniques[$i] ),
  									$this->_leftMargin,
  									$this->_yPosition);
  		
  							$this->_yPosition -= $noteLineHeight;
  							$nbLigne++;
  		
  						}
  		
  						$tableauTemoinInfos [6][] = $i;
  						$tableauTemoinInfos [6][] = count($TextExamensParacliniques);
  					}else{
  						$tableauTemoinLimite [5]  = 0;
  						$tableauTemoinLimite [6]  = 1;
  						$tableauTemoinInfos  [6][] = 0;
  						$tableauTemoinInfos  [6][] = count($TextExamensParacliniques);
  					}
  		
  		
  				}else{
  					$tableauTemoinLimite [5]  = 0;
  					$tableauTemoinLimite [6] = 0;
  				}
  		
  				$this->setEntrerExamenParaclinique($i);
  		
  			}else{
  				$this->setEntrerExamenParaclinique(-1);
  			}
  		
  		
  		}else{
  			$tableauTemoinInfos  [6][] = 0;
  			$tableauTemoinInfos  [6][] = count($TextExamensParacliniques);
  		}
  		
  		
  		//Fin Gestion des examens paracliniques
  		//Fin Gestion des examens paracliniques
  		
  		
  		/**
  		 * **************************************************************************************
  		 * ======================================================================================
  		 * **************************************************************************************
  		 */
  		
  		//************* Gestion des Résultat examen complementaire ************
  		//************* Gestion des Résultat examen complementaire ************
  		
  		//Préparation du texte des Résultat examen complementaire
  		//Préparation du texte des Résultat examen complementaire
  		
  		$TextResultatExamenComplementaire = array();
  		for($i = 0 ; $i < count($TableauExamenParaclinique) ; $i++){
  		
  			if( strlen($TableauExamenParaclinique[$i]) > 106 ){
  					
  				$textDecouper = wordwrap($TableauExamenParaclinique[$i], 106, "\n", false); // On découpe le texte
  				$textDecouperTab = explode( "\n" ,$textDecouper); // On le place dans un tableau
  					
  				for($j = 0 ; $j < count($textDecouperTab) ; $j++){
  					if(rtrim($textDecouperTab[$j]) != ''){
  						$TextResultatExamenComplementaire[] = $textDecouperTab[$j];
  					}
  				}
  					
  			}else{
  					
  				if(rtrim($TableauExamenParaclinique[$i]) != ''){
  					$TextResultatExamenComplementaire[] = $TableauExamenParaclinique[$i];
  				}
  					
  			}
  		
  		
  		}
  			
  		$TextResultatExamenComplementaire = $this->Justifier($TextResultatExamenComplementaire, 110);
  		$this->setTextResultatExamenComplementaire($TextResultatExamenComplementaire);
  		$NbTotaleLigne += count($TextResultatExamenComplementaire);
  		
  		//Fin préparation du texte des Résultat examen complementaire
  		//Fin préparation du texte des Résultat examen complementaire

  		/*7)*/
  		if($nbLigne < $maxLigne){
  		
  			if($TextResultatExamenComplementaire){
  				if($TextResultatExamenComplementaire[0]){
  					$maxLigne--;
  		
  					if($nbLigne < $maxLigne){
  							
  						$tableauTemoinLimite [6] = 0;
  						$tableauTemoinLimite [7] = 1;
  							
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
  		
  						$actuLigne = $nbLigne;
  						for($i = 0 ; $i < ($maxLigne-$actuLigne) && $i < count($TextResultatExamenComplementaire); $i++){
  							$this->_page->setlineColor(new ZendPdf\Color\Html('#efefef'));
  							$this->_page->setLineWidth(0.5);
  							$this->_page->drawLine($this->_leftMargin,
  									$this->_yPosition,
  									$this->_pageWidth -
  									$this->_leftMargin,
  									$this->_yPosition);
  		
  		
  							$this->_page->setFont($this->_policeContenu, 11);
  							$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $TextResultatExamenComplementaire[$i] ),
  									$this->_leftMargin,
  									$this->_yPosition);
  		
  							$this->_yPosition -= $noteLineHeight;
  							$nbLigne++;
  		
  						}
  		
  						$tableauTemoinInfos [7][] = $i;
  						$tableauTemoinInfos [7][] = count($TextResultatExamenComplementaire);
  					}else{
  						$tableauTemoinLimite [6]  = 0;
  						$tableauTemoinLimite [7]  = 1;
  						$tableauTemoinInfos  [7][] = 0;
  						$tableauTemoinInfos  [7][] = count($TextResultatExamenComplementaire);
  					}
  		
  		
  				}else{
  					$tableauTemoinLimite [6]  = 0;
  					$tableauTemoinLimite [7] = 0;
  				}
  		
  				$this->setEntrerResultatExamenComplementaire($i);
  		
  			}else{
  				$this->setEntrerResultatExamenComplementaire(-1);
  			}
  		
  		
  		}else{
  			$tableauTemoinInfos  [7][] = 0;
  			$tableauTemoinInfos  [7][] = count($TextResultatExamenComplementaire);
  		}
  		
  		//Fin Gestion des Résultat examen complementaire
  		//Fin Gestion des Résultat examen complementaire
  		
  		/**
  		 * **************************************************************************************
  		 * ======================================================================================
  		 * **************************************************************************************
  		 */
  		
  			
  		//************* Gestion des mecanismes ************
  		//************* Gestion des mecanismes ************
  		
  		$this->setTextMecanismes($mecanismes);
  		if($mecanismes != ''){ $NbTotaleLigne ++; }
  		/*8)*/
  		if($nbLigne < $maxLigne){
  		
  			if($mecanismes != ''){
  		
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
  					$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $this->_listeMecanismes[$mecanismes].'   '.'  ( '.$mecanismes_precision.' )'),
  							$this->_leftMargin+80,
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
  		
  		}else{
  			//var_dump('test 1'); exit();
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
  		
  		$this->setTextIndication($indication);
  		if($indication != ''){ $NbTotaleLigne ++; }
  		/*9)*/
  		if($nbLigne < $maxLigne){
  		
  			if($indication != ''){
  		
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
  			//var_dump('test 1'); exit();
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
  		
  		$this->setTextDiagnostic($diagnostic);
  		if($diagnostic != ''){ $NbTotaleLigne ++; }
  		/*10)*/
  		if($nbLigne < $maxLigne){
  		
  			if($diagnostic != ''){
  		
  				if($nbLigne < 19){
  					$tableauTemoinLimite [9] = 0;
  					$tableauTemoinLimite [10] = 0;
  		
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
  					$nbLigne++;
  		
  					$tableauTemoinInfos [10][] = 1;
  					$tableauTemoinInfos [10][] = 1;
  				}
  					
  			}
  			else{
  				if($nbLigne < 19){
  					$tableauTemoinLimite [9] = 0;
  					$tableauTemoinLimite [10] = 0;
  				}
  		
  			}
  		
  		}else{
  			//var_dump('test 1'); exit();
  		}
  			
  		//Fin Gestion des diagnostics
  		//Fin Gestion des diagnostics
  		
  		
  		

  		/**
  		 * **************************************************************************************
  		 * ======================================================================================
  		 * **************************************************************************************
  		 */
  		
  		//************* Gestion des conduites à tenir ************
  		//************* Gestion des conduites à tenir ************
  		
  		//Préparation du texte des conduites à tenir
  		//Préparation du texte des conduites à tenir
  		
  		$TextConduiteATenir = array();
  		for($i = 0 ; $i < count($TableauConduiteATenir) ; $i++){
  		
  			if( strlen($TableauConduiteATenir[$i]) > 106 ){
  					
  				$textDecouper = wordwrap($TableauConduiteATenir[$i], 106, "\n", false); // On découpe le texte
  				$textDecouperTab = explode( "\n" ,$textDecouper); // On le place dans un tableau
  					
  				for($j = 0 ; $j < count($textDecouperTab) ; $j++){
  					if(rtrim($textDecouperTab[$j]) != ''){
  						$TextConduiteATenir[] = $textDecouperTab[$j];
  					}
  				}
  					
  			}else{
  					
  				if(rtrim($TableauConduiteATenir[$i]) != ''){
  					$TextConduiteATenir[] = $TableauConduiteATenir[$i];
  				}
  					
  			}
  		
  		
  		}
  			
  		$TextConduiteATenir = $this->Justifier($TextConduiteATenir, 110);
  		$this->setTextConduiteATenir($TextConduiteATenir);
  		$NbTotaleLigne += count($TextConduiteATenir);
  		
  		//Fin préparation du texte des conduites à tenir
  		//Fin préparation du texte des conduites à tenir
  		/*11)*/
  		if($nbLigne < $maxLigne){
  		
  			if($TextConduiteATenir){
  				if($TextConduiteATenir[0]){
  					$maxLigne--;
  		
  					if($nbLigne < $maxLigne){
  							
  						$tableauTemoinLimite [10] = 0;
  						$tableauTemoinLimite [11] = 1;
  							
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
  		
  						$actuLigne = $nbLigne;
  						for($i = 0 ; $i < ($maxLigne-$actuLigne) && $i < count($TextConduiteATenir); $i++){
  							$this->_page->setlineColor(new ZendPdf\Color\Html('#efefef'));
  							$this->_page->setLineWidth(0.5);
  							$this->_page->drawLine($this->_leftMargin,
  									$this->_yPosition,
  									$this->_pageWidth -
  									$this->_leftMargin,
  									$this->_yPosition);
  		
  		
  							$this->_page->setFont($this->_policeContenu, 11);
  							$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $TextConduiteATenir[$i] ),
  									$this->_leftMargin,
  									$this->_yPosition);
  		
  							$this->_yPosition -= $noteLineHeight;
  							$nbLigne++;
  		
  						}
  		
  						$tableauTemoinInfos [11][] = $i;
  						$tableauTemoinInfos [11][] = count($TextConduiteATenir);
  					}else{
  						$tableauTemoinLimite [10]  = 0;
  						$tableauTemoinLimite [11]  = 1;
  						$tableauTemoinInfos  [11][] = 0;
  						$tableauTemoinInfos  [11][] = count($TextConduiteATenir);
  					}
  		
  		
  				}else{
  					$tableauTemoinLimite [10]  = 0;
  					$tableauTemoinLimite [11] = 0;
  				}
  		
  				$this->setEntrerConduiteATenir($i);
  		
  			}else{
  				$this->setEntrerConduiteATenir(-1);
  			}
  		
  		
  		}else{
  			$tableauTemoinInfos  [11][] = 0;
  			$tableauTemoinInfos  [11][] = count($TextConduiteATenir);
  		}
  		
  		
  		//Fin Gestion des conduites à tenir
  		//Fin Gestion des conduites à tenir
  		
  		/**
  		 * **************************************************************************************
  		 * ======================================================================================
  		 * **************************************************************************************
  		 */
  		
  			
  		//************* Gestion des modes de sortie ************
  		//************* Gestion des modes de sortie ************
  		
  		$this->setTextModeSortie($mode_sortie);
  		if($mode_sortie != ''){ $NbTotaleLigne ++; }
  		/*12)*/
  		if($nbLigne < $maxLigne){
  		
  			if($mode_sortie != ''){
  		
  				if($nbLigne < 19){
  					$tableauTemoinLimite [11] = 0;
  					$tableauTemoinLimite [12] = 0;
  		
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
  					$nbLigne++;
  		
  					$tableauTemoinInfos [12][] = 1;
  					$tableauTemoinInfos [12][] = 1;
  				}
  					
  			}
  			else{
  				if($nbLigne < 19){
  					$tableauTemoinLimite [11] = 0;
  					$tableauTemoinLimite [12] = 0;
  				}
  		
  			}
  		
  		}else{
  			//var_dump('test 1'); exit();
  		}
  			
  		//Fin Gestion des modes de sortie
  		//Fin Gestion des modes de sortie
  		

  		/**
  		 * **************************************************************************************
  		 * ======================================================================================
  		 * **************************************************************************************
  		 */
  		
  			
  		//************* Gestion des rendez-vous ************
  		//************* Gestion des rendez-vous ************
  		
  		$this->setTextRendezVous($rendez_vous);
  		if($rendez_vous != ''){ $NbTotaleLigne ++; }
  		/*13)*/
  		if($nbLigne < $maxLigne){
  		
  			if($rendez_vous != ''){
  		
  				if($nbLigne < 19){
  					$tableauTemoinLimite [12] = 0;
  					$tableauTemoinLimite [13] = 0;
  		
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
  					$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $rendez_vous),
  							$this->_leftMargin+93,
  							$this->_yPosition);
  		
  		
  		
  					$this->_yPosition -= $noteLineHeight;
  					$nbLigne++;
  		
  					$tableauTemoinInfos [13][] = 1;
  					$tableauTemoinInfos [13][] = 1;
  				}
  					
  			}
  			else{
  				if($nbLigne < 19){
  					$tableauTemoinLimite [12] = 0;
  					$tableauTemoinLimite [13] = 0;
  				}
  		
  			}
  		
  		}else{
  			//var_dump('test 1'); exit();
  		}
  			
  		//Fin Gestion des rendez-vous
  		//Fin Gestion des rendez-vous
  		
  		
  		
  		/**
  		 * **************************************************************************************
  		 * ======================================================================================
  		 * **************************************************************************************
  		 */
  		
  		//************* Gestion des avis specialistes ************
  		//************* Gestion des avis specialistes ************
  		
  		//Préparation du texte des avis specialistes
  		//Préparation du texte des avis specialistes
  		$TextSpecialiste = array();
  		for($i = 0 ; $i < count($TableauSpecialiste) ; $i++){
  		
  			if( strlen($TableauSpecialiste[$i]) > 106 ){
  					
  				$textDecouper = wordwrap($TableauSpecialiste[$i], 106, "\n", false); // On découpe le texte
  				$textDecouperTab = explode( "\n" ,$textDecouper); // On le place dans un tableau
  					
  				for($j = 0 ; $j < count($textDecouperTab) ; $j++){
  					if(rtrim($textDecouperTab[$j]) != ''){
  						$TextSpecialiste[] = $textDecouperTab[$j];
  					}
  				}
  					
  			}else{
  					
  				if(rtrim($TableauSpecialiste[$i]) != ''){
  					$TextSpecialiste[] = $TableauSpecialiste[$i];
  				}
  					
  			}
  		
  		
  		}
  			
  		$TextSpecialiste = $this->Justifier($TextSpecialiste, 110);
  		$this->setTextAvisSpecialiste($TextSpecialiste);
  		$NbTotaleLigne += count($TextSpecialiste);
  		
  		//Fin préparation du texte des specialistes
  		//Fin préparation du texte des specialistes
  		/*14)*/
  		if($nbLigne < $maxLigne){
  		
  			if($TextSpecialiste){
  				if($TextSpecialiste[0]){
  					$maxLigne--;
  		
  					if($nbLigne < $maxLigne){
  							
  						$tableauTemoinLimite [13] = 0;
  						$tableauTemoinLimite [14] = 1;
  							
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
  						for($i = 0 ; $i < ($maxLigne-$actuLigne) && $i < count($TextSpecialiste); $i++){
  							$this->_page->setlineColor(new ZendPdf\Color\Html('#efefef'));
  							$this->_page->setLineWidth(0.5);
  							$this->_page->drawLine($this->_leftMargin,
  									$this->_yPosition,
  									$this->_pageWidth -
  									$this->_leftMargin,
  									$this->_yPosition);
  		
  		
  							$this->_page->setFont($this->_policeContenu, 11);
  							$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $TextSpecialiste[$i] ),
  									$this->_leftMargin,
  									$this->_yPosition);
  		
  							$this->_yPosition -= $noteLineHeight;
  							$nbLigne++;
  		
  						}
  		
  						$tableauTemoinInfos [14][] = $i;
  						$tableauTemoinInfos [14][] = count($TextSpecialiste);
  					}else{
  						$tableauTemoinLimite [13]  = 0;
  						$tableauTemoinLimite [14]  = 1;
  						$tableauTemoinInfos  [14][] = 0;
  						$tableauTemoinInfos  [14][] = count($TextSpecialiste);
  					}
  		
  		
  				}else{
  					$tableauTemoinLimite [13]  = 0;
  					$tableauTemoinLimite [14] = 0;
  				}
  		
  				$this->setEntrerSpecialiste($i);
  		
  			}else{
  				$this->setEntrerSpecialiste(-1);
  			}
  		
  		
  		}else{
  			$tableauTemoinInfos  [14][] = 0;
  			$tableauTemoinInfos  [14][] = count($TextSpecialiste);
  		}
  		
  		
  		//Fin Gestion des specialistes
  		//Fin Gestion des specialistes
  		
  		/**
  		 * **************************************************************************************
  		 * ======================================================================================
  		 * **************************************************************************************
  		 */
  		
  		//************* Gestion des conduites à tenir pour le spécialiste ************
  		//************* Gestion des conduites à tenir pour le spécialiste ************
  		
  		//Préparation du texte des conduites à tenir
  		//Préparation du texte des conduites à tenir
  		$TextConduiteATenirSpecialiste = array();
  		for($i = 0 ; $i < count($TableauConduiteATenirSpecialiste) ; $i++){
  		
  			if( strlen($TableauConduiteATenirSpecialiste[$i]) > 106 ){
  					
  				$textDecouper = wordwrap($TableauConduiteATenirSpecialiste[$i], 106, "\n", false); // On découpe le texte
  				$textDecouperTab = explode( "\n" ,$textDecouper); // On le place dans un tableau
  					
  				for($j = 0 ; $j < count($textDecouperTab) ; $j++){
  					if(rtrim($textDecouperTab[$j]) != ''){
  						$TextConduiteATenirSpecialiste[] = $textDecouperTab[$j];
  					}
  				}
  					
  			}else{
  					
  				if(rtrim($TableauConduiteATenirSpecialiste[$i]) != ''){
  					$TextConduiteATenirSpecialiste[] = $TableauConduiteATenirSpecialiste[$i];
  				}
  					
  			}
  		
  		
  		}
  			
  		$TextConduiteATenirSpecialiste = $this->Justifier($TextConduiteATenirSpecialiste, 110);
  		$this->setTextConduiteATenirSpecialiste($TextConduiteATenirSpecialiste);
  		$NbTotaleLigne += count($TextConduiteATenirSpecialiste);
  		
  		//Fin préparation du texte des conduites à tenir
  		//Fin préparation du texte des conduites à tenir
  		/*15)*/
  		if($nbLigne < $maxLigne){
  		
  			if($TextConduiteATenirSpecialiste){
  				if($TextConduiteATenirSpecialiste[0]){
  					$maxLigne--;
  		
  					if($nbLigne < $maxLigne){
  							
  						$tableauTemoinLimite [14] = 0;
  						$tableauTemoinLimite [15] = 1;
  							
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
  		
  						$actuLigne = $nbLigne;
  						for($i = 0 ; $i < ($maxLigne-$actuLigne) && $i < count($TextConduiteATenirSpecialiste); $i++){
  							$this->_page->setlineColor(new ZendPdf\Color\Html('#efefef'));
  							$this->_page->setLineWidth(0.5);
  							$this->_page->drawLine($this->_leftMargin,
  									$this->_yPosition,
  									$this->_pageWidth -
  									$this->_leftMargin,
  									$this->_yPosition);
  		
  		
  							$this->_page->setFont($this->_policeContenu, 11);
  							$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $TextConduiteATenirSpecialiste[$i] ),
  									$this->_leftMargin,
  									$this->_yPosition);
  		
  							$this->_yPosition -= $noteLineHeight;
  							$nbLigne++;
  		
  						}
  		
  						$tableauTemoinInfos [15][] = $i;
  						$tableauTemoinInfos [15][] = count($TextConduiteATenirSpecialiste);
  					}else{
  						$tableauTemoinLimite [14]  = 0;
  						$tableauTemoinLimite [15]  = 1;
  						$tableauTemoinInfos  [15][] = 0;
  						$tableauTemoinInfos  [15][] = count($TextConduiteATenirSpecialiste);
  					}
  		
  		
  				}else{
  					$tableauTemoinLimite [14]  = 0;
  					$tableauTemoinLimite [15] = 0;
  				}
  		
  				$this->setEntrerConduiteATenirSpecialiste($i);
  		
  			}else{
  				$this->setEntrerConduiteATenirSpecialiste(-1);
  			}
  		
  		
  		}else{
  			$tableauTemoinInfos  [15][] = 0;
  			$tableauTemoinInfos  [15][] = count($TextConduiteATenirSpecialiste);
  		}
  		
  		
  		//Fin Gestion des conduites à tenir
  		//Fin Gestion des conduites à tenir
  		/**
  		 * **************************************************************************************
  		 * ======================================================================================
  		 * **************************************************************************************
  		 */
  		 	
 		//var_dump($tableauTemoinLimite); exit();
 		
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