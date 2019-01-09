<?php

namespace Contractor;

return [
    'routes' => [
        'contractorCommon' => [
            'type'          => 'segment',
            'options'       => [
                'route'    => '/contractor-common',
                'defaults' => [
                    'action'     => 'index',
                    'controller' => Controller\ContractorCommonController::class,
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
        'contractorAdditional' => [
            'type'          => 'segment',
            'options'       => [
                'route'    => '/contractor-additional',
                'defaults' => [
                    'action'     => 'index',
                    'controller' => Controller\ContractorAdditionalController::class,
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
        'contractorCarrier'    => [
            'type'          => 'segment',
            'options'       => [
                'route'    => '/contractor-carrier',
                'defaults' => [
                    'action'     => 'index',
                    'controller' => Controller\ContractorCarrierController::class,
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
        'contractorCompany'    => [
            'type'          => 'segment',
            'options'       => [
                'route'    => '/contractor-company',
                'defaults' => [
                    'action'     => 'index',
                    'controller' => Controller\ContractorCompanyController::class,
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
        'contractorConsignee'    => [
            'type'          => 'segment',
            'options'       => [
                'route'    => '/contractor-consignee',
                'defaults' => [
                    'action'     => 'index',
                    'controller' => Controller\ContractorConsigneeController::class,
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
        'contractorCorporate'  => [
            'type'          => 'segment',
            'options'       => [
                'route'    => '/contractor-corporate',
                'defaults' => [
                    'action'     => 'index',
                    'controller' => Controller\ContractorCorporateController::class,
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
        'contractorCustomer'   => [
            'type'          => 'segment',
            'options'       => [
                'route'    => '/contractor-customer',
                'defaults' => [
                    'action'     => 'index',
                    'controller' => Controller\ContractorCustomerController::class,
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
        'contractorPlant'      => [
            'type'          => 'segment',
            'options'       => [
                'route'    => '/contractor-plant',
                'defaults' => [
                    'action'     => 'index',
                    'controller' => Controller\ContractorPlantController::class,
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
        'contractorProvider'   => [
            'type'          => 'segment',
            'options'       => [
                'route'    => '/contractor-provider',
                'defaults' => [
                    'action'     => 'index',
                    'controller' => Controller\ContractorProviderController::class,
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