<?php

namespace Transport\Domain;

use DateTime;

class StationEntity {

    /**
     * @var int
     */
    protected $stationId = 0;

    /**
     * @var string
     */
    protected $stationType = '';

    /**
     * @var string
     */
    protected $stationName = '';

    /**
     * @var string
     */
    protected $description = '';

    /**
     * @var string
     */
    protected $stationCode = '';

    /**
     * @var string
     */
    protected $country = '';

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
    public function getStationId(): int {
        return $this->stationId;
    }

    /**
     * @param int $stationId
     */
    public function setStationId(int $stationId) {
        $this->stationId = $stationId;
    }

    /**
     * @return string
     */
    public function getStationType(): string {
        return $this->stationType;
    }

    /**
     * @param string $stationType
     */
    public function setStationType(string $stationType) {
        $this->stationType = $stationType;
    }

    /**
     * @return string
     */
    public function getStationName(): string {
        return $this->stationName;
    }

    /**
     * @param string $stationName
     */
    public function setStationName(string $stationName) {
        $this->stationName = $stationName;
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
     * @return string
     */
    public function getStationCode(): string {
        return $this->stationCode;
    }

    /**
     * @param string $stationCode
     */
    public function setStationCode(string $stationCode) {
        $this->stationCode = $stationCode;
    }

    /**
     * @return string
     */
    public function getCountry(): string {
        return $this->country;
    }

    /**
     * @param string $country
     */
    public function setCountry(string $country) {
        $this->country = $country;
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