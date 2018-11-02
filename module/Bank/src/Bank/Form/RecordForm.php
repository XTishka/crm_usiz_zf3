<?php

namespace Bank\Form;

use Bank\Service\BankManager;
use Contractor\Entity\ContractorAbstract;
use Contractor\Service\Repository\DatabaseContractorCompany;
use Zend\Db\TableGateway\Feature\GlobalAdapterFeature;
use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;

class RecordForm extends Form implements InputFilterProviderInterface {

    public function init() {

        $this->add([
            'type'    => 'csrf',
            'name'    => 'csrf',
            'options' => [
                'csrf_options' => [
                    'timeout' => 3600,
                ],
            ],
        ]);

        $this->add([
            'type' => 'hidden',
            'name' => 'record_id',
        ]);

        $this->add([
            'type' => 'hidden',
            'name' => 'company_id',
        ]);

        $this->add([
            'type'    => Element\BankSelectElement::class,
            'name'    => 'bank_id',
            'options' => [
                'label' => 'Bank',
            ],
        ]);

        $this->add([
            'type'       => 'text',
            'name'       => 'date',
            'attributes' => [
                //'class' => 'datepicker',
            ],
            'options'    => [
                'label' => 'Transaction date',
            ],
        ]);

        $this->add([
            'type'       => 'number',
            'name'       => 'amount',
            'attributes' => [
                'min'  => 0,
                'step' => 0.01,
            ],
            'options'    => [
                'label' => 'Transaction amount',
            ],
        ]);

        $this->add([
            'type'       => 'submit',
            'name'       => 'save',
            'attributes' => [
                'class' => 'btn btn-success',
                'value' => 'Save changes',
            ],
        ]);

    }

    public function getInputFilterSpecification() {
        return [
            'csrf'       => [
                'required'   => true,
                'filters'    => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    ['name' => 'NotEmpty', 'break_chain_on_failure' => true, 'options' => [
                        'messages' => [
                            'isEmpty' => 'CSRF is empty',
                        ],
                    ]],
                    ['name' => 'Csrf', 'break_chain_on_failure' => true],
                ],
            ],
            'record_id'  => [
                'filters' => [
                    ['name' => 'ToInt'],
                ],
            ],
            'bank_id'    => [
                'filters'    => [
                    ['name' => 'ToInt'],
                ],
                'validators' => [
                    ['name' => 'NotEmpty', 'break_chain_on_failure' => true, 'options' => [
                        'messages' => [
                            'isEmpty' => 'The bank must be selected',
                        ],
                    ]],
                    ['name' => 'DbRecordExists', 'break_chain_on_failure' => true, 'options' => [
                        'adapter'  => GlobalAdapterFeature::getStaticAdapter(),
                        'table'    => BankManager::TABLE_BANKS,
                        'field'    => 'bank_id',
                        'messages' => [
                            'noRecordFound' => 'The selected bank was not found in the database.',
                        ],
                    ]],
                ],
            ],
            'company_id' => [
                'filters'    => [
                    ['name' => 'ToInt'],
                ],
                'validators' => [
                    ['name' => 'NotEmpty', 'break_chain_on_failure' => true, 'options' => [
                        'messages' => [
                            'isEmpty' => 'The company must be selected',
                        ],
                    ]],
                    ['name' => 'DbRecordExists', 'break_chain_on_failure' => true, 'options' => [
                        'adapter'  => GlobalAdapterFeature::getStaticAdapter(),
                        'table'    => DatabaseContractorCompany::TABLE_CONTRACTORS,
                        'field'    => 'contractor_id',
                        'exclude'  => sprintf('contractor_type = %s OR contractor_type = %s',
                            GlobalAdapterFeature::getStaticAdapter()->platform->quoteValue(ContractorAbstract::TYPE_COMPANY),
                            GlobalAdapterFeature::getStaticAdapter()->platform->quoteValue(ContractorAbstract::TYPE_PLANT)
                        ),
                        'messages' => [
                            'noRecordFound' => 'The selected company was not found in the database.',
                        ],
                    ]],
                ],
            ],
            'date'       => [
                'required'   => true,
                'filters'    => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    ['name' => 'NotEmpty', 'break_chain_on_failure' => true, 'options' => [
                        'messages' => [
                            'isEmpty' => 'Enter transaction date',
                        ],
                    ]],
                    ['name' => 'Date', 'break_chain_on_failure' => true, 'options' => [
                        'format'   => 'd.m.Y',
                        'messages' => [
                            'dateInvalid'     => 'Invalid type given. String, integer, array or DateTime expected',
                            'dateInvalidDate' => 'The input does not appear to be a valid date',
                            'dateFalseFormat' => 'The input does not fit the date format "%format%"',
                        ],
                    ]],
                ],
            ],
            'amount'     => [
                'required'   => true,
                'filters'    => [
                    ['name' => 'NumberParse'],
                ],
                'validators' => [
                    ['name' => 'NotEmpty', 'break_chain_on_failure' => true, 'options' => [
                        'messages' => [
                            'isEmpty' => 'Enter transaction amount',
                        ],
                    ]],
                ],
            ],
        ];
    }

}