<?php

namespace Resource;

return [
    'routes' => [
        'dropout'  => [
            'type'          => 'segment',
            'options'       => [
                'route'    => '/dropout',
                'defaults' => [
                    'controller' => Controller\DropoutController::class,
                    'action'     => 'index',
                ],
            ],
            'may_terminate' => true,
            'child_routes'  => [
                'ajaxValue' => [
                    'type'    => 'segment',
                    'options' => [
                        'route'       => '/ajax-value[/:provider]',
                        'constraints' => [
                            'provider' => '\d+',
                        ],
                        'defaults'    => [
                            'action' => 'ajax-value',
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
        'material' => [
            'type'          => 'segment',
            'options'       => [
                'route'    => '/material',
                'defaults' => [
                    'action'     => 'index',
                    'controller' => Controller\MaterialController::class,
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
        'product'  => [
            'type'          => 'segment',
            'options'       => [
                'route'    => '/product',
                'defaults' => [
                    'action'     => 'index',
                    'controller' => Controller\ProductController::class,
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