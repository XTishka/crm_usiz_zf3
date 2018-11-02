<?php

namespace Contractor\Service;

class ContractorCarrierManager extends ContractorAbstractManager {

    public function __construct(Repository\DatabaseContractorCarrier $databaseContractorCarrierRepository) {
        parent::__construct($databaseContractorCarrierRepository);
    }

}