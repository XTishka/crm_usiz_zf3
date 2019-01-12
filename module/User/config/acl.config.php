<?php

return [
    'roles'     => [
        'guest'    => [
            'name'           => 'guest',
            'use_as_default' => true,
        ],
        'customer' => [
            'name'        => 'customer',
            'parent_role' => 'guest',
        ],
        'admin'    => [
            'name'        => 'admin',
            'parent_role' => 'customer',
        ],
    ],
    'resources' => [
        'home'                 => [
            'route' => 'home',
            'allow' => 'admin',
        ],
        'reset'                => [
            'route'        => 'reset',
            'child_routes' => [
                'all' => [
                    'route' => 'all',
                ],
            ],
        ],
        'test'                 => [
            'allow' => 'customer',
            'route' => 'test',
        ],
        'api'                  => [
            'allow'           => 'customer',
            'route'           => 'api',
            'child_resources' => [
                'bankBalances'                => [
                    'route' => 'bankBalances',
                ],
                'materialAssets'              => [
                    'route' => 'materialAssets',
                ],
                'companyDebits'               => [
                    'route' => 'companyDebits',
                ],
                'customerDebts'               => [
                    'route' => 'customerDebts',
                ],
                'plantDebts'                  => [
                    'route' => 'plantDebts',
                ],
                'companyPrepayments'          => [
                    'route' => 'companyPrepayments',
                ],
                'plantPrepayments'            => [
                    'route' => 'plantPrepayments',
                ],
                'companyToPlantPrepayments'   => [
                    'route' => 'companyToPlantPrepayments',
                ],
                'plantFromCompanyPrepayments' => [
                    'route' => 'plantFromCompanyPrepayments',
                ],
                'companyCorporates'           => [
                    'route' => 'companyCorporates',
                ],
                'customerPrepayments'         => [
                    'route' => 'customerPrepayments',
                ],
                'companyPayables'             => [
                    'route' => 'companyPayables',
                ],
                'providerTransactions'        => [
                    'route' => 'providerTransactions',
                ],
                'customerTransactions'        => [
                    'route' => 'customerTransactions',
                ],
                'carrierTransactions'         => [
                    'route' => 'carrierTransactions',
                ],
                'additionalTransactions'      => [
                    'route' => 'additionalTransactions',
                ],
                'corporateTransactions'       => [
                    'route' => 'corporateTransactions',
                ],
                'plantTransactions'           => [
                    'route' => 'plantTransactions',
                ],
                'companyTransactions'         => [
                    'route' => 'companyTransactions',
                ],
                'carriersReceivable'          => [
                    'route' => 'carriersReceivable',
                ],
                'assets'                      => [
                    'route'           => 'assets',
                    'child_resources' => [
                        'customer-receivable' => [
                            'route' => 'customer-receivable',
                        ],
                        'prepay-to-provider'  => [
                            'route' => 'prepay-to-provider',
                        ],
                        'prepay-to-carrier'   => [
                            'route' => 'prepay-to-carrier',
                        ],
                        'prepay-to-plant'     => [
                            'route' => 'prepay-to-plant',
                        ],
                        'prepay-to-other'     => [
                            'route' => 'prepay-to-other',
                        ],
                        'total-receivable'    => [
                            'route' => 'total-receivable',
                        ],
                    ],
                ],
                'liabilities'                 => [
                    'route'           => 'liabilities',
                    'child_resources' => [
                        'charter-capital'      => [
                            'route' => 'charter-capital',
                        ],
                        'prepay-from-customer' => [
                            'route' => 'prepay-from-customer',
                        ],
                        'debt-to-carrier'      => [
                            'route' => 'debt-to-carrier',
                        ],
                        'debt-to-provider'     => [
                            'route' => 'debt-to-provider',
                        ],
                        'debt-to-plant'        => [
                            'route' => 'debt-to-plant',
                        ],
                        'debt-to-other'        => [
                            'route' => 'debt-to-other',
                        ],
                        'accounts-payable'     => [
                            'route' => 'accounts-payable',
                        ],
                        'plant' => [
                            'route' => 'plant',
                            'child_resources' => [
                                'accounts-payable'     => [
                                    'route' => 'accounts-payable',
                                ],
                            ]
                        ]
                    ],
                ],
            ],
        ],
        'dashboard'            => [
            'allow'           => 'admin',
            'route'           => 'dashboard',
            'child_resources' => [
                'bank'             => [
                    'route'           => 'bank',
                    'child_resources' => [
                        'edit'   => [
                            'route' => 'edit',
                        ],
                        'delete' => [
                            'route' => 'delete',
                        ],
                    ],
                ],
                'purchaseContract' => [
                    'route'           => 'purchaseContract',
                    'child_resources' => [
                        'advanced' => [
                            'route' => 'advanced',
                        ],
                        'edit'     => [
                            'route' => 'edit',
                        ],
                        'delete'   => [
                            'route' => 'delete',
                        ],
                    ],
                ],
                'saleContract'     => [
                    'route'           => 'saleContract',
                    'child_resources' => [
                        'advanced' => [
                            'route' => 'advanced',
                        ],
                        'edit'     => [
                            'route' => 'edit',
                        ],
                        'delete'   => [
                            'route' => 'delete',
                        ],
                    ],
                ],
                'finance'          => [
                    'route' => 'finance',
                ],
                'purchaseWagon'    => [
                    'route'           => 'purchaseWagon',
                    'child_resources' => [
                        'export'    => [
                            'route' => 'export',
                        ],
                        'import'    => [
                            'route' => 'import',
                        ],
                        'edit'      => [
                            'route' => 'edit',
                        ],
                        'delete'    => [
                            'route' => 'delete',
                        ],
                        'loading'   => [
                            'route' => 'loading',
                        ],
                        'unloading' => [
                            'route' => 'unloading',
                        ],
                    ],
                ],
                'saleWagon'        => [
                    'route'           => 'saleWagon',
                    'child_resources' => [
                        'export'  => [
                            'route' => 'export',
                        ],
                        'import'  => [
                            'route' => 'import',
                        ],
                        'edit'    => [
                            'route' => 'edit',
                        ],
                        'delete'  => [
                            'route' => 'delete',
                        ],
                        'loading' => [
                            'route' => 'loading',
                        ],
                    ],
                ],
            ],
        ],
        'plantDashboard'       => [
            'allow'           => 'admin',
            'route'           => 'plantDashboard',
            'child_resources' => [
                'bank'    => [
                    'route'           => 'bank',
                    'child_resources' => [
                        'edit'   => [
                            'route' => 'edit',
                        ],
                        'delete' => [
                            'route' => 'delete',
                        ],
                    ],
                ],
                'finance' => [
                    'route' => 'finance',
                ],
            ],
        ],
        'transaction'          => [
            'allow'           => 'customer',
            'route'           => 'transaction',
            'child_resources' => [
                'additional' => [
                    'route'           => 'additional',
                    'child_resources' => [
                        'edit'   => [
                            'route' => 'edit',
                        ],
                        'delete' => [
                            'route' => 'delete',
                        ],
                    ],
                ],
                'carrier'    => [
                    'route'           => 'carrier',
                    'child_resources' => [
                        'edit'   => [
                            'route' => 'edit',
                        ],
                        'delete' => [
                            'route' => 'delete',
                        ],
                    ],
                ],
                'company'    => [
                    'route'           => 'company',
                    'child_resources' => [
                        'edit'   => [
                            'route' => 'edit',
                        ],
                        'delete' => [
                            'route' => 'delete',
                        ],
                    ],
                ],
                'customer'   => [
                    'route'           => 'customer',
                    'child_resources' => [
                        'edit'   => [
                            'route' => 'edit',
                        ],
                        'delete' => [
                            'route' => 'delete',
                        ],
                    ],
                ],
                'plant'      => [
                    'route'           => 'plant',
                    'child_resources' => [
                        'edit'   => [
                            'route' => 'edit',
                        ],
                        'delete' => [
                            'route' => 'delete',
                        ],
                    ],
                ],
                'provider'   => [
                    'route'           => 'provider',
                    'child_resources' => [
                        'edit'   => [
                            'route' => 'edit',
                        ],
                        'delete' => [
                            'route' => 'delete',
                        ],
                    ],
                ],
                'corporate'  => [
                    'route'           => 'corporate',
                    'child_resources' => [
                        'edit'   => [
                            'route' => 'edit',
                        ],
                        'delete' => [
                            'route' => 'delete',
                        ],
                    ],
                ],
                'import'     => [
                    'route' => 'import',
                ],
            ],
        ],
        'contractorAdditional' => [
            'route'           => 'contractorAdditional',
            'child_resources' => [
                'edit'   => [
                    'route' => 'edit',
                ],
                'delete' => [
                    'route' => 'delete',
                ],
            ],
        ],
        'contractorCarrier'    => [
            'route'           => 'contractorCarrier',
            'child_resources' => [
                'edit'   => [
                    'route' => 'edit',
                ],
                'delete' => [
                    'route' => 'delete',
                ],
            ],
        ],
        'contractorCompany'    => [
            'route'           => 'contractorCompany',
            'child_resources' => [
                'edit'   => [
                    'route' => 'edit',
                ],
                'delete' => [
                    'route' => 'delete',
                ],
            ],
        ],
        'contractorCorporate'  => [
            'route'           => 'contractorCorporate',
            'child_resources' => [
                'edit'   => [
                    'route' => 'edit',
                ],
                'delete' => [
                    'route' => 'delete',
                ],
            ],
        ],
        'contractorConsignee'  => [
            'route'           => 'contractorConsignee',
            'child_resources' => [
                'edit'   => [
                    'route' => 'edit',
                ],
                'delete' => [
                    'route' => 'delete',
                ],
            ],
        ],
        'contractorCustomer'   => [
            'route'           => 'contractorCustomer',
            'child_resources' => [
                'edit'   => [
                    'route' => 'edit',
                ],
                'delete' => [
                    'route' => 'delete',
                ],
            ],
        ],
        'contractorPlant'      => [
            'route'           => 'contractorPlant',
            'child_resources' => [
                'edit'   => [
                    'route' => 'edit',
                ],
                'delete' => [
                    'route' => 'delete',
                ],
            ],
        ],
        'contractorProvider'   => [
            'route'           => 'contractorProvider',
            'child_resources' => [
                'edit'   => [
                    'route' => 'edit',
                ],
                'delete' => [
                    'route' => 'delete',
                ],
            ],
        ],
        /*
        'company'              => [
            'route'           => 'company',
            'child_resources' => [
                'edit'   => [
                    'route' => 'edit',
                ],
                'delete' => [
                    'route' => 'delete',
                ],
            ],
        ],
        */
        'furnace'              => [
            'route'           => 'furnace',
            'child_resources' => [
                'api'       => [
                    'route' => 'api',
                ],
                'loading'   => [
                    'route' => 'loading',
                ],
                'unloading' => [
                    'route' => 'unloading',
                ],
                'edit'      => [
                    'route' => 'edit',
                ],
                'delete'    => [
                    'route' => 'delete',
                ],
            ],
        ],
        'plant'                => [
            'route'           => 'plant',
            'child_resources' => [
                'edit'   => [
                    'route' => 'edit',
                ],
                'delete' => [
                    'route' => 'delete',
                ],
            ],
        ],
        'dropout'              => [
            'route'           => 'dropout',
            'child_resources' => [
                'ajaxValue' => [
                    'route' => 'ajaxValue',
                ],
                'edit'      => [
                    'route' => 'edit',
                ],
                'delete'    => [
                    'route' => 'delete',
                ],
            ],
        ],
        'warehouse'            => [
            'route'           => 'warehouse',
            'child_resources' => [
                'edit'   => [
                    'route' => 'edit',
                ],
                'delete' => [
                    'route' => 'delete',
                ],
            ],
        ],
        'material'             => [
            'route'           => 'material',
            'child_resources' => [
                'edit'   => [
                    'route' => 'edit',
                ],
                'delete' => [
                    'route' => 'delete',
                ],
            ],
        ],
        'product'              => [
            'route'           => 'product',
            'child_resources' => [
                'edit'   => [
                    'route' => 'edit',
                ],
                'delete' => [
                    'route' => 'delete',
                ],
            ],
        ],
        'carrier'              => [
            'route'           => 'carrier',
            'child_resources' => [
                'valueOptions' => [
                    'route' => 'valueOptions',
                ],
                'edit'         => [
                    'route' => 'edit',
                ],
                'delete'       => [
                    'route' => 'delete',
                ],
            ],
        ],
        'rate'                 => [
            'route'           => 'rate',
            'child_resources' => [
                'ajaxRates'   => [
                    'route' => 'ajaxRates',
                ],
                'ajaxValues'  => [
                    'route' => 'ajaxValues',
                ],
                'add'         => [
                    'route' => 'add',
                ],
                'edit'        => [
                    'route' => 'edit',
                ],
                'delete'      => [
                    'route' => 'delete',
                ],
                'clearFilter' => [
                    'route' => 'clearFilter',
                ],
            ],
        ],
        'station'              => [
            'route'           => 'station',
            'child_resources' => [
                'edit'   => [
                    'route' => 'edit',
                ],
                'delete' => [
                    'route' => 'delete',
                ],
            ],
        ],
        'country'              => [
            'route'           => 'country',
            'child_resources' => [
                'edit'   => [
                    'route' => 'edit',
                ],
                'delete' => [
                    'route' => 'delete',
                ],
            ],
        ],
        'tax'                  => [
            'route'           => 'tax',
            'child_resources' => [
                'edit'   => [
                    'route' => 'edit',
                ],
                'delete' => [
                    'route' => 'delete',
                ],
            ],
        ],
        'bank'                 => [
            'route'           => 'bank',
            'child_resources' => [
                'edit'   => [
                    'route' => 'edit',
                ],
                'delete' => [
                    'route' => 'delete',
                ],
            ],
        ],
        'user'                 => [
            'allow'           => 'admin',
            'route'           => 'user',
            'child_resources' => [
                'edit'   => [
                    'route' => 'edit',
                ],
                'delete' => [
                    'route' => 'delete',
                ],
            ],
        ],
        'login'                => [
            'allow'           => 'guest',
            'route'           => 'login',
            'child_resources' => [
                'loginSuccess' => [
                    'route' => 'success',
                    'allow' => 'customer',
                ],
            ],
        ],
        'logout'               => [
            'allow' => 'guest',
            'route' => 'logout',
        ],
        // Reports
        'reports'              => [
            'allow'           => 'customer',
            'route'           => 'reports',
            'child_resources' => [
                'daily'          => [
                    'route' => 'daily',
                ],
                'shipments'      => [
                    'route' => 'shipments',
                ],
                'purchaseWagons' => [
                    'route' => 'purchaseWagons',
                ],
            ],
        ],
    ],
];