<?php

namespace Manufacturing\Service\Repository;

use Application\Service\Repository\AbstractDb;
use Contractor\Service\Repository\DatabaseContractorAbstract;
use Contractor\Service\Repository\DatabaseContractorProvider;
use Manufacturing\Domain\WarehouseEntity;
use Manufacturing\Domain\WarehouseLogEntity;
use Manufacturing\Exception;
use Resource\Service\DropoutManager;
use Resource\Service\Repository\MaterialDb;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Join;
use Zend\Db\Sql\Sql;

class WarehouseLogDb extends AbstractDb {

    const TABLE_WAREHOUSES_LOGS = 'warehouses_logs';

    /**
     * @param int $plantId
     * @param null $date
     * @return array
     * @throws Exception\ErrorException
     */
    public function fetchTotalMaterialBalances(int $plantId, $date = null) {
        if (!$date instanceof \DateTime)
            $date = new \DateTime($date);

        $sql = new Sql($this->dbAdapter);
        $select = $sql->select(['a' => self::TABLE_WAREHOUSES_LOGS]);
        $select->columns([
            'log_id',
            'contractor_id',
            'warehouse_id',
            'resource_id',
            'direction',
            'amount' => 'resource_price',
            'weight' => 'resource_weight'
        ]);
        $select->join(['b' => WarehouseDb::TABLE_WAREHOUSES], 'a.warehouse_id = b.warehouse_id', ['warehouse_name']);
        $select->join(['c' => MaterialDb::TABLE_MATERIALS], 'a.resource_id = c.material_id', ['material_id', 'material_name']);
        $select->join(['d' => DatabaseContractorProvider::TABLE_CONTRACTORS], 'a.contractor_id = d.contractor_id', ['contractor' => 'contractor_name']);

        /*
        $select->columns([
            'amount' => new Expression('(
                (SELECT COALESCE(SUM(resource_price), 0) FROM warehouses_logs WHERE warehouse_id = a.warehouse_id AND a.resource_id = resource_id AND direction = ? AND created <= :created LIMIT 1) -
                (SELECT COALESCE(SUM(resource_price), 0) FROM warehouses_logs WHERE warehouse_id = a.warehouse_id AND a.resource_id = resource_id AND direction = ? AND created <= :created LIMIT 1))',
                [WarehouseLogEntity::DIRECTION_INPUT, WarehouseLogEntity::DIRECTION_OUTPUT]),
            'weight' => new Expression('(
                (SELECT COALESCE(SUM(resource_weight), 0) FROM warehouses_logs WHERE warehouse_id = a.warehouse_id AND a.resource_id = resource_id AND direction = ? AND created <= :created LIMIT 1) -
                (SELECT COALESCE(SUM(resource_weight), 0) FROM warehouses_logs WHERE warehouse_id = a.warehouse_id AND a.resource_id = resource_id AND direction = ? AND created <= :created LIMIT 1))',
                [WarehouseLogEntity::DIRECTION_INPUT, WarehouseLogEntity::DIRECTION_OUTPUT]),
        ]);
        */

        $select->where->lessThanOrEqualTo('a.created', $date->format('Y-m-d'));
        $select->where->equalTo('b.warehouse_type', WarehouseEntity::TYPE_MATERIAL_WAREHOUSE);
        $select->where->equalTo('b.plant_id', $plantId);
        //$select->group('c.material_id');

        //$select->limit(1);

        $dataSource = $sql->prepareStatementForSqlObject($select)->execute();

        if (!$dataSource instanceof ResultInterface)
            throw new Exception\ErrorException('The remains of the warehouse were not received.');

        $resultSet = new ResultSet(ResultSet::TYPE_ARRAYOBJECT);
        $resultSet->initialize($dataSource);

        $data = [];
        foreach ($resultSet as $item) {
            $index = \join([
                $item['resource_id'],
                $item['contractor_id'],
                $item['warehouse_id'],
            ]);
            if (array_key_exists($index, $data)) {
                if ($item['direction'] == 'input') {
                    $data[$index]->weight = bcadd($data[$index]->weight, $item->weight, 4);
                    $data[$index]->amount = bcadd($data[$index]->amount, $item->amount, 4);

                } else {
                    $data[$index]->weight = bcsub($data[$index]->weight, $item->weight, 4);
                    $data[$index]->amount = bcsub($data[$index]->amount, $item->amount, 4);
                }
            } else {
                $data[$index] = $item;
            }
        }

        return $data;
    }

