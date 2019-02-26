<?php

namespace Application\Service\Repository;

use Application\Exception;
use Bank\Service\BankManager;
use Bank\Service\RecordManager;
use Contractor\Entity\ContractorCustomer;
use Contractor\Service\Repository\DatabaseContractorAbstract;
use Document\Domain\TransactionEntity;
use Document\Service\Repository\FinanceTransactionDb;
use Document\Service\Repository\PurchaseContractDb;
use Document\Service\Repository\PurchaseWagonDb;
use Manufacturing\Domain\WarehouseLogEntity;
use Manufacturing\Service\Repository\WarehouseDb;
use Manufacturing\Service\Repository\WarehouseLogDb;
use Resource\Service\Repository\MaterialDb;
use Transport\Service\Repository\CarrierDb;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Join;
use Zend\Db\Sql\Sql;
use Zend\Paginator\Adapter\ArrayAdapter;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class ApiDb extends AbstractDb {

    /**
     * @param      $plantId
     * @param      $materialId
     * @param null $date
     * @return array|\ArrayObject|null
     * @throws Exception\ErrorException
     */
    public function getMaterialWarehouseBalances($plantId, $materialId, $date = null) {
        $sql = new Sql($this->dbAdapter);
        $select = $sql->select(['a' => WarehouseLogDb::TABLE_WAREHOUSES_LOGS]);
        $select->join(['b' => WarehouseDb::TABLE_WAREHOUSES], 'a.warehouse_id = b.warehouse_id', ['warehouse_name']);
        $select->join(['c' => MaterialDb::TABLE_MATERIALS], 'a.resource_id = c.material_id', ['material_name']);
        $select->columns([
            'amount' => new Expression('(
                (SELECT COALESCE(SUM(resource_price), 0) FROM warehouses_logs WHERE a.resource_id = resource_id AND direction = ?) -
                (SELECT COALESCE(SUM(resource_price), 0) FROM warehouses_logs WHERE a.resource_id = resource_id AND direction = ?))',
                [WarehouseLogEntity::DIRECTION_INPUT, WarehouseLogEntity::DIRECTION_OUTPUT]),
            'weight' => new Expression('(
                (SELECT COALESCE(SUM(resource_weight), 0) FROM warehouses_logs WHERE a.resource_id = resource_id AND direction = ?) -
                (SELECT COALESCE(SUM(resource_weight), 0) FROM warehouses_logs WHERE a.resource_id = resource_id AND direction = ?))',
                [WarehouseLogEntity::DIRECTION_INPUT, WarehouseLogEntity::DIRECTION_OUTPUT]),
        ]);
        $select->where->equalTo('a.resource_id', $materialId);
        $select->where->equalTo('b.plant_id', $plantId);
        if ($date) {
            $date = \DateTime::createFromFormat('d.m.Y', $date);
            $date->setTime(23, 59, 59);
            $select->where->lessThanOrEqualTo('a.created', $date->format('Y-m-d H:i:s'));
        }
        $dataSource = $sql->prepareStatementForSqlObject($select)->execute();
        if (!$dataSource instanceof ResultInterface)
            throw new Exception\ErrorException('The remains of the warehouse were not received.');
        $resultSet = new ResultSet(ResultSet::TYPE_ARRAYOBJECT);
        $resultSet->initialize($dataSource);
        return $resultSet->current();
    }

    /**
     * @param      $companyId
     * @param      $materialId
     * @param null $date
     * @return array|\ArrayObject|null
     * @throws Exception\ErrorException
     */
    public function getExpectedMaterialBalances($companyId, $materialId, $date = null) {
        $sql = new Sql($this->dbAdapter);
        $select = $sql->select(['a' => PurchaseWagonDb::TABLE_PURCHASE_WAGONS]);
        $select->join(['b' => PurchaseContractDb::TABLE_PURCHASE_CONTRACTS], 'a.contract_id = b.contract_id', []);
        $select->join(['c' => MaterialDb::TABLE_MATERIALS], 'b.material_id = c.material_id', ['material_name']);
        $select->columns([
            'amount' => new Expression('COALESCE(SUM(material_price), 0)'),
            'weight' => new Expression('COALESCE(SUM(loading_weight), 0)'),
        ]);
        $select->where->equalTo('b.material_id', $materialId);
        $select->where->equalTo('b.company_id', $companyId);
        $select->where->isNull('a.unloading_date');
        if ($date) {
            $date = \DateTime::createFromFormat('d.m.Y', $date);
            $date->setTime(23, 59, 59);
            $select->where->lessThanOrEqualTo('a.created', $date->format('Y-m-d H:i:s'));
        }
        $dataSource = $sql->prepareStatementForSqlObject($select)->execute();
        if (!$dataSource instanceof ResultInterface)
            throw new Exception\ErrorException('The expected material data were not received.');
        $resultSet = new ResultSet(ResultSet::TYPE_ARRAYOBJECT);
        $resultSet->initialize($dataSource);
        return $resultSet->current();
    }

    public function getCompanyDebitOperationsPaginator($companyId, $date = null) {
        $sql = new Sql($this->dbAdapter);
        $select = $sql->select(['a' => FinanceTransactionDb::TABLE_FINANCE_TRANSACTIONS]);
        $select->join(['b' => DatabaseContractorAbstract::TABLE_CONTRACTORS], 'a.contractor_id = b.contractor_id AND a.contractor_type = b.contractor_type', ['contractor_name']);
        $select->where->equalTo('a.company_id', $companyId);
        $select->where->equalTo('a.transaction_type', TransactionEntity::TRANSACTION_PAYMENT);
        //$select->where->greaterThan('debit', 0);
        $select->order('created DESC');
        if ($date) {
            $date = \DateTime::createFromFormat('d.m.Y', $date);
            $date->setTime(23, 59, 59);
            $select->where->lessThanOrEqualTo('a.created', $date->format('Y-m-d H:i:s'));
        }
        $paginator = new Paginator(new DbSelect($select, $sql, new ResultSet()));
        return $paginator;
    }

    public function getBankBalancesPaginator($companyId, $dateStr = null) {
        $companyId = intval($companyId);
        $sql = new Sql($this->dbAdapter);

        if (trim($dateStr)) {
            $date = \DateTime::createFromFormat('d.m.Y', $dateStr);
        } else {
            $date = (new \DateTime('now'));
        }
        $date->setTime(23, 59, 59);

        $dateSub = clone $date;
        $dateSub->sub(new \DateInterval('P1D'));


        $subSel = $sql->select(RecordManager::TABLE_RECORDS);
        $subSel->columns(['record_id', 'bank_id', 'date' => new Expression('MAX(date)')]);
        $subSel->where->equalTo('company_id', $companyId);
        $subSel->where->isNotNull('amount');
        $subSel->where->lessThanOrEqualTo('date', $dateSub->format('Y-m-d'));
        $subSel->group('bank_id');

        $select = $sql->select(['t1' => RecordManager::TABLE_RECORDS]);
        $select->columns(['amount' => new Expression('COALESCE(amount, 0)')]);
        $select->join(['t2' => $subSel], 't1.bank_id = t2.bank_id AND t1.date = t2.date');
        $select->join(['t3' => BankManager::TABLE_BANKS], 't1.bank_id = t3.bank_id', ['name'], Join::JOIN_INNER);
        $select->where->isNotNull('amount');
        $select->where->equalTo('company_id', $companyId);
        $select->order('amount DESC');

        /*
        $select = $sql->select(['a' => BankManager::TABLE_BANKS]);
        $select->columns(['name']);
        $select->join(['b' => RecordManager::TABLE_RECORDS], 'a.bank_id = b.bank_id', ['amount' => new Expression('COALESCE(amount, 0)')], Join::JOIN_LEFT);
        $select->where->equalTo('b.company_id', $companyId);
        $select->where->lessThanOrEqualTo('b.date', (new \DateTime())->format('Y-m-d'));
        $select->group('a.bank_id');
        */

        $paginator = new Paginator(new DbSelect($select, $sql, new ResultSet()));
        return $paginator;
    }

    public function getCustomerDebtsPaginator($companyId, $date = null) {
        $sql = new Sql($this->dbAdapter);

        $select = $sql->select(['a' => FinanceTransactionDb::TABLE_FINANCE_TRANSACTIONS]);
        $select->join(['b' => DatabaseContractorAbstract::TABLE_CONTRACTORS], 'a.contractor_id = b.contractor_id AND a.contractor_type = b.contractor_type', ['contractor_name']);
        $select->columns([
            'amount' => 'debit',
            'company_id',
            'contractor_id',
            'contractor_type',
            'transaction_type',
        ]);
        $select->where->equalTo('a.contractor_type', TransactionEntity::CONTRACTOR_CUSTOMER);
        $select->where->equalTo('a.company_id', $companyId);
        $select->having->greaterThan('amount', 0);
        if ($date) {
            $dateObj = \DateTime::createFromFormat('d.m.Y', $date);
            $dateObj->setTime(23, 59, 59);
            $select->where->lessThanOrEqualTo('a.created', $dateObj->format('Y-m-d H:i:s'));
        }


        $dataSource = $sql->prepareStatementForSqlObject($select)->execute();

        $resultSet = new ResultSet(ResultSet::TYPE_ARRAYOBJECT);
        $resultSet->initialize($dataSource);

        $data = [];
        foreach ($resultSet as $item) {
            $index = \join([
                $item['company_id'],
                $item['contractor_id'],
                $item['contractor_type'],
            ]);
            if (array_key_exists($index, $data)) {
                if ($item->transaction_type == TransactionEntity::TRANSACTION_DEBT) {
                    $data[$index]->amount = bcadd($data[$index]->amount, $item->amount, 2);

                } elseif ($item->transaction_type == TransactionEntity::TRANSACTION_PAYMENT) {
                    $data[$index]->amount = bcsub($data[$index]->amount, $item->amount, 2);
                }
            } else {
                $data[$index] = $item;
            }
        }

        $data = array_filter($data, function (\ArrayObject $current) {
            return (0 < $current->offsetGet('amount'));
        });

        uasort($data, function ($a, $b) {
            return ($b->amount - $a->amount);
        });

        $paginator = new Paginator(new ArrayAdapter($data));
        $paginator->setItemCountPerPage($paginator->getTotalItemCount());

        return $paginator;


    }

    public function getPlantDebtsPaginator($companyId) {
        $sql = new Sql($this->dbAdapter);
        $select = $sql->select(['a' => FinanceTransactionDb::TABLE_FINANCE_TRANSACTIONS]);
        $select->join(['b' => DatabaseContractorAbstract::TABLE_CONTRACTORS],
            'a.contractor_id = b.contractor_id AND a.contractor_type = b.contractor_type',
            ['contractor_name']);
        $select->columns([
            'amount' => new Expression('
            (SELECT COALESCE(SUM(debit), 0) FROM finance_transactions
                WHERE contractor_id = a.contractor_id AND contractor_type = a.contractor_type AND transaction_type = ?) -
            (SELECT COALESCE(SUM(debit), 0) FROM finance_transactions
                WHERE contractor_id = a.contractor_id AND contractor_type = a.contractor_type AND transaction_type = ?)',
                [TransactionEntity::TRANSACTION_DEBT, TransactionEntity::TRANSACTION_PAYMENT]),
        ]);
        $select->having->greaterThan('amount', 0);
        $select->where->equalTo('a.contractor_type', TransactionEntity::CONTRACTOR_COMPANY);
        $select->where->equalTo('a.company_id', $companyId);
        $select->group('b.contractor_id');
        $paginator = new Paginator(new DbSelect($select, $sql, new ResultSet()));
        return $paginator;
    }

    public function getCompanyPrepaymentOperationsPaginator($companyId, $date = null) {
        $sql = new Sql($this->dbAdapter);
        $select = $sql->select(['a' => FinanceTransactionDb::TABLE_FINANCE_TRANSACTIONS]);
        $select->join(['b' => DatabaseContractorAbstract::TABLE_CONTRACTORS],
            'a.contractor_id = b.contractor_id AND a.contractor_type = b.contractor_type',
            ['contractor_name']);
        $select->columns([
            'amount' => new Expression('
            (SELECT COALESCE(SUM(credit), 0) FROM finance_transactions
                WHERE contractor_id = a.contractor_id AND contractor_type = a.contractor_type AND transaction_type = ?) -
            (SELECT COALESCE(SUM(credit), 0) FROM finance_transactions
                WHERE contractor_id = a.contractor_id AND contractor_type = a.contractor_type AND transaction_type = ?)',
                [TransactionEntity::TRANSACTION_PAYMENT, TransactionEntity::TRANSACTION_DEBT]),
        ]);
        $select->having->greaterThan('amount', 0);
        $select->where->equalTo('a.contractor_type', TransactionEntity::CONTRACTOR_PROVIDER);
        $select->where->equalTo('a.company_id', $companyId);
        if ($date) {
            $date = \DateTime::createFromFormat('d.m.Y', $date);
            $date->setTime(23, 59, 59);
            $select->where->lessThanOrEqualTo('a.created', $date->format('Y-m-d H:i:s'));
        }
        $select->group('b.contractor_id');
        $paginator = new Paginator(new DbSelect($select, $sql, new ResultSet()));
        return $paginator;
    }

    public function getPlantPrepaymentOperationsPaginator($plantId, $date = null) {
        $sql = new Sql($this->dbAdapter);
        $selectA = $sql->select(['a' => FinanceTransactionDb::TABLE_FINANCE_TRANSACTIONS]);
        $selectA->join(['b' => DatabaseContractorAbstract::TABLE_CONTRACTORS], 'a.contractor_id = b.contractor_id AND a.contractor_type = b.contractor_type', ['contractor_name']);
        $selectA->columns(['amount' => new Expression('
            (SELECT COALESCE(SUM(credit), 0) FROM finance_transactions WHERE contractor_id = a.contractor_id AND contractor_type = a.contractor_type AND transaction_type = ?) -
            (SELECT COALESCE(SUM(credit), 0) FROM finance_transactions WHERE contractor_id = a.contractor_id AND contractor_type = a.contractor_type AND transaction_type = ?)',
            [TransactionEntity::TRANSACTION_PAYMENT, TransactionEntity::TRANSACTION_DEBT]),
        ]);

        $selectA->where->equalTo('a.contractor_type', TransactionEntity::CONTRACTOR_COMPANY);
        $selectA->where->equalTo('a.company_id', $plantId);
        if ($date) {
            $date = \DateTime::createFromFormat('d.m.Y', $date);
            $date->setTime(23, 59, 59);
            $selectA->where->lessThanOrEqualTo('a.created', $date->format('Y-m-d H:i:s'));
        }
        $selectA->group('b.contractor_id');

        $selectB = $sql->select(['a' => FinanceTransactionDb::TABLE_FINANCE_TRANSACTIONS]);
        $selectB->join(['b' => CarrierDb::TABLE_CARRIERS], 'a.contractor_id = b.carrier_id', ['contractor_name' => 'carrier_name']);
        $selectB->columns(['amount' => new Expression('
            (SELECT COALESCE(SUM(credit), 0) FROM finance_transactions WHERE contractor_id = a.contractor_id AND contractor_type = a.contractor_type AND transaction_type = ?) -
            (SELECT COALESCE(SUM(credit), 0) FROM finance_transactions WHERE contractor_id = a.contractor_id AND contractor_type = a.contractor_type AND transaction_type = ?)',
            [TransactionEntity::TRANSACTION_PAYMENT, TransactionEntity::TRANSACTION_DEBT]),
        ]);

        $selectB->where->equalTo('a.contractor_type', TransactionEntity::CONTRACTOR_CARRIER);
        $selectB->where->equalTo('a.company_id', $plantId);
        if ($date) {
            $date = \DateTime::createFromFormat('d.m.Y', $date);
            $date->setTime(23, 59, 59);
            $selectB->where->lessThanOrEqualTo('a.created', $date->format('Y-m-d H:i:s'));
        }
        $selectB->group('b.carrier_id');

        $select = $selectA->combine($selectB);
        $select->having->greaterThan('amount', 0);

        //echo $select->getSqlString($this->dbAdapter->platform);

        $paginator = new Paginator(new DbSelect($select, $sql, new ResultSet()));
        return $paginator;
    }

    public function getCompanyToPlantPrepaymentOperationsPaginator($companyId, $date = null) {
        $sql = new Sql($this->dbAdapter);
        $select = $sql->select(['a' => FinanceTransactionDb::TABLE_FINANCE_TRANSACTIONS]);
        $select->join(['b' => DatabaseContractorAbstract::TABLE_CONTRACTORS],
            'a.contractor_id = b.contractor_id AND a.contractor_type = b.contractor_type',
            ['contractor_name']);
        $select->columns([
            'amount' => new Expression('
            (SELECT COALESCE(SUM(credit), 0) FROM finance_transactions WHERE contractor_id = a.contractor_id AND contractor_type = a.contractor_type AND transaction_type = ?) -
            (SELECT COALESCE(SUM(credit), 0) FROM finance_transactions WHERE contractor_id = a.contractor_id AND contractor_type = a.contractor_type AND transaction_type = ?)',
                [TransactionEntity::TRANSACTION_PAYMENT, TransactionEntity::TRANSACTION_DEBT]),
        ]);
        $select->having->greaterThan('amount', 0);
        $select->where->equalTo('a.contractor_type', TransactionEntity::CONTRACTOR_PLANT);
        $select->where->equalTo('a.company_id', $companyId);

        if ($date) {
            $date = \DateTime::createFromFormat('d.m.Y', $date);
            $date->setTime(23, 59, 59);
            $select->where->lessThanOrEqualTo('a.created', $date->format('Y-m-d H:i:s'));
        }
        $select->group('b.contractor_id');

        //echo $select->getSqlString($this->dbAdapter->platform);

        $paginator = new Paginator(new DbSelect($select, $sql, new ResultSet()));
        return $paginator;
    }

    public function getPlantFromCompanyPrepaymentOperationsPaginator($plantId, $date = null) {
        $sql = new Sql($this->dbAdapter);
        $select = $sql->select(['a' => FinanceTransactionDb::TABLE_FINANCE_TRANSACTIONS]);
        $select->join(['b' => DatabaseContractorAbstract::TABLE_CONTRACTORS],
            'a.contractor_id = b.contractor_id AND a.contractor_type = b.contractor_type',
            ['contractor_name']);
        $select->columns([
            'amount' => new Expression('
            (SELECT COALESCE(SUM(debit), 0) FROM finance_transactions WHERE contractor_id = a.contractor_id AND contractor_type = a.contractor_type AND transaction_type = ?) -
            (SELECT COALESCE(SUM(debit), 0) FROM finance_transactions WHERE contractor_id = a.contractor_id AND contractor_type = a.contractor_type AND transaction_type = ?)',
                [TransactionEntity::TRANSACTION_PAYMENT, TransactionEntity::TRANSACTION_DEBT]),
        ]);
        $select->having->greaterThan('amount', 0);
        $select->where->equalTo('a.contractor_type', TransactionEntity::CONTRACTOR_COMPANY);
        $select->where->equalTo('a.company_id', $plantId);
        if ($date) {
            $date = \DateTime::createFromFormat('d.m.Y', $date);
            $date->setTime(23, 59, 59);
            $select->where->lessThanOrEqualTo('a.created', $date->format('Y-m-d H:i:s'));
        }
        $select->group('b.contractor_id');

        #echo $select->getSqlString($this->dbAdapter->platform);

        $paginator = new Paginator(new DbSelect($select, $sql, new ResultSet()));
        return $paginator;
    }

    public function getCustomerPrepaymentOperationsPaginator($companyId, $date = null) {
        $sql = new Sql($this->dbAdapter);

        /*
        $select = $sql->select(['a' => FinanceTransactionDb::TABLE_FINANCE_TRANSACTIONS]);
        $select->join(['b' => DatabaseContractorAbstract::TABLE_CONTRACTORS],
            'a.contractor_id = b.contractor_id AND a.contractor_type = b.contractor_type',
            ['contractor_name']);
        $select->columns([
            'amount' => new Expression('
            (SELECT COALESCE(SUM(debit), 0) FROM finance_transactions
                WHERE contractor_id = a.contractor_id AND contractor_type = a.contractor_type AND transaction_type = ?) -
            (SELECT COALESCE(SUM(debit), 0) FROM finance_transactions
                WHERE contractor_id = a.contractor_id AND contractor_type = a.contractor_type AND transaction_type = ?)',
                [TransactionEntity::TRANSACTION_PAYMENT, TransactionEntity::TRANSACTION_DEBT]),
        ]);
        $select->having->greaterThan('amount', 0);
        $select->where->equalTo('a.contractor_type', TransactionEntity::CONTRACTOR_CUSTOMER);
        $select->where->equalTo('a.company_id', $companyId);
        if ($date) {
            $date = \DateTime::createFromFormat('d.m.Y', $date);
            $date->setTime(23, 59, 59);
            $select->where->lessThanOrEqualTo('a.created', $date->format('Y-m-d H:i:s'));
        }
        $select->group('b.contractor_id');
        $paginator = new Paginator(new DbSelect($select, $sql, new ResultSet()));
        return $paginator;
        */

        $select = $sql->select(['a' => FinanceTransactionDb::TABLE_FINANCE_TRANSACTIONS]);
        $select->join(['b' => DatabaseContractorAbstract::TABLE_CONTRACTORS],
            'a.contractor_id = b.contractor_id AND a.contractor_type = b.contractor_type',
            ['contractor_name', 'contractor_type']);
        $select->columns([
            'credit',
            'debit',
            'company_id',
            'contractor_id',
            'transaction_type',
        ]);
        $select->where->equalTo('a.contractor_type', TransactionEntity::CONTRACTOR_CUSTOMER);
        $select->where->equalTo('a.company_id', $companyId);
        $select->having->nest()
            ->greaterThan('credit', 0)
            ->or
            ->greaterThan('debit', 0);

        //echo $select->getSqlString($this->dbAdapter->getPlatform()); exit;

        if ($date) {
            $dateObj = \DateTime::createFromFormat('d.m.Y', $date);
            $dateObj->setTime(23, 59, 59);
            $select->where->lessThanOrEqualTo('a.created', $dateObj->format('Y-m-d H:i:s'));
        }

        $dataSource = $sql->prepareStatementForSqlObject($select)->execute();

        $resultSet = new ResultSet(ResultSet::TYPE_ARRAYOBJECT);
        $resultSet->initialize($dataSource);

        //echo '<pre>'; print_r($resultSet->toArray()); exit;

        $data = [];
        foreach ($resultSet as $item) {
            $index = join([
                $item['company_id'],
                $item['contractor_id'],
            ]);

            if (!array_key_exists($index, $data)) {
                $aObject = new \ArrayObject();
                $aObject->offsetSet('amount', 0);
                $aObject->offsetSet('contractor_name', $item->contractor_name);
                $data[$index] = $aObject;
            } else {
                $aObject = $data[$index];
            }

            if ($item->transaction_type == TransactionEntity::TRANSACTION_PAYMENT && $item->debit) {
                $aObject->offsetSet('amount', bcadd($aObject->offsetget('amount'), $item->debit, 2));
            } elseif ($item->transaction_type == TransactionEntity::TRANSACTION_DEBT && $item->credit) {
                $aObject->offsetSet('amount', bcadd($aObject->offsetget('amount'), $item->credit, 2));
            } elseif ($item->transaction_type == TransactionEntity::TRANSACTION_DEBT) {
                $aObject->offsetSet('amount', bcsub($aObject->offsetget('amount'), $item->debit, 2));
            }
        }

        $filter = new \CallbackFilterIterator(new \ArrayIterator($data), function (\ArrayObject $aObject) {
            return ($aObject->offsetGet('amount') > 0);
        });

        $paginator = new Paginator(new ArrayAdapter(iterator_to_array($filter)));

        return $paginator;

    }

    public function getCompanyPayableOperationsPaginator($companyId, $date = null) {
        $sql = new Sql($this->dbAdapter);
        /*
        $selectA = $sql->select(['a' => FinanceTransactionDb::TABLE_FINANCE_TRANSACTIONS]);
        $selectA->join(['b' => DatabaseContractorAbstract::TABLE_CONTRACTORS],
            'a.contractor_id = b.contractor_id AND a.contractor_type = b.contractor_type',
            ['contractor_name']);
        $selectA->columns([
            'amount' => new Expression('
            (SELECT COALESCE(SUM(credit), 0) FROM finance_transactions
                WHERE
                    company_id = ? AND
                    contractor_id = a.contractor_id AND
                    contractor_type = a.contractor_type AND
                    transaction_type = ?) -
            (SELECT COALESCE(SUM(credit), 0) FROM finance_transactions
                WHERE
                    company_id = ? AND
                    contractor_id = a.contractor_id AND
                    contractor_type = a.contractor_type AND
                    transaction_type = ?)',
                [$companyId, TransactionEntity::TRANSACTION_DEBT, $companyId, TransactionEntity::TRANSACTION_PAYMENT]),
        ]);
        $selectA->having->greaterThan('amount', 0);
        $selectA->where->equalTo('a.company_id', $companyId);
        $selectA->group('b.contractor_id');

        $selectB = $sql->select(['a' => FinanceTransactionDb::TABLE_FINANCE_TRANSACTIONS]);
        $selectB->join(['b' => CarrierDb::TABLE_CARRIERS], 'a.contractor_id = b.carrier_id',
            ['contractor_name' => 'carrier_name']);
        $selectB->columns([
            'amount' => new Expression('
            (SELECT COALESCE(SUM(credit), 0) FROM finance_transactions
                WHERE
                    company_id = ? AND
                    contractor_id = a.contractor_id AND
                    contractor_type = a.contractor_type AND
                    transaction_type = ?) -
            (SELECT COALESCE(SUM(credit), 0) FROM finance_transactions
                WHERE
                    company_id = ? AND
                    contractor_id = a.contractor_id AND
                    contractor_type = a.contractor_type AND
                    transaction_type = ?)',
                [$companyId, TransactionEntity::TRANSACTION_DEBT, $companyId, TransactionEntity::TRANSACTION_PAYMENT]),
        ]);
        $selectB->having->greaterThan('amount', 0);
        $selectB->where->equalTo('a.contractor_type', TransactionEntity::CONTRACTOR_CARRIER);
        $selectB->where->equalTo('a.company_id', $companyId);
        $selectB->group('b.carrier_id');

        if ($date) {
            $date = \DateTime::createFromFormat('d.m.Y', $date);
            $date->setTime(23, 59, 59);
            $selectA->where->lessThanOrEqualTo('a.created', $date->format('Y-m-d H:i:s'));
            $selectB->where->lessThanOrEqualTo('a.created', $date->format('Y-m-d H:i:s'));
        }

        $selectA->combine($selectB);
        $selectA->where->equalTo('company_id', $companyId);

        //echo $selectA->getSqlString($this->dbAdapter->platform);

        $paginator = new Paginator(new DbSelect($selectA, $sql, new ResultSet()));
        return $paginator;
        */

        $selectA = $sql->select(['a' => FinanceTransactionDb::TABLE_FINANCE_TRANSACTIONS]);
        $selectA->join(['b' => DatabaseContractorAbstract::TABLE_CONTRACTORS], 'a.contractor_id = b.contractor_id AND a.contractor_type = b.contractor_type', ['contractor_name']);
        $selectA->columns([
            'amount' => 'credit',
            'company_id',
            'contractor_id',
            'contractor_type',
            'transaction_type',
        ]);
        $selectA->having->greaterThan('amount', 0);
        $selectA->where->equalTo('a.company_id', $companyId);
        $selectA->where->notIn('a.contractor_type', [ContractorCustomer::TYPE_CUSTOMER]);
        if ($date) {
            $dateObj = \DateTime::createFromFormat('d.m.Y', $date);
            $dateObj->setTime(23, 59, 59);
            $selectA->where->lessThanOrEqualTo('a.created', $dateObj->format('Y-m-d H:i:s'));
        }


        $dataSourceA = $sql->prepareStatementForSqlObject($selectA)->execute();

        $resultSetA = new ResultSet(ResultSet::TYPE_ARRAYOBJECT);
        $resultSetA->initialize($dataSourceA);

        $dataA = [];
        foreach ($resultSetA as $item) {
            $index = \join([
                $item['company_id'],
                $item['contractor_id'],
                $item['contractor_type'],
            ]);
            if (array_key_exists($index, $dataA)) {
                if ($item->transaction_type == TransactionEntity::TRANSACTION_DEBT) {
                    $dataA[$index]->amount = bcadd($dataA[$index]->amount, $item->amount, 2);

                } else {
                    $dataA[$index]->amount = bcsub($dataA[$index]->amount, $item->amount, 2);
                }
            } else {
                $dataA[$index] = $item;
            }
        }

        $selectB = $sql->select(['a' => FinanceTransactionDb::TABLE_FINANCE_TRANSACTIONS]);
        $selectB->join(['b' => CarrierDb::TABLE_CARRIERS], 'a.contractor_id = b.carrier_id', ['contractor_name' => 'carrier_name']);
        $selectB->columns([
            'amount' => 'credit',
            'company_id',
            'contractor_id',
            'contractor_type',
            'transaction_type',
        ]);
        $selectB->having->greaterThan('amount', 0);
        $selectB->where->equalTo('a.contractor_type', TransactionEntity::CONTRACTOR_CARRIER);
        $selectB->where->equalTo('a.company_id', $companyId);
        if ($date) {
            $dateObj = \DateTime::createFromFormat('d.m.Y', $date);
            $dateObj->setTime(23, 59, 59);
            $selectB->where->lessThanOrEqualTo('a.created', $dateObj->format('Y-m-d H:i:s'));
        }

        $dataSourceB = $sql->prepareStatementForSqlObject($selectB)->execute();

        $resultSetB = new ResultSet(ResultSet::TYPE_ARRAYOBJECT);
        $resultSetB->initialize($dataSourceB);

        $dataB = [];
        foreach ($resultSetB as $item) {
            $index = \join([
                $item['company_id'],
                $item['contractor_id'],
                $item['contractor_type'],
            ]);
            if (array_key_exists($index, $dataB)) {
                if ($item->transaction_type == TransactionEntity::TRANSACTION_DEBT) {
                    $dataB[$index]->amount = bcadd($dataB[$index]->amount, $item->amount, 2);

                } else {
                    $dataB[$index]->amount = bcsub($dataB[$index]->amount, $item->amount, 2);
                }
            } else {
                $dataB[$index] = $item;
            }
        }

        $data = array_merge($dataA, $dataB);
        uasort($data, function ($a, $b) {
            return ($b->amount - $a->amount);
        });

        $paginator = new Paginator(new ArrayAdapter($data));
        $paginator->setItemCountPerPage($paginator->getTotalItemCount());

        return $paginator;

    }

    public function getCompanyCorporateOperationsPaginator($companyId, $date = null) {
        $sql = new Sql($this->dbAdapter);
        $select = $sql->select(['a' => FinanceTransactionDb::TABLE_FINANCE_TRANSACTIONS]);
        $select->join(['b' => DatabaseContractorAbstract::TABLE_CONTRACTORS],
            'a.contractor_id = b.contractor_id AND a.contractor_type = b.contractor_type',
            ['contractor_name']);
        $select->columns([
            'amount' => new Expression('
            (SELECT COALESCE(SUM(debit), 0) FROM finance_transactions
                WHERE contractor_id = a.contractor_id AND contractor_type = a.contractor_type AND transaction_type = ?) -
            (SELECT COALESCE(SUM(credit), 0) FROM finance_transactions
                WHERE contractor_id = a.contractor_id AND contractor_type = a.contractor_type AND transaction_type = ?)',
                [TransactionEntity::TRANSACTION_PAYMENT, TransactionEntity::TRANSACTION_PAYMENT]),
        ]);
        $select->having->greaterThan('amount', 0);
        $select->where->equalTo('a.contractor_type', TransactionEntity::CONTRACTOR_CORPORATE);
        $select->where->equalTo('a.company_id', $companyId);
        if ($date) {
            $date = \DateTime::createFromFormat('d.m.Y', $date);
            $date->setTime(23, 59, 59);
            $select->where->lessThanOrEqualTo('a.created', $date->format('Y-m-d H:i:s'));
        }
        $select->group('b.contractor_id');
        $paginator = new Paginator(new DbSelect($select, $sql, new ResultSet()));
        return $paginator;
    }

    public function getProviderTransactionsPaginator($companyId, $providerId = null) {
        $sql = new Sql($this->dbAdapter);
        $select = $sql->select(['a' => FinanceTransactionDb::TABLE_FINANCE_TRANSACTIONS]);
        $select->join(['b' => DatabaseContractorAbstract::TABLE_CONTRACTORS],
            'a.contractor_id = b.contractor_id AND a.contractor_type = b.contractor_type',
            ['contractor_name']);
        $select->where->equalTo('a.contractor_type', TransactionEntity::CONTRACTOR_PROVIDER);
        $select->where->equalTo('a.company_id', $companyId);
        if (!is_null($providerId) && is_numeric($providerId))
            $select->where->equalTo('a.contractor_id', $providerId);
        $select->order('a.created DESC');
        $paginator = new Paginator(new DbSelect($select, $sql, new ResultSet()));
        return $paginator;
    }

    public function getCustomerTransactionsPaginator($companyId, $customerId = null) {
        $sql = new Sql($this->dbAdapter);
        $select = $sql->select(['a' => FinanceTransactionDb::TABLE_FINANCE_TRANSACTIONS]);
        $select->join(['b' => DatabaseContractorAbstract::TABLE_CONTRACTORS],
            'a.contractor_id = b.contractor_id AND a.contractor_type = b.contractor_type',
            ['contractor_name']);
        $select->where->equalTo('a.contractor_type', TransactionEntity::CONTRACTOR_CUSTOMER);
        $select->where->equalTo('a.company_id', $companyId);
        if (!is_null($customerId) && is_numeric($customerId))
            $select->where->equalTo('a.contractor_id', $customerId);
        $select->order('a.created DESC');
        $paginator = new Paginator(new DbSelect($select, $sql, new ResultSet()));
        return $paginator;
    }

    public function getCarrierTransactionsPaginator($companyId, $carrierId = null) {
        $sql = new Sql($this->dbAdapter);
        $select = $sql->select(['a' => FinanceTransactionDb::TABLE_FINANCE_TRANSACTIONS]);
        $select->join(['b' => CarrierDb::TABLE_CARRIERS], 'a.contractor_id = b.carrier_id', ['contractor_name' => 'carrier_name']);
        $select->where->equalTo('a.contractor_type', TransactionEntity::CONTRACTOR_CARRIER);
        $select->where->equalTo('a.company_id', $companyId);
        if (!is_null($carrierId) && is_numeric($carrierId))
            $select->where->equalTo('a.contractor_id', $carrierId);
        $select->order('a.created DESC');
        $paginator = new Paginator(new DbSelect($select, $sql, new ResultSet()));
        return $paginator;
    }

    public function getAdditionalTransactionsPaginator($companyId, $additionalId = null) {
        $sql = new Sql($this->dbAdapter);
        $select = $sql->select(['a' => FinanceTransactionDb::TABLE_FINANCE_TRANSACTIONS]);
        $select->join(['b' => DatabaseContractorAbstract::TABLE_CONTRACTORS],
            'a.contractor_id = b.contractor_id AND a.contractor_type = b.contractor_type',
            ['contractor_name']);
        $select->where->equalTo('a.contractor_type', TransactionEntity::CONTRACTOR_ADDITIONAL);
        $select->where->equalTo('a.company_id', $companyId);
        if (!is_null($additionalId) && is_numeric($additionalId))
            $select->where->equalTo('a.contractor_id', $additionalId);
        $select->order('a.created DESC');
        $paginator = new Paginator(new DbSelect($select, $sql, new ResultSet()));
        return $paginator;
    }

    public function getCorporateTransactionsPaginator($companyId, $corporateId = null) {
        $sql = new Sql($this->dbAdapter);
        $select = $sql->select(['a' => FinanceTransactionDb::TABLE_FINANCE_TRANSACTIONS]);
        $select->join(['b' => DatabaseContractorAbstract::TABLE_CONTRACTORS],
            'a.contractor_id = b.contractor_id AND a.contractor_type = b.contractor_type',
            ['contractor_name']);
        $select->where->equalTo('a.contractor_type', TransactionEntity::CONTRACTOR_CORPORATE);
        $select->where->equalTo('a.company_id', $companyId);
        if (!is_null($corporateId) && is_numeric($corporateId))
            $select->where->equalTo('a.contractor_id', $corporateId);
        $select->order('a.created DESC');
        $paginator = new Paginator(new DbSelect($select, $sql, new ResultSet()));
        return $paginator;
    }

    public function getPlantTransactionsPaginator($companyId, $plantId = null) {
        $sql = new Sql($this->dbAdapter);
        $select = $sql->select(['a' => FinanceTransactionDb::TABLE_FINANCE_TRANSACTIONS]);
        $select->join(['b' => DatabaseContractorAbstract::TABLE_CONTRACTORS],
            'a.contractor_id = b.contractor_id AND a.contractor_type = b.contractor_type',
            ['contractor_name']);
        $select->where->equalTo('a.contractor_type', TransactionEntity::CONTRACTOR_PLANT);
        $select->where->equalTo('a.company_id', $companyId);
        if (!is_null($plantId) && is_numeric($plantId))
            $select->where->equalTo('a.contractor_id', $plantId);
        $select->order('a.created DESC');
        $paginator = new Paginator(new DbSelect($select, $sql, new ResultSet()));
        return $paginator;
    }

    public function getCompanyTransactionsPaginator($plantId, $companyId = null) {
        $sql = new Sql($this->dbAdapter);
        $select = $sql->select(['a' => FinanceTransactionDb::TABLE_FINANCE_TRANSACTIONS]);
        $select->columns(['transaction_id', 'transaction_type', 'debit' => 'credit', 'credit' => 'debit', 'comment', 'is_manual', 'created']);
        $select->join(['b' => DatabaseContractorAbstract::TABLE_CONTRACTORS], 'a.company_id = b.contractor_id', ['contractor_name']);
        $select->where->equalTo('a.contractor_type', TransactionEntity::CONTRACTOR_PLANT);
        $select->where->equalTo('a.contractor_id', $plantId);
        if (is_int($companyId)) {
            $select->where->equalTo('a.company_id', $companyId);
        }
        $select->order('a.created DESC');

        //echo $select->getSqlString($this->dbAdapter->platform);

        $paginator = new Paginator(new DbSelect($select, $sql, new ResultSet()));
        return $paginator;
    }


}