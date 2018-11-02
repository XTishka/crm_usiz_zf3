<?php

namespace Application\Hydrator;

use Application\Domain\ValueObjectInterface;
use Application\Exception\InvalidArgumentException;
use Zend\Hydrator\ClassMethods;
use Zend\Hydrator\HydratorOptionsInterface;

class ValueObject extends ClassMethods implements HydratorOptionsInterface {

    public function hydrate(array $data, $object) {
        if (!$object instanceof ValueObjectInterface) {
            throw new InvalidArgumentException(sprintf('%s expects the provided $object', __METHOD__));
        }
        return $object::factory($data);
    }

}