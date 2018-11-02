<?php

namespace Transport\Domain;

use DateTime;

class RateEntity {

    /**
     * Фиксированная ставка. Указывается минимальный и максимальный вес.
     * Стоимость указываеться за вагон.
     */
    const TYPE_FIXED_BETWEEN_WEIGHT = 'fixedBetweenWeight';

    /**
     * Фиксированная ставка. Указывается вес загрузки.
     * Стоимость указываеться за вагон.
     */
    const TYPE_FIXED_STATIC_WEIGHT = 'fixedStaticWeight';

    /**
     * Плавающая ставка. Указывается минимальный и максимальный вес загрузки.
     * Стоимость указываеться за тонну.
     */
    const TYPE_FLOAT_BETWEEN_WEIGHT = 'floatBetweenWeight';

    /**
     * Входящее направление доставки.
     */
    const DIRECTION_INBOUND = 'inboundDirection';

    /**
     * Исходящее направление доставки.
     */
    const DIRECTION_OUTGOING = 'outgoingDirection';

    /**
     * Идентификатор танрифной ставки
     * @var int
     */
    protected $rateId = 0;

    /**
     * Идентификатор завода
     * @var int
     */
    protected $plantId = 0;

    /**
     * Название завода
     * @var string
     */
    protected $plantName = '';

    /**
     * Идентификатор обслуживающей компании
     * @var int
     */
    protected $companyId = 0;

    /**
     * Название обслуживающей компании
     * @var string
     */
    protected $companyName = '';

    /**
     * Идентификатор станции
     * @var int
     */
    protected $stationId = 0;

    /**
     * Название станции
     * @var string
     */
    protected $stationName = '';

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
     * @var string
     */
    protected $carrierType = '';

    /**
     * Направление доставки
     * @var string
     */
    protected $direction = '';

    /**
     * Тип тарифной ставки
     * @var string
     */
    protected $rateType = '';

    /**
     * Минимальный вес загрузки для смешанного типа тарифа
     * @var double
     */
    protected $minWeight = 0;

    /**
     * @var array
     */
    protected $values = [];

    /**
     * @var bool
     */
    protected $isDeleted = false;

    /**
     * Дата последнего обновления записи тарифной ставки
     * @var DateTime
     */
    protected $updated;

    /**
     * Дата создания записи тарифной ставки
     * @var DateTime
     */
    protected $created;

    /**
     * Начало периода действия тарифа
     * @var DateTime
     */
    protected $periodBegin;

    /**
     * Окончания периода действия тарифа
     * @var DateTime
     */
    protected $periodEnd;

    /**
     * Позвращает идентификатор танрифной ставки
     * @return int
     */
    public function getRateId(): int {
        return $this->rateId;
    }

    /**
     * Устанавливает идентификатор танрифной ставки
     * @param int $rateId
     */
    public function setRateId(int $rateId) {
        $this->rateId = $rateId;
    }

    /**
     * Возвращает идентификатор завода
     * @return int
     */
    public function getPlantId(): int {
        return $this->plantId;
    }

    /**
     * Устанавливает идентификатор завода
     * @param int $plantId
     */
    public function setPlantId(int $plantId) {
        $this->plantId = $plantId;
    }

    /**
     * Возвращает название завода
     * @return string
     */
    public function getPlantName(): string {
        return $this->plantName;
    }

    /**
     * Устанавливает название завода
     * @param string $plantName
     */
    public function setPlantName(string $plantName) {
        $this->plantName = $plantName;
    }

    /**
     * Возвращает идентификатор обслуживающей компании
     * @return int
     */
    public function getCompanyId(): int {
        return $this->companyId;
    }

    /**
     * Устанавливает идентификатор обслуживающей компании
     * @param int $companyId
     */
    public function setCompanyId(int $companyId) {
        $this->companyId = $companyId;
    }

    /**
     * Возвращает название обслуживающей компании
     * @return string
     */
    public function getCompanyName(): string {
        return $this->companyName;
    }

