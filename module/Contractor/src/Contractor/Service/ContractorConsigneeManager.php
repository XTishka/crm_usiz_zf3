<?php

namespace Contractor\Service;

class ContractorConsigneeManager extends ContractorAbstractManager {

    public function __construct(Repository\DatabaseContractorConsignee $databaseContractorConsigneeRepository) {
        parent::__construct($databaseContractorConsigneeRepository);
    }

}