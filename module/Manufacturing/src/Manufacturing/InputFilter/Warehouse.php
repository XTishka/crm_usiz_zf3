<?php

namespace Manufacturing\InputFilter;

use Manufacturing\Domain\WarehouseEntity;
use Zend\InputFilter\InputFilter;

class Warehouse extends InputFilter {

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
        ], 'warehouse_id');

        $this->add([
            'filters' => [
                ['name' => 'ToInt'],
            ],
        ], 'plant_id');

        $this->add([
            'filters'    => [
                ['name' => 'StripTags'],
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                ['name' => 'NotEmpty', 'break_chain_on_failure' => true, 'options' => [
                    'messages' => [
                        'isEmpty' => 'Please enter the name of warehouse.',
                    ],
                ]],
                ['name' => 'StringLength', 'break_chain_on_failure' => true, 'options' => [
                    'max'      => 255,
                    'messages' => [
                        'stringLengthTooLong' => 'The entered name is too long. The maximum length is %max% characters.',
                    ],
                ]],
            ],
        ], 'warehouse_name');

        $this->add([
            'required'   => false,
            'filters'    => [
                ['name' => 'StripTags'],
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                ['name' => 'NotEmpty', 'break_chain_on_failure' => true, 'options' => [
                    'messages' => [
                        'isEmpty' => 'Please enter the description of warehouse.',
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
            'required'   => true,
            'filters'    => [
                ['name' => 'StripTags'],
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                ['name' => 'NotEmpty', 'break_chain_on_failure' => true, 'options' => [
                    'messages' => [
                        'isEmpty' => 'Please select the type of warehouse.',
                    ],
                ]],
                ['name' => 'InArray', 'break_chain_on_failure' => true, 'options' => [
                    'haystack' => [
                        WarehouseEntity::TYPE_MATERIAL_WAREHOUSE,
                        WarehouseEntity::TYPE_PRODUCT_WAREHOUSE,
                    ],
                    'messages' => [
                        'notInArray' => 'Please choose the correct type of warehouse.',
                    ],
                ]],
            ],
        ], 'warehouse_type');

    }

}