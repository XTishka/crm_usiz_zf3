<?php

namespace Document\InputFilter;

use Contractor\Entity\ContractorAbstract;
use Contractor\Service\Repository\DatabaseContractorAbstract;
use Zend\Db\TableGateway\Feature\GlobalAdapterFeature;

class TransactionCompany extends TransactionAbstract {

    public function init() {

        parent::init();

        $this->remove('company_id');

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
                    'exclude'  => sprintf('contractor_type = %s', GlobalAdapterFeature::getStaticAdapter()->platform->quoteValue(ContractorAbstract::TYPE_PLANT)),
                    'messages' => [
                        'noRecordFound' => 'The selected contractor was not found in the database.',
                    ],
                ]],
            ],
        ], 'company_id');

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
                    'exclude'  => sprintf('contractor_type = %s', GlobalAdapterFeature::getStaticAdapter()->platform->quoteValue(ContractorAbstract::TYPE_COMPANY)),
                    'messages' => [
                        'noRecordFound' => 'The selected contractor was not found in the database.',
                    ],
                ]],
            ],
        ], 'contractor_id');
    }

}