<?php

namespace Application;

return [
    'routes' => [
        'api'            => [
            'type'         => 'segment',
            'options'      => [
                'route' => '/api',
            ],
            'child_routes' => [
                'bankBalances'                => [
                    'type'    => 'segment',
                    'options' => [
                        'route'       => '/bank-balances/:company',
                        'constraints' => [
                            'company'  => '\d+',
                            'material' => '\d+',
                        ],
                        'defaults'    => [
                            'action'     => 'bank-balances',
                            'controller' => Controller\ApiController::class,
                        ],
                    ],
                ],
                'materialAssets'              => [
                    'type'    => 'segment',
                    'options' => [
                        'route'       => '/material-assets/:company/:material',
                        'constraints' => [
                            'company'  => '\d+',
                            'material' => '\d+',
                        ],
                        'defaults'    => [
                            'action'     => 'material-assets',
                            'controller' => Controller\ApiController::class,
                        ],
                    ],
                ],
                'companyDebits'               => [
                    'type'    => 'segment',
                    'options' => [
                        'route'       => '/company-debits/:company',
                        'constraints' => [
                            'company' => '\d+',
                        ],
                        'defaults'    => [
                            'action'     => 'company-debits',
                            'controller' => Controller\ApiController::class,
                        ],
                    ],
                ],
                'customerDebts'               => [
                    'type'    => 'segment',
                    'options' => [
                        'route'       => '/customer-debts/:company',
                        'constraints' => [
                            'company' => '\d+',
                        ],
                        'defaults'    => [
                            'action'     => 'customer-debts',
                            'controller' => Controller\ApiController::class,
                        ],
                    ],
                ],
                'plantDebts'                  => [
                    'type'    => 'segment',
                    'options' => [
                        'route'       => '/plant-debts/:company',
                        'constraints' => [
                            'company' => '\d+',
                        ],
                        'defaults'    => [
                            'action'     => 'plant-debts',
                            'controller' => Controller\ApiController::class,
                        ],
                    ],
                ],
                'companyPrepayments'          => [
                    'type'    => 'segment',
                    'options' => [
                        'route'       => '/company-prepayments/:company',
                        'constraints' => [
                            'company' => '\d+',
                        ],
                        'defaults'    => [
                            'action'     => 'company-prepayments',
                            'controller' => Controller\ApiController::class,
                        ],
                    ],
                ],
                'plantPrepayments'            => [
                    'type'    => 'segment',
                    'options' => [
                        'route'       => '/plant-prepayments/:plant',
                        'constraints' => [
                            'company' => '\d+',
                        ],
                        'defaults'    => [
                            'action'     => 'plant-prepayments',
                            'controller' => Controller\ApiController::class,
                        ],
                    ],
                ],
                'companyToPlantPrepayments'   => [
                    'type'    => 'segment',
                    'options' => [
                        'route'       => '/company-to-plant-prepayments/:company',
                        'constraints' => [
                            'company' => '\d+',
                        ],
                        'defaults'    => [
                            'action'     => 'company-to-plant-prepayments',
                            'controller' => Controller\ApiController::class,
                        ],
                    ],
                ],
                'plantFromCompanyPrepayments' => [
                    'type'    => 'segment',
                    'options' => [
                        'route'       => '/plant-from-company-prepayments/:plant',
                        'constraints' => [
                            'company' => '\d+',
                        ],
                        'defaults'    => [
                            'action'     => 'plant-from-company-prepayments',
                            'controller' => Controller\ApiController::class,
                        ],
                    ],
                ],
                'carriersReceivable' => [
                    'type'    => 'segment',
                    'options' => [
                        'route'       => '/carriers-receivable/:company',
                        'constraints' => [
                            'company' => '\d+',
                        ],
                        'defaults'    => [
                            'action'     => 'carriers-receivable',
                            'controller' => Controller\ApiController::class,
                        ],
                    ],
                ],
                'companyCorporates'           => [
                    'type'    => 'segment',
                    'options' => [
                        'route'       => '/company-corporates/:company',
                        'constraints' => [
                            'company' => '\d+',
                        ],
                        'defaults'    => [
                            'action'     => 'company-corporates',
                            'controller' => Controller\ApiController::class,
                        ],
                    ],
                ],
                'customerPrepayments'         => [
                    'type'    => 'segment',
                    'options' => [
                        'route'       => '/customer-prepayments/:company',
                        'constraints' => [
                            'customer' => '\d+',
                        ],
                        'defaults'    => [
                            'action'     => 'customer-prepayments',
                            'controller' => Controller\ApiController::class,
                        ],
                    ],
                ],
                'companyPayables'             => [
                    'type'    => 'segment',
                    'options' => [
                        'route'       => '/company-payables/:company',
                        'constraints' => [
                            'customer' => '\d+',
                        ],
                        'defaults'    => [
                            'action'     => 'company-payables',
                            'controller' => Controller\ApiController::class,
                        ],
                    ],
                ],
                'providerTransactions'        => [
                    'type'    => 'segment',
                    'options' => [
                        'route'       => '/provider-transactions/:company[/provider/:contractor]',
                        'constraints' => [
                            'contractor' => '\d+',
                            'customer'   => '\d+',
                        ],
                        'defaults'    => [
                            'action'     => 'provider-transactions',
                            'controller' => Controller\ApiController::class,
                        ],
                    ],
                ],
                'plantTransactions'           => [
                    'type'    => 'segment',
                    'options' => [
                        'route'       => '/plant-transactions/:company[/plant/:contractor]',
                        'constraints' => [
                            'contractor' => '\d+',
                            'plant'      => '\d+',
                        ],
                        'defaults'    => [
                            'action'     => 'plant-transactions',
                            'controller' => Controller\ApiController::class,
                        ],
                    ],
                ],
                'companyTransactions'         => [
                    'type'    => 'segment',
                    'options' => [
                        'route'       => '/company-transactions/:plant[/company/:contractor]',
                        'constraints' => [
                            'contractor' => '\d+',
                            'plant'      => '\d+',
                        ],
                        'defaults'    => [
                            'action'     => 'company-transactions',
                            'controller' => Controller\ApiController::class,
                        ],
                    ],
                ],
                'customerTransactions'        => [
                    'type'    => 'segment',
                    'options' => [
                        'route'       => '/customer-transactions/:company[/customer/:contractor]',
                        'constraints' => [
                            'contractor' => '\d+',
                            'customer'   => '\d+',
                        ],
                        'defaults'    => [
                            'action'     => 'customer-transactions',
                            'controller' => Controller\ApiController::class,
                        ],
                    ],
                ],
                'carrierTransactions'         => [
                    'type'    => 'segment',
                    'options' => [
                        'route'       => '/carrier-transactions/:company[/carrier/:contractor]',
                        'constraints' => [
                            'contractor' => '\d+',
                            'carrier'    => '\d+',
                        ],
                        'defaults'    => [
                            'action'     => 'carrier-transactions',
                            'controller' => Controller\ApiController::class,
                        ],
                    ],
                ],
                'additionalTransactions'      => [
                    'type'    => 'segment',
                    'options' => [
                        'route'       => '/additional-transactions/:company[/additional/:contractor]',
                        'constraints' => [
                            'contractor' => '\d+',
                            'additional' => '\d+',
                        ],
                        'defaults'    => [
                            'action'     => 'additional-transactions',
                            'controller' => Controller\ApiController::class,
                        ],
                    ],
                ],
                'corporateTransactions'       => [
                    'type'    => 'segment',
                    'options' => [
                        'route'       => '/corporate-transactions/:company[/corporate/:contractor]',
                        'constraints' => [
                            'contractor' => '\d+',
                            'corporate'  => '\d+',
                        ],
                        'defaults'    => [
                            'action'     => 'corporate-transactions',
                            'controller' => Controller\ApiController::class,
                        ],
                    ],
                ],
            ],
        ],
        'home'           => [
            'type'    => 'literal',
            'options' => [
                'route'    => '/',
                'defaults' => [
                    'action'     => 'welcome',
                    'controller' => Controller\DashboardController::class,
                ],
            ],
        ],
        'reset'          => [
            'type'          => 'segment',
            'options'       => [
                'route'    => '/reset',
                'defaults' => [
                    'action'     => 'reset',
                    'controller' => Controller\ResetController::class,
                ],
            ],
            'may_terminate' => true,
            'child_routes'  => [
                'all' => [
                    'type'    => 'segment',
                    'options' => [
                        'route'    => '/all',
                        'defaults' => [
                            'action' => 'all',
                        ],
                    ],
                ],
            ],
        ],
        'dashboard'      => [
            'type'          => 'segment',
            'options'       => [
                'route'       => '/dashboard/:company',
                'constraints' => [
                    'company' => '\d+',
                ],
                'defaults'    => [
                    'action'     => 'index',
                    'controller' => Controller\DashboardController::class,
                ],
            ],
            'may_terminate' => true,
            'child_routes'  => [
                'finance' => [
                    'type'    => 'segment',
                    'options' => [
                        'route'    => '/finance',
                        'defaults' => [
                            'action' => 'finance',
                        ],
                    ],
                ],
            ],
        ],
        'plantDashboard' => [
            'type'          => 'segment',
            'options'       => [
                'route'       => '/plant-dashboard/:plant',
                'constraints' => [
                    'plant' => '\d+',
                ],
                'defaults'    => [
                    'action'     => 'index',
                    'controller' => Controller\PlantDashboardController::class,
                ],
            ],
            'may_terminate' => true,
            'child_routes'  => [
                'finance' => [
                    'type'    => 'segment',
                    'options' => [
                        'route'    => '/finance',
                        'defaults' => [
                            'action' => 'finance',
                        ],
                    ],
                ],
            ],
        ],
        'country'        => [
            'type'          => 'segment',
            'options'       => [
                'route'    => '/country',
                'defaults' => [
                    'action'     => 'index',
                    'controller' => Controller\CountryController::class,
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
        'tax'            => [
            'type'          => 'segment',
            'options'       => [
                'route'    => '/tax',
                'defaults' => [
                    'action'     => 'index',
                    'controller' => Controller\TaxController::class,
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