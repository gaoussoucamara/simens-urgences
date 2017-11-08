<?php
namespace Consultation\View\Helpers;

use ZendPdf;
use ZendPdf\Page;
use ZendPdf\Font;
use Consultation\Model\Consultation;
use Urgence\View\Helper\DateHelper; 


class RpuSortiePdf
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
	protected $_infosInfirmiers;
	protected $_listeSalles;
	protected $_listeLits;
	
	
	protected $_tableauTemoinLimite;
	protected $_tableauTemoinInfos;
	protected $_listeMecanismes;
	protected $_listeCirconstances;
	protected $_listeIndications;
	protected $_listeDiagnostics;
	protected $_listeModeSortie;
	protected $_listeServiceMutation;
	
	
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
	
	public function setListeModeSortie($listeModeSortie){
		$this->_listeModeSortie = $listeModeSortie;
	}
	
	public function getListeModeSortie(){
		return $this->_listeModeSortie;
	}
	
	
	//******* Les différentes informations **********
	//******* Les différentes informations **********
	protected $_textInfos1;
	protected $_textInfos2;
	protected $_textInfos3;
	protected $_textInfos4;
	protected $_textInfos5;
	protected $_textInfos6;
	protected $_textInfos7;
	protected $_textInfos8;
	protected $_textInfos9;
	protected $_textInfos10;
	
	
	
	public function setTextInfos1($textInfos1) {
		$this->_textInfos1 = $textInfos1;
	}
	
	public function getTextInfos1(){
		return $this->_textInfos1;
	}
	
	public function setTextInfos2($textInfos2) {
		$this->_textInfos2 = $textInfos2;
	}
	
	public function getTextInfos2(){
		return $this->_textInfos2;
	}
	
	public function setTextInfos3($textInfos3) {
		$this->_textInfos3 = $textInfos3;
	}
	
	public function getTextInfos3(){
		return $this->_textInfos3;
	}
	
	public function setTextInfos4($textInfos4) {
		$this->_textInfos4 = $textInfos4;
	}
	
	public function getTextInfos4(){
		return $this->_textInfos4;
	}
	
	public function setTextInfos5($textInfos5) {
		$this->_textInfos5 = $textInfos5;
	}
	
	public function getTextInfos5(){
		return $this->_textInfos5;
	}
	
	public function setTextInfos6($textInfos6) {
		$this->_textInfos6 = $textInfos6;
	}
	
	public function getTextInfos6(){
		return $this->_textInfos6;
	}
	
	public function setTextInfos7($textInfos7) {
		$this->_textInfos7 = $textInfos7;
	}
	
	public function getTextInfos7(){
		return $this->_textInfos7;
	}
	
	public function setTextInfos8($textInfos8) {
		$this->_textInfos8 = $textInfos8;
	}
	
	public function getTextInfos8(){
		return $this->_textInfos8;
	}
	
	public function setTextInfos9($textInfos9) {
		$this->_textInfos9 = $textInfos9;
	}
	
	public function getTextInfos9(){
		return $this->_textInfos9;
	}
	
	public function setTextInfos10($textInfos10) {
		$this->_textInfos10 = $textInfos10;
	}
	
	public function getTextInfos10(){
		return $this->_textInfos10;
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
 		
 		//********************
 		$info1 = $this->_DonneesDemande['diganostic'];
 		$info2 = $this->_DonneesDemande['diganostic_associe'];
 		$info3 = $this->_DonneesDemande['traitement'];
 		$info4 = $this->_DonneesDemande['examens_complementaires_demandes'];
 		$info5 = $this->_DonneesDemande['mode_sortie'];
 		$info6 = $this->_DonneesDemande['liste_mutation'];
 		$info7 = $this->_DonneesDemande['transfert'];
 		$info8 = $this->_DonneesDemande['evacuation'];
 		
 		
 		//var_dump($info1); exit();
 		
 		$TableauInfos1 = explode( "\n" , $info1);
 		$TableauInfos2 = explode( "\n" , $info2);
 		$TableauInfos3 = explode( "\n" , $info3);
 		$TableauInfos4 = explode( "\n" , $info4);
 		
 		$textInfos5 = $info5; 
 		$textInfos6 = $info6; $this->setTextInfos6($textInfos6);
 		$textInfos7 = $info7; $this->setTextInfos7($textInfos7);
 		$textInfos8 = $info8; $this->setTextInfos8($textInfos8);
 		
 		
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
 				
 					$this->_page->setLineColor(new ZendPdf\Color\Html('#eeeeee'));
 					$this->_page->setLineWidth(17);
 					$this->_page->drawLine($this->_leftMargin,
 							$this->_yPosition +5,
 							$this->_pageWidth -
 							$this->_leftMargin -379,
 							$this->_yPosition +5);
 					
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
 				
 					$this->_page->setFont($this->_policeContenu, 10.8);
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
 						
 					$this->_page->setFont($this->_policeContenu, 10.8);
 					$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $motif_consultation_comp[0] ),
 							$this->_leftMargin,
 							$this->_yPosition);
 					$temoinMotifCons2 = 1;
 					$this->_yPosition -= $noteLineHeight;
 					$NbTotaleLigne++;
 				}	
 				
 			}
							
 		}
 		
 		$maxLigne = 23;
 		if($temoinMotifCons2 == 1){ $maxLigne--; }
 		$nbLigne = 0;
 		
 		
 		
 		/**
 		 * **************************************************************************************
 		 * ======================================================================================
 		 * **************************************************************************************
 		 */
 		
 		//************* Gestion de l'information N°1 ************
 		//************* Gestion de l'information N°1 ************
 		
 		// 1°) Diagnostic principal
 		
 		//Préparation du texte  
 		//Préparation du texte 
 		
 		$textInfos1 = array();
 		for($i = 0 ; $i < count($TableauInfos1) ; $i++){
 		
 			if( strlen($TableauInfos1[$i]) > 106 ){
 					
 				$textDecouper = wordwrap($TableauInfos1[$i], 106, "\n", false); // On découpe le texte
 				$textDecouperTab = explode( "\n" ,$textDecouper); // On le place dans un tableau
 					
 				for($j = 0 ; $j < count($textDecouperTab) ; $j++){
 					if(rtrim($textDecouperTab[$j]) != ''){
 						$textInfos1[] = $textDecouperTab[$j];
 					}
 				}
 					
 			}else{
 					
 				if(rtrim($TableauInfos1[$i]) != ''){
 					$textInfos1[] = $TableauInfos1[$i];
 				}
 					
 			}
 				
 				
 		}
 			
 		$textInfos1 = $this->Justifier($textInfos1, 110);
 		$this->setTextInfos1($textInfos1);
 		$NbTotaleLigne += count($textInfos1);

 		//Fin préparation du texte
 		//Fin préparation du texte
 		
 		if($nbLigne < $maxLigne){
 		
 			if($textInfos1){
 				if($textInfos1[0]){
 					$maxLigne--;
 		
 					if($nbLigne < $maxLigne){
 							
 						$tableauTemoinLimite [1] = 1;
 							
 						$this->_page->setLineColor(new ZendPdf\Color\Html('#eeeeee'));
 						$this->_page->setLineWidth(17);
 						$this->_page->drawLine($this->_leftMargin,
 								$this->_yPosition +5,
 								$this->_pageWidth -
 								$this->_leftMargin -385,
 								$this->_yPosition +5);
 						
 						$this->_page->setFont($this->_newTimeGrasNonItalic, 12);
 						$this->_page->drawText('Diagnostic principal : ',
 								$this->_leftMargin,
 								$this->_yPosition);
 		
 						$this->_page->setlineColor(new ZendPdf\Color\Html('black'));
 						$this->_page->setLineWidth(0.5);
 						$this->_page->drawLine($this->_leftMargin,
 								$this->_yPosition-2,
 								$this->_pageWidth -
 								$this->_leftMargin-385,
 								$this->_yPosition-2);
 		
 						$this->_yPosition -= $noteLineHeight;
 		
 						$actuLigne = $nbLigne;
 						for($i = 0 ; $i < ($maxLigne-$actuLigne) && $i < count($textInfos1); $i++){
 							$this->_page->setlineColor(new ZendPdf\Color\Html('#efefef'));
 							$this->_page->setLineWidth(0.5);
 							$this->_page->drawLine($this->_leftMargin,
 									$this->_yPosition,
 									$this->_pageWidth -
 									$this->_leftMargin,
 									$this->_yPosition);
 		
 		
 							$this->_page->setFont($this->_policeContenu, 10.8);
 							$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $textInfos1[$i] ),
 									$this->_leftMargin,
 									$this->_yPosition);
 		
 							$this->_yPosition -= $noteLineHeight;
 							$nbLigne++;
 		
 						}
 		
 						$tableauTemoinInfos [1][] = $i;
 						$tableauTemoinInfos [1][] = count($textInfos1);
 					}else{
 						$tableauTemoinLimite [1]  = 1;
 						$tableauTemoinInfos  [1][] = 0;
 						$tableauTemoinInfos  [1][] = count($textInfos1);
 					}
 		
 		
 				}else{
 					$tableauTemoinLimite [1] = 0;
 				}
 		
 				//$this->setEntrerInfos1($i);
 		
 			}else{
 				//$this->setEntrerInfos1(-1);
 			}
 		
 		
 		}else{
 			$tableauTemoinInfos  [1][] = 0;
 			$tableauTemoinInfos  [1][] = count($textInfos1);
 		}
 		
 		//************* FIN Gestion de l'information N°1 ************
 		//************* FIN Gestion de l'information N°1 ************
 		
 		
 		
 		//************* Gestion de l'information N°2 ************
 		//************* Gestion de l'information N°2 ************
 			
 		// 2°) Diagnostic associé
 			
 		//Préparation du texte
 		//Préparation du texte
 			
 		$textInfos2 = array();
 		for($i = 0 ; $i < count($TableauInfos2) ; $i++){
 				
 			if( strlen($TableauInfos2[$i]) > 106 ){
 		
 				$textDecouper = wordwrap($TableauInfos2[$i], 106, "\n", false); // On découpe le texte
 				$textDecouperTab = explode( "\n" ,$textDecouper); // On le place dans un tableau
 		
 				for($j = 0 ; $j < count($textDecouperTab) ; $j++){
 					if(rtrim($textDecouperTab[$j]) != ''){
 						$textInfos2[] = $textDecouperTab[$j];
 					}
 				}
 		
 			}else{
 		
 				if(rtrim($TableauInfos2[$i]) != ''){
 					$textInfos2[] = $TableauInfos2[$i];
 				}
 		
 			}
 				
 				
 		}
 		
 		$textInfos2 = $this->Justifier($textInfos2, 110);
 		$this->setTextInfos2($textInfos2);
 		$NbTotaleLigne += count($textInfos2);
 		
 		//Fin préparation du texte
 		//Fin préparation du texte
 			
 		if($nbLigne < $maxLigne){
 				
 			if($textInfos2){
 				if($textInfos2[0]){
 					$maxLigne--;
 						
 					if($nbLigne < $maxLigne){
 		
 						$tableauTemoinLimite [1] = 0;
 						$tableauTemoinLimite [2] = 1;
 		
 						$this->_page->setLineColor(new ZendPdf\Color\Html('#eeeeee'));
 						$this->_page->setLineWidth(17);
 						$this->_page->drawLine($this->_leftMargin,
 								$this->_yPosition +5,
 								$this->_pageWidth -
 								$this->_leftMargin -396,
 								$this->_yPosition +5);
 						
 						$this->_page->setFont($this->_newTimeGrasNonItalic, 12);
 						$this->_page->drawText('Diagnostic associé : ',
 								$this->_leftMargin,
 								$this->_yPosition);
 							
 						$this->_page->setlineColor(new ZendPdf\Color\Html('black'));
 						$this->_page->setLineWidth(0.5);
 						$this->_page->drawLine($this->_leftMargin,
 								$this->_yPosition-2,
 								$this->_pageWidth -
 								$this->_leftMargin-396,
 								$this->_yPosition-2);
 							
 						$this->_yPosition -= $noteLineHeight;
 							
 						$actuLigne = $nbLigne;
 						for($i = 0 ; $i < ($maxLigne-$actuLigne) && $i < count($textInfos2); $i++){
 							$this->_page->setlineColor(new ZendPdf\Color\Html('#efefef'));
 							$this->_page->setLineWidth(0.5);
 							$this->_page->drawLine($this->_leftMargin,
 									$this->_yPosition,
 									$this->_pageWidth -
 									$this->_leftMargin,
 									$this->_yPosition);
 								
 								
 							$this->_page->setFont($this->_policeContenu, 10.8);
 							$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $textInfos2[$i] ),
 									$this->_leftMargin,
 									$this->_yPosition);
 								
 							$this->_yPosition -= $noteLineHeight;
 							$nbLigne++;
 								
 						}
 							
 						$tableauTemoinInfos [2][] = $i;
 						$tableauTemoinInfos [2][] = count($textInfos2);
 					}else{
 						$tableauTemoinLimite [1]  = 0;
 						$tableauTemoinLimite [2]  = 1;
 						$tableauTemoinInfos  [2][] = 0;
 						$tableauTemoinInfos  [2][] = count($textInfos2);
 					}
 						
 						
 				}else{
 					$tableauTemoinLimite [1] = 0;
 					$tableauTemoinLimite [2] = 0;
 				}
 					
 				//$this->setEntrerInfos1($i);
 					
 			}else{
 				//$this->setEntrerInfos1(-1);
 			}
 				
 				
 		}else{
 			$tableauTemoinInfos  [2][] = 0;
 			$tableauTemoinInfos  [2][] = count($textInfos2);
 		}
 			
 		//************* FIN Gestion de l'information N°2 ************
 		//************* FIN Gestion de l'information N°2 ************
 		
 		
 		//************* Gestion de l'information N°3 ************
 		//************* Gestion de l'information N°3 ************
 		
 		// 3°) Traitement
 		
 		//Préparation du texte
 		//Préparation du texte
 		
 		$textInfos3 = array();
 		for($i = 0 ; $i < count($TableauInfos3) ; $i++){
 				
 			if( strlen($TableauInfos3[$i]) > 106 ){
 					
 				$textDecouper = wordwrap($TableauInfos3[$i], 106, "\n", false); // On découpe le texte
 				$textDecouperTab = explode( "\n" ,$textDecouper); // On le place dans un tableau
 					
 				for($j = 0 ; $j < count($textDecouperTab) ; $j++){
 					if(rtrim($textDecouperTab[$j]) != ''){
 						$textInfos3[] = $textDecouperTab[$j];
 					}
 				}
 					
 			}else{
 					
 				if(rtrim($TableauInfos3[$i]) != ''){
 					$textInfos3[] = $TableauInfos3[$i];
 				}
 					
 			}
 				
 				
 		}
 			
 		$textInfos3 = $this->Justifier($textInfos3, 110);
 		$this->setTextInfos3($textInfos3);
 		$NbTotaleLigne += count($textInfos3);
 			
 		//Fin préparation du texte
 		//Fin préparation du texte
 		
 		if($nbLigne < $maxLigne){
 				
 			if($textInfos3){
 				if($textInfos3[0]){
 					$maxLigne--;
 						
 					if($nbLigne < $maxLigne){
 							
 						$tableauTemoinLimite [2] = 0;
 						$tableauTemoinLimite [3] = 1;
 							
 						$this->_page->setLineColor(new ZendPdf\Color\Html('#eeeeee'));
 						$this->_page->setLineWidth(17);
 						$this->_page->drawLine($this->_leftMargin,
 								$this->_yPosition +5,
 								$this->_pageWidth -
 								$this->_leftMargin -430,
 								$this->_yPosition +5);
 							
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
 						for($i = 0 ; $i < ($maxLigne-$actuLigne) && $i < count($textInfos3); $i++){
 							$this->_page->setlineColor(new ZendPdf\Color\Html('#efefef'));
 							$this->_page->setLineWidth(0.5);
 							$this->_page->drawLine($this->_leftMargin,
 									$this->_yPosition,
 									$this->_pageWidth -
 									$this->_leftMargin,
 									$this->_yPosition);
 								
 								
 							$this->_page->setFont($this->_policeContenu, 10.8);
 							$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $textInfos3[$i] ),
 									$this->_leftMargin,
 									$this->_yPosition);
 								
 							$this->_yPosition -= $noteLineHeight;
 							$nbLigne++;
 								
 						}
 		
 						$tableauTemoinInfos [3][] = $i;
 						$tableauTemoinInfos [3][] = count($textInfos3);
 					}else{
 						$tableauTemoinLimite [2]   = 0;
 						$tableauTemoinLimite [3]   = 1;
 						$tableauTemoinInfos  [3][] = 0;
 						$tableauTemoinInfos  [3][] = count($textInfos3);
 					}
 						
 						
 				}else{
 					$tableauTemoinLimite [2] = 0;
 					$tableauTemoinLimite [3] = 0;
 				}
 		
 				//$this->setEntrerInfos1($i);
 		
 			}else{
 				//$this->setEntrerInfos1(-1);
 			}
 				
 				
 		}else{
 			$tableauTemoinInfos  [3][] = 0;
 			$tableauTemoinInfos  [3][] = count($textInfos3);
 		}
 		
 		//************* FIN Gestion de l'information N°3 ************
 		//************* FIN Gestion de l'information N°3 ************
 		 	

 		//************* Gestion de l'information N°4 ************
 		//************* Gestion de l'information N°4 ************
 			
 		// 4°) Examens complementaires demandés
 			
 		//Préparation du texte
 		//Préparation du texte
 			
 		$textInfos4 = array();
 		for($i = 0 ; $i < count($TableauInfos4) ; $i++){
 				
 			if( strlen($TableauInfos4[$i]) > 106 ){
 		
 				$textDecouper = wordwrap($TableauInfos4[$i], 106, "\n", false); // On découpe le texte
 				$textDecouperTab = explode( "\n" ,$textDecouper); // On le place dans un tableau
 		
 				for($j = 0 ; $j < count($textDecouperTab) ; $j++){
 					if(rtrim($textDecouperTab[$j]) != ''){
 						$textInfos4[] = $textDecouperTab[$j];
 					}
 				}
 		
 			}else{
 		
 				if(rtrim($TableauInfos4[$i]) != ''){
 					$textInfos4[] = $TableauInfos4[$i];
 				}
 		
 			}
 				
 				
 		}
 		
 		$textInfos4 = $this->Justifier($textInfos4, 110);
 		$this->setTextInfos4($textInfos4);
 		$NbTotaleLigne += count($textInfos4);
 		
 		//Fin préparation du texte
 		//Fin préparation du texte
 		if($nbLigne < $maxLigne){
 				
 			if($textInfos4){
 				if($textInfos4[0]){
 					$maxLigne--;
 						
 					if($nbLigne < $maxLigne){
 		
 						$tableauTemoinLimite [3] = 0;
 						$tableauTemoinLimite [4] = 1;
 		
 						$this->_page->setLineColor(new ZendPdf\Color\Html('#eeeeee'));
 						$this->_page->setLineWidth(17);
 						$this->_page->drawLine($this->_leftMargin,
 								$this->_yPosition +5,
 								$this->_pageWidth -
 								$this->_leftMargin -297,
 								$this->_yPosition +5);
 		
 						$this->_page->setFont($this->_newTimeGrasNonItalic, 12);
 						$this->_page->drawText('Examens complementaires demandés : ',
 								$this->_leftMargin,
 								$this->_yPosition);
 							
 						$this->_page->setlineColor(new ZendPdf\Color\Html('black'));
 						$this->_page->setLineWidth(0.5);
 						$this->_page->drawLine($this->_leftMargin,
 								$this->_yPosition-2,
 								$this->_pageWidth -
 								$this->_leftMargin-297,
 								$this->_yPosition-2);
 							
 						$this->_yPosition -= $noteLineHeight;
 							
 						$actuLigne = $nbLigne;
 						for($i = 0 ; $i < ($maxLigne-$actuLigne) && $i < count($textInfos4); $i++){
 							$this->_page->setlineColor(new ZendPdf\Color\Html('#efefef'));
 							$this->_page->setLineWidth(0.5);
 							$this->_page->drawLine($this->_leftMargin,
 									$this->_yPosition,
 									$this->_pageWidth -
 									$this->_leftMargin,
 									$this->_yPosition);
 								
 								
 							$this->_page->setFont($this->_policeContenu, 10.8);
 							$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $textInfos4[$i] ),
 									$this->_leftMargin,
 									$this->_yPosition);
 								
 							$this->_yPosition -= $noteLineHeight;
 							$nbLigne++;
 								
 						}
 							
 						$tableauTemoinInfos [4][] = $i;
 						$tableauTemoinInfos [4][] = count($textInfos4);
 					}else{
 						$tableauTemoinLimite [3]   = 0;
 						$tableauTemoinLimite [4]   = 1;
 						$tableauTemoinInfos  [4][] = 0;
 						$tableauTemoinInfos  [4][] = count($textInfos4);
 					}
 						
 						
 				}else{
 					$tableauTemoinLimite [3] = 0;
 					$tableauTemoinLimite [4] = 0;
 				}
 					
 				//$this->setEntrerInfos1($i);
 					
 			}else{
 				//$this->setEntrerInfos1(-1);
 			}
 				
 				
 		}else{
 			$tableauTemoinInfos  [4][] = 0;
 			$tableauTemoinInfos  [4][] = count($textInfos4);
 		}
 			
 		//************* FIN Gestion de l'information N°4 ************
 		//************* FIN Gestion de l'information N°4 ************
 		 		
 		
 		
 		//************* Gestion de l'information N°5 ************
 		//************* Gestion de l'information N°5 ************
 		
 		$this->setTextInfos5($textInfos5);
 		if($textInfos5 != 0){ $NbTotaleLigne ++; }

 		// 5°) Mode de sortie
 		//var_dump($this->getListeModeSortie()); exit();
 		if($nbLigne < $maxLigne){
 		
 			if($textInfos5 != 0){
 		
 				if($nbLigne < 19){
 						
 					$tableauTemoinLimite [5] = 0;
 						
 					$this->_page->setlineColor(new ZendPdf\Color\Html('#efefef'));
 					$this->_page->setLineWidth(0.5);
 					$this->_page->drawLine($this->_leftMargin,
 							$this->_yPosition,
 							$this->_pageWidth -
 							$this->_leftMargin,
 							$this->_yPosition);
 						
 					$this->_page->setLineColor(new ZendPdf\Color\Html('#eeeeee'));
 					$this->_page->setLineWidth(17);
 					$this->_page->drawLine($this->_leftMargin,
 							$this->_yPosition +5,
 							$this->_pageWidth -
 							$this->_leftMargin -412,
 							$this->_yPosition +5);
 					
 					$this->_page->setFont($this->_newTimeGrasNonItalic, 12);
 					$this->_page->drawText('Mode de sortie : ',
 							$this->_leftMargin,
 							$this->_yPosition);
 		
 					$this->_page->setlineColor(new ZendPdf\Color\Html('black'));
 					$this->_page->setLineWidth(0.5);
 					$this->_page->drawLine($this->_leftMargin,
 							$this->_yPosition-2,
 							$this->_pageWidth -
 							$this->_leftMargin-412,
 							$this->_yPosition-2);
 		
 					$this->_page->setFont($this->_policeContenu, 10.8);
 					$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $this->getListeModeSortie()[$textInfos5]),
 							$this->_leftMargin+93,
 							$this->_yPosition);
 					
 					if($textInfos5 == 4){ //Mutation

 						$this->_page->setLineColor(new ZendPdf\Color\Html('#eeeeee'));
 						$this->_page->setLineWidth(17);
 						$this->_page->drawLine($this->_leftMargin+170,
 								$this->_yPosition +5,
 								$this->_pageWidth -
 								$this->_leftMargin -262,
 								$this->_yPosition +5);
 						$this->_page->setlineColor(new ZendPdf\Color\Html('black'));
 						$this->_page->setLineWidth(0.5);
 						$this->_page->drawLine($this->_leftMargin+170,
 								$this->_yPosition-2,
 								$this->_pageWidth -
 								$this->_leftMargin-262,
 								$this->_yPosition-2);
 						
 				    	$this->_page->setFont($this->_newTimeGrasNonItalic, 12);
 					    $this->_page->drawText('Muter vers: ',
 							$this->_leftMargin+170,
 							$this->_yPosition);
 					    
 					    $this->_page->setFont($this->_policeContenu, 10.8);
 					    $this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $this->getListeServiceMutation()[$textInfos6]),
 					    		$this->_leftMargin+250,
 					    		$this->_yPosition);
 					    
 					}else 
 						if($textInfos5 == 5){ //Transfert
 						
 							$this->_page->setLineColor(new ZendPdf\Color\Html('#eeeeee'));
 							$this->_page->setLineWidth(17);
 							$this->_page->drawLine($this->_leftMargin+170,
 									$this->_yPosition +5,
 									$this->_pageWidth -
 									$this->_leftMargin -242,
 									$this->_yPosition +5);
 							$this->_page->setlineColor(new ZendPdf\Color\Html('black'));
 							$this->_page->setLineWidth(0.5);
 							$this->_page->drawLine($this->_leftMargin+170,
 									$this->_yPosition-2,
 									$this->_pageWidth -
 									$this->_leftMargin-242,
 									$this->_yPosition-2);
 								
 							$this->_page->setFont($this->_newTimeGrasNonItalic, 12);
 							$this->_page->drawText('Transferer vers: ',
 									$this->_leftMargin+170,
 									$this->_yPosition);
 						
 							$this->_page->setFont($this->_policeContenu, 10.8);
 							$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $textInfos7),
 									$this->_leftMargin+265,
 									$this->_yPosition);
 							
 						}else 
 							if($textInfos5 == 6){ //Evacuation
 									
 								$this->_page->setLineColor(new ZendPdf\Color\Html('#eeeeee'));
 								$this->_page->setLineWidth(17);
 								$this->_page->drawLine($this->_leftMargin+170,
 										$this->_yPosition +5,
 										$this->_pageWidth -
 										$this->_leftMargin -252,
 										$this->_yPosition +5);
 								$this->_page->setlineColor(new ZendPdf\Color\Html('black'));
 								$this->_page->setLineWidth(0.5);
 								$this->_page->drawLine($this->_leftMargin+170,
 										$this->_yPosition-2,
 										$this->_pageWidth -
 										$this->_leftMargin-252,
 										$this->_yPosition-2);
 									
 								$this->_page->setFont($this->_newTimeGrasNonItalic, 12);
 								$this->_page->drawText('Evacuer vers : ',
 										$this->_leftMargin+170,
 										$this->_yPosition);
 									
 								$this->_page->setFont($this->_policeContenu, 10.8);
 								$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $textInfos8),
 										$this->_leftMargin+255,
 										$this->_yPosition);
 								
 							}
 					

 					
 					//var_dump($textInfos5); exit();
 		
 					
 					$this->_yPosition -= $noteLineHeight;
 					$nbLigne++;
 						
 					$tableauTemoinInfos [5][] = 1;
 					$tableauTemoinInfos [5][] = 1;
 				}
 					
 			}
 			else{
 				if($nbLigne < 19){
 					$tableauTemoinLimite [5] = 0;
 				}
 		
 			}
 		
 		}
 			
 		//************* FIN Gestion de l'information N°5 ************
 		//************* FIN Gestion de l'information N°5 ************
 		
 		
 		
 		
 		
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