<?php

namespace Manufacturing\Domain;

use DateTime;

class WarehouseEntity {

    /**
     * Тип указывает на то, что этот склад используется для хранения сырья
     */
    const TYPE_MATERIAL_WAREHOUSE = 'materialWarehouse';

    /**
     * Тип указывает на то, что этот склад используется для хранения готовой продукции
     */
    const TYPE_PRODUCT_WAREHOUSE = 'productWarehouse';

    /**
     * Идентификатор склада
     * @var int
     */
    protected $warehouseId = 0;

    /**
     * Идентификатор завода к которому отностится склад
     * @var int
     */
    protected $plantId = 0;

    /**
     * Название завода к которому отностится склад
     * @var string
     */
    protected $plantName = '';

    /**
     * Название склада
     * @var string
     */
    protected $warehouseName = '';

    /**
     * Описание склада
     * @var string
     */
    protected $description = '';

    /**
     * Тип склада
     * @var string
     */
    protected $warehouseType = '';

    /**
     * Вместительность склада в тоннах
     * @var int
     */
    protected $capacity = 0;

    /**
     * Дата последнего обновления записи
     * @var DateTime
     */
    protected $updated;

    /**
     * Дата создания записи
     * @var DateTime
     */
    protected $created;

    /**
     * Возвращает идентификатор склада
     * @return int
     */
    public function getWarehouseId(): int {
        return $this->warehouseId;
    }

    /**
     * Устанавливает идентификатор склада
     * @param int $warehouseId
     */
    public function setWarehouseId(int $warehouseId) {
        $this->warehouseId = $warehouseId;
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
     * Возвращает название склада
     * @return string
     */
    public function getWarehouseName(): string {
        return $this->warehouseName;
    }

    /**
     * Устанавливает название склада
     * @param string $warehouseName
     */
    public function setWarehouseName(string $warehouseName) {
        $this->warehouseName = $warehouseName;
    }

    /**
     * Возвращает описание склада
     * @return string
     */
    public function getDescription(): string {
        return $this->description;
    }

    /**
     * Устанавливает описание склада
     * @param string $description
     */
    public function setDescription(string $description) {
        $this->description = $description;
    }

    /**
     * Возвращает тип склада
     * @return string
     */
    public function getWarehouseType(): string {
        return $this->warehouseType;
    }

    /**
     * Устанавливает тип склада
     * @param string $warehouseType
     */
    public function setWarehouseType(string $warehouseType) {
        $this->warehouseType = $warehouseType;
    }

    /**
     * Возвращает вместительность склада в тоннах
     * @return int
     */
    public function getCapacity(): int {
        return $this->capacity;
    }

    /**
     * Устанавливает вместительность склада в тоннах
     * @param int $capacity
     */
    public function setCapacity(int $capacity) {
        $this->capacity = $capacity;
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