    /**
     * Устанавливает название обслуживающей компании
     * @param string $companyName
     */
    public function setCompanyName(string $companyName) {
        $this->companyName = $companyName;
    }

    /**
     * Возвращает идентификатор станции
     * @return int
     */
    public function getStationId(): int {
        return $this->stationId;
    }

    /**
     * Устанавливает идентификатор станции
     * @param int $stationId
     */
    public function setStationId(int $stationId) {
        $this->stationId = $stationId;
    }

    /**
     * Возвращает название станции
     * @return string
     */
    public function getStationName(): string {
        return $this->stationName;
    }

    /**
     * Устанавливает название станции
     * @param string $stationName
     */
    public function setStationName(string $stationName) {
        $this->stationName = $stationName;
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
    public function setCarrierName(string $carrierName) {
        $this->carrierName = $carrierName;
    }

    /**
     * @return string
     */
    public function getCarrierType(): string {
        return $this->carrierType;
    }

    /**
     * @param string $carrierType
     */
    public function setCarrierType(string $carrierType) {
        $this->carrierType = $carrierType;
    }

    /**
     * Возвращает направление доставки
     * @return string
     */
    public function getDirection(): string {
        return $this->direction;
    }

    /**
     * Устанавливает направление доставки
     * @param string $direction
     */
    public function setDirection(string $direction) {
        $this->direction = $direction;
    }

    /**
     * Возвращает тип тарифной ставки
     * @return string
     */
    public function getRateType(): string {
        return $this->rateType;
    }

    /**
     * Устанавливает тип тарифной ставки
     * @param string $rateType
     */
    public function setRateType(string $rateType) {
        $this->rateType = $rateType;
    }

    /**
     * @return array
     */
    public function getValues(): array {
        return $this->values;
    }

    /**
     * @param array $values
     */
    public function setValues(array $values): void {
        $this->values = $values;
    }

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
     * @return bool
     */
    public function getIsDeleted(): bool {
        return $this->isDeleted;
    }

    /**
     * @param bool $isDeleted
     */
    public function setIsDeleted(bool $isDeleted) {
        $this->isDeleted = $isDeleted;
    }


    /**
     * Возвращает объект даты и времени обновления текущей записи, если значение пустое,
     * тогда будет возвращен объект DateTime с текущим значением даты и времени.
     * @return DateTime
     */
    public function getUpdated(): DateTime {
        if (!$this->updated instanceof DateTime)
            $this->updated = new DateTime($this->updated);
        return $this->updated;
    }

    /**
     * Устанавливает объект даты и времени обновления текущей записи.
     * @param DateTime $updated
     */
    public function setUpdated(DateTime $updated) {
        $this->updated = $updated;
    }

    /**
     * Возвращает объект даты и времени создания текущей записи, если значение пустое,
     * тогда будет возвращен объект DateTime с текущим значением даты и времени.
     * @return DateTime
     */
    public function getCreated(): DateTime {
        if (!$this->created instanceof DateTime)
            $this->created = new DateTime($this->created);
        return $this->created;
    }

    /**
     * Устанавливает объект даты и времени создания текущей записи.
     * @param DateTime $created
     */
    public function setCreated(DateTime $created) {
        $this->created = $created;
    }

    /**
     * @return DateTime
     */
    public function getPeriodBegin(): DateTime {
        if (!$this->periodBegin instanceof DateTime)
            $this->periodBegin = new DateTime($this->periodBegin);
        return $this->periodBegin;
    }

    /**
     * @param DateTime $periodBegin
     */
    public function setPeriodBegin($periodBegin): void {
        $this->periodBegin = $periodBegin;
    }

    /**
     * @return DateTime
     */
    public function getPeriodEnd(): DateTime {
        if (!$this->periodEnd instanceof DateTime)
            $this->periodEnd = new DateTime($this->periodEnd);
        return $this->periodEnd;
    }

    /**
     * @param DateTime $periodEnd
     */
    public function setPeriodEnd($periodEnd): void {
        $this->periodEnd = $periodEnd;
    }

}