<?php

namespace Transport\InputFilter;

use Zend\InputFilter\InputFilter;

class Carrier extends InputFilter {

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
        ], 'carrier_id');

        $this->add([
            'filters'    => [
                ['name' => 'StripTags'],
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                ['name' => 'NotEmpty', 'break_chain_on_failure' => true],
                ['name' => 'StringLength', 'break_chain_on_failure' => true, 'options' => [
                    'max'      => 255,
                    'messages' => [
                        'stringLengthTooLong' => 'The entered name is too long. The maximum length is %max% characters.',
                    ],
                ]],
            ],
        ], 'carrier_name');

        $this->add([
            'required'   => false,
            'filters'    => [
                ['name' => 'StripTags'],
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                ['name' => 'NotEmpty', 'break_chain_on_failure' => true],
                ['name' => 'StringLength', 'break_chain_on_failure' => true, 'options' => [
                    'max'      => 5000,
                    'messages' => [
                        'stringLengthTooLong' => 'The entered description is too long. The maximum length is %max% characters.',
                    ],
                ]],
            ],
        ], 'description');

    }

}