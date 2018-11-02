<?php

namespace Manufacturing\Service\Repository;

use Application\Service\Repository;
use Contractor\Entity\ContractorPlant;
use Contractor\Service\Repository\DatabaseContractorPlant;
use Manufacturing\Domain\WarehouseEntity;
use Manufacturing\Exception;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\Metadata\Source\Factory as MetadataSourceFactory;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Sql;
use Zend\Paginator;

class WarehouseDb extends Repository\AbstractDb {

    const TABLE_WAREHOUSES = 'warehouses';

    public function fetchTableColumns() {
        $metadata = MetadataSourceFactory::createSourceFromAdapter($this->dbAdapter);
        $columnNames = $metadata->getColumnNames(self::TABLE_WAREHOUSES);
        return $columnNames;
    }

    public function fetchWarehousesPaginator($sortColumn = 'warehouse_name', $sortDirection = 'ASC') {
        $sql = new Sql($this->dbAdapter);
        $select = $sql->select(['a' => self::TABLE_WAREHOUSES]);
        $select->join(['b' => DatabaseContractorPlant::TABLE_CONTRACTORS], 'a.plant_id = b.contractor_id', ['plant_name' => 'contractor_name']);
        $select->where->equalTo('b.contractor_type', ContractorPlant::TYPE_PLANT);
        $select->order(sprintf('%s %s', $sortColumn, $sortDirection));
        $resultSet = new HydratingResultSet($this->hydrator, $this->prototype);
        $paginator = new Paginator\Paginator(new Paginator\Adapter\DbSelect($select, $sql, $resultSet));
        return $paginator;
    }

    public function fetchWarehousesArray($columns = null) {
        $sql = new Sql($this->dbAdapter);
        $select = $sql->select(self::TABLE_WAREHOUSES);
        if (is_array($columns))
            $select->columns($columns);
        $dataSource = $sql->prepareStatementForSqlObject($select)->execute();
        $resultSet = new ResultSet('array');
        $resultSet->initialize($dataSource);
        return $resultSet->toArray();
    }

    public function fetchWarehouseById(int $warehouseId) {
        $sql = new Sql($this->dbAdapter);
        $select = $sql->select(self::TABLE_WAREHOUSES);
        $select->where->equalTo('warehouse_id', $warehouseId);
        $dataSource = $sql->prepareStatementForSqlObject($select)->execute();
        $resultSet = new HydratingResultSet($this->hydrator, $this->prototype);
        $resultSet->initialize($dataSource);
        /** @var WarehouseEntity $object */
        $object = $resultSet->current();
        return $object;
    }

    public function saveWarehouse(WarehouseEntity $object) {
        $data = $this->getHydrator()->extract($object);
        unset($data['warehouse_id']);
        $sql = new Sql($this->dbAdapter);
        if ($warehouseId = $object->getWarehouseId()) {
            unset($data['created']);
            $action = $sql->update(self::TABLE_WAREHOUSES);
            $action->set($data);
            $action->where->equalTo('warehouse_id', $warehouseId);
        } else {
            $action = $sql->insert(self::TABLE_WAREHOUSES);
            $action->values($data);
        }
        $dataSource = $sql->prepareStatementForSqlObject($action)->execute();
        if (!$dataSource instanceof ResultInterface || !$dataSource->getAffectedRows())
            throw new Exception\ErrorException('Warehouse data was not saved');
        if ($generatedId = $dataSource->getGeneratedValue())
            $object->setWarehouseId($generatedId);
        return $object;
    }

    public function deleteWarehouseById(int $warehouseId) {
        $sql = new Sql($this->dbAdapter);
        $action = $sql->delete(self::TABLE_WAREHOUSES);
        $action->where->equalTo('warehouse_id', $warehouseId);
        $dataSource = $sql->prepareStatementForSqlObject($action)->execute();
        if (!$dataSource instanceof ResultInterface || !$dataSource->getAffectedRows())
            throw new Exception\ErrorException('Warehouse data was not deleted');
        return;
    }

}