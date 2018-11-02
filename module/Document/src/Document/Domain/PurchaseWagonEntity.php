<?php

namespace Document\Domain;

use DateTime;

class PurchaseWagonEntity {

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
     * Стоимость сырья которое
     * @var float
     */
    protected $materialPrice = 0.00;

    protected $deliveryPrice = 0.00;

    /**
     * Стоимость дополнительный затрат на перевозку сырья
     * @var float
     */
    protected $transportPrice = 0.00;

    /**
     * @var int
     */
    protected $transportContractorId = 0;

    /**
     * Коммментарий о дополнительный затратах на перефозку сырья
     * @var string
     */
    protected $transportComment = '';

    /**
     * Вес при загрузке вагона
     * @var float
     */
    protected $loadingWeight = 0.000;

    /**
     * Вес при разгрузке вагона
     * @var float
     */
    protected $unloadingWeight = 0.000;

    /**
     * Дата загрузки вагнона
     * @var DateTime
     */
    protected $loadingDate;

    /**
     * Дата разгрузки вагона
     * @var DateTime
     */
    protected $unloadingDate;

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
        return $this->carrierName;
    }

    /**
     * Устанавливает название перевозчика
     * @param string $carrierName
     */
    public function setCarrierName($carrierName) {
        $this->carrierName = trim($carrierName);
    }

    /**
     * Возвращает идентификатор тарифной ставки
     * @return int
     */
    public function getRateId(): int {
        return $this->rateId;
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
        return $this->rateValueId;
    }

    /**
     * @param int $rateValueId
     */
    public function setRateValueId(int $rateValueId): void {
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
     * Возвращает общую стоимость сырья с НДС
     * @return float
     */
    public function getMaterialPrice(): float {
        return $this->materialPrice;
    }

    /**
     * Устанавливает общую стоимость сырья с НДС
     * @param float $materialPrice
     */
    public function setMaterialPrice(float $materialPrice) {
        $this->materialPrice = $materialPrice;
    }

    /**
     * @return float
     */
    public function getDeliveryPrice(): float {
        return $this->deliveryPrice;
    }

    /**
     * @param float $deliveryPrice
     */
    public function setDeliveryPrice(float $deliveryPrice) {
        $this->deliveryPrice = $deliveryPrice;
    }

    /**
     * @return int
     */
    public function getTransportContractorId(): int {
        return $this->transportContractorId;
    }

    /**
     * @param int $transportContractorId
     */
    public function setTransportContractorId(int $transportContractorId) {
        $this->transportContractorId = $transportContractorId;
    }

    /**
     * Возвращает стоимость дополнительныйх трат на перевозку
     * @return float
     */
    public function getTransportPrice() {
        return $this->transportPrice;
    }

    /**
     * Устанавливает стоимость дополнительныйх трат на перевозку
     * @param float $transportPrice
     */
    public function setTransportPrice($transportPrice) {
        $this->transportPrice = $transportPrice;
    }

    /**
     * Возвращает комментарий о дополнительный тратах на перевозку
     * @return string
     */
    public function getTransportComment(): string {
        return $this->transportComment;
    }

    /**
     * Устанавливает комментарий о дополнительный тратах на перевозку
     * @param string $transportComment
     */
    public function setTransportComment(string $transportComment) {
        $this->transportComment = $transportComment;
    }

    /**
     * Возвращает состояние вагона
     * @return string
     */
    public function getStatus(): string {
        return $this->status;
    }

    /**
     * Устанавливает состояние вагона
     * @param string $status
     */
    public function setStatus(string $status) {
        $this->status = $status;
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
     * Возвращает при разгрузке вагона
     * @return float
     */
    public function getUnloadingWeight(): float {
        return $this->unloadingWeight;
    }

    /**
     * Устанавливает вес разгрузке вагона
     * @param float $unloadingWeight
     */
    public function setUnloadingWeight(float $unloadingWeight) {
        $this->unloadingWeight = $unloadingWeight;
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
    public function setLoadingDate($loadingDate) {
        $this->loadingDate = $loadingDate;
    }

    /**
     * Возвращает дату разгрузки вагона
     * @return DateTime
     */
    public function getUnloadingDate() {
        return $this->unloadingDate;
    }

    /**
     * Устанавливает дату разгрузки  вагона
     * @param DateTime $unloadingDate
     */
    public function setUnloadingDate($unloadingDate) {
        $this->unloadingDate = $unloadingDate;
    }

}