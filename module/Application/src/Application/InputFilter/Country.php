<?php

namespace Application\InputFilter;

use Application\Service\Repository\CountryDb;
use Zend\Db\TableGateway\Feature\GlobalAdapterFeature;
use Zend\InputFilter\InputFilter;

class Country extends InputFilter {

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
        ], 'country_id');

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
        ], 'country_name');

        $this->add([
            'filters'    => [
                ['name' => 'StripTags'],
                ['name' => 'StringTrim'],
                ['name' => 'StringToLower'],
            ],
            'validators' => [
                ['name' => 'NotEmpty', 'break_chain_on_failure' => true],
                ['name' => 'StringLength', 'break_chain_on_failure' => true, 'options' => [
                    'min'      => 2,
                    'max'      => 2,
                    'messages' => [
                        'stringLengthTooShort' => 'The entered code is too short. The minimum length is %max% characters.',
                        'stringLengthTooLong'  => 'The entered code is too long. The maximum length is %max% characters.',
                    ],
                ]],
                ['name' => 'Regex', 'break_chain_on_failure' => true, 'options' => [
                    'pattern'  => '/^[a-z]{2}$/',
                    'messages' => [
                        'regexNotMatch' => 'Please enter the correct country code value. Only letters of the Latin alphabet are allowed.',
                    ],
                ]],
                ['name' => 'AppDbNoRecordExists', 'break_chain_on_failure' => true, 'options' => [
                    'adapter'  => GlobalAdapterFeature::getStaticAdapter(),
                    'table'    => CountryDb::TABLE_COUNTRIES,
                    'field'    => 'country_code',
                    'exclude'  => [
                        'field'      => 'country_id',
                        'form_value' => 'country_id',
                    ],
                    'messages' => [
                        'recordFound' => 'Country with this country code already exists.',
                    ],
                ]],
            ],
        ], 'country_code');

        $this->add([
            'filters'    => [
                ['name' => 'StripTags'],
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                ['name' => 'NotEmpty', 'break_chain_on_failure' => true],
                ['name' => 'StringLength', 'break_chain_on_failure' => true, 'options' => [
                    'min'      => 5,
                    'max'      => 5,
                    'messages' => [
                        'stringLengthTooShort' => 'The entered locale is too short. The minimum length is %max% characters.',
                        'stringLengthTooLong'  => 'The entered locale is too long. The maximum length is %max% characters.',
                    ],
                ]],
                ['name' => 'Regex', 'break_chain_on_failure' => true, 'options' => [
                    'pattern'  => '/^[a-z]{2}_[A-Z]{2}$/',
                    'messages' => [
                        'regexNotMatch' => 'Please enter the correct locale value. Only letters of the Latin alphabet and underlining are allowed.',
                    ],
                ]],
                ['name' => 'AppDbNoRecordExists', 'break_chain_on_failure' => true, 'options' => [
                    'adapter'  => GlobalAdapterFeature::getStaticAdapter(),
                    'table'    => CountryDb::TABLE_COUNTRIES,
                    'field'    => 'locale',
                    'exclude'  => [
                        'field'      => 'country_id',
                        'form_value' => 'country_id',
                    ],
                    'messages' => [
                        'recordFound' => 'Country with this locale already exists.',
                    ],
                ]],
            ],
        ], 'locale');

    }

}