<?php

namespace Urgence\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;

class AdmissionTable {
	protected $tableGateway;
	public function __construct(TableGateway $tableGateway) {
		$this->tableGateway = $tableGateway;
	}
	
	public function getPatientAdmis($id_admission){
		$id_admission = ( int ) $id_admission;
		$rowset = $this->tableGateway->select ( array (
				'id_admission' => $id_admission
		) );
		$row =  $rowset->current ();
		if (! $row) {
			$row = null;
		}
		return $row;
	}
	
	public function addAdmission($donnees){
	
		$db = $this->tableGateway->getAdapter();
		$sql = new Sql($db);
		$sQuery = $sql->insert() ->into('admission_urgence') ->values( $donnees );
	
		$stat = $sql->prepareStatementForSqlObject($sQuery);
		$id_admission = $stat->execute()->getGeneratedValue();
		return $id_admission;
	}
	
	public function updateAdmission($donnees, $id_admission){
		$this->tableGateway->update ( $donnees , array ( 'id_admission' => $id_admission ) );
	}
	
	
	//GESTION DE LA SUPPRESSION PAR L'INFIRMIER DE TRI
	//GESTION DE LA SUPPRESSION PAR L'INFIRMIER DE TRI
	public function getAdmissionParInfirmierTri($id_admission){
		$id_admission = ( int ) $id_admission;
		$rowset = $this->tableGateway->select ( array (
				'id_admission' => $id_admission,
				'id_infirmier_service' => null,
		) );
		$row =  $rowset->current ();
		if (! $row) {
			$row = null;
		}
		return $row;
	}
	
	public function deleteAdmission($id_admission){
		$this->tableGateway->delete ( array ( 'id_admission' => $id_admission ) );
	}
	
	public function addModeEntreeModeTransport($donnees){
	
		$db = $this->tableGateway->getAdapter();
		$sql = new Sql($db);
		$sQuery = $sql->insert() ->into('mode_entree_admission') ->values( $donnees );
	
		$sql->prepareStatementForSqlObject($sQuery)->execute();
	}
	
	public function getModeEntreeModeTransport($id_admission){
			
		$adapter = $this->tableGateway->getAdapter ();
		$sql = new Sql ( $adapter );
		$select = $sql->select ( 'mode_entree_admission' );
		$select->columns ( array ( '*' ) );
		$select->where ( array ( 'id_admission' => $id_admission ) );
		
		return $sql->prepareStatementForSqlObject ( $select )->execute ()->current();
	}
	
	public  function updateModeEntreeModeTransport($donnees, $id_admission){
		$db = $this->tableGateway->getAdapter();
		$sql = new Sql($db);
		$sQuery = $sql->update()
		->table('mode_entree_admission')->set( $donnees )
		->where(array('id_admission' => $id_admission ));
	
		$sql->prepareStatementForSqlObject($sQuery)->execute();
	}
	
