<?php

namespace Application\Domain;

use DateTime;

class CountryEntity {

    /**
     * Идентификатор страны
     * @var int
     */
    protected $countryId = 0;

    /**
     * Название страны
     * @var string
     */
    protected $countryName = '';

    /**
     * Код страны
     * @var string
     */
    protected $countryCode = '';

    /**
     * Локаль страны
     * @var string
     */
    protected $locale = '';

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
     * Возвращает идентификатор страны
     * @return int
     */
    public function getCountryId(): int {
        return $this->countryId;
    }

    /**
     * Устанавливает идентификатор страны
     * @param int $countryId
     */
    public function setCountryId(int $countryId) {
        $this->countryId = $countryId;
    }

    /**
     * Возвращает название страны
     * @return string
     */
    public function getCountryName(): string {
        return $this->countryName;
    }

    /**
     * Устанавливает название страны
     * @param string $countryName
     */
    public function setCountryName(string $countryName) {
        $this->countryName = $countryName;
    }

    /**
     * Возвращает код страны
     * @return string
     */
    public function getCountryCode(): string {
        return $this->countryCode;
    }

    /**
     * Устанавливает код страны
     * @param string $countryCode
     */
    public function setCountryCode(string $countryCode) {
        $this->countryCode = $countryCode;
    }

    /**
     * Возвращает локаль страны
     * @return string
     */
    public function getLocale(): string {
        return $this->locale;
    }

    /**
     * Устанавливает локаль страны
     * @param string $locale
     */
    public function setLocale(string $locale) {
        $this->locale = $locale;
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