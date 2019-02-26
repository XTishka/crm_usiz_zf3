<?php

namespace Application\InputFilter;

use Zend\InputFilter\InputFilter;

class Tax extends InputFilter {

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
        ], 'tax_id');

        $this->add([
            'filters'    => [
                ['name' => 'StripTags'],
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                ['name' => 'NotEmpty', 'break_chain_on_failure' => true, 'options' => [
                    'messages' => [
                        'isEmpty' => 'Please enter the name of the tax',
                    ],
                ]],
                ['name' => 'StringLength', 'break_chain_on_failure' => true, 'options' => [
                    'max'      => 255,
                    'messages' => [
                        'stringLengthTooLong' => 'The entered tax name is too long. The maximum length is %max% characters.',
                    ],
                ]],
            ],
        ], 'tax_name');

        $this->add([
            'filters'    => [
                ['name' => 'StripTags'],
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                ['name' => 'NotEmpty', 'break_chain_on_failure' => true, 'options' => [
                    'messages' => [
                        'isEmpty' => 'Please enter the description of the tax',
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
            'filters'    => [
                ['name' => 'ToInt'],
            ],
            'validators' => [
                ['name' => 'NotEmpty', 'break_chain_on_failure' => true, 'options' => [
                    'messages' => [
                        'isEmpty' => 'Please enter the value of the tax',
                    ],
                ]],
                ['name' => 'GreaterThan', 'break_chain_on_failure' => true, 'options' => [
                    'inclusive' => true,
                    'min'       => 0,
                    'messages'  => [
                        'notGreaterThan' => 'The tax value must be greater than or equal to %min%',
                    ],
                ]],
            ],
        ], 'value');

    }

}