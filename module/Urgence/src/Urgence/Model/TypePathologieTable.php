<?php

namespace Urgence\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;

class TypePathologieTable {
	protected $tableGateway;
	public function __construct(TableGateway $tableGateway) {
		$this->tableGateway = $tableGateway;
	}
	
	public function getListeTypePathologie(){
		$rowset = $this->tableGateway->select ( )->toArray();
		return $rowset;
	}
	
	public function getListeTypePathologieOrdreDecroissant(){
		return $this->tableGateway->select ( function (Select $select){
					$select->order('id DESC');
				})->toArray();
	}
	
	public function insertTypePathologie($tabTypePathologie, $id_employe){
		for($i = 0 ; $i < count($tabTypePathologie) ; $i++){
			$this->tableGateway->insert(
					array(
							'libelle_type_pathologie' => $tabTypePathologie[$i],
							'id_medecin' => $id_employe
			        )
			);
		}
	}
	
	
	public function updateTypePathologie($idType, $libelleType, $id_employe){
		$this->tableGateway->update(
				array('libelle_type_pathologie' => $libelleType, 'id_medecin_modif' => $id_employe),
				array('id' => $idType)
		);
	}
	
}