<?php

namespace Dashboard;

return [
    'routes' => [
        'dashboard' => [
            'child_routes' => [
                'purchaseContract' => [
                    'type'          => 'segment',
                    'options'       => [
                        'route'    => '/purchase-contract',
                        'defaults' => [
                            'controller' => Controller\PurchaseContractController::class,
                            'action'     => 'index',
                        ],
                    ],
                    'may_terminate' => true,
                    'child_routes'  => [
                        'advanced' => [
                            'type'    => 'segment',
                            'options' => [
                                'route'       => '/advanced[/:id]',
                                'constraints' => [
                                    'id' => '\d+',
                                ],
                                'defaults'    => [
                                    'action' => 'advanced',
                                ],
                            ],
                        ],
                        'edit'     => [
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
                        'delete'   => [
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
                'purchaseWagon'    => [
                    'type'         => 'segment',
                    'options'      => [
                        'route'       => '/purchase-wagon/:contract',
                        'constraints' => [
                            'contract' => '\d+',
                        ],
                        'defaults'    => [
                            'controller' => Controller\PurchaseWagonController::class,
                        ],
                    ],
                    'child_routes' => [
                        'export'    => [
                            'type'    => 'segment',
                            'options' => [
                                'route'    => '/export',
                                'defaults' => [
                                    'action' => 'export',
                                ],
                            ],
                        ],
                        'import'    => [
                            'type'    => 'segment',
                            'options' => [
                                'route'    => '/import',
                                'defaults' => [
                                    'action' => 'import',
                                ],
                            ],
                        ],
                        'edit'      => [
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
                        'loading'   => [
                            'type'    => 'segment',
                            'options' => [
                                'route'       => '/loading[/:id]',
                                'constraints' => [
                                    'id' => '\d+',
                                ],
                                'defaults'    => [
                                    'action' => 'loading',
                                ],
                            ],
                        ],
                        'unloading' => [
                            'type'    => 'segment',
                            'options' => [
                                'route'       => '/unloading[/:id]',
                                'constraints' => [
                                    'id' => '\d+',
                                ],
                                'defaults'    => [
                                    'action' => 'unloading',
                                ],
                            ],
                        ],
                        'delete'    => [
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
                'saleContract'     => [
                    'type'          => 'segment',
                    'options'       => [
                        'route'    => '/sale-contract',
                        'defaults' => [
                            'controller' => Controller\SaleContractController::class,
                            'action'     => 'index',
                        ],
                    ],
                    'may_terminate' => true,
                    'child_routes'  => [
                        'advanced' => [
                            'type'    => 'segment',
                            'options' => [
                                'route'       => '/advanced[/:id]',
                                'constraints' => [
                                    'id' => '\d+',
                                ],
                                'defaults'    => [
                                    'action' => 'advanced',
                                ],
                            ],
                        ],
                        'edit'     => [
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
                        'delete'   => [
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
                'saleWagon'        => [
                    'type'         => 'segment',
                    'options'      => [
                        'route'       => '/sale-wagon/:contract',
                        'constraints' => [
                            'contract' => '\d+',
                        ],
                        'defaults'    => [
                            'controller' => Controller\SaleWagonController::class,
                        ],
                    ],
                    'child_routes' => [
                        'export'  => [
                            'type'    => 'segment',
                            'options' => [
                                'route'    => '/export',
                                'defaults' => [
                                    'action' => 'export',
                                ],
                            ],
                        ],
                        'import'  => [
                            'type'    => 'segment',
                            'options' => [
                                'route'    => '/import',
                                'defaults' => [
                                    'action' => 'import',
                                ],
                            ],
                        ],
                        'edit'    => [
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
                        'loading' => [
                            'type'    => 'segment',
                            'options' => [
                                'route'       => '/loading[/:id]',
                                'constraints' => [
                                    'id' => '\d+',
                                ],
                                'defaults'    => [
                                    'action' => 'loading',
                                ],
                            ],
                        ],
                        'delete'  => [
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
        ],
    ],
];