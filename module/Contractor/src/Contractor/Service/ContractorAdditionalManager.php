<?php

namespace Contractor\Service;

class ContractorAdditionalManager extends ContractorAbstractManager {

    public function __construct(Repository\DatabaseContractorAdditional $databaseContractorAdditionalRepository) {
        parent::__construct($databaseContractorAdditionalRepository);
    }

}