<?php

namespace Contractor\Service\Repository;

use Contractor\Entity;

class DatabaseContractorCarrier extends DatabaseContractorAbstract {

    protected $contractorType = Entity\ContractorAbstract::TYPE_CARRIER;

}