<?php

namespace Contractor\Service\Repository;

use Contractor\Entity;

class DatabaseContractorCorporate extends DatabaseContractorAbstract {

    protected $contractorType = Entity\ContractorAbstract::TYPE_CORPORATE;

}