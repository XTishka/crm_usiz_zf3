<?php

namespace Resource\Domain;

class DropoutEntity {

    /**
     * @var int
     */
    protected $dropoutId = 0;

    /**
     * @var int
     */
    protected $providerId = 0;

    /**
     * @var string
     */
    protected $providerName = '';

    /**
     * @var int
     */
    protected $materialId = 0;

    /**
     * @var string
     */
    protected $materialName = '';

    /**
     * @var float
     */
    protected $value = 0.00;

    /**
     * @var \DateTime
     */
    protected $periodBegin;

    /**
     * @var \DateTime
     */
    protected $periodEnd;

    /**
     * @return int
     */
    public function getDropoutId(): int {
        return $this->dropoutId;
    }

    /**
     * @param int $dropoutId
     */
    public function setDropoutId(int $dropoutId): void {
        $this->dropoutId = $dropoutId;
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
    public function setProviderId(int $providerId): void {
        $this->providerId = $providerId;
    }

    /**
     * @return string
     */
    public function getProviderName(): string {
        return $this->providerName;
    }

    /**
     * @param string $providerName
     */
    public function setProviderName(string $providerName): void {
        $this->providerName = $providerName;
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
    public function setMaterialId(int $materialId): void {
        $this->materialId = $materialId;
    }

    /**
     * @return string
     */
    public function getMaterialName(): string {
        return $this->materialName;
    }

    /**
     * @param string $materialName
     */
    public function setMaterialName(string $materialName): void {
        $this->materialName = $materialName;
    }
    
    /**
     * @return float
     */
    public function getValue(): float {
        return $this->value;
    }

    /**
     * @param float $value
     */
    public function setValue(float $value): void {
        $this->value = $value;
    }

    /**
     * @return \DateTime
     */
    public function getPeriodBegin(): \DateTime {
        if (!$this->periodBegin)
            $this->periodBegin = new \DateTime();
        return $this->periodBegin;
    }

    /**
     * @param \DateTime $periodBegin
     */
    public function setPeriodBegin(\DateTime $periodBegin): void {
        $this->periodBegin = $periodBegin;
    }

    /**
     * @return \DateTime
     */
    public function getPeriodEnd(): \DateTime {
        if (!$this->periodEnd)
            $this->periodEnd = new \DateTime();
        return $this->periodEnd;
    }

    /**
     * @param \DateTime $periodEnd
     */
    public function setPeriodEnd(\DateTime $periodEnd): void {
        $this->periodEnd = $periodEnd;
    }

}