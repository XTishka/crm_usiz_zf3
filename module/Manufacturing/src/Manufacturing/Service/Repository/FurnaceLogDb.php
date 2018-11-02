<?php

namespace Manufacturing\Service\Repository;

use DateTime;
use Application\Service\Repository;
use Manufacturing\Domain\FurnaceSkipEntity;
use Manufacturing\Exception;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Sql;


class FurnaceLogDb extends Repository\AbstractDb {

    const TABLE_FURNACES_LOGS = 'furnaces_logs';

    public function loadingTransaction(FurnaceSkipEntity $object) {
        $data = $this->getHydrator()->extract($object);
        unset($data['skip_id']);
        $sql = new Sql($this->dbAdapter);
        $action = $sql->insert(self::TABLE_FURNACES_LOGS);
        $action->values($data);
        $dataSource = $sql->prepareStatementForSqlObject($action)->execute();
        if (!$dataSource instanceof ResultInterface || !$dataSource->getAffectedRows())
            throw new Exception\ErrorException('Furnace transaction was not saved.');
        if ($generatedId = $dataSource->getGeneratedValue())
            $object->setFurnaceId($generatedId);
        return $object;
    }

    public function fetchLogsArray($furnaceId, DateTime $date = null) {
        $date = ($date instanceof DateTime) ? $date : new DateTime();
        $sql = new Sql($this->dbAdapter);
        $select = $sql->select(['a' => self::TABLE_FURNACES_LOGS]);
        $select->columns(['id', 'furnace_id',
            'weight_material' => new Expression('(SELECT SUM(weight) FROM furnaces_logs WHERE date = a.date AND dropout != 0 AND furnace_id = a.furnace_id)'),
            'weight_coal'     => new Expression('(SELECT SUM(weight) FROM furnaces_logs WHERE date = a.date AND dropout = 0 AND furnace_id = a.furnace_id)'),
            'date']);
        $select->where->equalTo('a.furnace_id', $furnaceId);
        $select->where->greaterThanOrEqualTo('date', $date->format('Y-m-d'));
        $select->where->notEqualTo('dropout', 0);
        $select->group(['date']);
        $select->order('date DESC');

        $dataSource = $sql->prepareStatementForSqlObject($select)->execute();
        $resultSet = new ResultSet(ResultSet::TYPE_ARRAY);
        $resultSet->initialize($dataSource);
        $data = $resultSet->toArray();
        return $data;
    }

}