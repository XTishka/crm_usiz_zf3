<?php

namespace Transport\Form\Element;

use Document\Service\Rate\AdapterFactory;
use Transport\Domain\RateEntity;
use Zend\Form\Element\Select;

class RateTypeSelect extends Select {

    public function __construct($name = null, array $options = []) {
        parent::__construct($name, $options);
        $options = [
            AdapterFactory::ADAPTER_FIXED => [
                'label'      => 'Fixed rate adapter',
                'value'      => AdapterFactory::ADAPTER_FIXED,
                'attributes' => [
                    'data-min-weight' => 20,
                ],
            ],
            AdapterFactory::ADAPTER_FLOAT => [
                'label'      => 'Float rate adapter',
                'value'      => AdapterFactory::ADAPTER_FLOAT,
                'attributes' => [
                    'data-min-weight' => 20,
                ],
            ],
            AdapterFactory::ADAPTER_MIXED => [
                'label'      => 'Mixed rate adapter',
                'value'      => AdapterFactory::ADAPTER_MIXED,
                'attributes' => [
                    'data-min-weight' => 67,
                ],
            ]
            /*
            RateEntity::TYPE_FIXED_BETWEEN_WEIGHT => 'Fixed rate with between weight and price per wagon',
            RateEntity::TYPE_FIXED_STATIC_WEIGHT  => 'Fixed rate with static weight and price per wagon',
            RateEntity::TYPE_FLOAT_BETWEEN_WEIGHT => 'Float rate with between weight and price per ton'
            */
        ];
        $this->setValueOptions($options);
    }

}