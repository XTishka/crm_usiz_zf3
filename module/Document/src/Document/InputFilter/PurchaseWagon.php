<?php

namespace Document\InputFilter;

use Transport\Service\Repository\CarrierDb;
use Transport\Service\Repository\RateDb;
use Zend\Db\TableGateway\Feature\GlobalAdapterFeature;
use Zend\InputFilter\InputFilter;

class PurchaseWagon extends InputFilter {

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
        ], 'wagon_id');

        $this->add([
            'required'   => false,
            'filters'    => [
                ['name' => 'ToInt'],
            ],
            'validators' => [
                ['name' => 'NotEmpty', 'break_chain_on_failure' => true, 'options' => [
                    'messages' => [
                        'isEmpty' => 'Please select carrier.',
                    ],
                ]],
                ['name' => 'DbRecordExists', 'break_chain_on_failure' => true, 'options' => [
                    'adapter'  => GlobalAdapterFeature::getStaticAdapter(),
                    'table'    => CarrierDb::TABLE_CARRIERS,
                    'field'    => 'carrier_id',
                    'messages' => [
                        'noRecordFound' => 'The selected carrier was not found in the database.',
                    ],
                ]],
            ],
        ], 'carrier_id');

        $this->add([
            'required'   => false,
            'filters'    => [
                ['name' => 'ToInt'],
            ],
            'validators' => [
                ['name' => 'NotEmpty', 'break_chain_on_failure' => true, 'options' => [
                    'messages' => [
                        'isEmpty' => 'Please select transport rate.',
                    ],
                ]],
                ['name' => 'DbRecordExists', 'break_chain_on_failure' => true, 'options' => [
                    'adapter'  => GlobalAdapterFeature::getStaticAdapter(),
                    'table'    => RateDb::TABLE_RATES,
                    'field'    => 'rate_id',
                    'messages' => [
                        'noRecordFound' => 'The selected transport rate was not found in the database.',
                    ],
                ]],
            ],
        ], 'rate_id');

        $this->add([
            'required'   => false,
            'filters'    => [
                ['name' => 'ToInt'],
            ],
        ], 'rate_value_id');

        $this->add([
            'required'   => true,
            'filters'    => [
                ['name' => 'StripTags'],
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                ['name' => 'NotEmpty', 'break_chain_on_failure' => true, 'options' => [
                    'messages' => [
                        'isEmpty' => 'Please enter date of loading.',
                    ],
                ]],
                ['name' => 'Date', 'break_chain_on_failure' => true, 'options' => [
                    'format'   => 'd.m.Y',
                    'messages' => [
                        'dateInvalidDate' => 'Please enter a valid date.',
                        'dateFalseFormat' => 'Please enter the date in %format% format.',
                    ],
                ]],
            ],
        ], 'loading_date');

        $this->add([
            'required' => false,
            'filters'  => [
                ['name' => 'ToInt'],
            ],
        ], 'transport_contractor_id');

        $this->add([
            'required' => false,
            'filters'  => [
                ['name' => 'NumberParse'],
            ],
        ], 'transport_price');

    }

}