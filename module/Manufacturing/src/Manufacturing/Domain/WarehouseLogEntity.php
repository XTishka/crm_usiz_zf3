<?php

namespace Manufacturing\Domain;

use DateTime;

class WarehouseLogEntity {

    /**
     * Указывает на добавление ресурса на склад.
     */
    const DIRECTION_INPUT = 'input';

    /**
     * Указывает на извлечение ресурса со склада.
     */
    const DIRECTION_OUTPUT = 'output';

    /**
     * Идентификатор записи
     * @var int
     */
    protected $logId = 0;

    /**
     * @var int
     */
    protected $wagonId = 0;

    /**
     * @var int
     */
    protected $skipId = 0;

    /**
     * Идентификатор склада
     * @var int
     */
    protected $warehouseId = 0;

    /**
     * Название склада
     * @var string
     */
    protected $warehouseName = '';

    /**
     * Идентификатор ресурса
     * @var int
     */
    protected $resourceId = 0;

    /**
     * Наименование ресурса
     * @var string
     */
    protected $resourceName = '';

    /**
     * Стоимость ресурса, который добавляется или извлекается
     * @var float
     */
    protected $resourcePrice = 0.00;

    /**
     * Вес ресурса, который добавляется или извлекается
     * @var float
     */
    protected $resourceWeight = 0.000;

    /**
     * Идентификатор контрагента
     * @var int
     */
    protected $contractorId = 0;

    /**
     * Название контрагента
     * @var string
     */
    protected $contractorName = '';

    /**
     * Направление ресурса относительно склада - добавление или извлечение ресурса.
     * Существуют 2 типа направления input и output
     * @var string
     */
    protected $direction = '';

    /**
     * Коментарий к операции добавления/извлечения ресурса
     * @var string
     */
    protected $comment = '';

    /**
     * Дата создания записи.
     * @var DateTime
     */
    protected $created;

    /**
     * @return int
     */
    public function getLogId(): int {
        return $this->logId;
    }

    /**
     * @param int $logId
     */
    public function setLogId(int $logId) {
        $this->logId = $logId;
    }

    /**
     * @return int
     */
    public function getWagonId(): int {
        return $this->wagonId;
    }

    /**
     * @param int $wagonId
     */
    public function setWagonId(int $wagonId) {
        $this->wagonId = $wagonId;
    }

    /**
     * @return int
     */
    public function getSkipId(): int {
        return $this->skipId;
    }

    /**
     * @param int $skipId
     */
    public function setSkipId(int $skipId) {
        $this->skipId = $skipId;
    }

    /**
     * @return int
     */
    public function getWarehouseId(): int {
        return $this->warehouseId;
    }

    /**
     * @param int $warehouseId
     */
    public function setWarehouseId(int $warehouseId) {
        $this->warehouseId = $warehouseId;
    }

    /**
     * @return string
     */
    public function getWarehouseName(): string {
        return $this->warehouseName;
    }

    /**
     * @param string $warehouseName
     */
    public function setWarehouseName(string $warehouseName) {
        $this->warehouseName = $warehouseName;
    }

    /**
     * @return int
     */
    public function getResourceId(): int {
        return $this->resourceId;
    }

    /**
     * @param int $resourceId
     */
    public function setResourceId(int $resourceId) {
        $this->resourceId = $resourceId;
    }

    /**
     * @return string
     */
    public function getResourceName(): string {
        return $this->resourceName;
    }

    /**
     * @param string $resourceName
     */
    public function setResourceName(string $resourceName) {
        $this->resourceName = $resourceName;
    }

    /**
     * @return float
     */
    public function getResourcePrice(): float {
        return $this->resourcePrice;
    }

    /**
     * @param float $resourcePrice
     */
    public function setResourcePrice(float $resourcePrice) {
        $this->resourcePrice = $resourcePrice;
    }

    /**
     * @return float
     */
    public function getResourceWeight(): float {
        return $this->resourceWeight;
    }

    /**
     * @param float $resourceWeight
     */
    public function setResourceWeight(float $resourceWeight) {
        $this->resourceWeight = $resourceWeight;
    }

    /**
     * @return int
     */
    public function getContractorId(): int {
        return $this->contractorId;
    }

    /**
     * @param int $contractorId
     */
    public function setContractorId(int $contractorId) {
        $this->contractorId = $contractorId;
    }

    /**
     * @return string
     */
    public function getContractorName(): string {
        return $this->contractorName;
    }

    /**
     * @param string $contractorName
     */
    public function setContractorName(string $contractorName) {
        $this->contractorName = $contractorName;
    }

    /**
     * @return string
     */
    public function getDirection(): string {
        return $this->direction;
    }

    /**
     * @param string $direction
     */
    public function setDirection(string $direction) {
        $this->direction = $direction;
    }

    /**
     * @return string
     */
    public function getComment(): string {
        return $this->comment;
    }

    /**
     * @param string $comment
     */
    public function setComment(string $comment) {
        $this->comment = $comment;
    }

    /**
     * @return DateTime
     */
    public function getCreated(): DateTime {
        if (!$this->created instanceof DateTime)
            $this->created = new DateTime($this->created);
        return $this->created;
    }

    /**
     * @param DateTime $created
     */
    public function setCreated(DateTime $created) {
        $this->created = $created;
    }

}