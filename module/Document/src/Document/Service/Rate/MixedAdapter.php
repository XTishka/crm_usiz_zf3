<?php

namespace Document\Service\Rate;

class MixedAdapter extends AbstractAdapter {

    /**
     * Минимальный рассчетный вес
     * @var float
     */
    protected $minWeight = 0.0000;

    /**
     * @return float
     */
    public function getMinWeight(): float {
        return $this->minWeight;
    }

    /**
     * @param float $minWeight
     */
    public function setMinWeight(float $minWeight): void {
        $this->minWeight = $minWeight;
    }

    /**
     * Пересчет стоимости перевозки для смешанного тарифа
     * @param float $tax Налоговая ставка в процентах
     * @return float
     */
    public function calculate(float $tax = 0): float {
        $weight = $this->getWeight();
        if ($weight < $this->getMinWeight()) {
            $weight = $this->getMinWeight();
        }

        $price = bcmul($this->getPrice(), $weight, 10);

        if (0 < $tax) {
            $wTax = bcmul($this->getPrice(), bcdiv($tax, 100, 10), 10) + $this->getPrice();
            $price = bcmul($wTax, $weight, 10);
        }

        return round($price, 2);
    }

}