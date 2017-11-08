<?php
namespace Consultation\View\Helpers;

use ZendPdf;
use ZendPdf\Page;
use ZendPdf\Font;
use Consultation\Model\Consultation;
use Urgence\View\Helper\DateHelper; 


class RpuSortiePdf2
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
	
	
	public function setSuitePageRpu($suitePageRpu){
		$this->_suitePageRpu = $suitePageRpu;
	}
	
	public function getSuitePageRpu(){
		return $this->_suitePageRpu;
	}
	
	public function getTableauTemoinLimitePage2(){
		return $this->_tableauTemoinLimitePage2;
	}
	
	public function setTableauTemoinLimitePage2($tableauTemoinLimitePage2) {
		$this->_tableauTemoinLimitePage2 = $tableauTemoinLimitePage2;
	}

	
	protected  function getNoteTC(){
		
		$Control = new DateHelper();
		$noteLineHeight = 22;
		$baseUrl = $_SERVER['SCRIPT_FILENAME'];
		$tabURI  = explode('public', $baseUrl);
		
		$nbLigne = 0;
		$maxLigne = 31;
		
		
		$tableauTemoinLimite = $this->getTableauTemoinLimite();
		$tableauTemoinInfos  = $this->getTableauTemoinInfos();
			
		$tableauTemoinLimitePdf2 = array();
		$tableauTemoinInfosPdf2 = array();
		$suitePageRpu = 0;

		$textInfo1  = $this->getTextInfos1();
		$textInfo2  = $this->getTextInfos2();
		$textInfo3  = $this->getTextInfos3();
		$textInfo4  = $this->getTextInfos4();
		$textInfo5  = $this->getTextInfos5();
		$textInfo6  = $this->getTextInfos6();
		$textInfo7  = $this->getTextInfos7();
		$textInfo8  = $this->getTextInfos8();
		$textInfo9  = $this->getTextInfos9();
		$textInfo10 = $this->getTextInfos10();

		//var_dump($tableauTemoinInfos); exit();
		
		/**
		 * **************************************************************************************
		 * ======================================================================================
		 * **************************************************************************************
		 */
		//************* Gestion de l'information n°1 ************
		//************* Gestion de l'information n°1 ************
		/*1)*/
		if($tableauTemoinLimite[1] == 1){
				
			if($textInfo1){
				$debut = $tableauTemoinInfos[1][0];
				$fin = $tableauTemoinInfos[1][1];
					
				if($debut < $fin){
					if($debut == 0){
						if($nbLigne < $maxLigne){
							if($nbLigne == 30){
								$nbLigne++;
							}else{
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
							$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $textInfo1[$i] ),
									$this->_leftMargin,
									$this->_yPosition);
								
							$this->_yPosition -= $noteLineHeight;
							$nbLigne ++;
							$j++;
						}
					}
						
					if($j == $fin){
						$tableauTemoinLimitePdf2 [1] [1] = 0;
						$tableauTemoinLimitePdf2 [1] [2] = 'tout est affiche';
					}else{
						$tableauTemoinLimitePdf2 [1] [1] = 1;
						$tableauTemoinLimitePdf2 [1] [2] = $j;
						$tableauTemoinLimitePdf2 [1] [3] = $fin;
						$tableauTemoinLimitePdf2 [1] [4] = 'il reste '.($fin - $j).' ligne(s) a afficher';
						$suitePageRpu = 1;
					}
				}else{
					$tableauTemoinLimitePdf2 [1] [1] = 0;
					$tableauTemoinLimitePdf2 [1] [2] = 'donnees deja affichees';
				}
			}else{
				$tableauTemoinLimitePdf2 [1] [1] = 0;
				$tableauTemoinLimitePdf2 [1] [2] = 'pas de donnees';
			}
				
		}
		//Fin Gestion de l'information n°1
		//Fin Gestion de l'information n°1
		/**
		 * **************************************************************************************
		 * ======================================================================================
		 * **************************************************************************************
		 */
		 	
		//************* Gestion de l'information n°2 ************
		//************* Gestion de l'information n°2 ************
		/*2)*/
		if($tableauTemoinLimite[2] == 1){
		
			if($textInfo2){
				$debut = $tableauTemoinInfos[2][0];
				$fin = $tableauTemoinInfos[2][1];
					
				if($debut < $fin){
					if($debut == 0){
						if($nbLigne < $maxLigne){
							if($nbLigne == 30){
								$nbLigne++;
							}else{
								
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
							$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $textInfo2[$i] ),
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
		//Fin Gestion de l'information n°2
		//Fin Gestion de l'information n°2
		/**
		 * **************************************************************************************
		 * ======================================================================================
		 * **************************************************************************************
		 */
		
		//************* Gestion de l'information n°3 ************
		//************* Gestion de l'information n°3 ************
		/*3)*/
		if($tableauTemoinLimite[3] == 1){
		
			if($textInfo3){
				$debut = $tableauTemoinInfos[3][0];
				$fin = $tableauTemoinInfos[3][1];
					
				if($debut < $fin){
					if($debut == 0){
						if($nbLigne < $maxLigne){
							if($nbLigne == 30){
								$nbLigne++;
							}else{
		
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
							$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $textInfo3[$i] ),
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
		//Fin Gestion de l'information n°3
		//Fin Gestion de l'information n°3
		/**
		 * **************************************************************************************
		 * ======================================================================================
		 * **************************************************************************************
		 */
		
		//************* Gestion de l'information n°4 ************
		//************* Gestion de l'information n°4 ************
		/*4)*/
		if($tableauTemoinLimite[4] == 1){
		
			if($textInfo4){
				$debut = $tableauTemoinInfos[4][0];
				$fin = $tableauTemoinInfos[4][1];
					
				if($debut < $fin){
					if($debut == 0){
						if($nbLigne < $maxLigne){
							if($nbLigne == 30){
								$nbLigne++;
							}else{
		
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
							$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $textInfo4[$i] ),
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
		//Fin Gestion de l'information n°4
		//Fin Gestion de l'information n°4
		/**
		 * **************************************************************************************
		 * ======================================================================================
		 * **************************************************************************************
		 */
		
		//************* Gestion de l'information n°5 ************
		//************* Gestion de l'information n°5 ************
		/*5)*/
		//var_dump($textInfo5 == ''); exit();
		if($tableauTemoinLimite[5] == 1){
 				
 			if($textInfo5 != 0){
 				if($nbLigne < $maxLigne){

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
 					$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $this->getListeModeSortie()[$textInfo5]),
 							$this->_leftMargin+93,
 							$this->_yPosition);
 					
 					if($textInfo5 == 4){ //Mutation
 					
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
 						$this->_page->drawText('Muter vers : ',
 								$this->_leftMargin+170,
 								$this->_yPosition);
 					
 						$this->_page->setFont($this->_policeContenu, 10.8);
 						$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $this->getListeServiceMutation()[$textInfo6]),
 								$this->_leftMargin+250,
 								$this->_yPosition);
 					}
 					else
 					if($textInfo5 == 5){ //Transfert
 							
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
 						$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $textInfo7),
 								$this->_leftMargin+265,
 								$this->_yPosition);
 					}
 					else
 					if($textInfo5 == 6){ //Evacuation
 					
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
 						$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $textInfo8),
 								$this->_leftMargin+255,
 								$this->_yPosition);
 					}
 					
 						
 					$this->_yPosition -= $noteLineHeight;
 					$nbLigne ++;
 					$tableauTemoinLimitePdf2 [5] [1] = 0;
 					$tableauTemoinLimitePdf2 [5] [2] = 'donnee deja afficher';
 				}else{
 					$tableauTemoinLimitePdf2 [5] [1] = 1;
 					$tableauTemoinLimitePdf2 [5] [2] = 'il y a une ligne a afficher';
 				}
 			}else{
 				$tableauTemoinLimitePdf2 [5] [1] = 0;
 				$tableauTemoinLimitePdf2 [5] [2] = 'il y a aucune donnee afficher';
 			}
 				
 		}
		//Fin Gestion de l'information n°5
		//Fin Gestion de l'information n°5
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
	
	
	
	
	//var_dump($this->_leftMargin); exit();
	/*$imageHeader = ZendPdf\Image::imageWithPath($tabURI[0].'public/images_icons/text_padding_right.png');
	 $this->_page->drawImage($imageHeader,
	 		$this->_leftMargin - 2, //-x1
	 		$this->_pageHeight - 215, //-y1
	 		63, //+x2
	 		$this->_yPosition - 5); //+y2
	*/
	
	
	
	
	
	
	
	
	
	
	
	
 					/*10 $alphabetMajus  = array('A','B','C','D','E','F','G','H','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
 					/*4  $alphabetMajus2 = array('I','J');
 					/*2  $alphabetSpecio = array("'","/","\\","-","_");
 					/** 545
 					  * A= 9; B= 9; C=9; D= 9; M= 11
 					  * a= 6;
 					  */
 					
 					/*CODE POUR TESTER L'AJUSTEMENT*/
 					
	                /*
	                $this->_yPosition -= $noteLineHeight;
 					$this->_yPosition -= $noteLineHeight;
 					
 					
 					
 					$alpha ="Testons notre premier script d'ajustement de texte. Ce script doit permettre d'ajuster du texte. L'ajustement du texte doit être automatique. Testons notre premier script d'ajustement de texte. Ce script doit permettre d'ajuster du texte. L'ajustement du texte doit être automatique.";
 					//$alpha ="ZAAAA ZZZZEE EEEDDFTGT TT TGTHYHYHYH UUUUIIIIIIIIHHH HHHHHHHHHHHHHGGGG HHHHHHHHHH YYYYYYYY UUUUUUUUUU";
 					
 					$j = 0;
 					$ligne = '';
 					for($i = 45 ; $i < 545 ; ){
 						if(in_array($alpha[$j], $alphabetMajus)){
 							$ligne .= $alpha[$j]; 
 							$i += 9;
 						}
 						else
 							if(in_array($alpha[$j], $alphabetMajus2))
 							{
 								$ligne .= $alpha[$j];
 								$i += 2;
 							}
 							else 
 								if(in_array($alpha[$j], $alphabetSpecio))
 								{
 									$ligne .= $alpha[$j];
 									$i += 2;
 								}
 								else
 									{
 										$ligne .= $alpha[$j];
 										$i += 2;
 									}
 							
 									$j++;
 					}		
 					//var_dump($this->_pageWidth -	$this->_leftMargin); exit();
 					
 					
 					
 					
 					$this->_page->setFont($this->_newTime, 12);
 					$this->_page->drawText($ligne,
 							$this->_leftMargin,
 							$this->_yPosition);
 						
 					$this->_page->setlineColor(new ZendPdf\Color\Html('black'));
 					$this->_page->setLineWidth(0.5);
 					$this->_page->drawLine($this->_leftMargin,
 							$this->_yPosition-2,
 							545,
 							$this->_yPosition-2);
 					
	 
	               */
	
	
	
	
	
	
	
	
	
	
}