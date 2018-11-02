<?php

namespace Document\Service\Rate;

class FixedAdapter extends AbstractAdapter {

    /**
     * Пересчет стоимости перевозки для фиксированного тарифа
     * @param float $tax Налоговая ставка в процентах
     * @return float
     */
    public function calculate(float $tax = 0): float {
        $price = $this->getPrice();
        if (0 < $tax)
            $price += $price * $tax / 100;
        return $price;
    }


}