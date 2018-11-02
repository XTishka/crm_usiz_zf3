<?php

namespace Document\Domain;

use DateTime;

class FinanceLogEntity {

    /**
     * Тип контрагента - прочий
     */
    const CONTRACTOR_ADDITIONAL = 'additional';

    /**
     * Тип контрагента - перевозчик
     */
    const CONTRACTOR_CARRIER = 'carrier';

    /**
     * Тип контрагента - предприятие
     */
    const CONTRACTOR_COMPANY = 'company';

    /**
     * Тип контрагента - покупатель
     */
    const CONTRACTOR_CUSTOMER = 'customer';

    /**
     * Тип контрагента - поставщик
     */
    const CONTRACTOR_PROVIDER = 'provider';

    /**
     * Операция формирующая расходы
     */
    const TYPE_CREDIT = 'credit';

    /**
     * Операция формирующая прибыль
     */
    const TYPE_DEBIT = 'debit';

    /**
     * Идентификатор транзакции
     * @var int
     */
    protected $logId = 0;

    /**
     * Идентификатор вагона
     * @var int
     */
    protected $wagonId = 0;

    /**
     * Тип операции формирует доход или расход контрагента
     * @var string
     */
    protected $operationType = '';

    /**
     * Тип контрагента предприятие, поставщик, покупатель или перевозчик
     * @var string
     */
    protected $contractorType = '';

    /**
     * Идентификатор контрагента
     * @var int
     */
    protected $contractorId = 0;

    /**
     * Стоимость транзакции
     * @var float
     */
    protected $price = 0.00;

    /**
     * Сомментарий к транзакции
     * @var string
     */
    protected $comment = '';

    /**
     * Дата создания записи
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
     * @return string
     */
    public function getOperationType(): string {
        return $this->operationType;
    }

    /**
     * @param string $operationType
     */
    public function setOperationType(string $operationType) {
        $this->operationType = $operationType;
    }

    /**
     * @return string
     */
    public function getContractorType(): string {
        return $this->contractorType;
    }

    /**
     * @param string $contractorType
     */
    public function setContractorType(string $contractorType) {
        $this->contractorType = $contractorType;
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
     * @return float
     */
    public function getPrice(): float {
        return $this->price;
    }

    /**
     * @param float $price
     */
    public function setPrice(float $price) {
        $this->price = $price;
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
    public function setCreated($created) {
        $this->created = $created;
    }

}