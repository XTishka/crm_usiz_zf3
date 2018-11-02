<?php

namespace Transport\InputFilter;

use Transport\Service\Repository\StationDb;
use Zend\Db\TableGateway\Feature\GlobalAdapterFeature;
use Zend\InputFilter\InputFilter;

class Station extends InputFilter {

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
        ], 'station_id');

        $this->add([
            'filters'    => [
                ['name' => 'StripTags'],
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                ['name' => 'NotEmpty', 'break_chain_on_failure' => true, 'options' => [
                    'isEmpty' => 'Please enter the name of the station',
                ]],
                ['name' => 'StringLength', 'break_chain_on_failure' => true, 'options' => [
                    'max'      => 255,
                    'messages' => [
                        'stringLengthTooLong' => 'The entered name is too long. The maximum length is %max% characters.',
                    ],
                ]],
            ],
        ], 'station_name');

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

        $this->add([
            'filters'    => [
                ['name' => 'StripTags'],
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                ['name' => 'NotEmpty', 'break_chain_on_failure' => true, 'options' => [
                    'isEmpty' => 'Please enter the code of the station',
                ]],
                ['name' => 'StringLength', 'break_chain_on_failure' => true, 'options' => [
                    'max'      => 32,
                    'messages' => [
                        'stringLengthTooLong' => 'The entered code is too long. The maximum length is %max% characters.',
                    ],
                ]],
                ['name' => 'AppDbNoRecordExists', 'break_chain_on_failure' => true, 'options' => [
                    'adapter'  => GlobalAdapterFeature::getStaticAdapter(),
                    'table'    => StationDb::TABLE_STATIONS,
                    'field'    => 'station_code',
                    'exclude'  => [
                        'field'      => 'station_id',
                        'form_value' => 'station_id',
                    ],
                    'messages' => [
                        'recordFound' => 'Station with the specified code already exists',
                    ],
                ]],
            ],
        ], 'station_code');

    }

}