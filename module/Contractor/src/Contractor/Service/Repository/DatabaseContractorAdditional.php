<?php

namespace Contractor\Service\Repository;

use Contractor\Entity;

class DatabaseContractorAdditional extends DatabaseContractorAbstract {

    protected $contractorType = Entity\ContractorAbstract::TYPE_ADDITIONAL;

}