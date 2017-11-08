<?php
namespace Urgence\Form;

use Zend\Form\Form;


class AdmissionForm extends Form{

	public function __construct() {
		
		parent::__construct ();
		$today = new \DateTime ( 'now' );
		$dateheure = $today->format ( 'dmy-His' );
		$date  = $today->format ( "Y-m-d" );
		$heure = $today->format ( "H:i" );
		
		$this->add ( array (
				'name' => 'id_cons',
				'type' => 'hidden',
				'options' => array (
						'label' => 'Code consultation'
				),
				'attributes' => array (
						'readonly' => 'readonly',
						'value' => 's-c-' . $dateheure,
						'id' => 'id_cons'
				)
		) );
		
		$this->add ( array (
				'name' => 'heure_cons',
				'type' => 'Hidden',
				'attributes' => array (
						'value' => $heure
				)
		) );
		
		$this->add ( array (
				'name' => 'dateonly',
				'type' => 'Hidden',
				
				'attributes' => array (
						'id' => 'dateonly',
						'value' => $date,
				)
		) );
		
		$this->add ( array (
				'name' => 'id_patient',
				'type' => 'Hidden',
				'attributes' => array(
						'id' => 'id_patient'
				)
		) );
		
		$this->add ( array (
				'name' => 'id_admission',
				'type' => 'Hidden',
				'attributes' => array(
						'id' => 'id_admission'
				)
		) );

		$this->add ( array (
				'name' => 'service',
				'type' => 'Select',
				'options' => array (
						'label' => iconv('ISO-8859-1', 'UTF-8','Service'),
						'value_options' => array (
								''=>''
						)
				),
				'attributes' => array (
						'registerInArrrayValidator' => true,
						'onchange' => 'getmontant(this.value)',
						'id' =>'service',
						//'required' => true,
				)
		) );

		$this->add ( array (
				'name' => 'montant_avec_majoration',
				'type' => 'Hidden',
				'options' => array (
						'label' => iconv('ISO-8859-1', 'UTF-8','Tarif (frs)')
				),
				'attributes' => array (
						'id' => 'montant_avec_majoration',
				)
		) );
		
		$this->add ( array (
				'name' => 'montant',
				'type' => 'Hidden',
				'attributes' => array (
						'id' => 'montant',
				)
		) );

		$this->add ( array (
				'name' => 'numero',
				'type' => 'Hidden',
				'options' => array (
						'label' => iconv('ISO-8859-1', 'UTF-8','Numéro facture')
				),
				'attributes' => array (
						'id' => 'numero'
				)
		) );
		$this->add ( array (
				'name' => 'liste_service',
				'type' => 'Select',
				'options' => array (
						'value_options' => array (
								''=>''
						)
				),
				'attributes' => array (
						'id' => 'liste_service',
				)
		) );
		
		$this->add(array(
				'name' => 'type_facturation',
				'type' => 'Zend\Form\Element\radio',
				'options' => array (
						'value_options' => array(
								1 => 'Normal',
								2 => iconv ( 'ISO-8859-1', 'UTF-8','Prise en charge') ,
						),
				),
				'attributes' => array(
						'id' => 'type_facturation',
						//'required' => true,
				),
		));
		
		$this->add(array(
				'name' => 'organisme',
				'type' => 'textarea',
				'options' => array (
						'label' => iconv('ISO-8859-1', 'UTF-8','Organisme')
				),
				'attributes' => array(
						'id' => 'organisme',
				),
		));
		
		$this->add(array(
				'name' => 'taux',
				'type' => 'Select',
				'options' => array (
						'label' => iconv('ISO-8859-1', 'UTF-8','Taux (%)'),
						'value_options' => array(
								'' => '00',
								5  => '05',
								10 => '10',
						),
				),
				'attributes' => array(
						'registerInArrrayValidator' => true,
						'onchange' => 'getTarif(this.value)',
						'id' => 'taux',
				),
		));
		
		
		$this->add ( array (
				'name' => 'motif_admission',
				'type' => 'Text',
				'options' => array (
						'label' => 'Motif_admission'
				)
		) );
		/**
		 * ********* LES MOTIFS D ADMISSION *************
		*/
		/**
		 * ********* LES MOTIFS D ADMISSION *************
		*/
		$this->add ( array (
				'name' => 'motif_admission1',
				'type' => 'Text',
				'options' => array (
						'label' => 'motif 1'
				),
				'attributes' => array (
						'readonly' => 'readonly',
						'id' => 'motif_admission1'
				)
		) );
		$this->add ( array (
				'name' => 'motif_admission2',
				'type' => 'Text',
				'options' => array (
						'label' => 'motif 2'
				),
				'attributes' => array (
						'readonly' => 'readonly',
						'id' => 'motif_admission2'
				)
		) );
		$this->add ( array (
				'name' => 'motif_admission3',
				'type' => 'Text',
				'options' => array (
						'label' => 'motif 3'
				),
				'attributes' => array (
						'readonly' => 'readonly',
						'id' => 'motif_admission3'
				)
		) );
		$this->add ( array (
				'name' => 'motif_admission4',
				'type' => 'Text',
				'options' => array (
						'label' => 'motif 4'
				),
				'attributes' => array (
						'readonly' => 'readonly',
						'id' => 'motif_admission4'
				)
		) );
		$this->add ( array (
				'name' => 'motif_admission5',
				'type' => 'Text',
				'options' => array (
						'label' => 'motif 5'
				),
				'attributes' => array (
						'readonly' => 'readonly',
						'id' => 'motif_admission5'
				)
		) );
		
		
		/**
		 * ************************* CONSTANTES *****************************************************
		 */
		/**
		 * ************************* CONSTANTES *****************************************************
		 */
		/**
		 * ************************* CONSTANTES *****************************************************
		 */
		$this->add ( array (
				'name' => 'date_cons',
				'type' => 'hidden',
				'options' => array (
						'label' => 'Date'
				),
				'attributes' => array (
						'readonly' => 'readonly',
						'id' => 'date_cons',
				)
		) );
		$this->add ( array (
				'name' => 'poids',
				'type' => 'number',
				'options' => array (
						'label' => 'Poids (kg)'
				),
				'attributes' => array (
						'step' => 'any',
						'id' => 'poids',
						'min' => 1,
				)
		) );
		$this->add ( array (
				'name' => 'taille',
				'type' => 'number',
				'options' => array (
						'label' => 'Taille (cm)'
				),
				'attributes' => array (
						'step' => 'any',
						'id' => 'taille',
						'min' => 1,
				)
		) );
		$this->add ( array (
				'name' => 'temperature',
				'type' => 'number',
				'options' => array (
						'label' => iconv ( 'ISO-8859-1', 'UTF-8', 'Température (°C)' )
				),
				'attributes' => array (
						'id' => 'temperature',
						'step' => 'any',
						'min'  => 30,
						'max'  => 45,
				)
		) );
		
		$this->add ( array (
				'name' => 'tension',
				'type' => 'number',
				'options' => array (
						'label' => 'Tension'
				),
				'attributes' => array (
						'id' => 'tension'
				)
		) );
		
		$this->add ( array (
				'name' => 'pressionarterielle',
				'type' => 'number',
				'options' => array (
						'label' => iconv('ISO-8859-1', 'UTF-8', 'Pression artérielle (mmHg)')
				),
				'attributes' => array (
						'step' => 'any',
						'id' => 'pressionarterielle'
				)
		) );
		
		$this->add ( array (
				'name' => 'tensionmaximale',
				'type' => 'number',
				'attributes' => array (
						'step' => 'any',
						'id' => 'tensionmaximale',
						'min' => 1,
						'max' => 300,
				)
		) );
		
		$this->add ( array (
				'name' => 'tensionminimale',
				'type' => 'number',
				'attributes' => array (
						'step' => 'any',
						'id' => 'tensionminimale',
						'min' => 1,
						'max' => 300,
				)
		) );
		
		$this->add ( array (
				'name' => 'pouls',
				'type' => 'number',
				'options' => array (
						'label' => 'Pouls (bat/min)'
				),
				'attributes' => array (
						'step' => 'any',
						'id' => 'pouls',
						'min' => 20,
						'max' => 300,
				)
		) );
		$this->add ( array (
				'name' => 'frequence_respiratoire',
				'type' => 'number',
				'options' => array (
						'label' => iconv('ISO-8859-1', 'UTF-8','Fréquence respiratoire')
				),
				'attributes' => array (
						'step' => 'any',
						'id' => 'frequence_respiratoire',
						'min' => 5,
						'max' => 50,
				)
		) );
		$this->add ( array (
				'name' => 'glycemie_capillaire',
				'type' => 'number',
				'options' => array (
						'label' => iconv('ISO-8859-1', 'UTF-8', 'Glycémie capillaire (g/l)')
				),
				'attributes' => array (
						'step' => 'any',
						'id' => 'glycemie_capillaire',
						'min' => 1,
						'max' => 16,
				)
		) );
		$this->add ( array (
				'name' => 'bu',
				'type' => 'Text',
				'options' => array (
						'label' => 'Bandelette urinaire'
				),
				'attributes' => array (
						'id' => 'bu',
						'min' => 1,
				)
		) );
		
		
		/*** LES TYPES DE BANDELETTES URINAIRES ***/
		/*** LES TYPES DE BANDELETTES URINAIRES ***/
		/*** LES TYPES DE BANDELETTES URINAIRES ***/
		$this->add ( array (
				'name' => 'albumine',
				'type' => 'radio',
				'options' => array (
						'value_options' => array (
								'0' => 'â€“',
								'1' => '+',
						)
				),
				'attributes' => array (
						'id' => 'albumine',
		
				)
		) );
		$this->add ( array (
				'name' => 'croixalbumine',
				'type' => 'radio',
				'options' => array (
						'value_options' => array (
								'1' => '1',
								'2' => '2',
								'3' => '3',
								'4' => '4',
						)
				),
				'attributes' => array (
						'id' => 'croixalbumine',
		
				)
		) );
		
		
		$this->add ( array (
				'name' => 'sucre',
				'type' => 'radio',
				'options' => array (
						'value_options' => array (
								'0' => 'â€“',
								'1' => '+',
						)
				),
				'attributes' => array (
						'id' => 'sucre',
		
				)
		) );
		$this->add ( array (
				'name' => 'croixsucre',
				'type' => 'radio',
				'options' => array (
						'value_options' => array (
								'1' => '1',
								'2' => '2',
								'3' => '3',
								'4' => '4',
						)
				),
				'attributes' => array (
						'id' => 'croixsucre',
		
				)
		) );
		
		
		
		$this->add ( array (
				'name' => 'corpscetonique',
				'type' => 'radio',
				'options' => array (
						'value_options' => array (
								'0' => 'â€“',
								'1' => '+',
						)
				),
				'attributes' => array (
						'id' => 'corpscetonique',
		
				)
		) );
		$this->add ( array (
				'name' => 'croixcorpscetonique',
				'type' => 'radio',
				'options' => array (
						'value_options' => array (
								'1' => '1',
								'2' => '2',
								'3' => '3',
								'4' => '4',
						)
				),
				'attributes' => array (
						'id' => 'croixcorpscetonique',
						'class' => 'croixcorpscetonique',
		
				)
		) );
		
		
		//Niveau d'urgence du patient
		$this->add ( array (
				'name' => 'niveau',
				'type' => 'radio',
				'options' => array (
						'value_options' => array (
								array( 'label' => '4', 'value' => 4, 'attributes' => array ( 'id' => 'blanc'  ) ),
								array( 'label' => '3', 'value' => 3, 'attributes' => array ( 'id' => 'jaune'  ) ),
								array( 'label' => '2', 'value' => 2, 'attributes' => array ( 'id' => 'orange' ) ),
								array( 'label' => '1', 'value' => 1, 'attributes' => array ( 'id' => 'rouge'  ) ),
						)
				),
				'attributes' => array (
						'id' => 'niveau',
						'class' => 'niveau',
		
				)
		) );
		
		
		$this->add ( array (
				'name' => 'salle',
				'type' => 'Select',
				'options' => array (
						'label' => iconv('ISO-8859-1', 'UTF-8','Salle'),
				),
				'attributes' => array (
						'onchange' => 'getListeLits(this.value)',
						'id' => 'salle',
				)
		) );
		
		$this->add ( array (
				'name' => 'lit',
				'type' => 'Select',
				'options' => array (
						'label' => 'Lit'
				),
				'attributes' => array (
						'id' => 'lit'
				)
		) );
		
		$this->add ( array (
				'name' => 'couloir',
				'type' => 'Checkbox',
				'attributes' => array (
						'id' => 'couloir'
				)
		) );
		
		//RPU Hospitalisation  ---  RPU Hospitalisation
		//RPU Hospitalisation  ---  RPU Hospitalisation
		$this->add(array(
				'name' => 'rpu_hospitalisation',
				'type' => 'Textarea',
				'options' => array (
						'label' => iconv('ISO-8859-1', 'UTF-8','RPU')
				),
				'attributes' => array(
						'id' => 'rpu_hospitalisation',
				),
		));
		
		$this->add(array(
				'name' => 'rpu_hospitalisation_note',
				'type' => 'Textarea',
				'options' => array (
						'label' => iconv('ISO-8859-1', 'UTF-8','Note')
				),
				'attributes' => array(
						'id' => 'rpu_hospitalisation_note',
				),
		));
		/**
		 * *********************************************************
		 * *********************************************************
		 */
		$this->add(array(
				'name' => 'resume_syndromique',
				'type' => 'Textarea',
				'options' => array (
						'label' => iconv('ISO-8859-1', 'UTF-8','Résumé syndromiquee')
				),
				'attributes' => array(
						'id' => 'resume_syndromique',
						//'maxlength' => 1000,
				),
		));
		
		$this->add(array(
				'name' => 'hypotheses_diagnostiques',
				'type' => 'Textarea',
				'options' => array (
						'label' => iconv('ISO-8859-1', 'UTF-8','Hypothèses diagnostiques')
				),
				'attributes' => array(
						'id' => 'hypotheses_diagnostiques',
						//'maxlength' => 1000,
				),
		));
		
		$this->add(array(
				'name' => 'examens_complementaires',
				'type' => 'Textarea',
				'options' => array (
						'label' => iconv('ISO-8859-1', 'UTF-8','Examens complémentaires')
				),
				'attributes' => array(
						'id' => 'examens_complementaires',
						//'maxlength' => 1000,
				),
		));
		
		$this->add(array(
				'name' => 'traitement',
				'type' => 'Textarea',
				'options' => array (
						'label' => iconv('ISO-8859-1', 'UTF-8','Traitement')
				),
				'attributes' => array(
						'id' => 'traitement',
						//'maxlength' => 1000,
				),
		));
		
		$this->add(array(
				'name' => 'resultats_examens_complementaires',
				'type' => 'Textarea',
				'options' => array (
						'label' => iconv('ISO-8859-1', 'UTF-8','Résultats examens complémentaires')
				),
				'attributes' => array(
						'id' => 'resultats_examens_complementaires',
						//'maxlength' => 1000,
				),
		));
		
		$this->add(array(
				'name' => 'avis_specialiste',
				'type' => 'Textarea',
				'options' => array (
						'label' => iconv('ISO-8859-1', 'UTF-8','Avis du spécialiste')
				),
				'attributes' => array(
						'id' => 'avis_specialiste',
						//'maxlength' => 1500,
				),
		));
		
		$this->add(array(
				'name' => 'mutation',
				'type' => 'Select',
				'options' => array (
						'label' => iconv('ISO-8859-1', 'UTF-8','Mutation')
				),
				'attributes' => array(
						'id' => 'mutation',
						'style' => 'font-size:16px;'
				),
		));
		
		/*====================================*/
		/*====================================*/
		
		
		//RPU Traumatisme  ---  RPU Traumatisme
		//RPU Traumatisme  ---  RPU Traumatisme
		
		//Histoire de la maladie
		//Histoire de la maladie
		$this->add ( array (
				'name' => 'rpu_traumatisme_cote_dominant',
				'type' => 'Select',
				'options' => array (
						'label' => iconv('ISO-8859-1', 'UTF-8', 'Côté dominant'),
						'value_options' => array (
								'' => '',
								1 => 'Droite', 
								2 => 'Gauche', 
						)
				),
				'attributes' => array (
						'id' => 'rpu_traumatisme_cote_dominant',
				)
		) );
		
		$this->add ( array (
				'name' => 'rpu_traumatisme_date_heure',
				'type' => 'Text',
				'options' => array (
						'label' => 'Date & heure'
				),
				'attributes' => array (
						'id' => 'rpu_traumatisme_date_heure',
				)
		) );
		
		$this->add ( array (
				'name' => 'rpu_traumatisme_circonstances',
				'type' => 'Select',
				'options' => array (
						'label' => 'Circonstances'
				),
				'attributes' => array (
						'id' => 'rpu_traumatisme_circonstances',
				)
		) );
		
		$this->add ( array (
				'name' => 'rpu_traumatisme_mecanismes',
				'type' => 'Select',
				'options' => array (
						'label' => iconv('ISO-8859-1', 'UTF-8','Mécanismes')
				),
				'attributes' => array (
						'id' => 'rpu_traumatisme_mecanismes',
						'onchange' => 'getTraumatismeMecanisme(this.value)',
				)
		) );
		
		$this->add ( array (
				'name' => 'rpu_traumatisme_mecanismes_precision',
				'type' => 'Text',
				'options' => array (
						'label' => iconv('ISO-8859-1', 'UTF-8','Précision')
				),
				'attributes' => array (
						'id' => 'rpu_traumatisme_mecanismes_precision',
						'style' => 'width:96%;',
						//'maxlength' => 60,
				)
		) );
		
		
		$this->add(array(
				'name' => 'rpu_traumatisme_diagnostic',
				'type' => 'Select',
				'options' => array (
						'label' => iconv('ISO-8859-1', 'UTF-8','Diagnostic')
				),
				'attributes' => array(
						'id' => 'rpu_traumatisme_diagnostic',
						'onchange' => 'getTraumatismeDiagnostic(this.value)',
				),
		));
		
		$this->add(array(
				'name' => 'rpu_traumatisme_diagnostic_precision',
				'type' => 'Text',
				'options' => array (
						'label' => iconv('ISO-8859-1', 'UTF-8','Précision')
				),
				'attributes' => array(
						'id' => 'rpu_traumatisme_diagnostic_precision',
						'style' => 'width:96%;',
						//'maxlength' => 60,
				),
		));
		
		$this->add(array(
				'name' => 'rpu_traumatisme_conduite',
				'type' => 'Textarea',
				'options' => array (
						'label' => iconv('ISO-8859-1', 'UTF-8','Conduite à tenir')
				),
				'attributes' => array(
						'id' => 'rpu_traumatisme_conduite',
				),
		));
		
		$this->add ( array (
				'name' => 'transfert_consultation',
				'type' => 'Select',
				'options' => array (
						'label' => iconv('ISO-8859-1', 'UTF-8','Transfert || Consultat°'),
						'value_options' => array (
								'' => '',
								'1' => 'Transfert', 
								'2' => 'Consultation', 
						)
				),
				'attributes' => array (
						'id' => 'transfert_consultation'
				)
		) );
		
		$this->add(array(
				'name' => 'rpu_traumatisme_antecedent',
				'type' => 'Textarea',
				'options' => array (
						'label' => iconv('ISO-8859-1', 'UTF-8','Antécédents traumatismes')
				),
				'attributes' => array(
						'id' => 'rpu_traumatisme_antecedent',
				),
		));
		
		$this->add(array(
				'name' => 'rpu_traumatisme_examen_physique',
				'type' => 'Textarea',
				'options' => array (
						'label' => iconv('ISO-8859-1', 'UTF-8','Examen physique')
				),
				'attributes' => array(
						'id' => 'rpu_traumatisme_examen_physique',
				),
		));
		
		$this->add(array(
				'name' => 'rpu_traumatisme_examen_paraclinique',
				'type' => 'Textarea',
				'options' => array (
						'label' => iconv('ISO-8859-1', 'UTF-8','Examen paraclinique')
				),
				'attributes' => array(
						'id' => 'rpu_traumatisme_examen_paraclinique',
				),
		));
		
		$this->add(array(
				'name' => 'rpu_traumatisme_resultat_examen_complementaire',
				'type' => 'Textarea',
				'options' => array (
						'label' => iconv('ISO-8859-1', 'UTF-8','Résultat examen complementaire')
				),
				'attributes' => array(
						'id' => 'rpu_traumatisme_resultat_examen_complementaire',
				),
		));
		
		$this->add(array(
				'name' => 'rpu_traumatisme_avis_specialiste_trauma',
				'type' => 'Textarea',
				'options' => array (
						'label' => iconv('ISO-8859-1', 'UTF-8','Avis du spécialiste')
				),
				'attributes' => array(
						'id' => 'rpu_traumatisme_avis_specialiste_trauma',
						//'maxlength' => 1500,
				),
		));
		
		$this->add(array(
				'name' => 'rpu_traumatisme_diagnostic_autre',
				'type' => 'Text',
				'options' => array (
						'label' => iconv('ISO-8859-1', 'UTF-8','Autre diagnostic')
				),
				'attributes' => array(
						'id' => 'rpu_traumatisme_diagnostic_autre',
				),
		));
		
		$this->add(array(
				'name' => 'rpu_traumatisme_indication',
				'type' => 'Select',
				'options' => array (
						'label' => iconv('ISO-8859-1', 'UTF-8','Indication')
				),
				'attributes' => array(
						'id' => 'rpu_traumatisme_indication',
						'onchange' => 'getTraumatismeIndication(this.value)',
				),
		));
		
		$this->add(array(
				'name' => 'rpu_traumatisme_indication_precision',
				'type' => 'Text',
				'options' => array (
						'label' => iconv('ISO-8859-1', 'UTF-8','Précision')
				),
				'attributes' => array(
						'id' => 'rpu_traumatisme_indication_precision',
						'style' => 'width:96%;',
						//'maxlength' => 60,
				),
		));
		
		$this->add(array(
				'name' => 'rpu_traumatisme_conduite_specialiste',
				'type' => 'Textarea',
				'options' => array (
						'label' => iconv('ISO-8859-1', 'UTF-8','Conduite à tenir')
				),
				'attributes' => array(
						'id' => 'rpu_traumatisme_conduite_specialiste',
				),
		));
		
		$this->add(array(
				'name' => 'rpu_traumatisme_motif_sortie',
				'type' => 'Select',
				'options' => array (
						'label' => iconv('ISO-8859-1', 'UTF-8','Mode de sortie')
				),
				'attributes' => array(
						'id' => 'rpu_traumatisme_motif_sortie',
				),
		));
		
		$this->add(array(
				'name' => 'rpu_traumatisme_rendez_vous',
				'type' => 'Select',
				'options' => array (
						'label' => iconv('ISO-8859-1', 'UTF-8','Rendez-vous dans'),
						'value_options' => array (
								''=>'',
								'1 semaine'  => '1 semaine',
								'2 semaines' => '2 semaines',
								'3 semaines' => '3 semaines',
								'1 mois'  => '1 mois',
								'1 mois et 1 semaine'  => '1 mois et 1 semaine',
								'1 mois et 2 semaines' => '1 mois et 2 semaines',
								'1 mois et 3 semaines' => '1 mois et 3 semaines',
								'2 mois'  => '2 mois',
								'2 mois et 1 semaine'  => '2 mois et 1 semaine',
								'2 mois et 2 semaines' => '2 mois et 2 semaines',
								'2 mois et 3 semaines' => '2 mois et 3 semaines',
								'3 mois'  => '3 mois',
						)
				),
				'attributes' => array(
						'id' => 'rpu_traumatisme_rendez_vous',
				),
		));
		/*====================================*/
		/*====================================*/
		
		//RPU Sortie  ---  RPU Sortie
		//RPU Sortie  ---  RPU Sortie
		$this->add(array(
				'name' => 'rpu_sortie_diagnostic',
				'type' => 'Textarea',
				'options' => array (
						'label' => iconv('ISO-8859-1', 'UTF-8','Diagnostic de sortie')
				),
				'attributes' => array(
						'id' => 'rpu_sortie_diagnostic',
				),
		));
		
		$this->add(array(
				'name' => 'rpu_sortie_traitement',
				'type' => 'Textarea',
				'options' => array (
						'label' => iconv('ISO-8859-1', 'UTF-8','Traitement')
				),
				'attributes' => array(
						'id' => 'rpu_sortie_traitement',
				),
		));
		
		$this->add(array(
				'name' => 'rpu_sortie_diagnostic_principal',
				'type' => 'Textarea',
				'options' => array (
						'label' => iconv('ISO-8859-1', 'UTF-8','Diagnostic principal')
				),
				'attributes' => array(
						'id' => 'rpu_sortie_diagnostic_principal',
				),
		));
		
		$this->add(array(
				'name' => 'rpu_sortie_diagnostic_associe',
				'type' => 'Textarea',
				'options' => array (
						'label' => iconv('ISO-8859-1', 'UTF-8','Diagnostic associé')
				),
				'attributes' => array(
						'id' => 'rpu_sortie_diagnostic_associe',
				),
		));
		
		$this->add(array(
				'name' => 'rpu_sortie_examens_complementaires_demandes',
				'type' => 'Textarea',
				'options' => array (
						'label' => iconv('ISO-8859-1', 'UTF-8','Examens complementaires demandés')
				),
				'attributes' => array(
						'id' => 'rpu_sortie_examens_complementaires_demandes',
				),
		));
		
		$this->add(array(
				'name' => 'rpu_sortie_motif_sortie',
				'type' => 'Select',
				'options' => array (
						'label' => iconv('ISO-8859-1', 'UTF-8','Mode de sortie'),
				),
				'attributes' => array(
						'id' => 'rpu_sortie_motif_sortie',
						'onchange' => 'getChoixMotif(this.value)',
				),
		));
		
		$this->add(array(
				'name' => 'rpu_sortie_liste_mutation',
				'type' => 'Select',
				'options' => array (
						'label' => iconv('ISO-8859-1', 'UTF-8','muter vers')
				),
				'attributes' => array(
						'id' => 'rpu_sortie_liste_mutation',
				),
		));
		
		$this->add(array(
				'name' => 'rpu_sortie_transfert',
				'type' => 'Text',
				'options' => array (
						'label' => iconv('ISO-8859-1', 'UTF-8','transférer vers')
				),
				'attributes' => array(
						'id' => 'rpu_sortie_transfert',
				),
		));
		
		$this->add(array(
				'name' => 'rpu_sortie_evacuation',
				'type' => 'Text',
				'options' => array (
						'label' => iconv('ISO-8859-1', 'UTF-8','Evacuer vers')
				),
				'attributes' => array(
						'id' => 'rpu_sortie_evacuation',
				),
		));
		
		
		/*=========================*/
		/*=========================*/
		
		
		$this->add ( array (
				'name' => 'date_admission',
				'type' => 'Text',
				'options' => array (
						'label' => 'Date Admission'
				),
				'attributes' => array (
						'id' => 'date_admission',
				)
	    ) );
		
		
		/**
		 * LES HISTORIQUES OU TERRAINS PARTICULIERS
		 * LES HISTORIQUES OU TERRAINS PARTICULIERS
		 * LES HISTORIQUES OU TERRAINS PARTICULIERS
		 */
		/**** ANTECEDENTS PERSONNELS ****/
		/**** ANTECEDENTS PERSONNELS ****/
		
		/*LES HABITUDES DE VIE DU PATIENTS*/
		/*Alcoolique*/
		$this->add ( array (
				'name' => 'AlcooliqueHV',
				'type' => 'checkbox',
				'attributes' => array (
						'id' => 'AlcooliqueHV'
				)
		) );
		$this->add ( array (
				'name' => 'DateDebutAlcooliqueHV',
				'type' => 'text',
				'attributes' => array (
						'id' => 'DateDebutAlcooliqueHV'
				)
		) );
		$this->add ( array (
				'name' => 'DateFinAlcooliqueHV',
				'type' => 'text',
				'attributes' => array (
						'id' => 'DateFinAlcooliqueHV'
				)
		) );
		$this->add ( array (
				'name' => 'AutresHV',
				'type' => 'checkbox',
				'attributes' => array (
						'id' => 'AutresHV'
				)
		) );
		$this->add ( array (
				'name' => 'NoteAutresHV',
				'type' => 'text',
				'attributes' => array (
						'id' => 'NoteAutresHV'
				)
		) );
		/*Fumeur*/
		$this->add ( array (
				'name' => 'FumeurHV',
				'type' => 'checkbox',
				'attributes' => array (
						'id' => 'FumeurHV'
				)
		) );
		$this->add ( array (
				'name' => 'DateDebutFumeurHV',
				'type' => 'text',
				'attributes' => array (
						'id' => 'DateDebutFumeurHV'
				)
		) );
		$this->add ( array (
				'name' => 'DateFinFumeurHV',
				'type' => 'text',
				'attributes' => array (
						'id' => 'DateFinFumeurHV'
				)
		) );
		$this->add ( array (
				'name' => 'nbPaquetFumeurHV',
				'type' => 'number',
				'attributes' => array (
						'id' => 'nbPaquetFumeurHV',
						'min' => 1,
						'max' => 10,
				)
		) );
		$this->add ( array (
				'name' => 'nbPaquetAnneeFumeurHV',
				'type' => 'text',
				'attributes' => array (
						'id' => 'nbPaquetAnneeFumeurHV',
				)
		) );
		/*Drogué*/
		$this->add ( array (
				'name' => 'DroguerHV',
				'type' => 'checkbox',
				'attributes' => array (
						'id' => 'DroguerHV'
				)
		) );
		$this->add ( array (
				'name' => 'DateDebutDroguerHV',
				'type' => 'text',
				'attributes' => array (
						'id' => 'DateDebutDroguerHV'
				)
		) );
		$this->add ( array (
				'name' => 'DateFinDroguerHV',
				'type' => 'text',
				'attributes' => array (
						'id' => 'DateFinDroguerHV'
				)
		) );
		/*LES ANTECEDENTS MEDICAUX*/
		/*Diabete*/
		$this->add ( array (
				'name' => 'DiabeteAM',
				'type' => 'checkbox',
				'attributes' => array (
						'id' => 'DiabeteAM'
				)
		) );
		/*HTA*/
		$this->add ( array (
				'name' => 'htaAM',
				'type' => 'checkbox',
				'attributes' => array (
						'id' => 'htaAM'
				)
		) );
		/*Drepanocytose*/
		$this->add ( array (
				'name' => 'drepanocytoseAM',
				'type' => 'checkbox',
				'attributes' => array (
						'id' => 'drepanocytoseAM'
				)
		) );
		/*Dislipidemie*/
		$this->add ( array (
				'name' => 'dislipidemieAM',
				'type' => 'checkbox',
				'attributes' => array (
						'id' => 'dislipidemieAM'
				)
		) );
		/*Asthme*/
		$this->add ( array (
				'name' => 'asthmeAM',
				'type' => 'checkbox',
				'attributes' => array (
						'id' => 'asthmeAM'
				)
		) );
		/*Autre*/
		$this->add ( array (
				'name' => 'autresAM',
				'type' => 'text',
				'attributes' => array (
						'id' => 'autresAM',
						'maxlength' => 13,
				)
		) );
		/*nbCheckbox*/
		$this->add ( array (
				'name' => 'nbCheckboxAM',
				'type' => 'hidden',
				'attributes' => array (
						'id' => 'nbCheckboxAM',
				)
		) );
		/*GYNECO-OBSTETRIQUE*/
		/*Menarche*/
		$this->add ( array (
				'name' => 'MenarcheGO',
				'type' => 'checkbox',
				'attributes' => array (
						'id' => 'MenarcheGO'
				)
		) );
		/*Note Menarche*/
		$this->add ( array (
				'name' => 'NoteMenarcheGO',
				'type' => 'text',
				'attributes' => array (
						'id' => 'NoteMenarcheGO'
				)
		) );
		
		/*Gestite*/
		$this->add ( array (
				'name' => 'GestiteGO',
				'type' => 'checkbox',
				'attributes' => array (
						'id' => 'GestiteGO'
				)
		) );
		/*Note Gestite*/
		$this->add ( array (
				'name' => 'NoteGestiteGO',
				'type' => 'text',
				'attributes' => array (
						'id' => 'NoteGestiteGO'
				)
		) );
		
		
		/*Parite*/
		$this->add ( array (
				'name' => 'PariteGO',
				'type' => 'checkbox',
				'attributes' => array (
						'id' => 'PariteGO'
				)
		) );
		/*Note Parite*/
		$this->add ( array (
				'name' => 'NotePariteGO',
				'type' => 'text',
				'attributes' => array (
						'id' => 'NotePariteGO'
				)
		) );
		
		/*Cycle*/
		$this->add ( array (
				'name' => 'CycleGO',
				'type' => 'checkbox',
				'attributes' => array (
						'id' => 'CycleGO'
				)
		) );
		/*Duree Cycle*/
		$this->add ( array (
				'name' => 'DureeCycleGO',
				'type' => 'text',
				'attributes' => array (
						'id' => 'DureeCycleGO'
				)
		) );
		/*Regularite cycle*/
		$this->add ( array (
				'name' => 'RegulariteCycleGO',
				'type' => 'Zend\Form\Element\Select',
				'options' => array (
						'value_options' => array (
								' ' => '',
								'1' => 'Oui',
								'2' => 'Non',
						)
				),
				'attributes' => array (
						'id' => 'RegulariteCycleGO'
				)
		) );
		/*Dysmenorrhee cycle*/
		$this->add ( array (
				'name' => 'DysmenorrheeCycleGO',
				'type' => 'Zend\Form\Element\Select',
				'options' => array (
						'value_options' => array (
								' ' => '',
								'1' => 'Oui',
								'2' => 'Non',
						)
				),
				'attributes' => array (
						'id' => 'DysmenorrheeCycleGO'
				)
		) );
		
		/*Autres*/
		$this->add ( array (
				'name' => 'AutresGO',
				'type' => 'checkbox',
				'attributes' => array (
						'id' => 'AutresGO'
				)
		) );
		/*Note Autres*/
		$this->add ( array (
				'name' => 'NoteAutresGO',
				'type' => 'text',
				'attributes' => array (
						'id' => 'NoteAutresGO'
				)
		) );
		/**** ANTECEDENTS FAMILIAUX ****/
		/**** ANTECEDENTS FAMILIAUX ****/
		
		/*Diabete*/
		$this->add ( array (
				'name' => 'DiabeteAF',
				'type' => 'checkbox',
				'attributes' => array (
						'id' => 'DiabeteAF'
				)
		) );
		/*Note Diabete*/
		$this->add ( array (
				'name' => 'NoteDiabeteAF',
				'type' => 'text',
				'attributes' => array (
						'id' => 'NoteDiabeteAF'
				)
		) );
		
		/*Drepanocytose*/
		$this->add ( array (
				'name' => 'DrepanocytoseAF',
				'type' => 'checkbox',
				'attributes' => array (
						'id' => 'DrepanocytoseAF'
				)
		) );
		/*Note Drepanocytose*/
		$this->add ( array (
				'name' => 'NoteDrepanocytoseAF',
				'type' => 'text',
				'attributes' => array (
						'id' => 'NoteDrepanocytoseAF'
				)
		) );
		
		/*HTA*/
		$this->add ( array (
				'name' => 'htaAF',
				'type' => 'checkbox',
				'attributes' => array (
						'id' => 'htaAF'
				)
		) );
		/*Note HTA*/
		$this->add ( array (
				'name' => 'NoteHtaAF',
				'type' => 'text',
				'attributes' => array (
						'id' => 'NoteHtaAF'
				)
		) );
		
		/*Autres*/
		$this->add ( array (
				'name' => 'autresAF',
				'type' => 'checkbox',
				'attributes' => array (
						'id' => 'autresAF'
				)
		) );
		/*Note Autres*/
		$this->add ( array (
				'name' => 'NoteAutresAF',
				'type' => 'text',
				'attributes' => array (
						'id' => 'NoteAutresAF'
				)
		) );
		
		/*Mode d'entrée */
		$this->add(array(
				'name' => 'mode_entree',
				'type' => 'Select',
				'options' => array (
						'value_options' => array(
								'0' => '',
								'1' => iconv ( 'ISO-8859-1', 'UTF-8','Référence'),
								'2' => 'Domicile',
						),
				),
				'attributes' => array(
						'registerInArrrayValidator' => true,
						'onchange' => 'getModeEntre(this.value)',
						'id' => 'mode_entree',
				),
		));
		
		/*Précisison référence*/
		$this->add(array(
				'name' => 'precision_provenance',
				'type' => 'Text',
				'attributes' => array(
						'id' => 'precision_provenance',
				),
		));
		
		
		/*Mode de transport */
		$this->add(array(
				'name' => 'mode_transport',
				'type' => 'Select',
				'attributes' => array(
						'id' => 'mode_transport',
				),
		));
		
		/*Mises à jour 1*/
		$this->add(array(
				'name' => 'mise_a_jour_1',
				'type' => 'Text',
				'options' => array (
						'label' => iconv('ISO-8859-1', 'UTF-8','Mise à jour 1'),
				),
				'attributes' => array(
						'id' => 'mise_a_jour_1',
						'style' => 'font-size:15px; font-weight: normal;'
				),
		));
		
		/*Mises à jour 2*/
		$this->add(array(
				'name' => 'mise_a_jour_2',
				'type' => 'Text',
				'options' => array (
						'label' => iconv('ISO-8859-1', 'UTF-8','Mise à jour 2'),
				),
				'attributes' => array(
						'id' => 'mise_a_jour_2',
						'style' => 'font-size:15px; font-weight: normal;'
				),
		));
		
		/*Mises à jour 3*/
		$this->add(array(
				'name' => 'mise_a_jour_3',
				'type' => 'Text',
				'options' => array (
						'label' => iconv('ISO-8859-1', 'UTF-8','Mise à jour 3'),
				),
				'attributes' => array(
						'id' => 'mise_a_jour_3',
						'style' => 'font-size:15px; font-weight: normal;'
				),
		));
		
	}
}