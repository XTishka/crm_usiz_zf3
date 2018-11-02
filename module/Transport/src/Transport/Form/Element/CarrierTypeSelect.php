<?php

namespace Transport\Form\Element;

use Transport\Domain\CarrierEntity;
use Zend\Form\Element\Select;

class CarrierTypeSelect extends Select {

    public function __construct($name = null, array $options = []) {
        parent::__construct($name, $options);
        $options = [CarrierEntity::TYPE_TRAIN => 'Railway transport', CarrierEntity::TYPE_TRUCK => 'Motor transport'];
        $this->setValueOptions($options);
    }

}