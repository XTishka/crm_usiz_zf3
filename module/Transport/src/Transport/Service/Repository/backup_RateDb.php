<?php

namespace Transport\Service\Repository;

use Application\Service\Repository\AbstractDb;
use Contractor\Entity\ContractorAbstract;
use Contractor\Service\Repository\DatabaseContractorCompany;
use Contractor\Service\Repository\DatabaseContractorPlant;
use Manufacturing\Service\Repository as ManufacturingRepository;
use Transport\Domain\RateEntity;
use Transport\Exception;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\Metadata\Source\Factory as MetadataSourceFactory;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Sql;
use Zend\Paginator;

class backup_RateDb extends AbstractDb {

    const TABLE_RATES        = 'rates';
    const TABLE_RATES_VALUES = 'rates_values';



    public function fetchTableColumns() {
        $metadata = MetadataSourceFactory::createSourceFromAdapter($this->dbAdapter);
        $columnNames = $metadata->getColumnNames(self::TABLE_RATES);
        return $columnNames;
    }

    public function fetchRatesPaginator($direction = null, array $params = null, $deleted = false) {
        $sql = new Sql($this->dbAdapter);
        $select = $sql->select(['a' => self::TABLE_RATES]);
        $select->join(['b' => DatabaseContractorPlant::TABLE_CONTRACTORS], 'a.plant_id = b.contractor_id', ['plant_name' => 'contractor_name']);
        $select->join(['c' => DatabaseContractorCompany::TABLE_CONTRACTORS], 'a.company_id = c.contractor_id', ['company_name' => 'contractor_name']);
        $select->join(['d' => StationDb::TABLE_STATIONS], 'a.station_id = d.station_id', ['station_name']);
        $select->join(['e' => CarrierDb::TABLE_CARRIERS], 'a.carrier_id = e.carrier_id', ['carrier_name', 'carrier_type']);
        $select->where->equalTo('b.contractor_type', ContractorAbstract::TYPE_PLANT);
        $select->where->equalTo('c.contractor_type', ContractorAbstract::TYPE_COMPANY);
        $select->order('station_name ASC');
        if ($direction = trim($direction))
            $select->where->equalTo('direction', $direction);
        if (is_array($params)) {
            $columns = MetadataSourceFactory::createSourceFromAdapter($this->dbAdapter)->getColumnNames(self::TABLE_RATES);
            $params = array_intersect_key($params, array_flip($columns));
            foreach ($params as $col => $val) {
                if (!trim($val) || !$val) continue;
                $select->where->equalTo(sprintf('a.%s', $col), $val);
            }
        }
        if ($deleted) {
            $select->where->equalTo('is_deleted', 1);
        } else {
            $select->where->equalTo('is_deleted', 0);
        }
        $resultSet = new HydratingResultSet($this->hydrator, $this->prototype);
        $paginator = new Paginator\Paginator(new Paginator\Adapter\DbSelect($select, $sql, $resultSet));
        return $paginator;
    }

    public function fetchRatesArray($direction = null, $columns = null, $enabled = false) {
        $sql = new Sql($this->dbAdapter);
        $select = $sql->select(['a' => self::TABLE_RATES]);
        $select->join(['d' => StationDb::TABLE_STATIONS], 'a.station_id = d.station_id', ['station_name']);
        if ($direction = trim($direction))
            $select->where->equalTo('direction', $direction);
        if (is_array($columns))
            $select->columns($columns);
        if ($enabled)
            $select->where->notEqualTo('disabled', 1);
        $dataSource = $sql->prepareStatementForSqlObject($select)->execute();
        $resultSet = new ResultSet('array');
        $resultSet->initialize($dataSource);
        return $resultSet->toArray();
    }

    public function fetchRateValueOptionsByParams(array $params = null) {
        $sql = new Sql($this->dbAdapter);
        $select = $sql->select(['a' => self::TABLE_RATES]);
        $select->join(['d' => StationDb::TABLE_STATIONS], 'a.station_id = d.station_id', ['station_name']);
        if (is_array($params)) {
            if (key_exists('carrier', $params))
                $select->where->equalTo('carrier_id', $params['carrier']);
            if (key_exists('direction', $params))
                $select->where->equalTo('direction', $params['direction']);
            if (key_exists('period', $params) && $params['period']) {
                $select->where->lessThanOrEqualTo('period_begin', new Expression('CURDATE()'));
                $select->where->greaterThanOrEqualTo('period_end', new Expression('CURDATE()'));
            }
        }
        $dataSource = $sql->prepareStatementForSqlObject($select)->execute();
        $resultSet = new ResultSet('array');
        $resultSet->initialize($dataSource);
        return $resultSet->toArray();
    }

    public function fetchRateById(int $rateId) {
        $sql = new Sql($this->dbAdapter);
        $select = $sql->select(self::TABLE_RATES);
        $select->where->equalTo('rate_id', $rateId);
        $dataSource = $sql->prepareStatementForSqlObject($select)->execute();
        $resultSet = new HydratingResultSet($this->hydrator, $this->prototype);
        $resultSet->initialize($dataSource);
        /** @var RateEntity $object */
        $object = $resultSet->current();
        return $object;
    }

    public function saveRate(RateEntity $object) {
        $data = $this->getHydrator()->extract($object);
        unset($data['rate_id']);
        $sql = new Sql($this->dbAdapter);
        if ($rateId = $object->getRateId()) {
            unset($data['created']);
            $action = $sql->update(self::TABLE_RATES);
            $action->set($data);
            $action->where->equalTo('rate_id', $rateId);
        } else {
            $action = $sql->insert(self::TABLE_RATES);
            $action->values($data);
        }
        $dataSource = $sql->prepareStatementForSqlObject($action)->execute();
        if (!$dataSource instanceof ResultInterface || !$dataSource->getAffectedRows())
            throw new Exception\ErrorException('Rate data was not saved');
        if ($generatedId = $dataSource->getGeneratedValue())
            $object->setRateId($generatedId);
        return $object;
    }

    public function deleteRateById(int $rateId) {
        $sql = new Sql($this->dbAdapter);
        $action = $sql->delete(self::TABLE_RATES);
        $action->where->equalTo('rate_id', $rateId);
        $dataSource = $sql->prepareStatementForSqlObject($action)->execute();
        if (!$dataSource instanceof ResultInterface || !$dataSource->getAffectedRows())
            throw new Exception\ErrorException('Rate data was not deleted');
        return;
    }

}