<?php

namespace Application\Model;

use Document\Service\Repository\PurchaseWagonDb;
use Transport\Service\Repository\CarrierDb;

use Manufacturing\Service\Repository\FurnaceDb;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Sql;

class SkipWeightService {

    const TABLE_SKIP_COMMON    = 'skip_common';
    const TABLE_SKIP_MATERIALS = 'skip_materials';

    /** @var Adapter */
    protected $db;

    /**
     * PurchaseWagonsService constructor.
     * @param Adapter $db
     */
    public function __construct(Adapter $db) {
        $this->db = $db;
    }

    public function getSkips($furnaceId, DateTime $date = null) {
        $sql = new Sql($this->db);
        $select = $sql->select(['a' => FurnaceDb::TABLE_FURNACES]);


        $dataSource = $sql->prepareStatementForSqlObject($select)->execute();
        $resultSet = new ResultSet('array');
        $resultSet->initialize($dataSource);
        return $resultSet->toArray();
    }

    public function getWagons($contractId = null, $filterData = null) {
        $sql = new Sql($this->db);
        $select = $sql->select(['a' => PurchaseWagonDb::TABLE_PURCHASE_WAGONS]);
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

        $dataSource = $sql->prepareStatementForSqlObject($select)->execute();

        $resultSet = new ResultSet(ResultSet::TYPE_ARRAYOBJECT);
        $resultSet->initialize($dataSource);

        return new PurchaseWagonsContainer($resultSet->toArray());
    }

}