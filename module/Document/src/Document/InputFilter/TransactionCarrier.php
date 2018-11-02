<?php

namespace Document\InputFilter;

use Transport\Service\Repository\CarrierDb;
use Zend\Db\TableGateway\Feature\GlobalAdapterFeature;

class TransactionCarrier extends TransactionAbstract {

    public function init() {

        $this->add([
            'filters'    => [
                ['name' => 'ToInt'],
            ],
            'validators' => [
                ['name' => 'NotEmpty', 'break_chain_on_failure' => true, 'options' => [
                    'messages' => [
                        'isEmpty' => 'Please select contractor.',
                    ],
                ]],
                ['name' => 'DbRecordExists', 'break_chain_on_failure' => true, 'options' => [
                    'adapter'  => GlobalAdapterFeature::getStaticAdapter(),
                    'table'    => CarrierDb::TABLE_CARRIERS,
                    'field'    => 'carrier_id',
                    'messages' => [
                        'noRecordFound' => 'The selected contractor was not found in the database.',
                    ],
                ]],
            ],
        ], 'contractor_id');

        parent::init();
    }

}