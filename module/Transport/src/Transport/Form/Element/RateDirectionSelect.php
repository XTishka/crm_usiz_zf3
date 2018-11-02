<?php

namespace Transport\Form\Element;

use Transport\Domain\RateEntity;
use Zend\Form\Element\Select;

class RateDirectionSelect extends Select {

    public function __construct($name = null, array $options = []) {
        parent::__construct($name, $options);
        $options = [
            RateEntity::DIRECTION_INBOUND  => 'Inbound direction',
            RateEntity::DIRECTION_OUTGOING => 'Outgoing direction',
        ];
        $this->setValueOptions($options);
    }

}