<?php

namespace Manufacturing\Domain;

use DateTime;

class FurnaceEntity {

    /**
     * Идентификатор печи
     * @var int
     */
    protected $furnaceId = 0;

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
     * Название печи
     * @var string
     */
    protected $furnaceName = '';

    /**
     * Описание печи
     * @var string
     */
    protected $description = '';

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
     * Возвращает идентификатор печи
     * @return int
     */
    public function getFurnaceId(): int {
        return $this->furnaceId;
    }

    /**
     * Устанавливает идентификатор печи
     * @param int $furnaceId
     */
    public function setFurnaceId(int $furnaceId) {
        $this->furnaceId = $furnaceId;
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
     * Возвращает название печи
     * @return string
     */
    public function getFurnaceName(): string {
        return $this->furnaceName;
    }

    /**
     * Устанавливает имя печи
     * @param string $furnaceName
     */
    public function setFurnaceName(string $furnaceName) {
        $this->furnaceName = $furnaceName;
    }

    /**
     * Возвращает описание печи
     * @return string
     */
    public function getDescription(): string {
        return $this->description;
    }

    /**
     * Устанавливает описание печи
     * @param string $description
     */
    public function setDescription(string $description) {
        $this->description = $description;
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