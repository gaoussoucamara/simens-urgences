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
	
	public function getListeActes()
	{
		$adapter = $this->tableGateway->getAdapter ();
		$sql = new Sql ( $adapter );
		$select = $sql->select ();
		$select->from(array('au'=>'liste_acte_urg'));
		$select->columns(array ('id','libelle'));
		$select->order('id asc');
		$result = $sql->prepareStatementForSqlObject($select)->execute();

		$options = array(0 => '');
		foreach ($result as $data) {
			$options[$data['id']] = $data['libelle'];
		}
		return $options;
	}
	
	public function getListeExamenComp()
	{
		$adapter = $this->tableGateway->getAdapter ();
		$sql = new Sql ( $adapter );
		$select = $sql->select ();
		$select->from(array('au'=>'liste_typeexamencomp_urg'));
		$select->columns(array ('id','libelle'));
		$select->order('id asc');
		$result = $sql->prepareStatementForSqlObject($select)->execute();
	
		$options = array(0 => '');
		foreach ($result as $data) {
			$options[$data['id']] = $data['libelle'];
		}
		return $options;
	}
	
	/**
	 * Récuperer la liste des dates aux quelles il y a des actes  
	 * @param id du patient $id_patient
	 */
	public function getListeDesDatesDesActesDuPatient($id_patient)
	{

		$adapter = $this->tableGateway->getAdapter ();
		$sql = new Sql ( $adapter );
		$select = $sql->select ();
		$select->from(array('dau'=>'demande_acte_urg'))->columns(array ('id_acte_dem' => 'id_acte'));
		$select->join(array('lau'=>'liste_acte_urg') , 'lau.id = dau.id_acte' , array('libelle_acte' => 'libelle'));
		$select->join(array('au'=>'admission_urgence') , 'au.id_admission = dau.id_admission' , array('date_admission' => 'date'));
		$select->where(array('au.id_patient' => $id_patient));
		$select->order('au.date DESC');
		$select->group('date_admission');
		
		$result = $sql->prepareStatementForSqlObject($select)->execute();
		
		$listeDateAdmission = array();
		
		foreach ($result as $data) {
				
			if(!in_array($data['date_admission'], $listeDateAdmission)){
				$date_admission = $data['date_admission'];
				$listeDateAdmission[] = $date_admission;
			}
		}
		
		return $listeDateAdmission;
		
	}
	
	/**
	 * Recuperer la liste des actes de d'une date d'admission d'un patient
	 * @param id du patient $id_patient
	 */
	public function getListeDesActesDuPatient($id_patient, $date_admission)
	{
		$adapter = $this->tableGateway->getAdapter ();
		$sql = new Sql ( $adapter );
		$select = $sql->select ();
		$select->from(array('dau'=>'demande_acte_urg'))->columns(array ('id_acte_dem' => 'id_acte'));
		$select->join(array('lau'=>'liste_acte_urg') , 'lau.id = dau.id_acte' , array('libelle_acte' => 'libelle'));
		$select->join(array('au'=>'admission_urgence') , 'au.id_admission = dau.id_admission' , array('date_admission' => 'date'));
		$select->where(array('au.id_patient' => $id_patient, 'au.date' => $date_admission));
		
		$result = $sql->prepareStatementForSqlObject($select)->execute();
	
		$listeDesActes = array();
		
		foreach ($result as $data) {
			$listeDesActes[] = $data['libelle_acte'];
		}
		
		return $listeDesActes;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	/**
	 * Recuperer la liste des dates des examens complementaires du patient
	 * @param id du patient $id_patient
	 */
	public function getListeDesDatesDesExamensComplementairesDuPatient($id_patient)
	{
		$adapter = $this->tableGateway->getAdapter ();
		$sql = new Sql ( $adapter );
		$select = $sql->select ();
		$select->from(array('deu'=>'demande_examen_urg'))->columns(array ('id_examen_dem' => 'id_examen'));
		$select->join(array('leu'=>'liste_examencomp_urg') , 'leu.id = deu.id_examen' , array('libelle_examen' => 'libelle'));
		$select->join(array('au'=>'admission_urgence') , 'au.id_admission = deu.id_admission' , array('date_admission' => 'date'));
		$select->where(array('au.id_patient' => $id_patient));
		$select->order('au.date DESC');
		$select->group('date_admission');
	
		$result = $sql->prepareStatementForSqlObject($select)->execute();
	
		$listeDateAdmission = array();
	
		foreach ($result as $data) {
	
			if(!in_array($data['date_admission'], $listeDateAdmission)){
				$date_admission = $data['date_admission'];
				$listeDateAdmission[] = $date_admission;
			}
	
		}
	
		return $listeDateAdmission;
	}
	
	/**
	 * Recuperer la liste des examens complémentaires d'une date d'admission d'un patient
	 * @param id du patient $id_patient
	 */
	public function getListeDesExamensComplementairesDuPatient($id_patient, $date_admission)
	{
		$adapter = $this->tableGateway->getAdapter ();
		$sql = new Sql ( $adapter );
		$select = $sql->select ();
		$select->from(array('deu'=>'demande_examen_urg'))->columns(array ('id_examen_dem' => 'id_examen'));
		$select->join(array('leu'=>'liste_examencomp_urg') , 'leu.id = deu.id_examen' , array('libelle_examen' => 'libelle'));
		$select->join(array('ltu'=>'liste_typeexamencomp_urg') , 'ltu.id = leu.type' , array('libelle_type' => 'libelle'));
		$select->join(array('au'=>'admission_urgence') , 'au.id_admission = deu.id_admission' , array('date_admission' => 'date'));
		$select->where(array('au.id_patient' => $id_patient, 'au.date' => $date_admission));
	
		$result = $sql->prepareStatementForSqlObject($select)->execute();
	
		$listeTypesDesExamens = array();
		$listeDesExamens = array();
	
		foreach ($result as $data) {
			$listeTypesDesExamens[] = $data['libelle_type'];
			$listeDesExamens[] = $data['libelle_examen'];
		}
	
		return array($listeTypesDesExamens, $listeDesExamens);
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}