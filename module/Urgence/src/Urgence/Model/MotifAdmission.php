<?php
namespace Urgence\Model;

class MotifAdmission{
	public $id_motif;
	public $id_admission_urgence;
	public $libelle_motif;
	public $type_pathologie;
	public $date;
	public $libelle_type_pathologie;

	public function exchangeArray($data) {
		$this->id_motif = (! empty ( $data ['id_motif'] )) ? $data ['id_motif'] : null;
		$this->id_admission_urgence = (! empty ( $data ['id_admission_urgence'] )) ? $data ['id_admission_urgence'] : null;
		$this->libelle_motif = (! empty ( $data ['libelle_motif'] )) ? $data ['libelle_motif'] : null;
		$this->type_pathologie = (! empty ( $data ['type_pathologie'] )) ? $data ['type_pathologie'] : null;
		$this->date = (! empty ( $data ['date'] )) ? $data ['date'] : null;
		$this->libelle_type_pathologie = (! empty ( $data ['libelle_type_pathologie'] )) ? $data ['libelle_type_pathologie'] : null;
	}
	
	public function getArrayCopy()
	{
		return get_object_vars($this);
	}
}