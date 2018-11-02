<?php

namespace Document\Service\Repository;

use Application\Service\Repository\AbstractDb;
use ArrayObject;
use Contractor\Service\Repository\DatabaseContractorAbstract;
use DateTime;
use Document\Domain\TransactionEntity;
use Document\Exception;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\ResultSet\ResultSetInterface;
use Zend\Db\Sql\Sql;
use Zend\Paginator\Paginator;

class FinanceTransactionDb extends AbstractDb implements FinanceTransactionDbInterface {

    const TABLE_FINANCE_TRANSACTIONS = 'finance_transactions';

    /**
     * Баланс предприятия.
     *
     * Возвращает баланс предприятия состоянием на указанную дату.
     * Если дата не указана, будет возвращен баланс состоянием на текущую дату.
     *
     * @param int $companyId
     * @param DateTime|null $date
     * @return ArrayObject
     * @throws Exception\ErrorException
     */
    public function fetchCompanyBalance(int $companyId, DateTime $date = null) {
        if (!$date instanceof DateTime)
            $date = (new DateTime($date))->setTime(23, 59, 59);

        $dataSource = $this->dbAdapter->query('
          SELECT SUM(amount) AS amount, currency FROM ( SELECT currency, 
            (
            SELECT COALESCE(SUM(debit), 0) FROM finance_transactions
            WHERE
              company_id = :companyId AND
              transaction_type = :payment AND
              contractor_id = trans.contractor_id
            ) - (
            SELECT COALESCE(SUM(credit), 0) FROM finance_transactions
            WHERE
              company_id = :companyId AND
              transaction_type = :payment AND
              contractor_id = trans.contractor_id
            )
          AS amount FROM finance_transactions AS trans
          WHERE company_id = :companyId AND created <= :created
          GROUP BY contractor_id) AS subquery', [
            'payment'   => TransactionEntity::TRANSACTION_PAYMENT,
            'companyId' => $companyId,
            'created'   => $date->format('Y-m-d H:i:s'),
        ]);

