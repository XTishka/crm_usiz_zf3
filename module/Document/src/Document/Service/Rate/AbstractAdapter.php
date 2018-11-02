<?php

namespace Document\Service\Rate;

abstract class AbstractAdapter implements AdapterInterface {

    /**
     * Стоимость транспортировки груза
     * @var float
     */
    protected $price = 0.0000;

    /**
     * Указанный вес загрузки
     * @var float
     */
    protected $weight = 0.0000;

    /**
     * AbstractAdapter constructor.
     * @param float      $price
     * @param float|null $weight
     */
    public function __construct(float $price, float $weight = null) {
        $this->price = floatval($price);
        $this->weight = floatval($weight);
    }

    /**
     * @return float
     */
    public function getPrice(): float {
        return $this->price;
    }

    /**
     * @return float
     */
    public function getWeight(): float {
        return $this->weight;
    }

}