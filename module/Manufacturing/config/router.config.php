<?php

namespace Manufacturing;

return [
    'routes' => [
        'furnace'   => [
            'type'          => 'segment',
            'options'       => [
                'route'    => '/furnace',
                'defaults' => [
                    'action'     => 'index',
                    'controller' => Controller\FurnaceController::class,
                ],
            ],
            'may_terminate' => true,
            'child_routes'  => [
                'api'       => [
                    'type'    => 'segment',
                    'options' => [
                        'route'       => '/api[/:id]',
                        'constraints' => [
                            'id' => '\d+',
                        ],
                        'defaults'    => [
                            'controller' => Controller\SkipController::class,
                            'action'     => 'index',
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
                            'controller' => Controller\SkipController::class,
                            'action'     => 'edit',
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
                            'controller' => Controller\SkipController::class,
                            'action'     => 'delete',
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
        'plant'     => [
            'type'          => 'segment',
            'options'       => [
                'route'    => '/plant',
                'defaults' => [
                    'action'     => 'index',
                    'controller' => Controller\PlantController::class,
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
        'warehouse' => [
            'type'          => 'segment',
            'options'       => [
                'route'    => '/warehouse',
                'defaults' => [
                    'action'     => 'index',
                    'controller' => Controller\WarehouseController::class,
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