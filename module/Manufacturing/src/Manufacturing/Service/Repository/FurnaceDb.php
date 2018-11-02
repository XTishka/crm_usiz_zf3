<?php

namespace Manufacturing\Service\Repository;

use Application\Service\Repository;
use Contractor\Entity\ContractorPlant;
use Contractor\Service\Repository\DatabaseContractorPlant;
use Manufacturing\Domain\FurnaceEntity;
use Manufacturing\Exception;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\Metadata\Source\Factory as MetadataSourceFactory;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Sql;
use Zend\Paginator;

class FurnaceDb extends Repository\AbstractDb {

    const TABLE_FURNACES = 'furnaces';

    public function fetchTableColumns() {
        $metadata = MetadataSourceFactory::createSourceFromAdapter($this->dbAdapter);
        $columnNames = $metadata->getColumnNames(self::TABLE_FURNACES);
        return $columnNames;
    }

    public function fetchFurnacesPaginator($sortColumn = 'furnace_name', $sortDirection = 'ASC') {
        $sql = new Sql($this->dbAdapter);
        $select = $sql->select(['a' => self::TABLE_FURNACES]);
        $select->join(['b' => DatabaseContractorPlant::TABLE_CONTRACTORS], 'a.plant_id = b.contractor_id', ['plant_name' => 'contractor_name']);
        $select->where->equalTo('b.contractor_type', ContractorPlant::TYPE_PLANT);
        $select->order('plant_id ASC');
        $select->order(sprintf('%s %s', $sortColumn, $sortDirection));
        $resultSet = new HydratingResultSet($this->hydrator, $this->prototype);
        $paginator = new Paginator\Paginator(new Paginator\Adapter\DbSelect($select, $sql, $resultSet));
        return $paginator;
    }

    public function fetchFurnacesArray($columns = null) {
        $sql = new Sql($this->dbAdapter);
        $select = $sql->select(self::TABLE_FURNACES);
        if (is_array($columns))
            $select->columns($columns);
        $dataSource = $sql->prepareStatementForSqlObject($select)->execute();
        $resultSet = new ResultSet('array');
        $resultSet->initialize($dataSource);
        return $resultSet->toArray();
    }

    public function fetchFurnaceById(int $furnaceId) {
        $sql = new Sql($this->dbAdapter);
        $select = $sql->select(self::TABLE_FURNACES);
        $select->where->equalTo('furnace_id', $furnaceId);
        $dataSource = $sql->prepareStatementForSqlObject($select)->execute();
        $resultSet = new HydratingResultSet($this->hydrator, $this->prototype);
        $resultSet->initialize($dataSource);
        /** @var FurnaceEntity $object */
        $object = $resultSet->current();
        return $object;
    }

    public function saveFurnace(FurnaceEntity $object) {
        $data = $this->getHydrator()->extract($object);
        unset($data['furnace_id']);
        $sql = new Sql($this->dbAdapter);
        if ($furnaceId = $object->getFurnaceId()) {
            unset($data['created']);
            $action = $sql->update(self::TABLE_FURNACES);
            $action->set($data);
            $action->where->equalTo('furnace_id', $furnaceId);
        } else {
            $action = $sql->insert(self::TABLE_FURNACES);
            $action->values($data);
        }
        $dataSource = $sql->prepareStatementForSqlObject($action)->execute();
        if (!$dataSource instanceof ResultInterface || !$dataSource->getAffectedRows())
            throw new Exception\ErrorException('Furnace data was not saved');
        if ($generatedId = $dataSource->getGeneratedValue())
            $object->setFurnaceId($generatedId);
        return $object;
    }

    public function deleteFurnaceById(int $furnaceId) {
        $sql = new Sql($this->dbAdapter);
        $action = $sql->delete(self::TABLE_FURNACES);
        $action->where->equalTo('furnace_id', $furnaceId);
        $dataSource = $sql->prepareStatementForSqlObject($action)->execute();
        if (!$dataSource instanceof ResultInterface || !$dataSource->getAffectedRows())
            throw new Exception\ErrorException('Furnace data was not deleted');
        return;
    }

}