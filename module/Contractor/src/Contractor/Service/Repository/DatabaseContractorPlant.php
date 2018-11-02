<?php

namespace Contractor\Service\Repository;

use Contractor\Entity;

class DatabaseContractorPlant extends DatabaseContractorAbstract {

    protected $contractorType = Entity\ContractorAbstract::TYPE_PLANT;

}