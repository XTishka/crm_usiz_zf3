<?php

namespace Document\Service\Repository;

use Application\Service\Repository;
use Transport\Service\Repository\CarrierDb;
use Document\Domain\PurchaseWagonEntity as WagonEntity;
use Document\Exception;
use Resource\Service\Repository\MaterialDb;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\Metadata\Source\Factory as MetadataSourceFactory;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Join;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Where;
use Zend\Hydrator\ClassMethods;
use Zend\Hydrator\Strategy\DateTimeFormatterStrategy;
use Zend\Paginator;

class PurchaseWagonDb extends Repository\AbstractDb {

    const TABLE_PURCHASE_WAGONS = 'purchase_wagons';

    public function fetchTableColumns() {
        $metadata = MetadataSourceFactory::createSourceFromAdapter($this->dbAdapter);
        $columnNames = $metadata->getColumnNames(self::TABLE_PURCHASE_WAGONS);
        return $columnNames;
    }

    public function fetchExpectedMaterialWeight(int $companyId, $date = null) {
        if (!$date instanceof \DateTime)
            $date = new \DateTime($date);

        $sql = new Sql($this->dbAdapter);
        $select = $sql->select(['a' => self::TABLE_PURCHASE_WAGONS]);
        $select->join(['b' => PurchaseContractDb::TABLE_PURCHASE_CONTRACTS], 'a.contract_id = b.contract_id', []);
        $select->join(['c' => MaterialDb::TABLE_MATERIALS], 'b.material_id = c.material_id', ['material_name']);
        $select->columns([
            'weight'   => new Expression('SUM(loading_weight)'),
            'amount'   => new Expression('SUM(material_price + delivery_price)'),
        ]);
        $select->where->equalTo('b.company_id', $companyId);
        $select->where->nest()
            ->lessThan('a.loading_date', 'a.unloading_date', Where::TYPE_IDENTIFIER, Where::TYPE_IDENTIFIER)
            ->or
            ->isNull('a.unloading_date');
        $select->where->lessThanOrEqualTo('a.loading_date', $date->format('Y-m-d'));
        $select->group(['c.material_id']);

        //echo $select->getSqlString($this->dbAdapter->platform);

        $dataSource = $sql->prepareStatementForSqlObject($select)->execute();
        $resultSet = new ResultSet('array');
        $resultSet->initialize($dataSource);
        $data = $resultSet->toArray();

        //echo '<pre>'; print_r($data); exit;

        return $data;

    }

    /**
     * @param int $companyId
     * @return array
     */
    public function fetchExpectedWagonsStatistic(int $companyId) {
        $sql = new Sql($this->dbAdapter);
        $select = $sql->select(['a' => self::TABLE_PURCHASE_WAGONS]);
        $select->join(['b' => PurchaseContractDb::TABLE_PURCHASE_CONTRACTS], 'a.contract_id = b.contract_id', ['contract_id', 'contract_number']);
        $select->columns([
            'weight' => new Expression('SUM(loading_weight)'),
            'amount' => new Expression('SUM(material_price + delivery_price)'),
        ]);
        $select->where->isNull('a.unloading_date');
        $select->where->equalTo('b.company_id', $companyId);
        $select->group(['b.contract_id']);

        $dataSource = $sql->prepareStatementForSqlObject($select)->execute();
        $resultSet = new ResultSet('array');
        $resultSet->initialize($dataSource);
        return $resultSet->toArray();
    }

    /**
     * @deprecated
     * @param null $contractId
     * @param null $filterData
     * @return Paginator\Paginator
     */
    public function fetchWagonsPaginator($contractId = null, $filterData = null) {
        $sql = new Sql($this->dbAdapter);
        $select = $sql->select(['a' => self::TABLE_PURCHASE_WAGONS]);
        $select->join(['b' => CarrierDb::TABLE_CARRIERS], 'a.carrier_id = b.carrier_id', ['carrier_name'], 'left');
        $select->order('a.loading_date DESC');
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
            if (key_exists('status', $filterData) && trim($filterData['status'])) {
                $select->where->equalTo('status', trim($filterData['status']));
            }
            if (key_exists('conditions', $filterData) && trim($filterData['conditions'])) {
                $select->where->equalTo('conditions', trim($filterData['conditions']));
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
            if (key_exists('unloading_date', $filterData) && trim($filterData['unloading_date'])) {
                preg_match('/(\d{2}\.\d{2}\.\d{4})(?: - (\d{2}\.\d{2}\.\d{4}))?/', $filterData['unloading_date'], $matches);
                if (2 == count($matches)) {
                    $select->where->expression('unloading_date = STR_TO_DATE(?, "%d.%m.%Y")', $matches[1]);
                } elseif (3 == count($matches)) {
                    array_shift($matches);
                    $select->where->expression('unloading_date BETWEEN STR_TO_DATE(?, "%d.%m.%Y") AND STR_TO_DATE(?, "%d.%m.%Y")', $matches);
                }
            }
        }
        $resultSet = new HydratingResultSet($this->hydrator, $this->prototype);
        $paginator = new Paginator\Paginator(new Paginator\Adapter\DbSelect($select, $sql, $resultSet));
        return $paginator;
    }