        if (!$dataSource instanceof ResultSetInterface)
            throw new Exception\ErrorException('Company balance was not received. Unknown database error.');
        return $dataSource->current();
    }

    public function getCompanyTransactionsPaginator(int $companyId, DateTime $date = null) {
        if (!$date instanceof DateTime)
            $date = new DateTime($date);

        $sql = new Sql($this->dbAdapter);
        $select = $sql->select();
        $select->where->equalTo('company_id', $companyId);
        $select->where->lessThanOrEqualTo('created', $date->format('Y-m-d H:i:s'));
    }

    /**
     * Дебиторская задолженность.
     *
     * Возвращает дебиторскую задолженность предприятию состоянием на указанную дату.
     * Если дата не указана, будет возвращена сумма состоянием на текущую дату.
     *
     * @param int $companyId
     * @param DateTime|null $date
     * @return ArrayObject
     * @throws Exception\ErrorException
     */
    public function fetchCompanyReceivableSum(int $companyId, DateTime $date = null) {
        $sql = new Sql($this->dbAdapter);

        $select = $sql->select(['a' => FinanceTransactionDb::TABLE_FINANCE_TRANSACTIONS]);
        $select->join(['b' => DatabaseContractorAbstract::TABLE_CONTRACTORS], 'a.contractor_id = b.contractor_id AND a.contractor_type = b.contractor_type', ['contractor_name']);
        $select->columns([
            'amount' => 'debit',
            'currency',
            'company_id',
            'contractor_id',
            'contractor_type',
            'transaction_type'
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

        if (!$dataSource instanceof ResultInterface)
            throw new Exception\ErrorException('Company prepayment sum was not received. Unknown database error.');

        $resultSet = new ResultSet(ResultSet::TYPE_ARRAYOBJECT);
        $resultSet->initialize($dataSource);



        /* ------------------------------- */


        $data = [];
        foreach ($resultSet as $item) {
            $index = \join([
                $item['company_id'],
                $item['contractor_id'],
                $item['contractor_type']
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

        $amount = 0;
        $currency = 'UAH';
        foreach ($data as $value) {
            $amount = bcadd($amount, $value->offsetGet('amount'), 2);
            $currency = $value->offsetGet('currency');
        }

        $result = new ArrayObject();
        $result->offsetSet('amount', $amount);
        $result->offsetSet('currency', $currency);

        return $result;

    }

    /**
     * Предоплата предприятия.
     *
     * Возвращает сумму предоплат предприятия состоянием на указанную дату.
     * Если дата не указана, будет возвращена сумма состоянием на текущую дату.
     *
     * @param int $companyId
     * @param DateTime|null $date
     * @return ArrayObject
     * @throws Exception\ErrorException
     */
    public function fetchCompanyPrepaymentSum(int $companyId, DateTime $date = null) {
        if (!$date instanceof DateTime)
            $date = new DateTime($date);

        /*

        $dataSource = $this->dbAdapter->query('
          SELECT SUM(amount) AS amount, currency FROM ( SELECT currency, GREATEST(
            (SELECT COALESCE(SUM(credit), 0) FROM finance_transactions
             WHERE transaction_type = :payment AND contractor_id = trans.contractor_id AND contractor_type = trans.contractor_type) -
            (SELECT COALESCE(SUM(credit), 0) FROM finance_transactions
             WHERE transaction_type = :debt AND contractor_id = trans.contractor_id AND contractor_type = trans.contractor_type), 0)
          AS amount FROM finance_transactions AS trans
          WHERE company_id = :companyId AND contractor_type = :contractorType AND created <= :created 
          GROUP BY contractor_id) AS subquery', [
            'debt'           => TransactionEntity::TRANSACTION_DEBT,
            'payment'        => TransactionEntity::TRANSACTION_PAYMENT,
            'contractorType' => TransactionEntity::CONTRACTOR_PROVIDER,
            'companyId'      => $companyId,
            'created'        => $date->format('Y-m-d H:i:s'),
        ]);
        */

        $sql = new Sql($this->dbAdapter);
        $select = $sql->select(self::TABLE_FINANCE_TRANSACTIONS);
        $select->columns([
            'credit',
            'currency',
            'transaction_type'
        ]);
        $select->where->equalTo('company_id', $companyId);
        $select->where->equalTo('contractor_type', TransactionEntity::CONTRACTOR_PROVIDER);
        $select->where->lessThanOrEqualTo('created', $date->format('Y-m-d H:i:s'));

        $select->getSqlString($this->dbAdapter->platform);

        $dataSource = $sql->prepareStatementForSqlObject($select)->execute();


        if (!$dataSource instanceof ResultInterface)
            throw new Exception\ErrorException('Company prepayment sum was not received. Unknown database error.');

        $resultSet = new ResultSet(ResultSet::TYPE_ARRAY);
        $resultSet->initialize($dataSource);

        $amount = 0;
        $currency = 'UAH';
        foreach ($resultSet as $item) {
            if ($item['transaction_type'] == TransactionEntity::TRANSACTION_PAYMENT) {
                $amount = bcadd($amount, $item['credit'], 2);
            } elseif ($item['transaction_type'] == TransactionEntity::TRANSACTION_DEBT) {
                $amount = bcsub($amount, $item['credit'], 2);
            }
            $currency = $item['currency'];
        }

        $amount = ($amount < 0) ? 0 : $amount;


        return new ArrayObject(['amount' => $amount, 'currency' => $currency]);
    }

    /**
     * Предоплата завода.
     *
     * Возвращает сумму предоплат завода состоянием на указанную дату.
     * Если дата не указана, будет возвращена сумма состоянием на текущую дату.
     *
     * @param int $plantId
     * @param DateTime|null $date
     * @return ArrayObject
     * @throws Exception\ErrorException
     */
    public function fetchPlantPrepaymentSum(int $plantId, DateTime $date = null) {
        if (!$date instanceof DateTime)
            $date = new DateTime($date);

        /*
        $dataSource = $this->dbAdapter->query('
          SELECT SUM(amount) AS amount, currency FROM ( SELECT currency, GREATEST(
            (SELECT COALESCE(SUM(credit), 0) FROM finance_transactions WHERE transaction_type = :payment AND contractor_id = trans.contractor_id AND contractor_type = trans.contractor_type)
             -
            (SELECT COALESCE(SUM(credit), 0) FROM finance_transactions WHERE transaction_type = :debt AND contractor_id = trans.contractor_id AND contractor_type = trans.contractor_type)
            , 0)
          AS amount FROM finance_transactions AS trans
          WHERE company_id = :companyId AND contractor_type = :contractorType AND created <= :created 
          GROUP BY contractor_id) AS subquery', [
            'debt'           => TransactionEntity::TRANSACTION_DEBT,
            'payment'        => TransactionEntity::TRANSACTION_PAYMENT,
            'contractorType' => TransactionEntity::CONTRACTOR_CARRIER,
            'companyId'      => $plantId,
            'created'        => $date->format('Y-m-d H:i:s'),
        ]);
        */


        $dataSource = $this->dbAdapter->query('
          SELECT SUM(amount) AS amount, currency FROM ( SELECT currency,
           GREATEST(
            (SELECT COALESCE(SUM(credit), 0) FROM finance_transactions WHERE transaction_type = :payment AND contractor_id = trans.contractor_id AND contractor_type = :contractorCarrier) -
            (SELECT COALESCE(SUM(credit), 0) FROM finance_transactions WHERE transaction_type = :debt AND contractor_id = trans.contractor_id AND contractor_type = :contractorCarrier) , 0) +
           GREATEST(
            (SELECT COALESCE(SUM(credit), 0) FROM finance_transactions WHERE transaction_type = :payment AND contractor_id = trans.contractor_id AND contractor_type = :contractorCompany) -
            (SELECT COALESCE(SUM(credit), 0) FROM finance_transactions WHERE transaction_type = :debt AND contractor_id = trans.contractor_id AND contractor_type = :contractorCompany) , 0)
          AS amount FROM finance_transactions AS trans
          WHERE company_id = :companyId AND (contractor_type = :contractorCarrier OR contract_type = :contractorCompany)  AND created <= :created 
          GROUP BY contractor_id) AS subquery', [
            'debt'              => TransactionEntity::TRANSACTION_DEBT,
            'payment'           => TransactionEntity::TRANSACTION_PAYMENT,
            'contractorCarrier' => TransactionEntity::CONTRACTOR_CARRIER,
            'contractorCompany' => TransactionEntity::CONTRACTOR_COMPANY,
            'companyId'         => $plantId,
            'created'           => $date->format('Y-m-d H:i:s'),
        ]);

        if (!$dataSource instanceof ResultSetInterface)
            throw new Exception\ErrorException('Plant prepayment sum was not received. Unknown database error.');
        return $dataSource->current();
    }

    /**
     * Кредиторская задолженность.
     *
     * Возвращает сумму кредиторской задолженности предприятия на указанную дату.
     * Если дата не указана, будет возвращена сумма состоянием на текущую дату.
     *
     * @param int $companyId
     * @param DateTime|null $date
     * @return ArrayObject
     * @throws Exception\ErrorException
     */
    public function fetchCompanyPayableSum(int $companyId, DateTime $date = null) {
        if (!$date instanceof DateTime)
            $date = new DateTime($date);

        /*
        $dataSource = $this->dbAdapter->query('
          SELECT SUM(amount) AS amount, currency FROM ( SELECT currency, GREATEST(
            (
              SELECT COALESCE(SUM(credit), 0) FROM finance_transactions
              WHERE
                transaction_type = :debt AND
                contractor_id = trans.contractor_id AND
                contractor_type = trans.contractor_type AND
                company_id = :companyId AND
                created <= :created
            ) - (
              SELECT COALESCE(SUM(credit), 0) FROM finance_transactions
              WHERE
                transaction_type = :payment AND
                contractor_id = trans.contractor_id AND
                contractor_type = trans.contractor_type AND
                company_id = :companyId AND
                created <= :created
            ), 0)
          AS amount FROM finance_transactions AS trans
          WHERE company_id = :companyId AND (contractor_type = :contractorTypeA OR contractor_type = :contractorTypeB OR contractor_type = :contractorTypeC) AND created <= :created
          GROUP BY contractor_id) AS subquery', [
            'debt'            => TransactionEntity::TRANSACTION_DEBT,
            'payment'         => TransactionEntity::TRANSACTION_PAYMENT,
            'contractorTypeA' => TransactionEntity::CONTRACTOR_PROVIDER,
            'contractorTypeB' => TransactionEntity::CONTRACTOR_CARRIER,
            'contractorTypeC' => TransactionEntity::CONTRACTOR_PLANT,
            'companyId'       => $companyId,
            'created'         => $date->format('Y-m-d H:i:s'),
        ]);
        */

        $sql = new Sql($this->dbAdapter);
        $select = $sql->select(self::TABLE_FINANCE_TRANSACTIONS);
        $select->columns([
            'credit',
            'currency',
            'transaction_type'
        ]);
        $select->where->equalTo('company_id', $companyId);
        $select->where->nest()
            ->equalTo('contractor_type', TransactionEntity::CONTRACTOR_PROVIDER)->or
            ->equalTo('contractor_type', TransactionEntity::CONTRACTOR_CARRIER)->or
            ->equalTo('contractor_type', TransactionEntity::CONTRACTOR_PLANT);
        $select->where->lessThanOrEqualTo('created', $date->format('Y-m-d H:i:s'));

        $select->getSqlString($this->dbAdapter->platform);

        $dataSource = $sql->prepareStatementForSqlObject($select)->execute();


        if (!$dataSource instanceof ResultInterface)
            throw new Exception\ErrorException('Company payable sum was not received. Unknown database error.');

        $resultSet = new ResultSet(ResultSet::TYPE_ARRAY);
        $resultSet->initialize($dataSource);

        $amount = 0;
        $currency = 'UAH';
        foreach ($resultSet as $item) {
            if ($item['transaction_type'] == TransactionEntity::TRANSACTION_PAYMENT) {
                $amount = bcsub($amount, $item['credit'], 2);
            } elseif ($item['transaction_type'] == TransactionEntity::TRANSACTION_DEBT) {
                $amount = bcadd($amount, $item['credit'], 2);
            }
            $currency = $item['currency'];
        }

        $amount = ($amount < 0) ? 0 : $amount;

        return new ArrayObject(['amount' => $amount, 'currency' => $currency]);
    }

    /**
     * Предоплата покупателей.
     *
     * Возвращает сумму предоплаты покупателей предприятию состоянием на указанную дату.
     * Если дата не указана, будет возвращена сумма состоянием на текущую дату.
     *
     * @param int $companyId
     * @param DateTime|null $date
     * @return ArrayObject
     * @throws Exception\ErrorException
     */
    public function fetchCustomerPrepaymentSum(int $companyId, DateTime $date = null) {
        if (!$date instanceof DateTime)
            $date = new DateTime($date);

        $sql = new Sql($this->dbAdapter);

        $select = $sql->select(['a' => self::TABLE_FINANCE_TRANSACTIONS]);
        $select->columns([
            'amount' => 'debit',
            'company_id',
            'contractor_id',
            'currency',
            'transaction_type'
        ]);
        $select->join(['b' => DatabaseContractorAbstract::TABLE_CONTRACTORS],
            'a.contractor_id = b.contractor_id AND a.contractor_type = b.contractor_type',
            ['contractor_name', 'contractor_type']);
        $select->where->equalTo('a.company_id', $companyId);
        $select->where->equalTo('a.contractor_type', TransactionEntity::CONTRACTOR_CUSTOMER);
        $select->where->lessThanOrEqualTo('a.created', $date->format('Y-m-d H:i:s'));
        $select->having->greaterThan('amount', 0);

        $dataSource = $sql->prepareStatementForSqlObject($select)->execute();

        if (!$dataSource instanceof ResultInterface)
            throw new Exception\ErrorException('Customer payment sum was not received. Unknown database error.');

        $resultSet = new ResultSet();
        $resultSet->initialize($dataSource);

        $data = [];
        foreach ($resultSet as $item) {
            $index = join([
                $item['company_id'],
                $item['contractor_id'],
            ]);

            if (!array_key_exists($index, $data)) {
                $aObject = new \ArrayObject();
                $aObject->offsetSet('amount', 0);
                $aObject->offsetSet('currency', $item->currency);
                $data[$index] = $aObject;
            } else {
                $aObject = $data[$index];
            }

            if ($item->transaction_type == TransactionEntity::TRANSACTION_PAYMENT) {
                $aObject->offsetSet('amount', bcadd($aObject->offsetget('amount'), $item->amount, 2));
            } elseif ($item->transaction_type == TransactionEntity::TRANSACTION_DEBT) {
                $aObject->offsetSet('amount', bcsub($aObject->offsetget('amount'), $item->amount, 2));
            }
        }

        $filter = new \CallbackFilterIterator(new \ArrayIterator($data), function (\ArrayObject $aObject) {
            return ($aObject->offsetGet('amount') > 0);
        });

        $amount = 0;
        $currency = 'UAH';
        foreach ($filter as $value) {
            $amount = bcadd($amount, $value->offsetGet('amount'), 2);
            $currency = $value->offsetGet('currency');
        }

        $result = new ArrayObject();
        $result->offsetSet('amount', $amount);
        $result->offsetSet('currency', $currency);

        return $result;
    }

    public function fetchPlantFromCompanyPrepaymentSum(int $plantId, DateTime $date = null) {
        if (!$date instanceof DateTime)
            $date = new DateTime($date);

        $dataSource = $this->dbAdapter->query('
          SELECT SUM(amount) AS amount, currency FROM (
            SELECT currency, GREATEST(
              (SELECT COALESCE(SUM(debit), 0) FROM finance_transactions WHERE transaction_type = :payment AND contractor_id = trans.contractor_id AND contractor_type = trans.contractor_type) -
              (SELECT COALESCE(SUM(debit), 0) FROM finance_transactions WHERE transaction_type = :debt AND contractor_id = trans.contractor_id AND contractor_type = trans.contractor_type), 0)
            AS amount FROM finance_transactions AS trans WHERE company_id = :companyId AND contractor_type = :contractorType AND created <= :created
          GROUP BY contractor_id) AS subquery', [
            'payment'        => TransactionEntity::TRANSACTION_PAYMENT,
            'debt'           => TransactionEntity::TRANSACTION_DEBT,
            'contractorType' => TransactionEntity::CONTRACTOR_COMPANY,
            'companyId'      => $plantId,
            'created'        => $date->format('Y-m-d H:i:s'),
        ]);


        if (!$dataSource instanceof ResultSetInterface)
            throw new Exception\ErrorException('Customer payment sum was not received. Unknown database error.');
        return $dataSource->current();
    }

    /**
     * @param int $companyId
     * @param DateTime|null $date
     * @return array|ArrayObject|null
     * @throws Exception\ErrorException
     */
    public function fetchCompanyToPlantPrepaymentSum(int $companyId, DateTime $date = null) {
        if (!$date instanceof DateTime)
            $date = new DateTime($date);

        $dataSource = $this->dbAdapter->query('
          SELECT SUM(amount) AS amount, currency FROM (
            SELECT currency, GREATEST(
              (SELECT COALESCE(SUM(credit), 0) FROM finance_transactions WHERE transaction_type = :payment AND contractor_id = trans.contractor_id AND contractor_type = trans.contractor_type) -
              (SELECT COALESCE(SUM(credit), 0) FROM finance_transactions WHERE transaction_type = :debt AND contractor_id = trans.contractor_id AND contractor_type = trans.contractor_type), 0)
            AS amount FROM finance_transactions AS trans WHERE company_id = :companyId AND contractor_type = :contractorType AND created <= :created
          GROUP BY contractor_id) AS subquery', [
            'payment'        => TransactionEntity::TRANSACTION_PAYMENT,
            'debt'           => TransactionEntity::TRANSACTION_DEBT,
            'contractorType' => TransactionEntity::CONTRACTOR_PLANT,
            'companyId'      => $companyId,
            'created'        => $date->format('Y-m-d H:i:s'),
        ]);


        if (!$dataSource instanceof ResultSetInterface)
            throw new Exception\ErrorException('Prepayment sum was not received. Unknown database error.');
        return $dataSource->current();
    }

    /**
     * Устаной фонд.
     *
     * Возвращает сумму внутренней кредиторской задолженности предприятия на указанную дату.
     * Если дата не указана, будет возвращена сумма состоянием на текущую дату.
     *
     * @param int $companyId
     * @param DateTime|null $date
     * @return array|ArrayObject|null
     * @throws Exception\ErrorException
     */
    public function fetchInternalPayableSum(int $companyId, DateTime $date = null) {
        if (!$date instanceof DateTime)
            $date = new DateTime($date);

        $dataSource = $this->dbAdapter->query('
          SELECT SUM(amount) AS amount, currency FROM ( SELECT currency, 
            (SELECT COALESCE(SUM(debit), 0) FROM finance_transactions
             WHERE transaction_type = :payment AND contractor_id = trans.contractor_id AND contractor_type = trans.contractor_type) -
            (SELECT COALESCE(SUM(credit), 0) FROM finance_transactions
             WHERE transaction_type = :payment AND contractor_id = trans.contractor_id AND contractor_type = trans.contractor_type)
          AS amount FROM finance_transactions AS trans
          WHERE company_id = :companyId AND contractor_type = :contractorType AND created <= :created
          GROUP BY contractor_id) AS subquery', [
            'payment'        => TransactionEntity::TRANSACTION_PAYMENT,
            'contractorType' => TransactionEntity::CONTRACTOR_CORPORATE,
            'companyId'      => $companyId,
            'created'        => $date->format('Y-m-d H:i:s'),
        ]);

        if (!$dataSource instanceof ResultSetInterface)
            throw new Exception\ErrorException('Customer payment sum was not received. Unknown database error.');
        return $dataSource->current();
    }

    /**
     * Возвращает объекты финансовых транзакций предприятия.
     * Ксли указан параметр $contractorType, тогда будут возвращены только транзакции по указанному типу контрагентов.
     *
     * Подедрживаемые типы контрагентов:
     * TransactionEntity::CONTRACTOR_CARRIER  - "carrier"
     * TransactionEntity::CONTRACTOR_CUSTOMER - "customer"
     * TransactionEntity::CONTRACTOR_EXTRA    - "extra"
     * TransactionEntity::CONTRACTOR_INTERNAL - "internal"
     *
     * @param int $companyId
     * @param string|null $contractorType
     * @return Paginator
     */
    public function fetchTransactionPaginator(int $companyId, $contractorType = null) {
    }

    public function fetchTransactionById(int $transactionId) {
        $sql = new Sql($this->dbAdapter);
        $select = $sql->select(self::TABLE_FINANCE_TRANSACTIONS);
        $select->where->equalTo('transaction_id', $transactionId);
        $dataSource = $sql->prepareStatementForSqlObject($select)->execute();
        $resultSet = new HydratingResultSet($this->hydrator, $this->prototype);
        $resultSet->initialize($dataSource);
        /** @var TransactionEntity $object */
        $object = $resultSet->current();
        return $object;
    }

    /**
     * @param TransactionEntity $transaction
     * @return TransactionEntity
     * @throws Exception\ErrorException
     */
    public function saveTransaction(TransactionEntity $transaction) {
        $data = $this->hydrator->extract($transaction);
        $sql = new Sql($this->dbAdapter);
        if ($transactionId = $transaction->getTransactionId()) {
            $action = $sql->update(self::TABLE_FINANCE_TRANSACTIONS);
            $action->set($data);
            $action->where->equalTo('transaction_id', $transactionId);
        } else {
            $action = $sql->insert(self::TABLE_FINANCE_TRANSACTIONS);
            $action->values($data);
        }

        $dataSource = $sql->prepareStatementForSqlObject($action)->execute();

        if (!$dataSource instanceof ResultInterface)
            throw new Exception\ErrorException('Transaction was not saved.');

        if ($generatedId = $dataSource->getGeneratedValue())
            $transaction->setTransactionId($generatedId);

        return $transaction;
    }

    /**
     * @param $transactionId
     * @return void
     * @throws Exception\ErrorException
     */
    public function deleteTransaction($transactionId) {
        $sql = new Sql($this->dbAdapter);
        $action = $sql->delete(self::TABLE_FINANCE_TRANSACTIONS);
        $action->where->equalTo('transaction_id', $transactionId);
        $action->where->equalTo('is_manual', 1);
        $dataSource = $sql->prepareStatementForSqlObject($action)->execute();
        if (!$dataSource instanceof ResultInterface)
            throw new Exception\ErrorException('Transaction was not deleted.');
        return;
    }

    public function deleteTransactionByWagonId(int $wagonId, string $contractType = null) {
        $sql = new Sql($this->dbAdapter);
        $action = $sql->delete(self::TABLE_FINANCE_TRANSACTIONS);
        $action->where->equalTo('wagon_id', $wagonId);
        if (trim($contractType))
            $action->where->equalTo('contract_type', $contractType);
        $dataSource = $sql->prepareStatementForSqlObject($action)->execute();
        if (!$dataSource instanceof ResultInterface)
            throw new Exception\ErrorException('Transaction was not deleted.');
        return;
    }


}