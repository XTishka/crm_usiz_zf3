<?php

namespace Manufacturing\Domain;

use DateTime;

class SkipCommonEntity {

    /**
     * Уникальный идентификатор скипа
     * @var int
     */
    protected $skipId = 0;

    /**
     * Идентификатор печи
     * @var int
     */
    protected $furnaceId = 0;

    /**
     * Идентификатор склада сырья
     * @var int
     */
    protected $materialWarehouseId = 0;

    /**
     * Идентификатор склада готовой продукции
     * @var int
     */
    protected $productWarehouseId = 0;

    /**
     * Массив загружаемых материалов
     * @var array
     */
    protected $materials = [];

    /**
     * Дата загрузки печи
     * @var DateTime
     */
    protected $date;

    /**
     * @return int
     */
    public function getSkipId(): int {
        return $this->skipId;
    }

    /**
     * @param int $skipId
     */
    public function setSkipId(int $skipId) {
        $this->skipId = $skipId;
    }

    /**
     * @return int
     */
    public function getFurnaceId(): int {
        return $this->furnaceId;
    }

    /**
     * @param int $furnaceId
     */
    public function setFurnaceId(int $furnaceId) {
        $this->furnaceId = $furnaceId;
    }

    /**
     * @return int
     */
    public function getMaterialWarehouseId(): int {
        return $this->materialWarehouseId;
    }

    /**
     * @param int $materialWarehouseId
     */
    public function setMaterialWarehouseId(int $materialWarehouseId) {
        $this->materialWarehouseId = $materialWarehouseId;
    }

    /**
     * @return int
     */
    public function getProductWarehouseId(): int {
        return $this->productWarehouseId;
    }

    /**
     * @param int $productWarehouseId
     */
    public function setProductWarehouseId(int $productWarehouseId) {
        $this->productWarehouseId = $productWarehouseId;
    }

    /**
     * @return array
     */
    public function getMaterials(): array {
        return $this->materials;
    }

    /**
     * @param array $materials
     */
    public function setMaterials(array $materials) {
        $this->materials = $materials;
    }

    /**
     * @return DateTime
     */
    public function getDate() {
        return $this->date;
    }

    /**
     * @param DateTime $date
     */
    public function setDate($date) {
        $this->date = $date;
    }

}