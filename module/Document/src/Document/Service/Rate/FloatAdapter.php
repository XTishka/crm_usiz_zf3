<?php

namespace Document\Service\Rate;

class FloatAdapter extends AbstractAdapter {

    /**
     * Пересчет стоимости перевозки для плавающего тарифа
     * @param float $tax
     * @return float
     */
    public function calculate(float $tax = 0): float {
        $price = $this->getPrice() * $this->getWeight();
        if (0 < $tax)
            $price += $price * $tax / 100;
        return $price;
    }


}