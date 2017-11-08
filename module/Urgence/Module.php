<?php

namespace Urgence;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\ModuleManager\Feature\ViewHelperProviderInterface;
use Zend\Mvc\MvcEvent;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Urgence\Model\Patient;
use Urgence\Model\PatientTable;
use Urgence\Model\Urgence;
use Urgence\Model\TarifConsultationTable;
use Urgence\Model\TarifConsultation;
use Urgence\Model\Admission;
use Urgence\Model\AdmissionTable;
use Urgence\Model\ServiceTable;
use Urgence\Model\Service;
use Urgence\Model\ConsultationTable;
use Urgence\Model\Consultation;
use Urgence\Model\MotifAdmissionTable;
use Urgence\Model\MotifAdmission;
use Urgence\Model\PathologieTable;
use Urgence\Model\Pathologie;
use Urgence\Model\TypePathologieTable;
use Urgence\Model\TypePathologie;

class Module implements AutoloaderProviderInterface, ConfigProviderInterface, ServiceProviderInterface, ViewHelperProviderInterface {

	public function registerJsonStrategy(MvcEvent $e)
	{
		$app          = $e->getTarget();
		$locator      = $app->getServiceManager();
		$view         = $locator->get('Zend\View\View');
		$jsonStrategy = $locator->get('ViewJsonStrategy');

		// Attach strategy, which is a listener aggregate, at high priority
		$view->getEventManager()->attach($jsonStrategy, 100);
	}

	public function getAutoloaderConfig() {
		return array (
				'Zend\Loader\StandardAutoloader' => array (
						'namespaces' => array (
								__NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__
						)
				)
		);
	}
	public function getConfig() {
		return include __DIR__ . '/config/module.config.php';
	}
	public function getServiceConfig() {
		return array (
				'factories' => array (
						'Urgence\Model\ConsultationTable' => function ($sm) {
							$tableGateway = $sm->get ( 'ConsultationTableUrgenceGateway' );
							$table = new ConsultationTable ( $tableGateway );
							return $table;
						},
						'ConsultationTableUrgenceGateway' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$resultSetPrototype = new ResultSet();
							$resultSetPrototype->setArrayObjectPrototype ( new Consultation());
							return new TableGateway ( 'consultation', $dbAdapter, null, $resultSetPrototype );
						},
						'Urgence\Model\MotifAdmissionTable' => function ($sm) {
							$tableGateway = $sm->get ( 'MotifAdmissionUrgenceTableGateway' );
							$table = new MotifAdmissionTable($tableGateway);
							return $table;
						},
						'MotifAdmissionUrgenceTableGateway' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$resultSetPrototype = new ResultSet();
							$resultSetPrototype->setArrayObjectPrototype ( new MotifAdmission());
							return new TableGateway ( 'motif_admission_urgence', $dbAdapter, null, $resultSetPrototype );
						},
						'Urgence\Model\PatientTable' => function ($sm) {
							$tableGateway = $sm->get ( 'PatientTable1Gateway' );
							$table = new PatientTable ( $tableGateway );
							return $table;
						},
						'PatientTable1Gateway' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$resultSetPrototype = new ResultSet ();
							$resultSetPrototype->setArrayObjectPrototype ( new Patient () );
							return new TableGateway ( 'patient', $dbAdapter, null, $resultSetPrototype );
						},
						'Urgence\Model\DecesTable' => function ($sm) {
							$tableGateway = $sm->get ( 'DecesTableGateway' );
							$table = new DecesTable ( $tableGateway );
							return $table;
						},
						'DecesTableGateway' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$resultSetPrototype = new ResultSet ();
							$resultSetPrototype->setArrayObjectPrototype ( new Deces () );
							return new TableGateway ( 'deces', $dbAdapter, null, $resultSetPrototype );
						},
						'Urgence\Model\AdmissionTable' => function ($sm) {
							$tableGateway = $sm->get( 'AdmissionTableGateway' );
							$table = new AdmissionTable( $tableGateway );
							return $table;
						},
						'AdmissionTableGateway' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$resultSetPrototype = new ResultSet ();
							$resultSetPrototype->setArrayObjectPrototype ( new Admission() );
							return new TableGateway ( 'admission_urgence', $dbAdapter, null, $resultSetPrototype );
						},
						'Urgence\Model\NaissanceTable' => function ($sm) {
							$tableGateway = $sm->get( 'NaissanceTable1Gateway' );
							$table = new NaissanceTable( $tableGateway );
							return $table;
						},
						'NaissanceTable1Gateway' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$resultSetPrototype = new ResultSet ();
							$resultSetPrototype->setArrayObjectPrototype ( new Naissance() );
							return new TableGateway ( 'naissance', $dbAdapter, null, $resultSetPrototype );
						},
						'Urgence\Model\TarifConsultationTable' => function ($sm) {
							$tableGateway = $sm->get( 'TarifConsultationTableGateway' );
							$table = new TarifConsultationTable( $tableGateway );
							return $table;
						},
						'TarifConsultationTableGateway' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$resultSetPrototype = new ResultSet ();
							$resultSetPrototype->setArrayObjectPrototype (new TarifConsultation());
							return new TableGateway ( 'tarif_consultation', $dbAdapter, null, $resultSetPrototype );
						},
						'Urgence\Model\ServiceTable' => function ($sm) {
							$tableGateway = $sm->get('ServiceTableFactGateway');
							$table = new ServiceTable($tableGateway);
							return $table;
						},
						'ServiceTableFactGateway' => function($sm) {
							$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
							$resultSetPrototype = new ResultSet();
							$resultSetPrototype->setArrayObjectPrototype(new Service());
							return new TableGateway('service', $dbAdapter, null, $resultSetPrototype);
						},
						'Urgence\Model\PathologieTable' => function ($sm) {
							$tableGateway = $sm->get('PathologieTableFactGateway');
							$table = new PathologieTable($tableGateway);
							return $table;
						},
						'PathologieTableFactGateway' => function($sm) {
							$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
							$resultSetPrototype = new ResultSet();
							$resultSetPrototype->setArrayObjectPrototype(new Pathologie());
							return new TableGateway('pathologie', $dbAdapter, null, $resultSetPrototype);
						},
						'Urgence\Model\TypePathologieTable' => function ($sm) {
							$tableGateway = $sm->get('TypePathologieTableGateway');
							$table = new TypePathologieTable($tableGateway);
							return $table;
						},
						'TypePathologieTableGateway' => function($sm) {
							$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
							$resultSetPrototype = new ResultSet();
							$resultSetPrototype->setArrayObjectPrototype(new TypePathologie());
							return new TableGateway('type_pathologie', $dbAdapter, null, $resultSetPrototype);
						},
						

				)
		);
	}
	public function getViewHelperConfig() {
		return array ();
	}
}