<?php

namespace Transport;

return [
    'routes' => [
        'carrier' => [
            'type'          => 'segment',
            'options'       => [
                'route'    => '/carrier',
                'defaults' => [
                    'action'     => 'index',
                    'controller' => Controller\CarrierController::class,
                ],
            ],
            'may_terminate' => true,
            'child_routes'  => [
                'edit'         => [
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
                'delete'       => [
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
                'valueOptions' => [
                    'type'    => 'segment',
                    'options' => [
                        'route'    => '/value-options',
                        'defaults' => [
                            'action' => 'value-options',
                        ],
                    ],
                ],
            ],
        ],
        'rate'    => [
            'type'          => 'segment',
            'options'       => [
                'route'    => '/rate',
                'defaults' => [
                    'action'     => 'index',
                    'controller' => Controller\RateController::class,
                ],
            ],
            'may_terminate' => true,
            'child_routes'  => [
                'clearFilter' => [
                    'type'    => 'segment',
                    'options' => [
                        'route'    => '/clear-filter',
                        'defaults' => [
                            'action' => 'clear-filter',
                        ],
                    ],
                ],
                'add'         => [
                    'type'    => 'segment',
                    'options' => [
                        'route'    => '/add',
                        'defaults' => [
                            'action' => 'add',
                        ],
                    ],
                ],
                'edit'        => [
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
                'delete'      => [
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
                'ajaxRates'   => [
                    'type'    => 'segment',
                    'options' => [
                        'route'    => '/ajax-rates',
                        'defaults' => [
                            'action' => 'ajax-rates',
                        ],
                    ],
                ],
                'ajaxValues'  => [
                    'type'    => 'segment',
                    'options' => [
                        'route'    => '/ajax-values',
                        'defaults' => [
                            'action' => 'ajax-values',
                        ],
                    ],
                ],
            ],
        ],
        'station' => [
            'type'          => 'segment',
            'options'       => [
                'route'    => '/station',
                'defaults' => [
                    'action'     => 'index',
                    'controller' => Controller\StationController::class,
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