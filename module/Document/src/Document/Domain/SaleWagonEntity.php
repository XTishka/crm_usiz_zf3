<?php

namespace Document\Domain;

use DateTime;

class SaleWagonEntity {

    /**
     * Текущее состояние вагона
     */
    const STATUS_LOADED   = 'loaded';
    const STATUS_UNLOADED = 'unloaded';

    /**
     * Идентифиактор вагона в базе данных
     * @var int
     */
    protected $wagonId = 0;

    /**
     * @var array
     */
    protected $wagons = [];

    /**
     * Идентификатор договора к которому добавляется вагон
     * @var int
     */
    protected $contractId = 0;

    /**
     * Состояние вагона
     * @var string
     */
    protected $status = '';

    /**
     * Идентификатор перевозчика
     * @var int
     */
    protected $carrierId = 0;

    /**
     * Название перевозчика
     * @var string
     */
    protected $carrierName = '';

    /**
     * Идентификатор тарифа
     * @var int
     */
    protected $rateId = 0;

    /**
     * Идентификатор значения тарифа из сетки тарифов
     * @var int
     */
    protected $rateValueId = 0;

    /**
     * Номер вагона
     * @var string
     */
    protected $wagonNumber = '';

    /**
     * Стоимость товара которое
     * @var float
     */
    protected $productPrice = 0.00;

    /**
     * Стоимость перевозки за тонну
     * @var float
     */
    protected $transportPrice = 0.00;

    /**
     * Вес при загрузке вагона
     * @var float
     */
    protected $loadingWeight = 0.000;

    /**
     * Дата загрузки вагнона
     * @var DateTime
     */
    protected $loadingDate;


    /**
     * Возвращает идентификатор вагона в базе данных
     * @return int
     */
    public function getWagonId(): int {
        return $this->wagonId;
    }

    /**
     * Устанавливает идентификатор вагона в базе данных
     * @param int $wagonId
     */
    public function setWagonId(int $wagonId) {
        $this->wagonId = $wagonId;
    }

    /**
     * @return array|null
     */
    public function getWagons() {
        return $this->wagons;
    }

    /**
     * @param array|null $wagons
     */
    public function setWagons($wagons) {
        $this->wagons = $wagons;
    }

    /**
     * Возвращает идентификатор договора
     * @return int
     */
    public function getContractId(): int {
        return $this->contractId;
    }

    /**
     * Устанавливает идентификатор договора
     * @param int $contractId
     */
    public function setContractId(int $contractId) {
        $this->contractId = $contractId;
    }

    /**
     * @return string
     */
    public function getStatus(): string {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus(string $status) {
        $this->status = $status;
    }

    /**
     * Возвращает идентификатор перевозчика
     * @return int
     */
    public function getCarrierId(): int {
        return $this->carrierId;
    }

    /**
     * Устанавливает идентификатор перевозчика
     * @param int $carrierId
     */
    public function setCarrierId(int $carrierId) {
        $this->carrierId = $carrierId;
    }

    /**
     * Возвращает название перевозчика
     * @return string
     */
    public function getCarrierName(): string {
        return trim($this->carrierName);
    }

    /**
     * Устанавливает название перевозчика
     * @param string $carrierName
     */
    public function setCarrierName($carrierName) {
        $this->carrierName = $carrierName;
    }

    /**
     * Возвращает идентификатор тарифной ставки
     * @return int
     */
    public function getRateId(): int {
        return (int)$this->rateId;
    }

    /**
     * Устанавливает идентификатор тарифной ставки
     * @param int $rateId
     */
    public function setRateId($rateId) {
        $this->rateId = $rateId;
    }

    /**
     * @return int
     */
    public function getRateValueId(): int {
        return (int)$this->rateValueId;
    }

    /**
     * @param int $rateValueId
     */
    public function setRateValueId($rateValueId): void {
        $this->rateValueId = $rateValueId;
    }

    /**
     * Возвращает номер вагона
     * @return string
     */
    public function getWagonNumber(): string {
        return $this->wagonNumber;
    }

    /**
     * Устанавливает номер вагона
     * @param string $wagonNumber
     */
    public function setWagonNumber(string $wagonNumber) {
        $this->wagonNumber = $wagonNumber;
    }

    /**
     * Возвращает общую стоимость товара с НДС
     * @return float
     */
    public function getProductPrice(): float {
        return $this->productPrice;
    }

    /**
     * Устанавливает общую стоимость товара с НДС
     * @param float $productPrice
     */
    public function setProductPrice(float $productPrice) {
        $this->productPrice = $productPrice;
    }

    /**
     * @return float
     */
    public function getTransportPrice(): float {
        return $this->transportPrice;
    }

    /**
     * @param float $transportPrice
     */
    public function setTransportPrice(float $transportPrice): void {
        $this->transportPrice = $transportPrice;
    }

    /**
     * Возвращает вес при загрузке вагона
     * @return float
     */
    public function getLoadingWeight(): float {
        return $this->loadingWeight;
    }

    /**
     * Устанавливает вес при загрузке вагона
     * @param float $loadingWeight
     */
    public function setLoadingWeight(float $loadingWeight) {
        $this->loadingWeight = $loadingWeight;
    }

    /**
     * Возвращает дату загрузки вагона
     * @return DateTime
     */
    public function getLoadingDate() {
        return $this->loadingDate;
    }

    /**
     * Устанавливает дату загрузки вагона
     * @param DateTime $loadingDate
     */
    public function setLoadingDate(DateTime $loadingDate) {
        $this->loadingDate = $loadingDate;
    }

}