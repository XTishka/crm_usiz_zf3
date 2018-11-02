<?php

namespace Manufacturing\Domain;


class SkipMaterialEntity {

    protected $providerId = 0;

    protected $materialId = 0;

    protected $weight = 0.000;

    protected $dropout = 0.000;

    protected $productWeight = 0.000;

    protected $dropoutWeight = 0.000;

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
     * @return float
     */
    public function getDropout(): float {
        return $this->dropout;
    }

    /**
     * @param float $dropout
     */
    public function setDropout(float $dropout) {
        $this->dropout = $dropout;
    }

    /**
     * @return float
     */
    public function getProductWeight(): float {
        return $this->productWeight;
    }

    /**
     * @param float $productWeight
     */
    public function setProductWeight(float $productWeight): void {
        $this->productWeight = $productWeight;
    }

    /**
     * @return float
     */
    public function getDropoutWeight(): float {
        return $this->dropoutWeight;
    }

    /**
     * @param float $dropoutWeight
     */
    public function setDropoutWeight(float $dropoutWeight): void {
        $this->dropoutWeight = $dropoutWeight;
    }

}