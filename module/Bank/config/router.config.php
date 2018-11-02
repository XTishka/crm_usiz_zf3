<?php

namespace Bank;

return [
    'routes' => [
        'dashboard'      => [
            'child_routes' => [
                'bank' => [
                    'type'          => 'segment',
                    'options'       => [
                        'route'    => '/bank',
                        'defaults' => [
                            'action'     => 'index',
                            'controller' => Controller\RecordController::class,
                        ],
                    ],
                    'may_terminate' => true,
                    'child_routes'  => [
                        'import' => [
                            'type'    => 'segment',
                            'options' => [
                                'route'       => '/import[/:id]',
                                'constraints' => [
                                    'id' => '\d+',
                                ],
                                'defaults'    => [
                                    'action' => 'import',
                                ],
                            ],
                        ],
                        'edit'   => [
                            'type'    => 'segment',
                            'options' => [
                                'route'       => '/edit[/:id]',
                                'constraints' => [
                                    'id' => '\d+',
                                ],
                                'defaults'    => [
                                    'action' => 'edit',
                                ],
                            ],
                        ],
                        'delete' => [
                            'type'    => 'segment',
                            'options' => [
                                'route'       => '/delete/:id',
                                'constraints' => [
                                    'id' => '\d+',
                                ],
                                'defaults'    => [
                                    'action' => 'delete',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
        'plantDashboard' => [
            'child_routes' => [
                'bank' => [
                    'type'          => 'segment',
                    'options'       => [
                        'route'    => '/bank',
                        'defaults' => [
                            'action'     => 'index',
                            'controller' => Controller\PlantRecordController::class,
                        ],
                    ],
                    'may_terminate' => true,
                    'child_routes'  => [
                        'edit'   => [
                            'type'    => 'segment',
                            'options' => [
                                'route'       => '/edit[/:id]',
                                'constraints' => [
                                    'id' => '\d+',
                                ],
                                'defaults'    => [
                                    'action' => 'edit',
                                ],
                            ],
                        ],
                        'delete' => [
                            'type'    => 'segment',
                            'options' => [
                                'route'       => '/delete/:id',
                                'constraints' => [
                                    'id' => '\d+',
                                ],
                                'defaults'    => [
                                    'action' => 'delete',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
        'bank'           => [
            'type'          => 'segment',
            'options'       => [
                'route'    => '/bank',
                'defaults' => [
                    'controller' => Controller\BankController::class,
                    'action'     => 'index',
                ],
            ],
            'may_terminate' => true,
            'child_routes'  => [
                'edit'   => [
                    'type'    => 'segment',
                    'options' => [
                        'route'       => '/edit[/:id]',
                        'constraints' => [
                            'id' => '\d+',
                        ],
                        'defaults'    => [
                            'action' => 'edit',
                        ],
                    ],
                ],
                'delete' => [
                    'type'    => 'segment',
                    'options' => [
                        'route'       => '/delete[/:id]',
                        'constraints' => [
                            'id' => '\d+',
                        ],
                        'defaults'    => [
                            'action' => 'delete',
                        ],
                    ],
                ],
            ],
        ],
    ],
];