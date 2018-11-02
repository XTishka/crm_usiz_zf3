<?php

namespace Contractor\Service;

class ContractorProviderManager extends ContractorAbstractManager {

    public function __construct(Repository\DatabaseContractorProvider $databaseContractorProviderRepository) {
        parent::__construct($databaseContractorProviderRepository);
    }

}