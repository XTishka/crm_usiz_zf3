<?php

namespace Contractor\Service;

class ContractorPlantManager extends ContractorAbstractManager {

    public function __construct(Repository\DatabaseContractorPlant $databaseContractorPlantRepository) {
        parent::__construct($databaseContractorPlantRepository);
    }

}