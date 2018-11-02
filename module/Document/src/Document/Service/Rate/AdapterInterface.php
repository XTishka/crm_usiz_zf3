<?php

namespace Document\Service\Rate;

interface AdapterInterface {

    /**
     * AdapterInterface constructor.
     * @param float      $price
     * @param float|null $weight
     */
    public function __construct(float $price, float $weight = null);

    /**
     * Подсчитывает сумму относительно указанного адаптера
     * @param float $tax Налоговая ставка в процентах
     * @return float
     */
    public function calculate(float $tax = 0): float;

}