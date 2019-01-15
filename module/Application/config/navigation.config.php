<?php

return [
    'default' => [
        'reports'          => [
            'label' => 'Reports',
            'icon'  => 'fa fa-pie-chart fa-fw',
            'uri'   => '#none',
            'pages' => [
                'daily' => [
                    'label' => 'Daily report',
                    'icon'  => 'fa fa-table fa-fw',
                    'route' => 'reports/daily',
                ],
                'purchaseWagons' => [
                    'label' => 'Purchase wagons',
                    'icon'  => 'fa fa-table fa-fw',
                    'route' => 'reports/purchaseWagons',
                ],
                'shipments'      => [
                    'label' => 'Shipments',
                    'icon'  => 'fa fa-table fa-fw',
                    'route' => 'reports/shipments',
                ],
                'finances'      => [
                    'label' => 'Finances',
                    'icon'  => 'fa fa-table fa-fw',
                    'route' => 'reports/finance',
                ],
            ],
        ],
        /*
        'dashboard'        => [
            'label' => 'Dashboard',
            'id'    => 'dashboard',
            'icon'  => 'fa fa-dashboard fa-fw',
            'class' => 'active no-hide',
            'uri'   => '#none',
        ],
        */
        'companyDashboard' => [
            'label' => 'Companies',
            'id'    => 'companyDashboard',
            'icon'  => 'fa fa-dashboard fa-fw',
            'class' => 'active no-hide',
            'uri'   => '#none',
        ],
        'plantDashboard'   => [
            'label' => 'Plants',
            'id'    => 'plantDashboard',
            'icon'  => 'fa fa-dashboard fa-fw',
            'class' => 'active no-hide',
            'uri'   => '#none',
        ],
        /*
        'documents'     => [
            'label' => 'Documents',
            'icon'  => 'fa fa-briefcase fa-fw',
            'uri'   => '#none',
            'pages' => [
                'purchaseContracts' => [
                    'label' => 'Purchase contracts',
                    'icon'  => 'fa fa-plus-circle fa-fw',
                    'route' => 'purchaseContract',
                    'pages' => [
                        'edit' => [
                            'label'   => 'Add new contract',
                            'route'   => 'purchaseContract/edit',
                            'visible' => false,
                        ],
                    ],
                ],
                'saleContracts'     => [
                    'label' => 'Sale contracts',
                    'icon'  => 'fa fa-minus-circle fa-fw',
                    'route' => 'saleContract',
                    'pages' => [
                        'edit' => [
                            'label'   => 'Add new contract',
                            'route'   => 'saleContract/edit',
                            'visible' => false,
                        ],
                    ],
                ],
            ],
        ],
        */
        'contractors'      => [
            'label' => 'Contractors',
            'icon'  => 'fa fa-users fa-fw',
            'uri'   => '#none',
            'pages' => [
                'contractorProvider'   => [
                    'label' => 'Providers',
                    'icon'  => 'fa fa-download fa-fw',
                    'route' => 'contractorProvider',
                    'pages' => [
                        'edit' => [
                            'label'   => 'Add new contractor',
                            'route'   => 'contractorProvider/edit',
                            'visible' => false,
                        ],
                    ],
                ],
                'contractorCustomer'   => [
                    'label' => 'Customers',
                    'icon'  => 'fa fa-upload fa-fw',
                    'route' => 'contractorCustomer',
                    'pages' => [
                        'edit' => [
                            'label'   => 'Add new contractor',
                            'route'   => 'contractorCustomer/edit',
                            'visible' => false,
                        ],
                    ],
                ],
                'contractorAdditional' => [
                    'label' => 'Additional contractors',
                    'icon'  => 'fa fa-plus-circle fa-fw',
                    'route' => 'contractorAdditional',
                    'pages' => [
                        'edit' => [
                            'label'   => 'Add new contractor',
                            'route'   => 'contractorAdditional/edit',
                            'visible' => false,
                        ],
                    ],
                ],
                'contractorCorporate'  => [
                    'label' => 'Corporate contractors',
                    'icon'  => 'fa fa-building fa-fw',
                    'route' => 'contractorCorporate',
                    'pages' => [
                        'edit' => [
                            'label'   => 'Add new contractor',
                            'route'   => 'contractorCorporate/edit',
                            'visible' => false,
                        ],
                    ],
                ],
                'contractorCompany'    => [
                    'label' => 'Companies',
                    'icon'  => 'fa fa-building fa-fw',
                    'route' => 'contractorCompany',
                    'pages' => [
                        'edit' => [
                            'label'   => 'Add new contractor',
                            'route'   => 'contractorCompany/edit',
                            'visible' => false,
                        ],
                    ],
                ],
                'contractorPlant'      => [
                    'label' => 'Plants',
                    'icon'  => 'fa fa-industry fa-fw',
                    'route' => 'contractorPlant',
                    'pages' => [
                        'edit' => [
                            'label'   => 'Add new contractor',
                            'route'   => 'contractorPlant/edit',
                            'visible' => false,
                        ],
                    ],
                ],
                'contractorConsignee'  => [
                    'label' => 'Consignees',
                    'icon'  => 'fa fa-inbox fa-fw',
                    'route' => 'contractorConsignee',
                    'pages' => [
                        'edit' => [
                            'label'   => 'Add new contractor',
                            'route'   => 'contractorConsignee/edit',
                            'visible' => false,
                        ],
                    ],
                ],
            ],
        ],
        'manufacturing'    => [
            'label' => 'Manufacturing',
            'icon'  => 'fa fa-cogs fa-fw',
            'uri'   => '#none',
            'pages' => [
                'furnaces'   => [
                    'label' => 'Furnaces',
                    'icon'  => 'fa fa-fire fa-fw',
                    'route' => 'furnace',
                    'pages' => [
                        'edit' => [
                            'label'   => 'Add new furnace',
                            'route'   => 'furnace/edit',
                            'visible' => false,
                        ],
                    ],
                ],
                'warehouses' => [
                    'label' => 'Warehouses',
                    'icon'  => 'fa fa-inbox fa-fw',
                    'route' => 'warehouse',
                    'pages' => [
                        'edit' => [
                            'label'   => 'Add new warehouse',
                            'route'   => 'warehouse/edit',
                            'visible' => false,
                        ],
                    ],
                ],
            ],
        ],
        'resources'        => [
            'label' => 'Resources',
            'icon'  => 'fa fa-tasks fa-fw',
            'uri'   => '#none',
            'pages' => [
                'dropout'   => [
                    'label' => 'Rates of dropout',
                    'icon'  => 'fa fa-arrow-circle-down fa-fw',
                    'route' => 'dropout',
                    'pages' => [
                        'edit' => [
                            'label'   => 'Add new rate of dropout',
                            'route'   => 'dropout/edit',
                            'visible' => false,
                        ],
                    ],
                ],
                'materials' => [
                    'label' => 'Materials',
                    'icon'  => 'fa fa-star-o fa-fw',
                    'route' => 'material',
                    'pages' => [
                        'edit' => [
                            'label'   => 'Add new material',
                            'route'   => 'material/edit',
                            'visible' => false,
                        ],
                    ],
                ],
                'products'  => [
                    'label' => 'Production',
                    'icon'  => 'fa fa-star fa-fw',
                    'route' => 'product',
                    'pages' => [
                        'edit' => [
                            'label'   => 'Add new product',
                            'route'   => 'product/edit',
                            'visible' => false,
                        ],
                    ],
                ],
            ],
        ],
        'transport'        => [
            'label' => 'Transport',
            'icon'  => 'fa fa-truck fa-fw',
            'uri'   => '#none',
            'pages' => [
                'carriers' => [
                    'label' => 'Carriers',
                    'icon'  => 'fa fa-truck fa-fw',
                    'route' => 'carrier',
                    'pages' => [
                        'edit' => [
                            'label'   => 'Add new carrier',
                            'route'   => 'carrier/edit',
                            'visible' => false,
                        ],
                    ],
                ],
                'rates'    => [
                    'label' => 'Rates',
                    'icon'  => 'fa fa-pie-chart fa-fw',
                    'route' => 'rate',
                    'pages' => [
                        'edit' => [
                            'label'   => 'Add new rate',
                            'route'   => 'rate/edit',
                            'visible' => false,
                        ],
                    ],
                ],
                'stations' => [
                    'label' => 'Stations',
                    'icon'  => 'fa fa-map-marker fa-fw',
                    'route' => 'station',
                    'pages' => [
                        'edit' => [
                            'label'   => 'Add new station',
                            'route'   => 'station/edit',
                            'visible' => false,
                        ],
                    ],
                ],
            ],
        ],
        'settings'         => [
            'label' => 'Settings',
            'icon'  => 'fa fa-cog fa-fw',
            'uri'   => '#none',
            'pages' => [
                'countries'      => [
                    'label' => 'Countries',
                    'icon'  => 'fa fa-globe fa-fw',
                    'route' => 'country',
                    'pages' => [
                        'edit' => [
                            'label'   => 'Add new country',
                            'route'   => 'country/edit',
                            'visible' => false,
                        ],
                    ],
                ],
                'taxes'          => [
                    'label' => 'Taxes',
                    'icon'  => 'fa fa-percent fa-fw',
                    'route' => 'tax',
                    'pages' => [
                        'edit' => [
                            'label'   => 'Add new tax',
                            'route'   => 'tax/edit',
                            'visible' => false,
                        ],
                    ],
                ],
                'banks'          => [
                    'label' => 'Banks',
                    'icon'  => 'fa fa-bank fa-fw',
                    'route' => 'bank',
                    'pages' => [
                        'edit' => [
                            'label'   => 'Add new bank',
                            'route'   => 'bank/edit',
                            'visible' => false,
                        ],
                    ],
                ],
                'reset'          => [
                    'label' => 'Reset DB without Contracts',
                    'icon'  => 'fa fa-refresh fa-fw',
                    'route' => 'reset',
                ],
                'resetContracts' => [
                    'label' => 'Reset DB All',
                    'icon'  => 'fa fa-refresh fa-fw',
                    'route' => 'reset/all',
                ],
            ],
        ],
        'users'            => [
            'label' => 'Users',
            'icon'  => 'fa fa-user-circle fa-fw',
            'route' => 'user',
            'pages' => [
                'edit' => [
                    'label'   => 'Add new user',
                    'route'   => 'user/edit',
                    'visible' => false,
                ],
            ],
        ],
    ],
];