<?php

namespace Manufacturing\InputFilter;

use Application\Validator\Db\NoRecordExists;
use Manufacturing\Service\FurnaceLogRepository;
use Zend\Db\TableGateway\Feature\GlobalAdapterFeature;
use Zend\InputFilter\InputFilter;

class FurnaceSkip extends InputFilter {

    public function init() {

        $this->add([
            'filters'    => [
                ['name' => 'StripTags'],
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                ['name' => 'NotEmpty', 'break_chain_on_failure' => true],
                ['name' => 'Csrf', 'break_chain_on_failure' => true],
            ]
        ], 'csrf');

        $this->add([
            'filters' => [
                ['name' => 'ToInt']
            ]
        ], 'skip_id');

        $this->add([
            'filters' => [
                ['name' => 'ToInt']
            ]
        ], 'material_warehouse_id');

        $this->add([
            'filters' => [
                ['name' => 'ToInt']
            ]
        ], 'product_warehouse_id');

        $this->add([
            'filters' => [
                ['name' => 'ToInt']
            ]
        ], 'furnace_id');

        $this->add([
            'filters'    => [
                ['name' => 'StripTags'],
                ['name' => 'StringTrim']
            ],
            'validators' => [
                ['name' => 'NotEmpty', 'break_chain_on_failure' => true, 'options' => [
                    'messages' => [
                        'isEmpty' => 'Please enter the date of loading the furnace'
                    ]
                ]],
                /*
                ['name' => 'callback', 'break_chain_on_failure' => true, 'options' => [
                    'callback' => function ($value, $context) {
                        $adapter = GlobalAdapterFeature::getStaticAdapter();
                        $clause = sprintf('furnace_id = %s', $adapter->getPlatform()->quoteValue($context['furnace_id']));
                        $validator = new NoRecordExists([
                            'adapter' => GlobalAdapterFeature::getStaticAdapter(),
                            'table'   => FurnaceLogRepository::TABLE_LOGS,
                            'field'   => 'date']);
                        $validator->setExclude($clause);
                        return $validator->isValid($value);
                    },
                    'messages' => [
                        'callbackValue' => 'This furnace is already loaded with the state for this date'
                    ]
                ]],
                */
            ],
        ], 'date');

    }

}