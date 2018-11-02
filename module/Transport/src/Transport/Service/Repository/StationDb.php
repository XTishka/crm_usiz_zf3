<?php

namespace Transport\Service\Repository;

use Application\Service\Repository\AbstractDb;
use Transport\Domain\StationEntity;
use Transport\Exception;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\Metadata\Source\Factory as MetadataSourceFactory;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Sql;
use Zend\Paginator;

class StationDb extends AbstractDb {

    const TABLE_STATIONS = 'stations';

    public function fetchTableColumns() {
        $metadata = MetadataSourceFactory::createSourceFromAdapter($this->dbAdapter);
        $columnNames = $metadata->getColumnNames(self::TABLE_STATIONS);
        return $columnNames;
    }

    public function fetchStationsPaginator($stationType = null, $sortColumn = 'station_name', $sortDirection = 'ASC') {
        $sql = new Sql($this->dbAdapter);
        $select = $sql->select(self::TABLE_STATIONS);
        $select->order(sprintf('%s %s', $sortColumn, $sortDirection));
        if ($stationType = trim($stationType))
            $select->where->equalTo('station_type', $stationType);
        $resultSet = new HydratingResultSet($this->hydrator, $this->prototype);
        $paginator = new Paginator\Paginator(new Paginator\Adapter\DbSelect($select, $sql, $resultSet));
        return $paginator;
    }

    public function fetchStationsArray($stationType = null, $columns = null) {
        $sql = new Sql($this->dbAdapter);
        $select = $sql->select(self::TABLE_STATIONS);
        if ($stationType = trim($stationType))
            $select->where->equalTo('station_type', $stationType);
        if (is_array($columns))
            $select->columns($columns);
        $dataSource = $sql->prepareStatementForSqlObject($select)->execute();
        $resultSet = new ResultSet('array');
        $resultSet->initialize($dataSource);
        return $resultSet->toArray();
    }

    public function fetchStationById(int $stationId) {
        $sql = new Sql($this->dbAdapter);
        $select = $sql->select(self::TABLE_STATIONS);
        $select->where->equalTo('station_id', $stationId);
        $dataSource = $sql->prepareStatementForSqlObject($select)->execute();
        $resultSet = new HydratingResultSet($this->hydrator, $this->prototype);
        $resultSet->initialize($dataSource);
        /** @var StationEntity $object */
        $object = $resultSet->current();
        return $object;
    }

    public function saveStation(StationEntity $object) {
        $data = $this->getHydrator()->extract($object);
        unset($data['station_id']);
        $sql = new Sql($this->dbAdapter);
        if ($stationId = $object->getStationId()) {
            unset($data['created']);
            $action = $sql->update(self::TABLE_STATIONS);
            $action->set($data);
            $action->where->equalTo('station_id', $stationId);
        } else {
            $action = $sql->insert(self::TABLE_STATIONS);
            $action->values($data);
        }
        $dataSource = $sql->prepareStatementForSqlObject($action)->execute();
        if (!$dataSource instanceof ResultInterface || !$dataSource->getAffectedRows())
            throw new Exception\ErrorException('Station data was not saved');
        if ($generatedId = $dataSource->getGeneratedValue())
            $object->setStationId($generatedId);
        return $object;
    }

    public function deleteStationById(int $stationId) {
        $sql = new Sql($this->dbAdapter);
        $action = $sql->delete(self::TABLE_STATIONS);
        $action->where->equalTo('station_id', $stationId);
        $dataSource = $sql->prepareStatementForSqlObject($action)->execute();
        if (!$dataSource instanceof ResultInterface || !$dataSource->getAffectedRows())
            throw new Exception\ErrorException('Station data was not deleted');
        return;
    }

}