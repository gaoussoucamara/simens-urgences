<?php
namespace Urgence\Model;

class Admission {
	public $id_admission;
	public $id_patient;
	public $niveau;
	public $salle;
	public $lit;
	public $couloir;
	public $date;
	public $id_infirmier_tri;
	public $id_infirmier_service;
	public $heure_infirmier_tri;
	public $heure_infirmier_service;
	

	public function exchangeArray($data) {
		$this->id_admission = (! empty ( $data ['id_admission'] )) ? $data ['id_admission'] : null;
		$this->id_patient = (! empty ( $data ['id_patient'] )) ? $data ['id_patient'] : null;
		$this->niveau = (! empty ( $data ['niveau'] )) ? $data ['niveau'] : null;
		$this->salle = (! empty ( $data ['salle'] )) ? $data ['salle'] : null;
		$this->lit = (! empty ( $data ['lit'] )) ? $data ['lit'] : null;
		$this->couloir = (! empty ( $data ['couloir'] )) ? $data ['couloir'] : null;
		$this->date = (! empty ( $data ['date'] )) ? $data ['date'] : null;
		$this->id_infirmier_tri = (! empty ( $data ['id_infirmier_tri'] )) ? $data ['id_infirmier_tri'] : null;
		$this->id_infirmier_service = (! empty ( $data ['id_infirmier_service'] )) ? $data ['id_infirmier_service'] : null;
		$this->heure_infirmier_tri = (! empty ( $data ['$heure_infirmier_tri'] )) ? $data ['$heure_infirmier_tri'] : null;
		$this->heure_infirmier_service = (! empty ( $data ['heure_infirmier_service'] )) ? $data ['heure_infirmier_service'] : null;
	}
	public function getArrayCopy() {
		return get_object_vars ( $this );
	}
}