    /**
     * @param int $plantId
     * @return array
     * @throws Exception\ErrorException
     */
    public function fetchMaterialBalances(int $plantId) {
        $sql = new Sql($this->dbAdapter);
        $select = $sql->select(['a' => self::TABLE_WAREHOUSES_LOGS]);
        $select->columns([
            'log_id',
            'contractor_id',
            'warehouse_id',
            'resource_id',
            'direction',
            'price'  => 'resource_price',
            'weight' => 'resource_weight'
        ]);
        $select->join(['b' => WarehouseDb::TABLE_WAREHOUSES], 'a.warehouse_id = b.warehouse_id', ['warehouse_name']);
        $select->join(['c' => DatabaseContractorAbstract::TABLE_CONTRACTORS], 'a.contractor_id = c.contractor_id', ['contractor_name']);
        $select->join(['d' => MaterialDb::TABLE_MATERIALS], 'a.resource_id = d.material_id', ['material_name']);
        $select->join(['e' => DropoutManager::TABLE_DROPOUTS], 'd.material_id = e.material_id', ['dropout' => new Expression('IFNULL(value, 0)')], Join::JOIN_LEFT);

        $select->where->equalTo('b.warehouse_type', WarehouseEntity::TYPE_MATERIAL_WAREHOUSE);
        $select->where->equalTo('b.plant_id', $plantId);
        $select->group('a.log_id');
        $select->order('material_name ASC');

        //echo $select->getSqlString($this->dbAdapter->platform);

        $dataSource = $sql->prepareStatementForSqlObject($select)->execute();

        if (!$dataSource instanceof ResultInterface)
            throw new Exception\ErrorException('The remains of the warehouse were not received.');

        $resultSet = new ResultSet(ResultSet::TYPE_ARRAY);
        $resultSet->initialize($dataSource);

        $data = [];

        foreach ($resultSet as $item) {

            $index = join([$item['resource_id'], $item['contractor_id'], $item['warehouse_id']]);

            if (!array_key_exists($index, $data)) {
                $data[$index] = $item;
                $data[$index]['weight'] = 0;
                $data[$index]['price'] = 0;
            }

            if ($item['direction'] == 'input') {
                $data[$index]['weight'] = bcadd($data[$index]['weight'], $item['weight'], 4);
                $data[$index]['price'] = bcadd($data[$index]['price'], $item['price'], 4);

            } else {
                $data[$index]['weight'] = bcsub($data[$index]['weight'], $item['weight'], 4);
                $data[$index]['price'] = bcsub($data[$index]['price'], $item['price'], 4);
            }

        }
        //exit;
        return $data;
    }

    public function fetchProductBalances(int $plantId) {
        $sql = new Sql($this->dbAdapter);
        $select = $sql->select(['a' => self::TABLE_WAREHOUSES_LOGS]);
        $select->join(['b' => WarehouseDb::TABLE_WAREHOUSES], 'a.warehouse_id = b.warehouse_id', ['warehouse_name']);
        $select->columns([
            'weight' => new Expression('(
                (SELECT COALESCE(SUM(resource_weight), 0) FROM warehouses_logs WHERE warehouse_id = a.warehouse_id AND direction = ?) -
                (SELECT COALESCE(SUM(resource_weight), 0) FROM warehouses_logs WHERE warehouse_id = a.warehouse_id AND direction = ?))',
                [WarehouseLogEntity::DIRECTION_INPUT, WarehouseLogEntity::DIRECTION_OUTPUT]),
        ]);
        $select->where->equalTo('b.plant_id', $plantId);
        $select->where->equalTo('b.warehouse_type', WarehouseEntity::TYPE_PRODUCT_WAREHOUSE);
        $select->group(['a.warehouse_id']);

        //echo $select->getSqlString($this->dbAdapter->platform);

        $dataSource = $sql->prepareStatementForSqlObject($select)->execute();

        if (!$dataSource instanceof ResultInterface)
            throw new Exception\ErrorException('The remains of the warehouse were not received.');

        $resultSet = new ResultSet(ResultSet::TYPE_ARRAY);
        $resultSet->initialize($dataSource);

