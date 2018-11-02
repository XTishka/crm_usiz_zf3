<?php

namespace Contractor\Service\Repository;

use Contractor\Entity;

class DatabaseContractorConsignee extends DatabaseContractorAbstract {

    protected $contractorType = Entity\ContractorAbstract::TYPE_CONSIGNEE;

}