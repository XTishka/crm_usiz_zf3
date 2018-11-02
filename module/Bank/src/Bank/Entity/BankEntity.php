<?php

namespace Bank\Entity;

class BankEntity {

    /**
     * Unique ID
     * @var int
     */
    protected $bankId = 0;

    /**
     * Bank name
     * @var string
     */
    protected $name = '';

    /**
     * Bank Code
     * @var string
     */
    protected $code = '';

    /**
     * Update date
     * @var \DateTime
     */
    protected $updated;

    /**
     * Create date
     * @var \DateTime
     */
    protected $created;

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
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getCode(): string {
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode(string $code): void {
        $this->code = $code;
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