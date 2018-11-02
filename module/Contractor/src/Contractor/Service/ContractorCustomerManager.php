<?php

namespace Contractor\Service;

class ContractorCustomerManager extends ContractorAbstractManager {

    public function __construct(Repository\DatabaseContractorCustomer $databaseContractorCustomerRepository) {
        parent::__construct($databaseContractorCustomerRepository);
    }

}