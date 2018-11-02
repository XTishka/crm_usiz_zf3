<?php

namespace Application\Hydrator\Strategy;

use Zend\Hydrator\Strategy\StrategyInterface;

class IntegerStrategy implements StrategyInterface {

    public function extract($value) {
        return intval($value);
    }

    public function hydrate($value) {
        return intval($value);
    }

}