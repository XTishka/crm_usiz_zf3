<?php

namespace Contractor\InputFilter;

use Contractor\Service\Repository\DatabaseContractorAbstract;
use Zend\Db\TableGateway\Feature\GlobalAdapterFeature;
use Zend\InputFilter\InputFilter;
use Zend\Validator\Db\NoRecordExists;

class Contractor extends InputFilter {

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
        ], 'contractor_id');

        $this->add([
            'required'   => true,
            'filters'    => [
                ['name' => 'StripTags'],
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                ['name' => 'NotEmpty', 'break_chain_on_failure' => true, 'options' => [
                    'messages' => [
                        'isEmpty' => 'Please enter a contractor name.',
                    ],
                ]],
                ['name' => 'StringLength', 'break_chain_on_failure' => true, 'options' => [
                    'max'      => 255,
                    'messages' => [
                        'stringLengthTooLong' => 'The entered name is too long. The maximum length is %max% characters.',
                    ],
                ]],
            ],
        ], 'contractor_name');

        $this->add([
            'required'   => true,
            'filters'    => [
                ['name' => 'StripTags'],
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                ['name' => 'NotEmpty', 'break_chain_on_failure' => true, 'options' => [
                    'messages' => [
                        'isEmpty' => 'Please enter a full contractor name.',
                    ],
                ]],
                ['name' => 'StringLength', 'break_chain_on_failure' => true, 'options' => [
                    'max'      => 1000,
                    'messages' => [
                        'stringLengthTooLong' => 'The entered name is too long. The maximum length is %max% characters.',
                    ],
                ]],
            ],
        ], 'full_name');

        $this->add([
            'required'   => false,
            'filters'    => [
                ['name' => 'StripTags'],
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                ['name' => 'NotEmpty', 'break_chain_on_failure' => true, 'options' => [
                    'messages' => [
                        'isEmpty' => 'Please enter a contractor description.',
                    ],
                ]],
                ['name' => 'StringLength', 'break_chain_on_failure' => true, 'options' => [
                    'max'      => 5000,
                    'messages' => [
                        'stringLengthTooLong' => 'The entered description is too long. The maximum length is %max% characters.',
                    ],
                ]],
            ],
        ], 'description');

        $this->add([
            'required'   => false,
            'filters'    => [
                ['name' => 'StripTags'],
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                ['name' => 'NotEmpty', 'break_chain_on_failure' => true, 'options' => [
                    'messages' => [
                        'isEmpty' => 'Please enter the contractor registration code.',
                    ],
                ]],
                ['name' => 'Digits', 'break_chain_on_failure' => true, 'options' => [
                    'messages' => [
                        'notDigits' => 'The register code must contain only digits.',
                    ],
                ]],
                ['name' => 'StringLength', 'break_chain_on_failure' => true, 'options' => [
                    'min'      => 7,
                    'max'      => 12,
                    'messages' => [
                        'stringLengthTooShort' => 'The entered code is too short. The minimum length is %min% characters.',
                        'stringLengthTooLong'  => 'The entered code is too long. The maximum length is %max% characters.',
                    ],
                ]],
                ['name' => 'Callback', 'break_chain_on_failure' => true, 'options' => [
                    'callback' => function ($value, $context) {
                        $adapter = GlobalAdapterFeature::getStaticAdapter();
                        $clause = sprintf('contractor_id != %s', $adapter->getPlatform()->quoteValue($context['contractor_id']));
                        $validator = new NoRecordExists([
                            'adapter' => GlobalAdapterFeature::getStaticAdapter(),
                            'table'   => DatabaseContractorAbstract::TABLE_CONTRACTORS,
                            'field'   => 'register_code']);
                        $validator->setExclude($clause);
                        return $validator->isValid($value);
                    },
                    'messages' => [
                        'callbackValue' => 'This register code is already exists'
                    ]
                ]]
            ],
        ], 'register_code');

        $this->add([
            'required'   => false,
            'filters'    => [
                ['name' => 'StripTags'],
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                ['name' => 'NotEmpty', 'break_chain_on_failure' => true, 'options' => [
                    'messages' => [
                        'isEmpty' => 'Please enter the contractor bank account.',
                    ],
                ]],
                ['name' => 'Digits', 'break_chain_on_failure' => true, 'options' => [
                    'messages' => [
                        'notDigits' => 'The bank account must contain only digits.',
                    ],
                ]],
                ['name' => 'StringLength', 'break_chain_on_failure' => true, 'options' => [
                    'max'      => 32,
                    'messages' => [
                        'stringLengthTooLong' => 'The entered bank account is too long. The maximum length is %max% characters.',
                    ],
                ]],
            ],
        ], 'bank_account');

    }

}