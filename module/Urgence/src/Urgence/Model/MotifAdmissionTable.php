<?php
namespace Urgence\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
class MotifAdmissionTable{
	
	protected $tableGateway;
	
	public function __construct(TableGateway $tableGateway) {
		$this->tableGateway = $tableGateway;
	}
	
	public function addMotifAdmission($values, $id_admission){
		
		for($i=1 ; $i<=5; $i++){
			$leMotif = $values->get ( 'motif_admission'.$i )->getValue ();
			if($leMotif){
				
				$resultat = $this->getPathologieAvecLibelle($leMotif);
				
				if($resultat){
					$datamotifadmission = array(
							'libelle_motif' => $resultat['libelle_pathologie'],
							'type_pathologie' => $resultat['type_pathologie'],
							'id_admission_urgence' => $id_admission,
					);
					$this->tableGateway->insert($datamotifadmission);
				}else{
					$datamotifadmission = array(
							'libelle_motif' => $leMotif,
							'id_admission_urgence' => $id_admission,
					);
					$this->tableGateway->insert($datamotifadmission);
				}

			}
	
		}
	}
	
	public function getMotifAdmissionUrgence($id_admission){ 
		$id_admission = ( int ) $id_admission;
		$rowset = $this->tableGateway->select ( array (
				'id_admission_urgence' => $id_admission
		) );
		
		return $rowset;
	}
	
	public function deleteMotifAdmission($id_admission){
		$this->tableGateway->delete(array('id_admission_urgence' => $id_admission));
	}
	
	
	public function getListeMotifsAdmissionUrgence(){
		$rowset = $this->tableGateway->select (function (Select $select){
			$select->group('libelle_motif');
			$select->where(array('type_pathologie' => null));
		})->toArray();
		
		return $rowset;
	}
	
	
	public function modificationListeMotifsAdmissionUrgence($tabMotisSelectionnes, $valeurDeCorrection){
		
		for($i=0 ; $i < count($tabMotisSelectionnes) ; $i++){
			$rowset = $this->tableGateway->update(
					array('libelle_motif' => $valeurDeCorrection->libelle_pathologie, 'type_pathologie'=>$valeurDeCorrection->type_pathologie),
					array('libelle_motif' => $tabMotisSelectionnes[$i])
			);
		}
		
	}
	
	public function getPathologieAvecLibelle($libelle_motif){
		$adapter = $this->tableGateway->getAdapter();
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from('pathologie');
		$select->columns(array('*'));
		$select->where(array('libelle_pathologie' => $libelle_motif));
		return $sql->prepareStatementForSqlObject($select)->execute()->current();
	}
	
	
	
	/***
	 * * PARTIE STATISTIQUE ---- PARTIE STATISTIQUE ---- PARTIE STATISTIQUE
	 * * PARTIE STATISTIQUE ---- PARTIE STATISTIQUE ---- PARTIE STATISTIQUE
	 * * PARTIE STATISTIQUE ---- PARTIE STATISTIQUE ---- PARTIE STATISTIQUE
	 */
	
	/**
	 * Récupère la liste des motifs d'admission
	 * @return liste des motifs d'admission
	 */
	public function getListeDesMotifsAdmissionUrgence(){
		$rowset = $this->tableGateway->select (function (Select $select){
			$select->join('admission_urgence', 'admission_urgence.id_admission = motif_admission_urgence.id_admission_urgence', array('date'));
			$select->join('type_pathologie', 'type_pathologie.id = motif_admission_urgence.type_pathologie', array('libelle_type_pathologie'));
			$select->where(array('type_pathologie != ?' => ''));
			$select->order('type_pathologie ASC');
		})->toArray();
	
		return $rowset;
	}
	
	/**
	 * Récupérer la liste des motifs d'admission pour un type de pathologie donné
	 * @param $id_type_pathologie
	 * @return liste des motifs d'un type donné
	 */
	public function getListeDesMotifsAdmissionUrgencePourUnType($id_type_pathologie){
		$rowset = $this->tableGateway->select (function (Select $select) use ($id_type_pathologie){
			$select->join('admission_urgence', 'admission_urgence.id_admission = motif_admission_urgence.id_admission_urgence', array('date'));
			$select->join('type_pathologie', 'type_pathologie.id = motif_admission_urgence.type_pathologie', array('libelle_type_pathologie'));
			$select->where(array('type_pathologie != ?' => '', 'type_pathologie' => $id_type_pathologie));
			$select->order('type_pathologie ASC');
		})->toArray();
	
		return $rowset;
	}
	
