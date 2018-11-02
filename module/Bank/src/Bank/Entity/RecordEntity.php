<?php

namespace Bank\Entity;

class RecordEntity {

    /**
     * @var int
     */
    protected $recordId = 0;

    /**
     * @var int
     */
    protected $bankId = 0;

    /**
     * @var int
     */
    protected $companyId = 0;

    /**
     * @var \DateTime
     */
    protected $date;

    /**
     * @var float
     */
    protected $amount = 0.00;

    /**
     * @var \DateTime
     */
    protected $updated;

    /**
     * @var \DateTime
     */
    protected $created;

    /**
     * @return int
     */
    public function getRecordId(): int {
        return $this->recordId;
    }

    /**
     * @param int $recordId
     */
    public function setRecordId(int $recordId): void {
        $this->recordId = $recordId;
    }

    /**
     * @return int
     */
    public function getBankId(): int {
        return $this->bankId;
    }

    /**
     * @param int $bankId
     */
    public function setBankId(int $bankId): void {
        $this->bankId = $bankId;
    }

    /**
     * @return int
     */
    public function getCompanyId(): int {
        return $this->companyId;
    }

    /**
     * @param int $companyId
     */
    public function setCompanyId(int $companyId): void {
        $this->companyId = $companyId;
    }

    /**
     * @return \DateTime
     */
    public function getDate(): \DateTime {
        if (!$this->date instanceof \DateTime)
            $this->date = new \DateTime();
        return $this->date;
    }

    /**
     * @param \DateTime $date
     */
    public function setDate(\DateTime $date): void {
        $this->date = $date;
    }

    /**
     * @return float
     */
    public function getAmount(): float {
        return $this->amount;
    }

    /**
     * @param float $amount
     */
    public function setAmount(float $amount): void {
        $this->amount = $amount;
    }

    /**
     * @return \DateTime
     */
    public function getUpdated(): \DateTime {
        if (!$this->updated)
            $this->updated = new \DateTime();
        return $this->updated;
    }

    /**
     * @param \DateTime $updated
     */
    public function setUpdated(\DateTime $updated): void {
        $this->updated = $updated;
    }

    /**
     * @return \DateTime
     */
    public function getCreated(): \DateTime {
        if (!$this->created)
            $this->created = new \DateTime();
        return $this->created;
    }

    /**
     * @param \DateTime $created
     */
    public function setCreated(\DateTime $created): void {
        $this->created = $created;
    }

}