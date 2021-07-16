<?php

namespace Urgence\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;

class PathologieTable {
	
	protected $tableGateway;
	
	
	public function __construct(TableGateway $tableGateway) {
		$this->tableGateway = $tableGateway;
	}

    public function getListePathologie(){
		$rowset = $this->tableGateway->select ( )->toArray();
		return $rowset;
	}
	
	public function getListePathologieOrdreDecroissant($id_type_pathologie){
		$rowset = $this->tableGateway->select (function (Select $select) use ($id_type_pathologie) { 
			            $select->where(array('type_pathologie = ?' => $id_type_pathologie));
			            $select->order('id DESC'); 
		          })->toArray();
		return $rowset;
	}
	
	public function getLaPathologie($idPathologie){
		return $this->tableGateway->select ( array('id' => $idPathologie))->current();
	}
	
	public function insertPathologie($tabTypePathologie, $tabPathologie, $id_medecin){
		for($i = 0 ; $i  < count($tabPathologie) ; $i++) {
			$this->tableGateway->insert(
					array(
							'libelle_pathologie' => $tabPathologie[$i],
							'type_pathologie' => $tabTypePathologie[$i],
							'id_medecin' =>  $id_medecin,
			        )
		    );
		}
	}
	
	
}