    public function fetchWagonsArray($contractId, $filterData = null) {
        $sql = new Sql($this->dbAdapter);
        $select = $sql->select(['a' => self::TABLE_PURCHASE_WAGONS]);
        $select->join(['b' => CarrierDb::TABLE_CARRIERS], 'a.carrier_id = b.carrier_id', [], Join::JOIN_LEFT);
        $select->columns([
            'wagon_number',
            'conditions',
            'carrier' => 'b.carrier_name',
            'status',
            'material_price',
            'delivery_price',
            'loading_weight',
            'loading_date',
            'unloading_weight',
            'unloading_date',
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
            if (key_exists('conditions', $filterData) && trim($filterData['conditions'])) {
                $select->where->equalTo('conditions', trim($filterData['conditions']));
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
            if (key_exists('unloading_date', $filterData) && trim($filterData['unloading_date'])) {
                preg_match('/(\d{2}\.\d{2}\.\d{4})(?: - (\d{2}\.\d{2}\.\d{4}))?/', $filterData['unloading_date'], $matches);
                if (2 == count($matches)) {
                    $select->where->expression('unloading_date = STR_TO_DATE(?, "%d.%m.%Y")', $matches[1]);
                } elseif (3 == count($matches)) {
                    array_shift($matches);
                    $select->where->expression('unloading_date BETWEEN STR_TO_DATE(?, "%d.%m.%Y") AND STR_TO_DATE(?, "%d.%m.%Y")', $matches);
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
        $select = $sql->select(['a' => self::TABLE_PURCHASE_WAGONS]);
        $select->where->equalTo('wagon_id', $wagonId);
        $dataSource = $sql->prepareStatementForSqlObject($select)->execute();
        $resultSet = new HydratingResultSet($this->hydrator, $this->prototype);
        $resultSet->initialize($dataSource);
        /** @var WagonEntity $object */
        $object = $resultSet->current();
        return $object;
    }

    /**
     * @param int $rateId
     * @return HydratingResultSet
     */
    public function fetchWagonsByRateId(int $rateId) {
        $sql = new Sql($this->dbAdapter);
        $select = $sql->select(['a' => self::TABLE_PURCHASE_WAGONS]);
        $select->where->equalTo('rate_id', $rateId);
        $dataSource = $sql->prepareStatementForSqlObject($select)->execute();
        $resultSet = new HydratingResultSet($this->hydrator, $this->prototype);
        $resultSet->initialize($dataSource);
        return $resultSet;
    }

    /**
     * @param WagonEntity $object
     * @return WagonEntity
     * @throws Exception\ErrorException
     */
    public function saveWagon(WagonEntity $object) {

        /** @var ClassMethods $hydrator */
        $hydrator = $this->getHydrator();
        $hydrator->addStrategy('loading_date', new DateTimeFormatterStrategy('Y-m-d'));
        $hydrator->addStrategy('unloading_date', new DateTimeFormatterStrategy('Y-m-d'));

        $data = $hydrator->extract($object);

        unset($data['wagon_id']);
        $sql = new Sql($this->dbAdapter);
        if ($wagonId = $object->getWagonId()) {
            unset($data['created']);
            $action = $sql->update(self::TABLE_PURCHASE_WAGONS);
            $action->set($data);
            $action->where->equalTo('wagon_id', $wagonId);
        } else {
            $action = $sql->insert(self::TABLE_PURCHASE_WAGONS);
            $action->values($data);
        }
        $dataSource = $sql->prepareStatementForSqlObject($action)->execute();
        if (!$dataSource instanceof ResultInterface || !$dataSource->getAffectedRows())
            throw new Exception\ErrorException('Wagon data was not saved.');
        if ($generatedId = $dataSource->getGeneratedValue())
            $object->setWagonId($generatedId);
        return $object;
    }

    /**
     * @param int $wagonId
     * @throws Exception\ErrorException
     */
    public function deleteWagonById(int $wagonId) {
        $sql = new Sql($this->dbAdapter);
        $action = $sql->delete(self::TABLE_PURCHASE_WAGONS);
        $action->where->equalTo('wagon_id', $wagonId);
        $dataSource = $sql->prepareStatementForSqlObject($action)->execute();
        if (!$dataSource->getAffectedRows())
            throw new Exception\ErrorException('Wagon data was not deleted');
        return;
    }

}