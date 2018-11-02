<?php

namespace Transport\Domain;

class RateValueEntity {

    protected $valueId = 0;

    /**
     * Вес загрузки вагона, может быть указан как одним числом так и множеством,
     * В зависимости от выбранного типа ставки.
     * @var string
     */
    protected $weight = '';

    /**
     * Стоимость перевозки. Указывается цена за тонну или за вогон соответственно от выбранного типа ставки.
     * @var double
     */
    protected $price = 0;

    /**
     * @return int
     */
    public function getValueId(): int {
        return $this->valueId;
    }

    /**
     * @param int $valueId
     */
    public function setValueId(int $valueId): void {
        $this->valueId = $valueId;
    }

    /**
     * Возвращает вес загрузки вагона в зависимости от типа тарифной ставки
     * @return string
     */
    public function getWeight(): string {
        return $this->weight;
    }

    /**
     * Устанавливает вес загрузки вагона в зависимости от типа тарифной ставки
     * @param string $weight
     */
    public function setWeight(string $weight) {
        $this->weight = $weight;
    }

    /**
     * Возвращает стоимость перевозки в зависимости от типа тарифной ставки
     * @return float
     */
    public function getPrice(): float {
        return $this->price;
    }

    /**
     * Устанавливает стоимость перевозки в зависимости от типа тарифной ставки
     * @param float $price
     */
    public function setPrice(float $price) {
        $this->price = $price;
    }

}