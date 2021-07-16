<?php
namespace Urgence\Model;

class TypePathologie {
	public $id;
	public $libelle_type_pathologie;
	public $date_enregistrement;
	public $id_medecin;
	
	

	public function exchangeArray($data) {
		$this->id = (! empty ( $data ['id'] )) ? $data ['id'] : null;
		$this->libelle_type_pathologie = (! empty ( $data ['libelle_type_pathologie'] )) ? $data ['libelle_type_pathologie'] : null;
		$this->date_enregistrement = (! empty ( $data ['date_enregistrement'] )) ? $data ['date_enregistrement'] : null;
		$this->id_medecin = (! empty ( $data ['id_medecin'] )) ? $data ['id_medecin'] : null;
	}
	public function getArrayCopy() {
		return get_object_vars ( $this );
	}
}