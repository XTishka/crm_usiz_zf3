<?php

namespace Contractor\Service\Repository;

use Contractor\Entity;

class DatabaseContractorCommon extends DatabaseContractorAbstract {

    protected $contractorType = Entity\ContractorAbstract::TYPE_COMMON;

}