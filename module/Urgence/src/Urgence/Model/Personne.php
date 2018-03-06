<?php

namespace Urgence\Model;

class Personne{
	public $numero_dossier;
	public $nom;
	public $prenom;
	public $date_naissace;
	public $adresse;
	public $date_enregistrement;
	public $age;
	public $sexe;
	public $id_personne;
	
	public function exchangeArray($data) {
		//$this->id_personne = (! empty ( $data ['ID_PERSONNE'] )) ? $data ['ID_PERSONNE'] : null;
		//$this->numero_dossier = (! empty ( $data ['NUMERO_DOSSIER'] )) ? $data ['NUMERO_DOSSIER'] : null;
	
		 
		/*
		 * ADRESSE URL RELATIF
		*/
		$baseUrl = $_SERVER['REQUEST_URI'];
		$tabURI  = explode('public', $baseUrl);
		
		
		$this->numero_dossier = "<span style='font-size: 19px;'>1 054 022018<span style='display: none;'> 1054022018</span></span>";
		$this->nom = "DIALLO";
		$this->prenom = "";
		$this->date_naissace = "";
		$this->adresse = "";
		$this->date_enregistrement = "";
		$this->age = "";
		$this->sexe  ="<infoBulleVue> <a href='".$tabURI[0]."public/urgence/info-patient/id_patient/".$data ['ID_PERSONNE']."'>";
		$this->sexe .="<img style='display: inline; margin-right: 10%;' src='".$tabURI[0]."public/images_icons/voir2.png' title='d&eacute;tails'></a></infoBulleVue>";
		$this->id_personne = "";

		
	}
	
	public function getArrayCopy() {
		return array_values(get_object_vars ( $this ));
	}
	
}