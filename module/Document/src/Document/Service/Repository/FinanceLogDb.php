<?php

namespace Document\Service\Repository;

use Application\Service\Repository\AbstractDb;
use Contractor\Service_old\Repository\CustomerDb;
use Contractor\Service_old\Repository\ProviderDb;
use Document\Domain\FinanceLogEntity;
use Document\Exception;
use Manufacturing\Service\Repository\CompanyDb;
use Transport\Service\Repository\CarrierDb;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Join;
use Zend\Db\Sql\Sql;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class FinanceLogDb extends AbstractDb {

    const TABLE_FINANCES_LOGS = 'finances_logs';

    public function fetchLogWithPaginator($contractorType = null) {
        $sql = new Sql($this->dbAdapter);
        $select = $sql->select(['a' => CarrierDb::TABLE_CARRIERS]);
        $select->order('created DESC');
        if ($contractorType)
            $select->where->equalTo('contractor_type', $contractorType);

        $adapter = new DbSelect($select, $sql, new HydratingResultSet($this->hydrator, $this->prototype));
        $paginator = new Paginator($adapter);
        return $paginator;
    }

    public function fetchCarrierBalances() {
        $sql = new Sql($this->dbAdapter);
        $select = $sql->select(['a' => CarrierDb::TABLE_CARRIERS]);
        $select->join(['b' => self::TABLE_FINANCES_LOGS],
            new Expression('a.carrier_id = b.contractor_id AND b.contractor_type = ? AND b.operation_type = ?', ['carrier', FinanceLogEntity::TYPE_DEBIT]),
            ['debit' => new Expression('SUM(b.price)')], Join::JOIN_LEFT);
        $select->join(['c' => self::TABLE_FINANCES_LOGS],
            new Expression('a.carrier_id = c.contractor_id AND c.contractor_type = ? AND b.operation_type = ?', ['carrier', FinanceLogEntity::TYPE_CREDIT]),
            ['credit' => new Expression('SUM(c.price)')], Join::JOIN_LEFT);
        $select->columns(['contractor_name' => 'carrier_name']);
        $select->group('a.carrier_id');
        $dataSource = $sql->prepareStatementForSqlObject($select)->execute();
        if (!$dataSource instanceof ResultInterface)
            throw new Exception\ErrorException('The remains of the contractor were not received.');
        $resultSet = new ResultSet('array');
        $resultSet->initialize($dataSource);
        return $resultSet->toArray();
    }

    public function fetchCustomerBalances() {
        $sql = new Sql($this->dbAdapter);
        $select = $sql->select(['a' => CustomerDb::TABLE_CUSTOMERS]);
        $select->join(['b' => self::TABLE_FINANCES_LOGS],
            new Expression('a.customer_id = b.contractor_id AND b.contractor_type = ? AND b.operation_type = ?', ['customer', FinanceLogEntity::TYPE_DEBIT]),
            ['debit' => new Expression('SUM(b.price)')], Join::JOIN_LEFT);
        $select->join(['c' => self::TABLE_FINANCES_LOGS],
            new Expression('a.customer_id = c.contractor_id AND c.contractor_type = ? AND b.operation_type = ?', ['customer', FinanceLogEntity::TYPE_CREDIT]),
            ['credit' => new Expression('SUM(c.price)')], Join::JOIN_LEFT);
        $select->columns(['contractor_name' => 'customer_name']);
        $select->group('a.customer_id');
        $dataSource = $sql->prepareStatementForSqlObject($select)->execute();
        if (!$dataSource instanceof ResultInterface)
            throw new Exception\ErrorException('The remains of the contractor were not received.');
        $resultSet = new ResultSet('array');
        $resultSet->initialize($dataSource);
        return $resultSet->toArray();
    }

    public function fetchCompanyBalances(int $companyId = null) {
        $sql = new Sql($this->dbAdapter);
        $select = $sql->select(['a' => CompanyDb::TABLE_COMPANIES]);
        $select->join(['b' => FinanceLogDb::TABLE_FINANCES_LOGS], 'a.company_id = b.contractor_id', []);
        $select->columns([
            'contractor_name' => 'company_name',
            'balance'         => new Expression('(
                (SELECT COALESCE(SUM(price), 0) FROM finances_logs WHERE a.company_id = contractor_id AND operation_type = ? AND contractor_type = ?) -
                (SELECT COALESCE(SUM(price), 0) FROM finances_logs WHERE a.company_id = contractor_id AND operation_type = ? AND contractor_type = ?))',
                [FinanceLogEntity::TYPE_DEBIT, FinanceLogEntity::CONTRACTOR_COMPANY, FinanceLogEntity::TYPE_CREDIT, FinanceLogEntity::CONTRACTOR_COMPANY]),
        ]);
        $select->group('a.company_id');
        if ($companyId)
            $select->where->equalTo('a.company_id', $companyId);

        $dataSource = $sql->prepareStatementForSqlObject($select)->execute();
        if (!$dataSource instanceof ResultInterface)
            throw new Exception\ErrorException('The remains of the contractor were not received.');
        $resultSet = new ResultSet(ResultSet::TYPE_ARRAYOBJECT);
        $resultSet->initialize($dataSource);
        if ($companyId)
            return $resultSet->current();
        return $resultSet->toArray();
    }

    public function fetchProviderBalances() {
        $sql = new Sql($this->dbAdapter);
        $select = $sql->select(['a' => ProviderDb::TABLE_PROVIDERS]);
        $select->join(['b' => self::TABLE_FINANCES_LOGS],
            new Expression('a.provider_id = b.contractor_id AND b.contractor_type = ? AND b.operation_type = ?', ['provider', FinanceLogEntity::TYPE_DEBIT]),
            ['debit' => new Expression('SUM(b.price)')], Join::JOIN_LEFT);
        $select->join(['c' => self::TABLE_FINANCES_LOGS],
            new Expression('a.provider_id = c.contractor_id AND c.contractor_type = ? AND b.operation_type = ?', ['provider', FinanceLogEntity::TYPE_CREDIT]),
            ['credit' => new Expression('SUM(c.price)')], Join::JOIN_LEFT);
        $select->columns(['contractor_name' => 'provider_name']);
        $select->group('a.provider_id');
        $dataSource = $sql->prepareStatementForSqlObject($select)->execute();
        if (!$dataSource instanceof ResultInterface)
            throw new Exception\ErrorException('The remains of the contractor were not received.');
        $resultSet = new ResultSet('array');
        $resultSet->initialize($dataSource);
        return $resultSet->toArray();
    }


    public function saveLog(FinanceLogEntity $object) {
        $data = $this->hydrator->extract($object);
        unset($data['log_id']);
        $sql = new Sql($this->dbAdapter);
        $action = $sql->insert(self::TABLE_FINANCES_LOGS);
        $action->values($data);
        $dataSource = $sql->prepareStatementForSqlObject($action)->execute();
        if (!$dataSource instanceof ResultInterface)
            throw new Exception\ErrorException('Finance transaction was not saved.');
        if ($generatedId = $dataSource->getGeneratedValue())
            $object->setLogId($generatedId);
        return $object;
    }

    public function deleteLogByWagonId($wagonId) {
        $sql = new Sql($this->dbAdapter);
        $action = $sql->delete(self::TABLE_FINANCES_LOGS);
        $action->where->equalTo('wagon_id', $wagonId);
        $dataSource = $sql->prepareStatementForSqlObject($action)->execute();
        if (!$dataSource instanceof ResultInterface)
            throw new Exception\ErrorException('Finance transaction was not saved.');
        return;
    }

}