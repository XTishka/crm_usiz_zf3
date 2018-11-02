<?php

namespace Document\Service\Repository;

use Application\Service\Repository;
use Transport\Service\Repository\CarrierDb;
use Document\Domain\SaleWagonEntity as WagonEntity;
use Document\Exception;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\Metadata\Source\Factory as MetadataSourceFactory;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Join;
use Zend\Db\Sql\Sql;
use Zend\Paginator;

class SaleWagonDb extends Repository\AbstractDb {

    const TABLE_SALE_WAGONS = 'sale_wagons';

    public function fetchTableColumns() {
        $metadata = MetadataSourceFactory::createSourceFromAdapter($this->dbAdapter);
        $columnNames = $metadata->getColumnNames(self::TABLE_SALE_WAGONS);
        return $columnNames;
    }

    public function fetchWagonsPaginator($contractId = null, $filterData = null) {
        $sql = new Sql($this->dbAdapter);
        $select = $sql->select(['a' => self::TABLE_SALE_WAGONS]);
        $select->join(['b' => CarrierDb::TABLE_CARRIERS], 'a.carrier_id = b.carrier_id', ['carrier_name'], Join::JOIN_LEFT);
        $select->order(['a.loading_date DESC', 'a.unloading_date DESC']);
        $select->group('a.wagon_id');
        if ($contractId = intval($contractId))
            $select->where->equalTo('contract_id', $contractId);

        if (is_array($filterData)) {
            if (key_exists('number', $filterData) && trim($filterData['number'])) {
                $select->where->like('a.wagon_number', trim('%' . $filterData['number'] . '%'));
            }
            if (key_exists('carrier', $filterData) && is_numeric($filterData['carrier'])) {
                $select->where->equalTo('a.carrier_id', intval($filterData['carrier']));
            }
            if (key_exists('loading_date', $filterData) && trim($filterData['loading_date'])) {
                preg_match('/(\d{2}\.\d{2}\.\d{4})(?: - (\d{2}\.\d{2}\.\d{4}))?/', $filterData['loading_date'], $matches);
                if (2 == count($matches)) {
                    $select->where->expression('loading_date = STR_TO_DATE(?, "%d.%m.%Y")', $matches[1]);
                } elseif (3 == count($matches)) {
                    array_shift($matches);
                    $select->where->expression('loading_date BETWEEN STR_TO_DATE(?, "%d.%m.%Y") AND STR_TO_DATE(?, "%d.%m.%Y")', $matches);
                }
            }
        }

        $resultSet = new HydratingResultSet($this->hydrator, $this->prototype);
        $paginator = new Paginator\Paginator(new Paginator\Adapter\DbSelect($select, $sql, $resultSet));
        return $paginator;
    }

    public function fetchWagonsArray($contractId, $filterData = null) {
        $sql = new Sql($this->dbAdapter);
        $select = $sql->select(['a' => self::TABLE_SALE_WAGONS]);
        $select->join(['b' => CarrierDb::TABLE_CARRIERS], 'a.carrier_id = b.carrier_id', [], Join::JOIN_LEFT);
        $select->columns([
            'wagon_number',
            'carrier' => 'b.carrier_name',
            'status',
            'product_price',
            'loading_weight',
            'loading_date',
        ], false);

        $select->where->equalTo('a.contract_id', $contractId);
        $select->group('wagon_id');
        if (is_array($filterData)) {
            if (key_exists('carrier', $filterData) && is_numeric($filterData['carrier'])) {
                $select->where->equalTo('a.carrier_id', intval($filterData['carrier']));
            }
            if (key_exists('status', $filterData) && trim($filterData['status'])) {
                $select->where->equalTo('status', trim($filterData['status']));
            }
            if (key_exists('loading_date', $filterData) && trim($filterData['loading_date'])) {
                preg_match('/(\d{2}\.\d{2}\.\d{4})(?: - (\d{2}\.\d{2}\.\d{4}))?/', $filterData['loading_date'], $matches);
                if (2 == count($matches)) {
                    $select->where->expression('loading_date = STR_TO_DATE(?, "%d.%m.%Y")', $matches[1]);
                } elseif (3 == count($matches)) {
                    array_shift($matches);
                    $select->where->expression('loading_date BETWEEN STR_TO_DATE(?, "%d.%m.%Y") AND STR_TO_DATE(?, "%d.%m.%Y")', $matches);
                }
            }
        }


        $dataSource = $sql->prepareStatementForSqlObject($select)->execute();
        $resultSet = new ResultSet('array');
        $resultSet->initialize($dataSource);
        return $resultSet->toArray();
    }

    public function fetchWagonById(int $wagonId) {
        $sql = new Sql($this->dbAdapter);
        $select = $sql->select(['a' => self::TABLE_SALE_WAGONS]);
        $select->where->equalTo('wagon_id', $wagonId);
        $dataSource = $sql->prepareStatementForSqlObject($select)->execute();
        $resultSet = new HydratingResultSet($this->hydrator, $this->prototype);
        $resultSet->initialize($dataSource);
        /** @var WagonEntity $object */
        $object = $resultSet->current();
        return $object;
    }

    public function saveWagon(WagonEntity $object) {
        $data = $this->getHydrator()->extract($object);

        if (preg_match('/^\d{2}\.\d{2}\.\d{4}$/', $data['loading_date'])) {
            $data['loading_date'] = $object->getLoadingDate()->format('Y-m-d');
        }

        unset($data['wagon_id']);
        $sql = new Sql($this->dbAdapter);
        if ($wagonId = $object->getWagonId()) {
            unset($data['created']);
            $action = $sql->update(self::TABLE_SALE_WAGONS);
            $action->set($data);
            $action->where->equalTo('wagon_id', $wagonId);
        } else {
            $action = $sql->insert(self::TABLE_SALE_WAGONS);
            $action->values($data);
        }
        $dataSource = $sql->prepareStatementForSqlObject($action)->execute();
        if (!$dataSource instanceof ResultInterface || !$dataSource->getAffectedRows())
            throw new Exception\ErrorException('Wagon data was not saved');
        if ($generatedId = $dataSource->getGeneratedValue())
            $object->setWagonId($generatedId);
        return $object;
    }

    public function deleteWagonById(int $wagonId) {
        $sql = new Sql($this->dbAdapter);
        $action = $sql->delete(self::TABLE_SALE_WAGONS);
        $action->where->equalTo('wagon_id', $wagonId);
        $dataSource = $sql->prepareStatementForSqlObject($action)->execute();
        if (!$dataSource instanceof ResultInterface || !$dataSource->getAffectedRows())
            throw new Exception\ErrorException('Wagon data was not deleted');
        return;
    }

}