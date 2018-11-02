<?php

namespace Contractor\Service\Repository;

use Contractor\Entity;

class DatabaseContractorProvider extends DatabaseContractorAbstract {

    protected $contractorType = Entity\ContractorAbstract::TYPE_PROVIDER;

}