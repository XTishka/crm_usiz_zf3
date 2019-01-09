<?php

namespace Contractor\Service\Repository;

use Contractor\Entity;
use Contractor\Service\Repository\DatabaseContractorAbstract;
use Zend\Db\Metadata\Source\Factory as MetadataSourceFactory;

class DatabaseContractorCommon extends DatabaseContractorAbstract {

    const TABLE_CARRIERS = 'contractors';

//    protected $contractorType = Entity\ContractorAbstract::TYPE_COMMON;

    public function fetchTableColumns() {
        $metadata = MetadataSourceFactory::createSourceFromAdapter($this->adapter);
        $columnNames = $metadata->getColumnNames(self::TABLE_CARRIERS);
        return $columnNames;
    }

}