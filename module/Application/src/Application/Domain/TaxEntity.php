<?php

namespace Application\Domain;

use DateTime;

class TaxEntity {

    /**
     * Идентифиакор налога
     * @var int
     */
    protected $taxId = 0;

    /**
     * Название налога
     * @var string
     */
    protected $taxName = '';

    /**
     * Описание налога
     * @var string
     */
    protected $description = '';

    /**
     * Значение налога в процентах
     * @var float
     */
    protected $value = 0.00;

    /**
     * Время последнего обновления записи страны
     * @var DateTime
     */
    protected $updated;

    /**
     * Время создание записи страны
     * @var DateTime
     */
    protected $created;

    /**
     * Возвращает идентификатор налога
     * @return int
     */
    public function getTaxId(): int {
        return $this->taxId;
    }

    /**
     * Устанавливает идентификатор налога
     * @param int $taxId
     */
    public function setTaxId(int $taxId) {
        $this->taxId = $taxId;
    }

    /**
     * Возвращает название налога
     * @return string
     */
    public function getTaxName(): string {
        return $this->taxName;
    }

    /**
     * Устанавливает название налога
     * @param string $taxName
     */
    public function setTaxName(string $taxName) {
        $this->taxName = $taxName;
    }

    /**
     * Возвращает описание налога
     * @return string
     */
    public function getDescription(): string {
        return $this->description;
    }

    /**
     * Устанавливает описание налога
     * @param string $description
     */
    public function setDescription(string $description) {
        $this->description = $description;
    }

    /**
     * Возвращает значение налога в процентах
     * @return float
     */
    public function getValue(): float {
        return $this->value;
    }

    /**
     * Устанавливает значение налога в процентах
     * @param float $value
     */
    public function setValue(float $value) {
        $this->value = $value;
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