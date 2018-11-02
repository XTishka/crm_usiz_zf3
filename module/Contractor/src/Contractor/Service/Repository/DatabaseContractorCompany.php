<?php

namespace Contractor\Service\Repository;

use Contractor\Entity;

class DatabaseContractorCompany extends DatabaseContractorAbstract {

    protected $contractorType = Entity\ContractorAbstract::TYPE_COMPANY;

}