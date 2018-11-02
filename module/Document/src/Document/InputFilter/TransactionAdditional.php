<?php

namespace Document\InputFilter;

use Contractor\Entity\ContractorAbstract;
use Contractor\Service\Repository\DatabaseContractorAbstract;
use Zend\Db\TableGateway\Feature\GlobalAdapterFeature;

class TransactionAdditional extends TransactionAbstract {

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
                    'table'    => DatabaseContractorAbstract::TABLE_CONTRACTORS,
                    'field'    => 'contractor_id',
                    'exclude'  => sprintf('contractor_type = %s', GlobalAdapterFeature::getStaticAdapter()->platform->quoteValue(ContractorAbstract::TYPE_ADDITIONAL)),
                    'messages' => [
                        'noRecordFound' => 'The selected contractor was not found in the database.',
                    ],
                ]],
            ],
        ], 'contractor_id');

        parent::init();
    }

}