	public function getAdmissionUrgence($id_admission){
		$id_admission = ( int ) $id_admission;
		$rowset = $this->tableGateway->select ( array (
				'id_admission' => $id_admission,
		) );
		$row =  $rowset->current ();
		if (! $row) {
			$row = null;
		}
		return $row;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	public function getPatientsAdmis() {
		$today = new \DateTime ( 'now' );
		$date = $today->format ( 'Y-m-d' );
		$adapter = $this->tableGateway->getAdapter ();
		$sql = new Sql ( $adapter );
		$select = $sql->select ();
		$select->from ( array (
				'p' => 'patient'
		) );
		$select->columns ( array () );
		$select->join(array('pers' => 'personne'), 'pers.ID_PERSONNE = p.ID_PERSONNE', array(
				'Nom' => 'NOM',
				'Prenom' => 'PRENOM',
				'Datenaissance' => 'DATE_NAISSANCE',
				'Sexe' => 'SEXE',
				'Adresse' => 'ADRESSE',
				'Nationalite' => 'NATIONALITE_ACTUELLE',
				'Id' => 'ID_PERSONNE'
		));
		$select->join ( array (
				'a' => 'admission'
		), 'p.ID_PERSONNE = a.id_patient', array (
				'Id_admission' => 'id_admission'
		) );
		$select->join ( array (
				's' => 'service'
		), 's.ID_SERVICE = a.id_service', array (
				'Id_Service' => 'ID_SERVICE',
				'Nomservice' => 'NOM'
		) );
				$select->where ( array (
				'a.date_cons' => $date
		) );
		
		$select->order ( 'id_admission DESC' );
		$stat = $sql->prepareStatementForSqlObject ( $select );
		$result = $stat->execute ();
		return $result;
	}
	
	public function nbAdmission() {
		$today = new \DateTime ( 'now' );
		$date = $today->format ( 'Y-m-d' );
		$adapter = $this->tableGateway->getAdapter ();
		$sql = new Sql ( $adapter );
		$select = $sql->select ( 'admission' );
		$select->columns ( array (
				'id_admission'
		) );
		$select->where ( array (
				'date_cons' => $date
		) );
		$stat = $sql->prepareStatementForSqlObject ( $select );
		$nb = $stat->execute ()->count ();
		return $nb;
	}
	
	/*
	 * Recupérer la liste des patients admis et déjà consultés pour aujourd'hui
	 */
	public function getPatientAdmisCons(){
		$today = new \DateTime ( 'now' );
		$date = $today->format ( 'Y-m-d' );
		
		$adapter = $this->tableGateway->getAdapter ();
		$sql = new Sql ( $adapter );
		$select = $sql->select ( 'consultation' );
		$select->columns ( array (
				'ID_PATIENT'
		) );
		$select->where ( array (
				'DATEONLY' => $date,
		) );
		
		$result = $sql->prepareStatementForSqlObject ( $select )->execute ();
		$tab = array();
		foreach ($result as $res) {
			$tab[] = $res['ID_PATIENT'];
		}
		
		return $tab;
	}
	
	/*
	 * Fonction qui vérifie est ce que le patient n'est pas déja consulté
	 */
	public function verifierPatientConsulter($idPatient, $idService){
		$today = new \DateTime ( 'now' );
		$date = $today->format ( 'Y-m-d' );
		
		$adapter = $this->tableGateway->getAdapter ();
		$sql = new Sql ( $adapter );
		$select = $sql->select ( 'consultation' );
		$select->columns ( array (
				'ID_PATIENT'
		) );
		$select->where ( array (
				'DATEONLY' => $date,
				'ID_SERVICE' => $idService,
				'ID_PATIENT' => $idPatient,
		) );
		
		return $sql->prepareStatementForSqlObject ( $select )->execute ()->current();
	}
	
	public function deleteAdmissionPatient($id, $idPatient, $idService){
		if($this->verifierPatientConsulter($idPatient, $idService)){
		    return 1;
		} else {
			$this->tableGateway->delete(array('id_admission'=> $id));
			return 0;
		}

	}
	
	public function getLastAdmission() {
		$db = $this->tableGateway->getAdapter();
		$sql = new Sql($db);
		$sQuery = $sql->select('admission')
		->order('id_admission DESC');
		$requete = $sql->prepareStatementForSqlObject($sQuery);
		return $requete->execute()->current();
	}
	
	//Ajouter la consultation dans la table << consultation >> pour permettre au medecin de pouvoir lui même ajouter les constantes
	//Ajouter la consultation dans la table << consultation >> pour permettre au medecin de pouvoir lui même ajouter les constantes
	public function addConsultation($values , $IdDuService){
		$today = new \DateTime ( 'now' );
		$date = $today->format ( 'Y-m-d H:i:s' );
		$dateOnly = $today->format ( 'Y-m-d' );
		
		$db = $this->tableGateway->getAdapter();
		$this->tableGateway->getAdapter()->getDriver()->getConnection()->beginTransaction();
		try {
	
			$dataconsultation = array(
					'ID_CONS'=> $values->get ( "id_cons" )->getValue (),
					'ID_PATIENT'=> $values->get ( "id_patient" )->getValue (),
					'DATE'=> $date,
 					'DATEONLY' => $dateOnly,
					'HEURECONS' => $values->get ( "heure_cons" )->getValue (),
					'ID_SERVICE' => $IdDuService
			);
			
			$sql = new Sql($db);
			$sQuery = $sql->insert()
			->into('consultation')
			->values($dataconsultation);
			$sql->prepareStatementForSqlObject($sQuery)->execute();
	
			$this->tableGateway->getAdapter()->getDriver()->getConnection()->commit();
		} catch (\Exception $e) {
			$this->tableGateway->getAdapter()->getDriver()->getConnection()->rollback();
		}
	}
	
	public function addConsultationEffective($id_cons){
		$db = $this->tableGateway->getAdapter();
		$sql = new Sql($db);
		$sQuery = $sql->insert()
		->into('consultation_effective')
		->values(array('ID_CONS' => $id_cons));
		$requete = $sql->prepareStatementForSqlObject($sQuery);
		$requete->execute();
	}
	
}