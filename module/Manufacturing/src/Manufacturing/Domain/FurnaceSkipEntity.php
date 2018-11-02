<?php

namespace Manufacturing\Domain;

use DateTime;

class FurnaceSkipEntity {

    protected $skipId = 0;

    protected $furnaceId = 0;

    protected $materialWarehouseId = 0;

    protected $productWarehouseId = 0;

    protected $materials = [];

    protected $providerId = 0;

    protected $materialId = 0;

    protected $weight = 0.000;

    protected $dropout = 0;

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
    public function getMaterials() {
        return $this->materials;
    }

    /**
     * @param array $materials
     */
    public function setMaterials($materials) {
        $this->materials = $materials;
    }

    /**
     * @return int
     */
    public function getProviderId(): int {
        return $this->providerId;
    }

    /**
     * @param int $providerId
     */
    public function setProviderId(int $providerId) {
        $this->providerId = $providerId;
    }

    /**
     * @return int
     */
    public function getMaterialId(): int {
        return $this->materialId;
    }

    /**
     * @param int $materialId
     */
    public function setMaterialId(int $materialId) {
        $this->materialId = $materialId;
    }

    /**
     * @return float
     */
    public function getWeight(): float {
        return $this->weight;
    }

    /**
     * @param float $weight
     */
    public function setWeight(float $weight) {
        $this->weight = $weight;
    }

    /**
     * @return int
     */
    public function getDropout(): int {
        return $this->dropout;
    }

    /**
     * @param int $dropout
     */
    public function setDropout(int $dropout) {
        $this->dropout = $dropout;
    }

    /**
     * @return mixed
     */
    public function getDate() {
        if (!$this->date instanceof DateTime)
            $this->date = new DateTime($this->date);
        return $this->date;
    }

    /**
     * @param mixed $date
     */
    public function setDate($date) {
        $this->date = $date;
    }

}