	/**
	 * Récupérer la liste des motifs d'admission pour une pathologie donnée
	 * @param $id_type_pathologie
	 * @return liste des motifs d'une pathologie donnée
	 */
	public function getListeDesMotifsAdmissionUrgencePourUnePathologie($libelle_pathologie){
		$rowset = $this->tableGateway->select (function (Select $select) use ($libelle_pathologie){
			$select->join('admission_urgence', 'admission_urgence.id_admission = motif_admission_urgence.id_admission_urgence', array('date'));
			$select->join('type_pathologie', 'type_pathologie.id = motif_admission_urgence.type_pathologie', array('libelle_type_pathologie'));
			$select->where(array('type_pathologie != ?' => '', 'libelle_motif' => $libelle_pathologie));
			$select->order('type_pathologie ASC');
		})->toArray();
	
		return $rowset;
	}
	
	/**
	 * @return liste des types de motifs d'admission
	 */
	public function getListeTypeDesMotifsAdmissionUrgence(){
		$rowset = $this->tableGateway->select (function (Select $select){
			$select->join('admission_urgence', 'admission_urgence.id_admission = motif_admission_urgence.id_admission_urgence', array('date'));
			$select->join('type_pathologie', 'type_pathologie.id = motif_admission_urgence.type_pathologie', array('libelle_type_pathologie'));
			$select->where(array('type_pathologie != ?' => ''));
			$select->group('type_pathologie');
			$select->order('type_pathologie ASC');
		})->toArray();
	
		return $rowset;
	}
	
	
	
