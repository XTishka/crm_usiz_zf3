<?php

namespace Contractor\Entity;

use Application\Domain;
use DateTime;

abstract class ContractorAbstract {

    /**
     * Не явный
     */
    const TYPE_COMMON = 'common';

    /**
     * Прочие контрагенты
     */
    const TYPE_ADDITIONAL = 'additional';

    /**
     * Внутренние контрагенты компании
     */
    const TYPE_COMPANY = 'company';

    /**
     * Внутренние контрагенты заводы
     */
    const TYPE_PLANT = 'plant';

    /**
     * Контрагенты перевозчики
     */
    const TYPE_CARRIER = 'carrier';

    /**
     * Корпоративные контрагенты
     */
    const TYPE_CORPORATE = 'corporate';

    /**
     * Грузополучатели
     */
    const TYPE_CONSIGNEE = 'consignee';

    /**
     * Контрагенты покупатели
     */
    const TYPE_CUSTOMER = 'customer';

    /**
     * Контрагенты поставщики
     */
    const TYPE_PROVIDER = 'provider';


    /**
     * Идентификатор контрагента
     * @var int
     */
    protected $contractorId;

    /**
     * Тип контрагента
     * @var null
     */
    protected $contractorType;

    /**
     * Название контрагента
     * @var string
     */
    protected $contractorName = '';

    /**
     * Полное название обслуживающего клиента
     * @var string
     */
    protected $fullName = '';

    /**
     * Описание обслуживающего клиента
     * @var string
     */
    protected $description = '';

    /**
     * Код клиента в ЕГДРПОУ
     * @var string
     */
    protected $registerCode = '';

    /**
     * Рассчетный счет клиента
     * @var string
     */
    protected $bankAccount = '';

    /**
     * Объект почтового адреса обслуживающего клиента
     * @var Domain\AddressValueObject
     */
    protected $address;

    /**
     * An array containing EmailValueObject objects
     * @var array
     */
    protected $emails = [];

    /**
     * An array containing PhoneValueObject objects
     * @var array
     */
    protected $phones = [];

    /**
     * Объект контактного лица обслуживающего клиента
     * @var Domain\PersonValueObject
     */
    protected $person;

    /**
     * Дата последнего обновления записи в базе данных
     * @var DateTime
     */
    protected $updated;

    /**
     * Дата создания записи в базе данных
     * @var DateTime
     */
    protected $created;

    /**
     * Возвращает идентификатор контрагента
     * @return int
     */
    public function getContractorId(): int {
        return (int)$this->contractorId;
    }

    /**
     * Утанавливает идентификатор клиента
     * @param int $contractorId
     */
    public function setContractorId(int $contractorId) {
        $this->contractorId = $contractorId;
    }

    /**
     * Возвращает тип контрагента
     * @return string
     */
    public function getContractorType(): string {
        if ($this->contractorType == self::TYPE_COMMON) $this->contractorType = 'test';
        return $this->contractorType;
    }

    /**
     * Возвращает тип контрагента
     * @return string
     */
    public function setContractorType(): string {
        return $this->contractorType;
    }

    /**
     * Возвращает название контрагента
     * @return string
     */
    public function getContractorName(): string {
        return $this->contractorName;
    }

    /**
     * Устанавливает название контрагента
     * @param string $contractorName
     */
    public function setContractorName(string $contractorName) {
        $this->contractorName = $contractorName;
    }

    /**
     * Возвращает полное название клиента
     * @return string
     */
    public function getFullName(): string {
        return $this->fullName;
    }

    /**
     * Устанавливает полное название клиента
     * @param string $fullName
     */
    public function setFullName(string $fullName) {
        $this->fullName = $fullName;
    }

    /**
     * Возвращает описание клиента
     * @return string
     */
    public function getDescription(): string {
        return $this->description;
    }

    /**
     * Устанавливает описание клиента
     * @param string $description
     */
    public function setDescription(string $description) {
        $this->description = $description;
    }

    /**
     * Возвращает код клиента в ЕГДРПОУ
     * @return string
     */
    public function getRegisterCode(): string {
        return $this->registerCode;
    }

    /**
     * Устанавливает код клиента в ЕГДРПОУ
     * @param string $registerCode
     */
    public function setRegisterCode(string $registerCode) {
        $this->registerCode = $registerCode;
    }

    /**
     * Возвращает рассчетный счет клиента
     * @return string
     */
    public function getBankAccount(): string {
        return $this->bankAccount;
    }

    /**
     * Устанавливает рассчетный счет клиента
     * @param string $bankAccount
     */
    public function setBankAccount(string $bankAccount) {
        $this->bankAccount = $bankAccount;
    }

    /**
     * Возвращает объект адреса клиента
     * @return Domain\AddressValueObject
     */
    public function getAddress(): Domain\AddressValueObject {
        if (!$this->address instanceof Domain\AddressValueObject)
            $this->address = Domain\AddressValueObject::factory();
        return $this->address;
    }

    /**
     * Устанавливает объект адреса клиента
     * @param Domain\AddressValueObject $address
     */
    public function setAddress(Domain\AddressValueObject $address) {
        $this->address = $address;
    }

    /**
     * Возвращает массив объектов EmailValueObject с адресами электронной почты клиента
     * @return array
     */
    public function getEmails(): array {
        return $this->emails;
    }

    /**
     * Устанавливает массив объектов EmailValueObject с адресами электронной почты клиента
     * @param array $emails
     */
    public function setEmails(array $emails) {
        $this->emails = $emails;
    }

    /**
     * Возвращает массив объектов PhoneValueObject с номерами телефонов клиента
     * @return array
     */
    public function getPhones(): array {
        return $this->phones;
    }

    /**
     * Устанавливает массив объектов PhoneValueObject с номерами телефонов клиента
     * @param array $phones
     */
    public function setPhones(array $phones) {
        $this->phones = $phones;
    }

    /**
     * Возвращает объект PersonValueObject с именем контактного лица клиента
     * @return Domain\PersonValueObject
     */
    public function getPerson(): Domain\PersonValueObject {
        if (!$this->person instanceof Domain\PersonValueObject)
            $this->person = Domain\PersonValueObject::factory();
        return $this->person;
    }

    /**
     * Устанавливает объект PersonValueObject с именем контактного лица клиента
     * @param Domain\PersonValueObject $person
     */
    public function setPerson(Domain\PersonValueObject $person) {
        $this->person = $person;
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


}