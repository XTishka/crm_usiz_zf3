<?php

namespace Contractor\Service;

class ContractorCorporateManager extends ContractorAbstractManager {

    public function __construct(Repository\DatabaseContractorCorporate $databaseContractorCorporateRepository) {
        parent::__construct($databaseContractorCorporateRepository);
    }

}