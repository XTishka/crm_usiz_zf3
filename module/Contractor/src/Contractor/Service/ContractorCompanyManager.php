<?php

namespace Contractor\Service;

class ContractorCompanyManager extends ContractorAbstractManager {

    public function __construct(Repository\DatabaseContractorCompany $databaseContractorCompanyRepository) {
        parent::__construct($databaseContractorCompanyRepository);
    }

}