<?php
return array (
		'controllers' => array (
				'invokables' => array (
						'Urgence\Controller\Urgence' => 'Urgence\Controller\UrgenceController'
				)
		),
		'router' => array (
				'routes' => array (
						'urgence' => array (
								'type' => 'segment',
								'options' => array (
										'route' => '/urgence[/][:action][/:id][/:id_patient]',
										'constraints' => array (
												'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
												'id' => '[a-zA-Z][a-zA-Z0-9_-]*',
												'id_patient' => '[0-9]+'
										),
										'defaults' => array (
												'controller' => 'Urgence\Controller\Urgence',
												'action' => 'admission'
										)
								)
						)
				)
		),
		'view_manager' => array (
				'template_map' => array (
						'layout/urgence' => __DIR__ . '/../view/layout/urgence.phtml',
						'layout/menugaucheurgence' => __DIR__ . '/../view/layout/menugaucheurgence.phtml',
						'layout/piedpagecons' => __DIR__ . '/../view/layout/piedpagecons.phtml'
				),
				'template_path_stack' => array (
						'urgence' => __DIR__ . '/../view'
				),
				'strategies' => array(
						'ViewJsonStrategy',
				),
		)
);