<?php

namespace Application\Hydrator\Strategy;

use Zend\Hydrator\Strategy\StrategyInterface;

class StringTrimStrategy implements StrategyInterface {

    public function extract($value) {
        return trim($value);
    }

    public function hydrate($value) {
        return trim($value);
    }

}