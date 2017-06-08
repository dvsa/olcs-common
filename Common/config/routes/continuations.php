<?php

use Zend\Mvc\Router\Http;

return [
    'continuation' => [
        'type' => Http\Segment::class,
        'options' =>  [
            'route' => '/continuation/:continuationDetailId',
            'constraints' => [
                'continuationDetailId' => '[0-9]+',
            ],
            'defaults' => [
                'controller' => 'ContinuationController/Start',
                'action' => 'index'
            ]
        ],
        'may_terminate' => true,
        'child_routes' => [
            'checklist' => [
                'type' => Http\Literal::class,
                'options' =>  [
                    'route' => '/checklist',
                    'defaults' => [
                        'controller' => 'ContinuationController/Checklist',
                        'action' => 'index'
                    ]
                ],
            ],
            'finances' => [
                'type' => Http\Literal::class,
                'options' =>  [
                    'route' => '/finances',
                    'defaults' => [
                        'controller' => 'ContinuationController/Finances',
                        'action' => 'index'
                    ]
                ],
            ],
            'declaration' => [
                'type' => Http\Literal::class,
                'options' =>  [
                    'route' => '/declaration',
                    'defaults' => [
                        'controller' => 'ContinuationController/Declaration',
                        'action' => 'index'
                    ]
                ],
            ],
            'payment' => [
                'type' => Http\Literal::class,
                'options' =>  [
                    'route' => '/payment',
                    'defaults' => [
                        'controller' => 'ContinuationController/Payment',
                        'action' => 'index'
                    ]
                ],
            ],
            'success' => [
                'type' => Http\Literal::class,
                'options' =>  [
                    'route' => '/success',
                    'defaults' => [
                        'controller' => 'ContinuationController/Success',
                        'action' => 'index'
                    ]
                ],
            ],
            'review' => [
                'type' => Http\Literal::class,
                'options' =>  [
                    'route' => '/review',
                    'defaults' => [
                        'controller' => 'ContinuationController/Review',
                        'action' => 'index'
                    ]
                ],
            ],
        ],
    ],
];