        return $resultSet->toArray();
    }

    public function fetchWarehouseMaterialValueOptions() {
        $sql = new Sql($this->dbAdapter);
        $select = $sql->select(['a' => self::TABLE_WAREHOUSES_LOGS]);
        $select->join(['b' => MaterialDb::TABLE_MATERIALS], 'a.resource_id = b.material_id', ['fraction', 'material_name']);
        $select->join(['c' => DatabaseContractorAbstract::TABLE_CONTRACTORS], 'a.contractor_id = c.contractor_id', ['provider_name' => 'contractor_name']);
        $select->join(['d' => WarehouseDb::TABLE_WAREHOUSES], 'a.warehouse_id = d.warehouse_id', ['plant_id']);
        $select->columns(['material_id' => 'resource_id', 'provider_id' => 'contractor_id',
                          'weight'      => new Expression('(
                SUM(CASE WHEN direction = "input" THEN resource_weight ELSE 0 END) -
                SUM(CASE WHEN direction = "output" THEN resource_weight ELSE 0 END))'),
                          'price'       => new Expression('(
                SUM(CASE WHEN direction = "input" THEN resource_price ELSE 0 END) -
                SUM(CASE WHEN direction = "output" THEN resource_price ELSE 0 END))'),
        ]);
        $select->group(['a.resource_id', 'a.contractor_id']);
        $select->order('b.material_name');

        $dataSource = $sql->prepareStatementForSqlObject($select)->execute();
        $resultSet = new ResultSet(ResultSet::TYPE_ARRAY);
        $resultSet->initialize($dataSource);
        return $resultSet->toArray();
    }

    public function saveLog(WarehouseLogEntity $object) {
        $data = $this->hydrator->extract($object);
        unset($data['log_id']);
        $sql = new Sql($this->dbAdapter);
        if ($object::DIRECTION_OUTPUT == $object->getDirection()) {
            $select = $sql->select(self::TABLE_WAREHOUSES_LOGS);
            $select->columns([
                'sum' => new Expression('(? *
                    (SUM(CASE WHEN direction = "input" THEN resource_price ELSE 0 END) - SUM(CASE WHEN direction = "output" THEN resource_price ELSE 0 END)) /
                    (SUM(CASE WHEN direction = "input" THEN resource_weight ELSE 0 END) - SUM(CASE WHEN direction = "output" THEN resource_weight ELSE 0 END))
                 )', $object->getResourceWeight())]);
            $select->where->equalTo('warehouse_id', $object->getWarehouseId());
            $select->where->equalTo('resource_id', $object->getResourceId());
            $select->where->equalTo('contractor_id', $object->getContractorId());

            $dataSource = $sql->prepareStatementForSqlObject($select)->execute();
            $resultSet = new ResultSet('array');
            $result = $resultSet->initialize($dataSource)->current();
            $data['resource_price'] = $result['sum'];
        }
        $action = $sql->insert(self::TABLE_WAREHOUSES_LOGS);
        $action->values($data);
        $dataSource = $sql->prepareStatementForSqlObject($action)->execute();
        if (!$dataSource instanceof ResultInterface)
            throw new Exception\ErrorException('Warehouse transaction was not saved.');
        if ($generatedId = $dataSource->getGeneratedValue())
            $object->setLogId($generatedId);
        return $object;
    }

    public function deleteLogByWagonId($wagonId, $direction) {
        $sql = new Sql($this->dbAdapter);
        $action = $sql->delete(self::TABLE_WAREHOUSES_LOGS);
        $action->where->equalTo('wagon_id', $wagonId);
        $action->where->equalTo('direction', $direction);
        $dataSource = $sql->prepareStatementForSqlObject($action)->execute();
        if (!$dataSource instanceof ResultInterface)
            throw new Exception\ErrorException('Delete transaction was not commit.');
        return;
    }

    public function deleteLogBySkipId($skipId) {
        $sql = new Sql($this->dbAdapter);
        $action = $sql->delete(self::TABLE_WAREHOUSES_LOGS);
        $action->where->equalTo('skip_id', $skipId);
        $dataSource = $sql->prepareStatementForSqlObject($action)->execute();
        if (!$dataSource instanceof ResultInterface)
            throw new Exception\ErrorException('Delete transaction was not commit.');
        return;
    }

}