	/**
	 * Récupérer la liste des motifs d'admission pour une période donnée
	 */
	public function getListeDesMotifsAdmissionUrgencePourUnePeriode($date_debut, $date_fin){
		$rowset = $this->tableGateway->select (function (Select $select) use($date_debut, $date_fin){
			$select->join('admission_urgence', 'admission_urgence.id_admission = motif_admission_urgence.id_admission_urgence', array('date'));
			$select->join('type_pathologie', 'type_pathologie.id = motif_admission_urgence.type_pathologie', array('libelle_type_pathologie'));
			$select->where(array('type_pathologie != ?' => '',
					'date >= ?' => $date_debut,
					'date <= ?' => $date_fin,
			));
			$select->order('type_pathologie ASC');
		})->toArray();
	
		return $rowset;
	}
	
	
	/**
	 * Récupérer la liste des motifs d'admission pour un type de pathologie et une période donnée
	 */
	public function getListeDesMotifsAdmissionUrgencePourUnTypeEtUnePeriode($id_type_pathologie, $date_debut, $date_fin){
		$rowset = $this->tableGateway->select (function (Select $select) use ($id_type_pathologie, $date_debut, $date_fin){
			$select->join('admission_urgence', 'admission_urgence.id_admission = motif_admission_urgence.id_admission_urgence', array('date'));
			$select->join('type_pathologie', 'type_pathologie.id = motif_admission_urgence.type_pathologie', array('libelle_type_pathologie'));
			$select->where(array('type_pathologie != ?' => '', 
					             'type_pathologie' => $id_type_pathologie,
					             'date >= ?' => $date_debut,
              					 'date <= ?' => $date_fin,
			              ));
			$select->order('type_pathologie ASC');
		})->toArray();
	
		return $rowset;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	public function getMotifAdmission($id){
		$adapter = $this->tableGateway->getAdapter();
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from('motif_admission');
		$select->columns(array('Id_motif'=>'id_motif', 'Id_cons'=>'id_cons', 'Libelle_motif'=>'libelle_motif'));
		$select->where(array('id_cons'=>$id));
		$select->order('id_motif ASC');
		$stat = $sql->prepareStatementForSqlObject($select);
		$result = $stat->execute();
		return $result;
	}
	
	public function nbMotifs($id){
		$adapter = $this->tableGateway->getAdapter();
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from('motif_admission');
		$select->columns(array('id_motif'));
		$select->where(array('id_cons'=>$id));
		$stat = $sql->prepareStatementForSqlObject($select);
		$result = $stat->execute()->count();
		return $result;
	}
	
	public function addMotifAdmissionPourExamenJour($values, $codeExamen){
		$tabMotif = array(
				1 => $values->motif_admission1,
				2 => $values->motif_admission2,
				3 => $values->motif_admission3,
				4 => $values->motif_admission4,
				5 => $values->motif_admission5,
		);
		for($i=1 ; $i<=5; $i++){
			if($tabMotif[$i]){
				$datamotifadmission	 = array(
						'libelle_motif' => $tabMotif[$i],
						'id_cons' => $codeExamen,
				);
				$this->tableGateway->insert($datamotifadmission);
			}
		}
	}
	
	
	
	
	
	
	//ACTES --- ACTES --- ACTES --- ACTES --- ACTES --- ACTES --- ACTES --- ACTES --- ACTES
	//ACTES --- ACTES --- ACTES --- ACTES --- ACTES --- ACTES --- ACTES --- ACTES --- ACTES
	
	public function addDemandesActes($id_admission, $tabActesDemandes, $notesActes, $idemplye){
		
		$donnees = array();
		for( $i=1 ; $i< count($tabActesDemandes) ; $i++ ){
			$donnees['id_admission'] = $id_admission;
			$donnees['id_acte'] = $tabActesDemandes[$i];
			$donnees['note'] = $notesActes[$i];
			$donnees['idemploye'] = $idemplye;

			$db = $this->tableGateway->getAdapter();
			$sql = new Sql($db);
			$sQuery = $sql->insert() ->into('demande_acte_urg') ->values( $donnees );
			$stat = $sql->prepareStatementForSqlObject($sQuery)->execute();
		}
		
	}
	
	public function getDemandesActes($id_admission){
		$adapter = $this->tableGateway->getAdapter();
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from('demande_acte_urg')->columns(array('*'));
		$select->where(array('id_admission' => $id_admission));
		return $sql->prepareStatementForSqlObject($select)->execute();
	}
	
	public function deleteDemandesActes($id_admission){
		$adapter = $this->tableGateway->getAdapter();
		$sql = new Sql($adapter);
		$select = $sql->delete('demande_acte_urg');
		$select->where(array('id_admission' => $id_admission));
		return $sql->prepareStatementForSqlObject($select)->execute();
	}
	
	public function getListeActesDemandes($id_admission){
		$adapter = $this->tableGateway->getAdapter();
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from(array('dau' => 'demande_acte_urg'))->columns(array('*'));
		$select->join(array('lau' =>'liste_acte_urg') ,'lau.id = dau.id_acte', array('libelle'));
		$select->where(array('id_admission' => $id_admission));
		$listeActesDemandes = $sql->prepareStatementForSqlObject($select)->execute();

		$i = 0;
		$donneesActes = '';
		foreach ($listeActesDemandes as $listeActesDem){
			if($i == 0){
				$donneesActes .= $listeActesDem['libelle'];
				$i++;
			}else{
				$donneesActes .= ' ; '.$listeActesDem['libelle'];
			}
		}
		
		return $donneesActes;
	}
	
	//EXAMEN COMPLEMENTAIRE --- EXAMEN COMPLEMENTAIRE --- EXAMEN COMPLEMENTAIRE
	//EXAMEN COMPLEMENTAIRE --- EXAMEN COMPLEMENTAIRE --- EXAMEN COMPLEMENTAIRE
	
	public function getLiteExamensComplementairesParType($id){
		$adapter = $this->tableGateway->getAdapter();
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from('liste_examencomp_urg')->columns(array('*'));
		$select->where(array('type' => $id));
		return $sql->prepareStatementForSqlObject($select)->execute();
	}
	
	public function addDemandesExamensComplementaire($id_admission, $idemplye, $tabTypesExamensDemandes, $tabExamensDemandes){
	
		$donnees = array();
		for( $i=1 ; $i< count($tabExamensDemandes) ; $i++ ){
			$donnees['id_examen'] = $tabExamensDemandes[$i];
			$donnees['id_admission'] = $id_admission;
			$donnees['idemploye'] = $idemplye;
	
			$db = $this->tableGateway->getAdapter();
			$sql = new Sql($db);
			$sQuery = $sql->insert() ->into('demande_examen_urg') ->values( $donnees );
			$stat = $sql->prepareStatementForSqlObject($sQuery)->execute();
		}
	
	}
	
	public function getDemandesExamenComplementaire($id_admission){
		$adapter = $this->tableGateway->getAdapter();
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from(array('deu'=>'demande_examen_urg'))->columns(array('*'));
		$select->join(array('leu' =>'liste_examencomp_urg') ,'leu.id = deu.id_examen', array('type'));
		$select->where(array('id_admission' => $id_admission));
		return $sql->prepareStatementForSqlObject($select)->execute();
	}
	
	public function deleteDemandesExamenComplementaire($id_admission){
		$adapter = $this->tableGateway->getAdapter();
		$sql = new Sql($adapter);
		$select = $sql->delete('demande_examen_urg');
		$select->where(array('id_admission' => $id_admission));
		return $sql->prepareStatementForSqlObject($select)->execute();
	}
	
	public function getListeExamensDemandes($id_admission){
		$adapter = $this->tableGateway->getAdapter();
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from(array('deu'=>'demande_examen_urg'))->columns(array('*'));
		$select->join(array('leu' =>'liste_examencomp_urg') ,'leu.id = deu.id_examen', array('libelleExamen'=>'libelle'));
		$select->join(array('lteu' =>'liste_typeexamencomp_urg') ,'lteu.id = leu.type', array('libelleType'=>'libelle'));
		$select->where(array('id_admission' => $id_admission));
		$select->order(array('leu.type' => 'ASC'));
		$listeExamensDemandes = $sql->prepareStatementForSqlObject($select)->execute();
	
		$tabListeTypesEtExamens = array();
		
		foreach ($listeExamensDemandes as $listeExamensDem){
			$tabListeTypesEtExamens [] = array($listeExamensDem['libelleType'], $listeExamensDem['libelleExamen']); 
		}
	
		return $tabListeTypesEtExamens;
	}
	
}
