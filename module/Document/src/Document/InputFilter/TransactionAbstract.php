<?php

namespace Document\InputFilter;

use Contractor\Entity\ContractorCompany;
use Contractor\Service\Repository\DatabaseContractorCompany;
use Document\Domain\TransactionEntity;
use Zend\Db\TableGateway\Feature\GlobalAdapterFeature;
use Zend\InputFilter\InputFilter;

abstract class TransactionAbstract extends InputFilter {

    public function init() {

        $this->add([
            'filters' => [
                ['name' => 'ToInt'],
            ],
        ], 'transaction_id');

        $this->add([
            'filters' => [
                ['name' => 'ToInt'],
            ],
        ], 'is_manual');

        $this->add([
            'filters'    => [
                ['name' => 'ToInt'],
            ],
            'validators' => [
                ['name' => 'NotEmpty', 'break_chain_on_failure' => true, 'options' => [
                    'messages' => [
                        'isEmpty' => 'Please select company.',
                    ],
                ]],
                ['name' => 'DbRecordExists', 'break_chain_on_failure' => true, 'options' => [
                    'adapter'  => GlobalAdapterFeature::getStaticAdapter(),
                    'table'    => DatabaseContractorCompany::TABLE_CONTRACTORS,
                    'field'    => 'contractor_id',
                    'exclude'  => sprintf('contractor_type = %s OR contractor_type = %s',
                        GlobalAdapterFeature::getStaticAdapter()->platform->quoteValue(ContractorCompany::TYPE_PLANT),
                        GlobalAdapterFeature::getStaticAdapter()->platform->quoteValue(ContractorCompany::TYPE_COMPANY)),
                    'messages' => [
                        'noRecordFound' => 'The selected company was not found in the database.',
                    ],
                ]],
            ],
        ], 'company_id');

        $this->add([
            'required'   => true,
            'filters'    => [
                ['name' => 'StripTags'],
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                ['name' => 'NotEmpty', 'break_chain_on_failure' => true, 'options' => [
                    'messages' => [
                        'isEmpty' => 'Please enter date of transaction.',
                    ],
                ]],
                ['name' => 'Date', 'break_chain_on_failure' => true, 'options' => [
                    'format'   => 'd.m.Y H:i:s',
                    'messages' => [
                        'dateInvalidDate' => 'Please enter a valid date.',
                        'dateFalseFormat' => 'Please enter the date in %format% format.',
                    ],
                ]],
            ],
        ], 'created');

        $this->add([
            'filters' => [
                ['name' => 'StripTags'],
                ['name' => 'StringTrim'],
            ],
        ], 'direction');

        $this->add([
            'required'   => false,
            'filters'    => [
                ['name' => 'NumberParse'],
            ],
            'validators' => [
                ['name' => 'Callback', 'break_chain_on_failure' => true, 'options' => [
                    'callback' => function ($value, $context) {
                        if ('credit' == $context['direction']) {
                            $context['debit'] = 0;
                            return 0 < $value;
                        }
                        return true;
                    },
                    'messages' => [
                        'callbackValue' => 'Please enter credit amount of transaction.',
                    ],
                ]],
            ],
        ], 'credit');

        $this->add([
            'required'   => false,
            'filters'    => [
                ['name' => 'NumberParse'],
            ],
            'validators' => [
                ['name' => 'Callback', 'break_chain_on_failure' => true, 'options' => [
                    'callback' => function ($value, $context) {
                        if ('debit' == $context['direction']) {
                            $context['credit'] = 0;
                            return 0 < $value;
                        }
                        return true;
                    },
                    'messages' => [
                        'callbackValue' => 'Please enter debit amount of transaction.',
                    ],
                ]],
            ],
        ], 'debit');

        $this->add([
            'filters'    => [
                ['name' => 'StripTags'],
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                ['name' => 'NotEmpty', 'break_chain_on_failure' => true, 'options' => [
                    'messages' => [
                        'isEmpty' => 'Please select transaction type.',
                    ],
                ]],
                ['name' => 'InArray', 'break_chain_on_failure' => true, 'options' => [
                    'haystack' => [TransactionEntity::TRANSACTION_DEBT, TransactionEntity::TRANSACTION_PAYMENT],
                    'messages' => [
                        'notInArray' => 'Invalid transaction type.',
                    ],
                ]],
            ],
        ], 'transaction_type');
    }

}