<?php

namespace Resource\Domain;

use DateTime;

class MaterialEntity {

    /**
     * Идентификатор сырья
     * @var int
     */
    protected $materialId = 0;

    /**
     * Название сырья
     * @var string
     */
    protected $materialName = '';

    /**
     * Описание сыръя
     * @var string
     */
    protected $description = '';

    /**
     * Фраскция материала, для угля фракция отсутствует
     * @var null|FractionValueObject
     */
    protected $fraction;

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
     * Возвращает идентификатор сырья
     * @return int
     */
    public function getMaterialId(): int {
        return $this->materialId;
    }

    /**
     * Устанавливает идентификатор сырья
     * @param int $materialId
     */
    public function setMaterialId(int $materialId) {
        $this->materialId = $materialId;
    }

    /**
     * Возвращает название сыръя
     * @return string
     */
    public function getMaterialName(): string {
        return $this->materialName;
    }

    /**
     * Устанавливает название сыръя
     * @param string $materialName
     */
    public function setMaterialName(string $materialName) {
        $this->materialName = $materialName;
    }

    /**
     * Возвращает описание сырья
     * @return string
     */
    public function getDescription(): string {
        return $this->description;
    }

    /**
     * Устанавливает описание сырья
     * @param string $description
     */
    public function setDescription(string $description) {
        $this->description = $description;
    }

    /**
     * Ворзвращает фракцию сырья
     * @return null|FractionValueObject
     */
    public function getFraction() {
        return $this->fraction;
    }

    /**
     * Устанавливает фракцию сырья
     * @param null|FractionValueObject $fraction
     */
    public function setFraction($fraction) {
        $this->fraction = $fraction;
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
