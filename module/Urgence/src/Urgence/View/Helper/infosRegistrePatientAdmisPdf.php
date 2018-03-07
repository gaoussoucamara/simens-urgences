<?php
namespace Urgence\View\Helper;

use Consultation\View\Helpers\fpdf181\fpdf;

class infosRegistrePatientAdmisPdf extends fpdf
{

	function Footer()
	{
		// Positionnement à 1,5 cm du bas
		$this->SetY(-15);
		
		$this->SetFillColor(0,128,0);
		$this->Cell(0,0.3,"",0,1,'C',true);
		$this->SetTextColor(0,0,0);
		$this->SetFont('Times','',9.5);
		$this->Cell(125,5,'Téléphone: 33 961 00 21 ',0,0,'L',false);
		$this->SetTextColor(128);
		$this->SetFont('Times','I',9);
		$this->Cell(20,8,'Page '.$this->PageNo(),0,0,'C',false);
		$this->SetTextColor(0,0,0);
		$this->SetFont('Times','',9.5);
		$this->Cell(125,5,'SIMENS+: www.simens.sn',0,0,'R',false);
	}
	
	protected $B = 0;
	protected $I = 0;
	protected $U = 0;
	protected $HREF = '';
	
	function WriteHTML($html)
	{
		// Parseur HTML
		$html = str_replace("\n",' ',$html);
		$a = preg_split('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE);
		foreach($a as $i=>$e)
		{
			if($i%2==0)
			{
				// Texte
				if($this->HREF)
					$this->PutLink($this->HREF,$e);
				else
					$this->Write(5,$e);
			}
			else
			{
				// Balise
				if($e[0]=='/')
					$this->CloseTag(strtoupper(substr($e,1)));
				else
				{
					// Extraction des attributs
					$a2 = explode(' ',$e);
					$tag = strtoupper(array_shift($a2));
					$attr = array();
					foreach($a2 as $v)
					{
						if(preg_match('/([^=]*)=["\']?([^"\']*)/',$v,$a3))
							$attr[strtoupper($a3[1])] = $a3[2];
					}
					$this->OpenTag($tag,$attr);
				}
			}
		}
	}
	
	function OpenTag($tag, $attr)
	{
		// Balise ouvrante
		if($tag=='B' || $tag=='I' || $tag=='U')
			$this->SetStyle($tag,true);
		if($tag=='A')
			$this->HREF = $attr['HREF'];
		if($tag=='BR')
			$this->Ln(5);
	}
	
	function CloseTag($tag)
	{
		// Balise fermante
		if($tag=='B' || $tag=='I' || $tag=='U')
			$this->SetStyle($tag,false);
		if($tag=='A')
			$this->HREF = '';
	}
	
	function SetStyle($tag, $enable)
	{
		// Modifie le style et sélectionne la police correspondante
		$this->$tag += ($enable ? 1 : -1);
		$style = '';
		foreach(array('B', 'I', 'U') as $s)
		{
			if($this->$s>0)
				$style .= $s;
		}
		$this->SetFont('',$style);
	}
	
	function PutLink($URL, $txt)
	{
		// Place un hyperlien
		$this->SetTextColor(0,0,255);
		$this->SetStyle('U',true);
		$this->Write(5,$txt,$URL);
		$this->SetStyle('U',false);
		$this->SetTextColor(0);
	}
	
	
	
	
	
	
	
	
	protected $tabInformations;
	protected $nomService;
	protected $infosComp;
	protected $periodeConsultation;
	protected $dateAdmission;
	protected $listePatientsAdmis;
	
	
	public function setTabInformations($tabInformations)
	{
		$this->tabInformations = $tabInformations;
	}
	
	public function getTabInformations()
	{
		return $this->tabInformations;
	}
	
	public function getNomService()
	{
		return $this->nomService;
	}
	
	public function setNomService($nomService)
	{
		$this->nomService = $nomService;
	}
	
	public function getPeriodeConsultation()
	{
		return $this->periodeConsultation;
	}
	
	public function setPeriodeConsultation($periodeConsultation)
	{
		$this->periodeConsultation = $periodeConsultation;
	}

	public function getInfosComp()
	{
		return $this->infosComp;
	}
	
	public function setInfosComp($infosComp)
	{
		$this->infosComp = $infosComp;
	}
	
	public function getListePatientsAdmis()
	{
		return $this->listePatientsAdmis;
	}
	
	public function setListePatientsAdmis($listePatientsAdmis)
	{
		$this->listePatientsAdmis = $listePatientsAdmis;
	}
	
	public function getDateAdmission()
	{
		return $this->dateAdmission;
	}
	
	public function setDateAdmission($dateAdmission)
	{
		$this->dateAdmission = $dateAdmission;
	}
	
	function EnTetePage()
	{
		$this->SetFont('Times','',10.3);
		$this->SetTextColor(0,0,0);
		$this->Cell(0,4,"République du Sénégal");
		$this->SetFont('Times','',8.5);
		$this->Cell(0,4,"Date d'admission : ".(new DateHelper())->convertDate($this->getDateAdmission()),0,0,'R');
		$this->SetFont('Times','',10.3);
		$this->Ln(5.4);
		$this->Cell(100,4,"Ministère de la santé et de l'action sociale");
		
		$this->AddFont('timesbi','','timesbi.php');
		$this->Ln(5.4);
		$this->Cell(100,4,"C.H.R de Saint-louis");
		$this->Ln(5.4);
		$this->SetFont('timesbi','',10.3);
		$this->Cell(14,4,"Service : ",0,0,'L');
		$this->SetFont('Times','',10.3);
		$this->Cell(86,4,$this->getNomService(),0,0,'L');
		
		$this->Ln(5);
		$this->SetFont('Times','',12.3);
		$this->SetTextColor(0,128,0);
		$this->Cell(270,5,"LISTE DES PATIENTS ADMIS",0,0,'C');
		$this->Ln(5.5);
		$this->SetFillColor(0,128,0);
		$this->Cell(0,0.3,"",0,1,'C',true);
		
		// EMPLACEMENT DU LOGO
		// EMPLACEMENT DU LOGO
		$baseUrl = $_SERVER['SCRIPT_FILENAME'];
		$tabURI  = explode('public', $baseUrl);
		$this->Image($tabURI[0].'public/images_icons/hrsl.png', 249, 19, 35, 15);
		
	}
	
	function CorpsDocument()
	{
		/*
		if($this->getDateAdmission()){
			//$dateConvert = new DateHelper();
			//$date_debut = $dateConvert->convertDate($this->getPeriodeConsultation()[0]);
			//$date_fin   = $dateConvert->convertDate($this->getPeriodeConsultation()[1]);
			
			$this->Ln(2);
			$this->SetFillColor(220,220,220);
			$this->SetDrawColor(205,193,197);
			$this->SetTextColor(0,0,0);
			$this->AddFont('zap','','zapfdingbats.php');
			$this->SetFont('zap','',13);
			
			$this->SetFillColor(255,255,255);
			$this->Cell(103.5 ,7,'',0,0,'L',1);
			
			$this->SetFillColor(220,220,220);
			$this->SetLineWidth(0.5);
			$this->Cell(5,6,'B','BLT',0,'L',1); //BLT
			
			$this->AddFont('timesb','','timesb.php');
			$this->AddFont('timesi','','timesi.php');
			$this->AddFont('times','','times.php');
			
			$this->SetFont('times','',11);
			$this->Cell(58,6,"Date d'admission : ".$this->getDateAdmission(),'BRT',0,'C',1);
			
			$this->SetFillColor(255,255,255);
			$this->Cell(103.5 ,6,'','L',0,'L',1); //L
			
			$this->Ln(3);
			$this->SetLineWidth(0);
		}
		*/

		
		$listePatientsAdmis = $this->getListePatientsAdmis(); 
	
		if($listePatientsAdmis['iTotalDisplayRecords'] != 0){
			
			
			//EN TETE DE LA LISTE DES PATIENTS ADMIS
			//EN TETE DE LA LISTE DES PATIENTS ADMIS
			//EN TETE DE LA LISTE DES PATIENTS ADMIS
			
			$this->Ln(2.5);
			$this->SetDrawColor(205,193,197);
			$this->SetFillColor(220,220,220);
			
			$this->SetTextColor(0,0,0);
			$this->AddFont('zap','','zapfdingbats.php');
			$this->AddFont('timesb','','timesb.php');
			$this->AddFont('timesbi','','timesbi.php');
			$this->AddFont('timesi','','timesi.php');
			$this->AddFont('times','','times.php');
			
			$hauteurLigneTete = 7;
			$this->SetFont('timesbi','',9);
			$this->Cell(8,$hauteurLigneTete,'Ord',1,0,'L',1);
			
			//$this->SetFont('zap','',6);
			//$this->Cell(3,$hauteurLigneTete,'t','BLT',0,'L',1);
			
			$this->SetFont('timesb','',10);
			$this->Cell(24,$hauteurLigneTete,'N° DOSSIER',1,0,'L',1);
			$this->Cell(38,$hauteurLigneTete,'PRENOM',1,0,'L',1);
			$this->Cell(28,$hauteurLigneTete,'NOM',1,0,'L',1);
			$this->Cell(10,$hauteurLigneTete,'AGE',1,0,'L',1);
			$this->Cell(42,$hauteurLigneTete,'ACTES',1,0,'L',1);
			$this->Cell(72,$hauteurLigneTete,'EXAMENS COMPLEMENTAIRES',1,0,'L',1);
			$this->Cell(48,$hauteurLigneTete,'DIAGNOSTIC',1,0,'L',1);
			
			$this->Ln(8);
			
			for($i=0 ; $i<$listePatientsAdmis['iTotalDisplayRecords'] ; $i++){
				
				$donneesPatientsAdmis = $listePatientsAdmis['aaData'];
				
				//Recuperer le nombre d'actes et d'examens
				$listeDesActes = $donneesPatientsAdmis[$i][4];
				$listeDesExamens = $donneesPatientsAdmis[$i][5];
				$nbActes = count($listeDesActes);
				$nbExamens = count($listeDesExamens);
				$maxNbActeExamen = max(array($nbActes, $nbExamens));
				$hauteurLigne = 5;
				if($maxNbActeExamen != 0){
					$hauteurLigne = 5*$maxNbActeExamen;
				}
				
				if($i%2 == 0){
					$this->SetFillColor(249,249,249);
					$this->SetDrawColor(220,220,220);
				}else{
					$this->SetDrawColor(205,193,197);
					$this->SetFillColor(220,220,220);
				}
				
				$this->SetFont('Timesi','',9.5);
				$this->Cell(8,$hauteurLigne,($i+1).'.',1,0,'L');
					
				$this->SetFont('Times','',9.5,1);
				$this->Cell(24,$hauteurLigne,$donneesPatientsAdmis[$i][0],'BLT',0,'L',1); //BLT
				$this->Cell(38,$hauteurLigne,$donneesPatientsAdmis[$i][2],'BT',0,'L',1); //BT
				$this->Cell(28,$hauteurLigne,$donneesPatientsAdmis[$i][1],'BT',0,'L',1);
				$this->Cell(10,$hauteurLigne,$donneesPatientsAdmis[$i][3],'BT',0,'C',1);
				
				//Affichage des actes
				$this->Cell(42,$hauteurLigne,'','BT',0,'L',1);
				$interLigne = 0;
				for($iacte=0 ; $iacte<$nbActes ; $iacte++){
					$y = $this->GetY()+3.5+$interLigne;
					$this->Text(123, $y, iconv ('UTF-8' , 'windows-1252', '- '.$listeDesActes[$iacte]));
					
					$interLigne += 5;
				}
				
				//Affichage des examens 
				$this->Cell(72,$hauteurLigne,'','BT',0,'L',1);
				$interLigne = 0;
				for($iexam=0 ; $iexam<$nbExamens ; $iexam++){
					$y = $this->GetY()+3.5+$interLigne;
					
					$this->Text(165, $y, iconv ('UTF-8' , 'windows-1252', '- '));
					
					//Libelle type d'examen
					$libelleTypeExamen = $listeDesExamens[$iexam][0];
					$this->SetFont('Timesi','',7);
					$this->Text(167, $y, iconv ('UTF-8' , 'windows-1252', $libelleTypeExamen));

					//Id type examen
					$idTypeExamen = $listeDesExamens[$iexam][2];
					
					//Libelle examen
					if($idTypeExamen == 1){
						$this->SetFont('Times','',7.5);
						$this->Text(185, $y, iconv ('UTF-8' , 'windows-1252', ' : '.$listeDesExamens[$iexam][1]));
					}else if($idTypeExamen == 2){
						$this->SetFont('Times','',7.5);
						$this->Text(193, $y, iconv ('UTF-8' , 'windows-1252', ' : '.$listeDesExamens[$iexam][1]));
					}else if($idTypeExamen == 3){
						$this->SetFont('Times','',7.5);
						$this->Text(184, $y, iconv ('UTF-8' , 'windows-1252', ' : '.$listeDesExamens[$iexam][1]));
					}else if($idTypeExamen == 4){
						$this->SetFont('Times','',7.5);
						$this->Text(175, $y, iconv ('UTF-8' , 'windows-1252', ' : '.$listeDesExamens[$iexam][1]));
					}else if($idTypeExamen == 5){
						$this->SetFont('Times','',7.5);
						$this->Text(180, $y, iconv ('UTF-8' , 'windows-1252', ' : '.$listeDesExamens[$iexam][1]));
					}else{
						$this->SetFont('Times','',7.5);
						$this->Text(195, $y, iconv ('UTF-8' , 'windows-1252', ' : '.$listeDesExamens[$iexam][1]));						
					}
					
					$interLigne += 5;
				}
				
				
				
				$this->SetFont('Times','',7.5);
				$this->Cell(48,$hauteurLigne,' ---','BTR',1,'L',1);
				$this->Ln(1);
			}
			
				
		}
	}
	
	//IMPRESSION DES INFOS STATISTIQUES
	//IMPRESSION DES INFOS STATISTIQUES
	function ImpressionInfosStatistiques()
	{
		$this->AddPage();
		$this->EnTetePage();
		$this->CorpsDocument();
	}

}

?>
