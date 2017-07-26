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
                'type' => Http\Segment::class,
                'options' =>  [
                    'route' => '/checklist',
                    'defaults' => [
                        'controller' => 'ContinuationController/Checklist',
                        'action' => 'index'
                    ]
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'people' => [
                        'type' => Http\Segment::class,
                        'options' => [
                            'route' => '/people[/]',
                            'defaults' => [
                                'action' => 'people',
                            ]
                        ],
                        'may_terminate' => true,
                    ],
                    'vehicles' => [
                        'type' => Http\Segment::class,
                        'options' => [
                            'route' => '/vehicles[/]',
                            'defaults' => [
                                'action' => 'vehicles',
                            ]
                        ],
                        'may_terminate' => true,
                    ],
                    'operating-centres' => [
                        'type' => Http\Segment::class,
                        'options' => [
                            'route' => '/operating-centres[/]',
                            'defaults' => [
                                'action' => 'operating-centres',
                            ]
                        ],
                        'may_terminate' => true,
                    ],
                    'transport-managers' => [
                        'type' => Http\Segment::class,
                        'options' => [
                            'route' => '/transport-managers[/]',
                            'defaults' => [
                                'action' => 'transport-managers',
                            ]
                        ],
                        'may_terminate' => true,
                    ],
                    'safety-inspectors' => [
                        'type' => Http\Segment::class,
                        'options' => [
                            'route' => '/safety-inspectors[/]',
                            'defaults' => [
                                'action' => 'safety-inspectors',
                            ]
                        ],
                        'may_terminate' => true,
                    ],
                ],
            ],
            'finances' => [
                'type' => Http\Segment::class,
                'options' =>  [
                    'route' => '/finances[/]',
                    'defaults' => [
                        'controller' => 'ContinuationController/Finances',
                        'action' => 'index'
                    ]
                ],
            ],
            'other-finances' => [
                'type' => Http\Segment::class,
                'options' =>  [
                    'route' => '/other-finances[/]',
                    'defaults' => [
                        'controller' => 'ContinuationController/OtherFinances',
                        'action' => 'index'
                    ]
                ],
            ],
            'insufficient-finances' => [
                'type' => Http\Segment::class,
                'options' =>  [
                    'route' => '/insufficient-finances[/]',
                    'defaults' => [
                        'controller' => 'ContinuationController/InsufficientFinances',
                        'action' => 'index'
                    ]
                ],
            ],
            'declaration' => [
                'type' => Http\Segment::class,
                'options' =>  [
                    'route' => '/declaration[/]',
                    'defaults' => [
                        'controller' => 'ContinuationController/Declaration',
                        'action' => 'index'
                    ]
                ],
            ],
            'payment' => [
                'type' => Http\Segment::class,
                'options' =>  [
                    'route' => '/payment[/]',
                    'defaults' => [
                        'controller' => 'ContinuationController/Payment',
                        'action' => 'index'
                    ]
                ],
            ],
            'success' => [
                'type' => Http\Segment::class,
                'options' =>  [
                    'route' => '/success[/]',
                    'defaults' => [
                        'controller' => 'ContinuationController/Success',
                        'action' => 'index'
                    ]
                ],
            ],
            'review' => [
                'type' => Http\Segment::class,
                'options' =>  [
                    'route' => '/review[/]',
                    'defaults' => [
                        'controller' => 'ContinuationController/Review',
                        'action' => 'index'
                    ]
                ],
            ],
        ],
    ],
];
