<?php

namespace Transport\Domain;

use Application\Domain\PersonValueObject;
use DateTime;

class CarrierEntity {

    const TYPE_TRAIN = 'train';
    const TYPE_TRUCK = 'truck';

    /**
     * @var int
     */
    protected $carrierId = 0;

    /**
     * Mode of transportation of the carrier
     * @var string
     */
    protected $carrierType = '';

    /**
     * @var string
     */
    protected $carrierName = '';

    /**
     * @var string
     */
    protected $description = '';

    /**
     * Код клиента в ЕГДРПОУ
     * @var string
     */
    protected $registerCode = '';

    /**
     * @var string
     */
    protected $country = '';

    /**
     * @var PersonValueObject
     */
    protected $person;

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
     * @var DateTime
     */
    protected $updated;

    /**
     * @var DateTime
     */
    protected $created;

    /**
     * @return int
     */
    public function getCarrierId(): int {
        return $this->carrierId;
    }

    /**
     * @param int $carrierId
     */
    public function setCarrierId(int $carrierId) {
        $this->carrierId = $carrierId;
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
     * @return string
     */
    public function getCarrierName(): string {
        return $this->carrierName;
    }

    /**
     * @param string $carrierName
     */
    public function setCarrierName(string $carrierName) {
        $this->carrierName = $carrierName;
    }

    /**
     * @return string
     */
    public function getDescription(): string {
        return $this->description;
    }

    /**
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
        return trim($this->registerCode);
    }

    /**
     * Устанавливает код клиента в ЕГДРПОУ
     * @param string $registerCode
     */
    public function setRegisterCode($registerCode) {
        $this->registerCode = $registerCode;
    }

    /**
     * @return string
     */
    public function getCountry(): string {
        return trim($this->country);
    }

    /**
     * @param string $country
     */
    public function setCountry(string $country) {
        $this->country = $country;
    }

    /**
     * @return PersonValueObject
     */
    public function getPerson(): PersonValueObject {
        if (!$this->person instanceof PersonValueObject)
            $this->person = PersonValueObject::factory();
        return $this->person;
    }

    /**
     * @param PersonValueObject $person
     */
    public function setPerson(PersonValueObject $person) {
        $this->person = $person;
    }

    /**
     * @return array
     */
    public function getEmails(): array {
        return $this->emails;
    }

    /**
     * @param array $emails
     */
    public function setEmails(array $emails) {
        $this->emails = $emails;
    }

    /**
     * @return array
     */
    public function getPhones(): array {
        return $this->phones;
    }

    /**
     * @param array $phones
     */
    public function setPhones(array $phones) {
        $this->phones = $phones;
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