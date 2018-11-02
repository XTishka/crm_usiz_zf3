<?php

namespace Document;

return [
    'routes' => [
        'transaction'      => [
            'type'         => 'segment',
            'options'      => [
                'route'    => '/transaction',
                'defaults' => [
                    'controller' => Controller\TransactionController::class,
                ],
            ],
            'child_routes' => [
                'additional' => [
                    'type'         => 'segment',
                    'options'      => [
                        'route' => '/additional',
                    ],
                    'child_routes' => [
                        'edit'   => [
                            'type'    => 'segment',
                            'options' => [
                                'route'       => '/edit[/:id]',
                                'constraints' => [
                                    'id' => '\d+',
                                ],
                                'defaults'    => [
                                    'action' => 'additional-edit',
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
                                    'action' => 'additional-delete',
                                ],
                            ],
                        ],
                    ],
                ],
                'carrier'    => [
                    'type'         => 'segment',
                    'options'      => [
                        'route' => '/carrier',
                    ],
                    'child_routes' => [
                        'edit'   => [
                            'type'    => 'segment',
                            'options' => [
                                'route'       => '/edit[/:id]',
                                'constraints' => [
                                    'id' => '\d+',
                                ],
                                'defaults'    => [
                                    'action' => 'carrier-edit',
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
                                    'action' => 'carrier-delete',
                                ],
                            ],
                        ],
                    ],
                ],
                'company'    => [
                    'type'         => 'segment',
                    'options'      => [
                        'route' => '/company',
                    ],
                    'child_routes' => [
                        'edit'   => [
                            'type'    => 'segment',
                            'options' => [
                                'route'       => '/edit[/:id]',
                                'constraints' => [
                                    'id' => '\d+',
                                ],
                                'defaults'    => [
                                    'action' => 'company-edit',
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
                                    'action' => 'company-delete',
                                ],
                            ],
                        ],
                    ],
                ],
                'corporate'  => [
                    'type'         => 'segment',
                    'options'      => [
                        'route' => '/corporate',
                    ],
                    'child_routes' => [
                        'edit'   => [
                            'type'    => 'segment',
                            'options' => [
                                'route'       => '/edit[/:id]',
                                'constraints' => [
                                    'id' => '\d+',
                                ],
                                'defaults'    => [
                                    'action' => 'corporate-edit',
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
                                    'action' => 'corporate-delete',
                                ],
                            ],
                        ],
                    ],
                ],
                'customer'   => [
                    'type'         => 'segment',
                    'options'      => [
                        'route' => '/customer',
                    ],
                    'child_routes' => [
                        'edit'   => [
                            'type'    => 'segment',
                            'options' => [
                                'route'       => '/edit[/:id]',
                                'constraints' => [
                                    'id' => '\d+',
                                ],
                                'defaults'    => [
                                    'action' => 'customer-edit',
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
                                    'action' => 'customer-delete',
                                ],
                            ],
                        ],
                    ],
                ],
                'plant'      => [
                    'type'         => 'segment',
                    'options'      => [
                        'route' => '/plant',
                    ],
                    'child_routes' => [
                        'edit'   => [
                            'type'    => 'segment',
                            'options' => [
                                'route'       => '/edit[/:id]',
                                'constraints' => [
                                    'id' => '\d+',
                                ],
                                'defaults'    => [
                                    'action' => 'plant-edit',
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
                                    'action' => 'plant-delete',
                                ],
                            ],
                        ],
                    ],
                ],
                'provider'   => [
                    'type'         => 'segment',
                    'options'      => [
                        'route' => '/provider',
                    ],
                    'child_routes' => [
                        'edit'   => [
                            'type'    => 'segment',
                            'options' => [
                                'route'       => '/edit[/:id]',
                                'constraints' => [
                                    'id' => '\d+',
                                ],
                                'defaults'    => [
                                    'action' => 'provider-edit',
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
                                    'action' => 'provider-delete',
                                ],
                            ],
                        ],
                    ],
                ],
                'import'     => [
                    'type'    => 'segment',
                    'options' => [
                        'route'    => '/import/:company',
                        'constraints' => [
                            'company' => '\d+',
                        ],
                        'defaults' => [
                            'action' => 'import',
                        ],
                    ],
                ],
            ],
        ],
        'purchaseContract' => [
            'type'          => 'segment',
            'options'       => [
                'route'    => '/purchase-contract',
                'defaults' => [
                    'action'     => 'index',
                    'controller' => Controller\PurchaseContractController::class,
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
                    'action'     => 'index',
                    'controller' => Controller\SaleContractController::class,
                ],
            ],
            'may_terminate' => true,
            'child_routes'  => [
                'byCompany' => [
                    'type'    => 'segment',
                    'options' => [
                        'route'       => '/:id',
                        'constraints' => [
                            'id' => '\d+',
                        ],
                    ],
                ],
                'advanced'  => [
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
];