<?php

namespace Document\Service\Rate;

use Document\Exception\TypeErrorException;

class AdapterFactory {

    const ADAPTER_FIXED = 'fixed';
    const ADAPTER_FLOAT = 'float';
    const ADAPTER_MIXED = 'mixed';

    protected static $adapters = [
        self::ADAPTER_FIXED => FixedAdapter::class,
        self::ADAPTER_FLOAT => FloatAdapter::class,
        self::ADAPTER_MIXED => MixedAdapter::class,
    ];

    /**
     * @param       $type
     * @param float $price
     * @param float $weight
     * @return AbstractAdapter
     */
    public static function create($type, float $price, float $weight = 0.0000) {
        $type = strtolower($type);
        if (!key_exists($type, self::$adapters))
            throw new TypeErrorException(sprintf('Adapter type "%s" not defined', $type));
        /** @var AbstractAdapter $adapter */
        $adapter = new self::$adapters[$type]($price, $weight);
        return $adapter;
    }

}