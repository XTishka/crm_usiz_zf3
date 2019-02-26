<?php

namespace Transport\InputFilter;

use Document\Service\Rate\AdapterFactory;
use Transport\Domain;
use Zend\InputFilter\InputFilter;

class Rate extends InputFilter {

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
        ], 'rate_id');

        $this->add([
            'filters'    => [
                ['name' => 'ToInt'],
            ],
            'validators' => [
                ['name' => 'NotEmpty', 'break_chain_on_failure' => true, 'options' => [
                    'messages' => [
                        'isEmpty' => 'Please select the plant.',
                    ],
                ]],
            ],
        ], 'plant_id');

        $this->add([
            'filters'    => [
                ['name' => 'ToInt'],
            ],
            'validators' => [
                ['name' => 'NotEmpty', 'break_chain_on_failure' => true, 'options' => [
                    'messages' => [
                        'isEmpty' => 'Please select the company.',
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
                        'isEmpty' => 'Please select the station.',
                    ],
                ]],
            ],
        ], 'station_id');

        $this->add([
            'filters'    => [
                ['name' => 'ToInt'],
            ],
            'validators' => [
                ['name' => 'NotEmpty', 'break_chain_on_failure' => true, 'options' => [
                    'messages' => [
                        'isEmpty' => 'Please select the carrier.',
                    ],
                ]],
            ],
        ], 'carrier_id');

        $this->add([
            'filters'    => [
                ['name' => 'StripTags'],
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                ['name' => 'NotEmpty', 'break_chain_on_failure' => true, 'options' => [
                    'messages' => [
                        'isEmpty' => 'Please select the type of rate.',
                    ],
                ]],
                ['name' => 'InArray', 'break_chain_on_failure' => true, 'options' => [
                    'haystack' => [
                        AdapterFactory::ADAPTER_FIXED,
                        AdapterFactory::ADAPTER_FLOAT,
                        AdapterFactory::ADAPTER_MIXED,
                    ],
                    'messages' => [
                        'notInArray' => 'Please select a valid type of rate.',
                    ],
                ]],
            ],
        ], 'rate_type');

        $this->add([
            'required'   => false,
            'filters'    => [
                ['name' => 'Callback', 'options' => [
                    'callback' => 'floatval',
                ]],
            ],
            'validators' => [
                ['name' => 'NotEmpty', 'break_chain_on_failure' => true, 'options' => [
                    'messages' => [
                        'isEmpty' => 'Please select the min weight of rate.',
                    ],
                ]],
            ],
        ], 'min_weight');

    }

}