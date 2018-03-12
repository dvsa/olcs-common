<?php

return array(
    'application_start' => array(
        'type' => 'segment',
        'options' => array(
            'route' => '/application_start_page[/]'
        )
    ),
    'getfile' => array(
        'type' => 'segment',
        'options' => array(
            'route' => '/file/:identifier',
            'defaults' => array(
                'controller' => 'Common\Controller\File',
                'action' => 'download'
            )
        )
    ),
    'transport_manager_review' => array(
        'type' => 'segment',
        'options' => array(
            'route' => '/transport-manager-application/review/:id[/]',
            'defaults' => array(
                'controller' => Common\Controller\TransportManagerReviewController::class,
                'action' => 'index'
            )
        )
    ),
    'correspondence_inbox' => array(
        'type' => 'segment',
        'options' => array(
            'route' => '/correspondence[/]'
        )
    ),
    'not-found' => array(
        'type' => 'segment',
        'options' =>  array(
            'route' => '/404[/]',
            'defaults' => array(
                'controller' => \Common\Controller\ErrorController::class,
                'action' => 'notFound'
            )
        )
    ),
    'server-error' => array(
        'type' => 'segment',
        'options' =>  array(
            'route' => '/error[/]',
            'defaults' => array(
                'controller' => \Common\Controller\ErrorController::class,
                'action' => 'serverError'
            )
        )
    ),
    'guides' => array(
        'type' => 'segment',
        'options' =>  array(
            'route' => '/guides[/]'
        ),
        'may_terminate' => false,
        'child_routes' => array(
            'guide' => array(
                'type' => 'segment',
                'may_terminate' => true,
                'options' =>  array(
                    'route' => ':guide[/]',
                    'constraints' => [
                        'guide' => '[a-zA-Z\-0-9]+'
                    ],
                    'defaults' => array(
                        'controller' => \Common\Controller\GuidesController::class,
                        'action' => 'index'
                    )
                ),
            ),
        )
    ),
);
