<?php

namespace Document\Domain;

use DateTime;
use Document\Exception\InvalidArgumentException;

class TransactionEntity {

    /**
     * Тип транзакции задолженность
     */
    const TRANSACTION_DEBT = 'debt';

    /**
     * Тип транзакции оплата
     */
    const TRANSACTION_PAYMENT = 'payment';

    const CONTRACT_PURCHASE = 'purchase';
    const CONTRACT_SALE     = 'sale';

    /**
     * Указывает на тип прочие контрагенты.
     */
    const CONTRACTOR_ADDITIONAL = 'additional';

    /**
     * Указывает на тип контрагента перевозчики.
     */
    const CONTRACTOR_CARRIER = 'carrier';

    /**
     * Указывает на тип контрагента компании.
     */
    const CONTRACTOR_COMPANY = 'company';

    /**
     * Указывает на тип контрагента покупатели.
     */
    const CONTRACTOR_CUSTOMER = 'customer';

    /**
     * Указывает на тип внутренние контрагенты.
     */
    const CONTRACTOR_CORPORATE = 'corporate';

    /**
     * Указывает на тип контрагента заводы.
     */
    const CONTRACTOR_PLANT = 'plant';

    /**
     * Указывает на тип контрагента поставщика.
     */
    const CONTRACTOR_PROVIDER = 'provider';

    protected $availableTransactions = [
        self::TRANSACTION_DEBT,     // Формирование задолженности
        self::TRANSACTION_PAYMENT,  // Формирование платежа
    ];

    protected $availableContractors = [
        self::CONTRACTOR_ADDITIONAL,// Прочие контрагенты
        self::CONTRACTOR_CARRIER,   // Перевозчики
        self::CONTRACTOR_COMPANY,   // Компании
        self::CONTRACTOR_CORPORATE, // Внутренние контрагенты
        self::CONTRACTOR_CUSTOMER,  // Покупатели
        self::CONTRACTOR_PLANT,     // Заводы
        self::CONTRACTOR_PROVIDER,  // Поставщики
    ];

    /**
     * Уникальный идентификатор транзакции
     * @var int
     */
    protected $transactionId = 0;

    /**
     * Тип траннзакции может быть debt (задолженность) и payment (оплата)
     * @var string
     */
    protected $transactionType = '';

    /**
     * @var string
     */
    protected $contractType = '';

    /**
     * Уникальный идентификатор предприятия
     * @var int
     */
    protected $companyId = 0;

    /**
     * Уникальный идентификатор контрагента
     * @var int
     */
    protected $contractorId = 0;

    /**
     * Тип контрагента
     * @var string
     */
    protected $contractorType = '';

    /**
     * Кредит финансовой транзакции
     * @var double
     */
    protected $credit = 0.00;

    /**
     * Дебит финансовой транзакции
     * @var double
     */
    protected $debit = 0.00;

    /**
     * Валюта финансовой транзакции
     * @var string
     */
    protected $currency = 'UAH';

    /**
     * Комментарий к платежу
     * @var string
     */
    protected $comment = '';

    /**
     * Идентификатор вагона
     * @var int
     */
    protected $wagonId = 0;

    protected $isManual = 0;

    /**
     * Дата формирования задолженности
     * @var DateTime
     */
    protected $created;

    /**
     * TransactionEntity constructor.
     * @param string $transactionType
     * @param string $contractorType
     */
    public function __construct($transactionType = null, $contractorType = null) {
        if (trim($transactionType) && !in_array($transactionType, $this->availableTransactions))
            throw new InvalidArgumentException('An unsupported transaction type was specified.');
        if (trim($contractorType) && !in_array($contractorType, $this->availableContractors))
            throw new InvalidArgumentException('An unsupported contractor type was specified.');
        $this->transactionType = $transactionType;
        $this->contractorType = $contractorType;
    }

    /**
     * Возвращает уникальный идентификатор транзакции
     * @return int
     */
    public function getTransactionId(): int {
        return $this->transactionId;
    }

    /**
     * Устанавливает уникальный идентификатор транзакции
     * @param int $transactionId
     */
    public function setTransactionId(int $transactionId) {
        $this->transactionId = $transactionId;
    }

    /**
     * Возвращает тип транзакции
     * @return string
     */
    public function getTransactionType(): string {
        return (string)$this->transactionType;
    }

    /**
     * Устанавливает тип транзакции
     * @param string $transactionType
     */
    public function setTransactionType(string $transactionType) {
        $this->transactionType = $transactionType;
    }

    /**
     * @return string
     */
    public function getContractType(): string {
        return $this->contractType;
    }

    /**
     * @param string $contractType
     */
    public function setContractType(string $contractType) {
        $this->contractType = $contractType;
    }

    /**
     * Возвращает уникальный идентификатор предприятия
     * @return int
     */
    public function getCompanyId(): int {
        return $this->companyId;
    }

    /**
     * Устанавливает уникальный идентификатор предприятия
     * @param int $companyId
     */
    public function setCompanyId(int $companyId) {
        $this->companyId = $companyId;
    }

    /**
     * Возвращает уникальный идентификатор контрагента
     * @return int
     */
    public function getContractorId(): int {
        return $this->contractorId;
    }

    /**
     * Устанавливает уникальный идентификатор контрагента
     * @param int $contractorId
     */
    public function setContractorId(int $contractorId) {
        $this->contractorId = $contractorId;
    }

    /**
     * Возвращет тип контрагента
     * @return string
     */
    public function getContractorType(): string {
        return (string)$this->contractorType;
    }

    /**
     * Устанавливает тип контрагента
     * @param string $contractorType
     */
    public function setContractorType(string $contractorType) {
        $this->contractorType = $contractorType;
    }

    /**
     * Возвращает кредит финансовой транзакции
     * @return double
     */
    public function getCredit(): float {
        return $this->credit;
    }

    /**
     * Возвращает кредит финансовой транзакции
     * @param double $credit
     */
    public function setCredit(float $credit) {
        $this->credit = $credit;
    }

    /**
     * Возвращает дебет финансовой транзакции
     * @return double
     */
    public function getDebit(): float {
        return $this->debit;
    }

    /**
     * Устанавливает дебет финансовой транзакции
     * @param double $debit
     */
    public function setDebit(float $debit) {
        $this->debit = $debit;
    }

    /**
     * Возвращает валюту финансовой транзакции
     * @return string
     */
    public function getCurrency(): string {
        return $this->currency;
    }

    /**
     * Устанавливает валюту финансовой транзакции
     * @param string $currency
     */
    public function setCurrency(string $currency) {
        $this->currency = $currency;
    }

    /**
     * Возвращает комментарий к транзакции
     * @return string
     */
    public function getComment(): string {
        return $this->comment;
    }

    /**
     * Устанавливает комментарий к транзакции
     * @param string $comment
     */
    public function setComment(string $comment) {
        $this->comment = $comment;
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
    public function getIsManual(): int {
        return $this->isManual;
    }

    /**
     * @param int $isManual
     */
    public function setIsManual(int $isManual) {
        $this->isManual = $isManual;
    }

    /**
     * Возвращает дату проведения транзакции
     * @return DateTime
     */
    public function getCreated(): DateTime {
        if (!$this->created instanceof DateTime)
            $this->created = new DateTime($this->created);
        return $this->created;
    }

    /**
     * Устанавливает дату проведения транзакции
     * @param DateTime $created
     */
    public function setCreated(DateTime $created) {
        $this->created = $created;
    }

}