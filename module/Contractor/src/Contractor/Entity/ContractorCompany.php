<?php

namespace Contractor\Entity;

class ContractorCompany extends ContractorAbstract {

    protected $contractorType = self::TYPE_COMPANY;

    protected $plantId = 0;

    /**
     * @return int
     */
    public function getPlantId(): int {
        return (int)$this->plantId;
    }

    /**
     * @param int $plantId
     */
    public function setPlantId($plantId): void {
        $this->plantId = $plantId;
    }

}