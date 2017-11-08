<?php
return array (
		'controllers' => array (
				'invokables' => array (
						'Consultation\Controller\Consultation' => 'Consultation\Controller\ConsultationController'
				)
		),
		'router' => array (
				'routes' => array (
						'consultation' => array (
								'type' => 'segment',
								'options' => array (
										'route' => '/consultation[/][:action][/:val]',
										'constraints' => array (
												'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
												'val' => '[0-9]+',
												//'val' => '[0-9]+'
										),
										'defaults' => array (
												'controller' => 'Consultation\Controller\Consultation',
												'action' => 'liste-patients-admis'
										)
								)
						)
				)
		),
		'view_manager' => array (
				'template_map' => array (
						'layout/consultation' => __DIR__ . '/../view/layout/consultation.phtml',
						'layout/menugauchecons' => __DIR__ . '/../view/layout/menugauche.phtml',
						'layout/piedpagecons' => __DIR__ . '/../view/layout/piedpagecons.phtml'
				),
				'template_path_stack' => array (
						'consultation' => __DIR__ . '/../view'
				),
				'strategies' => array(
						'ViewJsonStrategy',
				),
		)
);