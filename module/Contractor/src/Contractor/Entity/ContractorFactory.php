<?php

namespace Contractor\Entity;

use Contractor\Exception\InvalidArgumentException;

class ContractorFactory {

    public static function build($type) {
        $contractor = sprintf('Contractor\Entity\Contractor%s', ucfirst(preg_replace('/^Contractor|contractor/', '', $type)));
        if (!class_exists($contractor) || !is_subclass_of($contractor, ContractorAbstract::class))
            throw new InvalidArgumentException('Invalid contractor type was invoked.');
        return new $contractor();
    }

}