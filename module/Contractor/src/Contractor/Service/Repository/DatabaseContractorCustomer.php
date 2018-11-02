<?php

namespace Contractor\Service\Repository;

use Contractor\Entity;

class DatabaseContractorCustomer extends DatabaseContractorAbstract {

    protected $contractorType = Entity\ContractorAbstract::TYPE_CUSTOMER;

}