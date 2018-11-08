<?php

namespace Transport\Service\Repository;

use Application\Service\Repository\AbstractDb;
use Transport\Domain\CarrierEntity;
use Transport\Exception;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\Metadata\Source\Factory as MetadataSourceFactory;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Sql;
use Zend\Paginator;

class CarrierDb extends AbstractDb {

    const TABLE_CARRIERS = 'carriers';

    public function fetchTableColumns() {
        $metadata = MetadataSourceFactory::createSourceFromAdapter($this->dbAdapter);
        $columnNames = $metadata->getColumnNames(self::TABLE_CARRIERS);
        return $columnNames;
    }

    public function fetchCarriersPaginator($carrierType = null, $sortColumn = 'carrier_name', $sortDirection = 'ASC', $query = null) {
        $sql = new Sql($this->dbAdapter);
        $select = $sql->select(self::TABLE_CARRIERS);
        $select->order(sprintf('%s %s', $sortColumn, $sortDirection));
        if ($carrierType = trim($carrierType))
            $select->where->equalTo('carrier_type', $carrierType);

        if (is_array($query)) {
            if (isset($query['carrier_name']) && trim($query['carrier_name'])) {
                $select->where->like('carrier_name', sprintf('%%%s%%', $query['carrier_name']));
            }
            if (isset($query['register_code']) && trim($query['register_code'])) {
                $select->where->like('register_code', sprintf('%%%s%%', $query['register_code']));
            }
        }
        //echo $select->getSqlString($this->dbAdapter->platform);
        $resultSet = new HydratingResultSet($this->hydrator, $this->prototype);
        $paginator = new Paginator\Paginator(new Paginator\Adapter\DbSelect($select, $sql, $resultSet));
        return $paginator;
    }

    public function fetchCarriersArray($carrierType = null, $columns = null) {
        $sql = new Sql($this->dbAdapter);
        $select = $sql->select(self::TABLE_CARRIERS);
        if ($carrierType = trim($carrierType))
            $select->where->equalTo('carrier_type', $carrierType);
        if (is_array($columns))
            $select->columns($columns);
        $dataSource = $sql->prepareStatementForSqlObject($select)->execute();
        $resultSet = new ResultSet('array');
        $resultSet->initialize($dataSource);
        return $resultSet->toArray();
    }

    public function fetchCarrierValueOptionsByParams(array $params = null) {
        $sql = new Sql($this->dbAdapter);
        $select = $sql->select(['a' => self::TABLE_CARRIERS]);
        if (is_array($params)) {
            if (key_exists('carrier_type', $params))
                $select->where->equalTo('carrier_type', $params['carrier_type']);
            if (key_exists('country', $params))
                $select->where->equalTo('country', $params['country']);
        }
        $dataSource = $sql->prepareStatementForSqlObject($select)->execute();
        $resultSet = new ResultSet('array');
        $resultSet->initialize($dataSource);
        return $resultSet->toArray();
    }

    public function fetchCarrierById(int $carrierId) {
        $sql = new Sql($this->dbAdapter);
        $select = $sql->select(self::TABLE_CARRIERS);
        $select->where->equalTo('carrier_id', $carrierId);
        $dataSource = $sql->prepareStatementForSqlObject($select)->execute();
        $resultSet = new HydratingResultSet($this->hydrator, $this->prototype);
        $resultSet->initialize($dataSource);
        /** @var CarrierEntity $object */
        $object = $resultSet->current();
        return $object;
    }

    public function saveCarrier(CarrierEntity $object) {
        $data = $this->getHydrator()->extract($object);
        unset($data['carrier_id']);
        $sql = new Sql($this->dbAdapter);
        if ($carrierId = $object->getCarrierId()) {
            unset($data['created']);
            $action = $sql->update(self::TABLE_CARRIERS);
            $action->set($data);
            $action->where->equalTo('carrier_id', $carrierId);
        } else {
            $action = $sql->insert(self::TABLE_CARRIERS);
            $action->values($data);
        }
        $dataSource = $sql->prepareStatementForSqlObject($action)->execute();
        if (!$dataSource instanceof ResultInterface || !$dataSource->getAffectedRows())
            throw new Exception\ErrorException('Carrier data was not saved');
        if ($generatedId = $dataSource->getGeneratedValue())
            $object->setCarrierId($generatedId);
        return $object;
    }

    public function deleteCarrierById(int $carrierId) {
        $sql = new Sql($this->dbAdapter);
        $action = $sql->delete(self::TABLE_CARRIERS);
        $action->where->equalTo('carrier_id', $carrierId);
        $dataSource = $sql->prepareStatementForSqlObject($action)->execute();
        if (!$dataSource instanceof ResultInterface || !$dataSource->getAffectedRows())
            throw new Exception\ErrorException('Carrier data was not deleted');
        return;
    }

}