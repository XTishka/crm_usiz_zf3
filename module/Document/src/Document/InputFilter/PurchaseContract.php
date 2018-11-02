<?php

namespace Document\InputFilter;

use Application\Service\Repository\CountryDb;
use Contractor\Entity\ContractorAbstract;
use Contractor\Entity\ContractorCompany;
use Contractor\Service\Repository\DatabaseContractorAbstract;
use Contractor\Service\Repository\DatabaseContractorCompany;
use Document\Domain\PurchaseContractEntity;
use Document\Service\Repository\PurchaseContractDb;
use Manufacturing\Service\Repository\WarehouseDb;
use Resource\Service\Repository\MaterialDb;
use Transport\Domain\CarrierEntity;
use Transport\Service\Repository\StationDb;
use Zend\Db\TableGateway\Feature\GlobalAdapterFeature;
use Zend\InputFilter\InputFilter;

class PurchaseContract extends InputFilter {

    public function init() {

        $this->add([
            'filters'    => [
                ['name' => 'StripTags'],
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                ['name' => 'NotEmpty', 'break_chain_on_failure' => true],
                ['name' => 'Csrf', 'break_chain_on_failure' => true],
            ],
        ], 'csrf');

        $this->add([
            'filters' => [
                ['name' => 'ToInt'],
            ],
        ], 'contract_id');

        $this->add([
            'required'   => true,
            'filters'    => [
                ['name' => 'StripTags'],
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                ['name' => 'NotEmpty', 'break_chain_on_failure' => true, 'options' => [
                    'messages' => [
                        'isEmpty' => 'Please enter contract number.',
                    ],
                ]],
                ['name' => 'StringLength', 'break_chain_on_failure' => true, 'options' => [
                    'max'      => 32,
                    'messages' => [
                        'stringLengthTooLong' => 'The entered contract number is too long. The maximum length is %max% characters.',
                    ],
                ]],
                ['name' => 'AppDbNoRecordExists', 'break_chain_on_failure' => true, 'options' => [
                    'adapter'  => GlobalAdapterFeature::getStaticAdapter(),
                    'table'    => PurchaseContractDb::TABLE_PURCHASE_CONTRACTS,
                    'field'    => 'contract_number',
                    'exclude'  => [
                        'field'      => 'contract_id',
                        'form_value' => 'contract_id',
                    ],
                    'messages' => [
                        'recordFound' => 'Contract with this number already exists',
                    ],
                ]],
            ],
        ], 'contract_number');

        $this->add([
            'required'   => true,
            'filters'    => [
                ['name' => 'ToInt'],
            ],
            'validators' => [
                ['name' => 'NotEmpty', 'break_chain_on_failure' => true, 'options' => [
                    'messages' => [
                        'isEmpty' => 'Please select service company.',
                    ],
                ]],
                ['name' => 'DbRecordExists', 'break_chain_on_failure' => true, 'options' => [
                    'adapter'  => GlobalAdapterFeature::getStaticAdapter(),
                    'table'    => DatabaseContractorCompany::TABLE_CONTRACTORS,
                    'field'    => 'contractor_id',
                    'exclude'  => sprintf('contractor_type = %s', GlobalAdapterFeature::getStaticAdapter()->platform->quoteValue(ContractorCompany::TYPE_COMPANY)),
                    'messages' => [
                        'noRecordFound' => 'The selected company was not found in the database.',
                    ],
                ]],
            ],
        ], 'company_id');

        $this->add([
            'required'   => true,
            'filters'    => [
                ['name' => 'ToInt'],
            ],
            'validators' => [
                ['name' => 'NotEmpty', 'break_chain_on_failure' => true, 'options' => [
                    'messages' => [
                        'isEmpty' => 'Please select warehouse.',
                    ],
                ]],
                ['name' => 'DbRecordExists', 'break_chain_on_failure' => true, 'options' => [
                    'adapter'  => GlobalAdapterFeature::getStaticAdapter(),
                    'table'    => WarehouseDb::TABLE_WAREHOUSES,
                    'field'    => 'warehouse_id',
                    'messages' => [
                        'noRecordFound' => 'The selected warehouse was not found in the database.',
                    ],
                ]],
            ],
        ], 'warehouse_id');

        $this->add([
            'required'   => true,
            'filters'    => [
                ['name' => 'ToInt'],
            ],
            'validators' => [
                ['name' => 'NotEmpty', 'break_chain_on_failure' => true, 'options' => [
                    'messages' => [
                        'isEmpty' => 'Please select material.',
                    ],
                ]],
                ['name' => 'DbRecordExists', 'break_chain_on_failure' => true, 'options' => [
                    'adapter'  => GlobalAdapterFeature::getStaticAdapter(),
                    'table'    => MaterialDb::TABLE_MATERIALS,
                    'field'    => 'material_id',
                    'messages' => [
                        'noRecordFound' => 'The selected material was not found in the database.',
                    ],
                ]],
            ],
        ], 'material_id');

        $this->add([
            'required'   => true,
            'filters'    => [
                ['name' => 'ToInt'],
            ],
            'validators' => [
                ['name' => 'NotEmpty', 'break_chain_on_failure' => true, 'options' => [
                    'messages' => [
                        'isEmpty' => 'Please select provider.',
                    ],
                ]],
                ['name' => 'DbRecordExists', 'break_chain_on_failure' => true, 'options' => [
                    'adapter'  => GlobalAdapterFeature::getStaticAdapter(),
                    'table'    => DatabaseContractorAbstract::TABLE_CONTRACTORS,
                    'field'    => 'contractor_id',
                    'exclude'  => sprintf('contractor_type = %s', GlobalAdapterFeature::getStaticAdapter()->platform->quoteValue(ContractorAbstract::TYPE_PROVIDER)),
                    'messages' => [
                        'noRecordFound' => 'The selected provider was not found in the database.',
                    ],
                ]],
            ],
        ], 'provider_id');

        $this->add([
            'required'   => true,
            'filters'    => [
                ['name' => 'StripTags'],
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                ['name' => 'NotEmpty', 'break_chain_on_failure' => true, 'options' => [
                    'messages' => [
                        'isEmpty' => 'Please select the type of carrier.',
                    ],
                ]],
                ['name' => 'InArray', 'break_chain_on_failure' => true, 'options' => [
                    'haystack' => [
                        CarrierEntity::TYPE_TRAIN,
                        CarrierEntity::TYPE_TRUCK,
                    ],
                    'messages' => [
                        'notInArray' => 'Please select the correct type of carrier.',
                    ],
                ]],
            ],
        ], 'carrier_type');

        $this->add([
            'required'   => true,
            'filters'    => [
                ['name' => 'ToInt'],
            ],
            'validators' => [
                ['name' => 'NotEmpty', 'break_chain_on_failure' => true, 'options' => [
                    'messages' => [
                        'isEmpty' => 'Please select station.',
                    ],
                ]],
                ['name' => 'DbRecordExists', 'break_chain_on_failure' => true, 'options' => [
                    'adapter'  => GlobalAdapterFeature::getStaticAdapter(),
                    'table'    => StationDb::TABLE_STATIONS,
                    'field'    => 'station_id',
                    'messages' => [
                        'noRecordFound' => 'The selected station was not found in the database.',
                    ],
                ]],
            ],
        ], 'station_id');

        $this->add([
            'required'   => true,
            'filters'    => [
                ['name' => 'StripTags'],
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                ['name' => 'NotEmpty', 'break_chain_on_failure' => true, 'options' => [
                    'messages' => [
                        'isEmpty' => 'Please select the conditions.',
                    ],
                ]],
                ['name' => 'InArray', 'break_chain_on_failure' => true, 'options' => [
                    'haystack' => [
                        PurchaseContractEntity::CONDITIONS_TYPE_FCA,
                        PurchaseContractEntity::CONDITIONS_TYPE_CPT,
                        PurchaseContractEntity::CONDITIONS_TYPE_CPT_RETURN,
                    ],
                    'messages' => [
                        'notInArray' => 'Please select the correct conditions.',
                    ],
                ]],
            ],
        ], 'conditions');

        $this->add([
            'required'   => true,
            'filters'    => [
                ['name' => 'StripTags'],
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                ['name' => 'NotEmpty', 'break_chain_on_failure' => true, 'options' => [
                    'messages' => [
                        'isEmpty' => 'Please select country.',
                    ],
                ]],
                ['name' => 'DbRecordExists', 'break_chain_on_failure' => true, 'options' => [
                    'adapter'  => GlobalAdapterFeature::getStaticAdapter(),
                    'table'    => CountryDb::TABLE_COUNTRIES,
                    'field'    => 'country_code',
                    'messages' => [
                        'noRecordFound' => 'The selected country was not found in the database.',
                    ],
                ]],
            ],
        ], 'country');

        $this->add([
            'required'   => true,
            'filters'    => [
                ['name' => 'NumberParse'],
            ],
            'validators' => [
                ['name' => 'NotEmpty', 'break_chain_on_failure' => true, 'options' => [
                    'messages' => [
                        'isEmpty' => 'Please enter the contract price without tax.',
                    ],
                ]],
            ],
        ], 'price');

        $this->add([
            'required' => true,
            'filters'  => [
                ['name' => 'NumberParse'],
            ],
        ], 'tax');

        $this->add([
            'required'   => true,
            'filters'    => [
                ['name' => 'StripTags'],
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                ['name' => 'NotEmpty', 'break_chain_on_failure' => true, 'options' => [
                    'messages' => [
                        'isEmpty' => 'Please enter date of contract.',
                    ],
                ]],
                ['name' => 'Date', 'break_chain_on_failure' => true, 'options' => [
                    'format'   => 'd.m.Y',
                    'messages' => [
                        'dateInvalidDate' => 'Please enter a valid date.',
                        'dateFalseFormat' => 'Please enter the date in %format% format.',
                    ],
                ]],
            ],
        ], 'created');


    }

}