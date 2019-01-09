<?php

namespace Contractor\Service;

class ContractorCommonManager extends ContractorAbstractManager {

    public function __construct(Repository\DatabaseContractorCommon $databaseContractorCommonRepository) {
        parent::__construct($databaseContractorCommonRepository);
    }

}