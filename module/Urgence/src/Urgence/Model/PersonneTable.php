<?php

namespace Urgence\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;

class PersonneTable {
	protected $tableGateway;
	
	public function __construct(TableGateway $tableGateway) {
		$this->tableGateway = $tableGateway;
	}
	
	public function fetchAll() {
		$resultSet = $this->tableGateway->select (function(Select $select){
			$select->join('patient', 'patient.ID_PERSONNE = personne.ID_PERSONNE', array('NUMERO_DOSSIER'));
		});
		return $resultSet;
	}
}