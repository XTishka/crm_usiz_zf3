<?php

namespace Application\Model;

use Contractor\Service\Repository\DatabaseContractorAbstract;
use Document\Domain\TransactionEntity;
use Document\Service\Repository\FinanceTransactionDb;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Sql;

class CustomerPayableService {

    /** @var Adapter */
    protected $db;

    /**
     * AccountsPayableService constructor.
     * @param Adapter $db
     */
    public function __construct(Adapter $db) {
        $this->db = $db;
    }

    /**
     * @param $companyId
     * @param null $dateTime
     * @return CustomerPayableContainer
     */
    public function getRecords($companyId, $dateTime = null) {

        if (!$dateTime instanceof \DateTime) {
            $dateTime = trim($dateTime) ? \DateTime::createFromFormat('d.m.Y', $dateTime) : new \DateTime();
            $dateTime->setTime(23, 59, 59);
        }

        $sql = new Sql($this->db);

        $select = $sql->select(['a' => FinanceTransactionDb::TABLE_FINANCE_TRANSACTIONS]);
        $select->join(['b' => DatabaseContractorAbstract::TABLE_CONTRACTORS], 'a.contractor_id = b.contractor_id AND a.contractor_type = b.contractor_type', ['contractor_name', 'contractor_type']);
        $select->columns(['credit', 'debit', 'company_id', 'contractor_id', 'transaction_type']);
        $select->where->equalTo('a.contractor_type', TransactionEntity::CONTRACTOR_CUSTOMER);
        $select->where->equalTo('a.company_id', $companyId);
        $select->where->lessThanOrEqualTo('a.created', $dateTime->format('Y-m-d H:i:s'));
        $select->having->nest()->greaterThan('credit', 0)->or->greaterThan('debit', 0);
        $select->group('a.transaction_id');

        $dataSource = $sql->prepareStatementForSqlObject($select)->execute();


        $resultSet = new ResultSet(ResultSet::TYPE_ARRAYOBJECT);
        $resultSet->initialize($dataSource);

        //echo '<pre style="font-size:10px">'; print_r($resultSet->toArray()); echo '</pre>'; exit;

        $data = [];
        foreach ($resultSet as $item) {
            $index = \join([
                $item['company_id'],
                $item['contractor_id'],
            ]);

            if (!array_key_exists($index, $data)) {
                $data[$index] = clone $item;
                $data[$index]->amount = 0;
            }

            /*
            if ($item->transaction_type == TransactionEntity::TRANSACTION_DEBT && 0 < $item->debit) {
                $data[$index]->amount = bcsub($data[$index]->amount, $item->debit, 2);
            } elseif ($item->transaction_type == TransactionEntity::TRANSACTION_DEBT && 0 < $item->credit) {
                //$data[$index]->amount = bcadd($data[$index]->amount, $item->credit, 2);
            } elseif ($item->transaction_type == TransactionEntity::TRANSACTION_PAYMENT && 0 < $item->debit) {
                $data[$index]->amount = bcadd($data[$index]->amount, $item->debit, 2);
            }
            */

            if ($item->transaction_type == TransactionEntity::TRANSACTION_DEBT && 0 < $item->debit) {
                //echo 'Покупатель должен нам';
                $data[$index]->amount = bcsub($data[$index]->amount, $item->debit, 2);
            } elseif ($item->transaction_type == TransactionEntity::TRANSACTION_PAYMENT && 0 < $item->debit) {
                //echo 'Покупатель заплатил нам: ' . $item->debit . "\n";
                $data[$index]->amount = bcadd($data[$index]->amount, $item->debit, 2);
            } elseif ($item->transaction_type == TransactionEntity::TRANSACTION_DEBT && 0 < $item->credit) {
                //echo 'Мы должны покупателю: ' . $item->credit . "\n";
                $data[$index]->amount = bcadd($data[$index]->amount, $item->credit, 2);
            } elseif ($item->transaction_type == TransactionEntity::TRANSACTION_PAYMENT && 0 < $item->credit) {
                //echo 'Мы заплатили покупателю: ' . $item->credit . "\n";
                $data[$index]->amount = bcsub($data[$index]->amount, $item->credit, 2);
            }

        }


        $data = array_filter($data, function (\ArrayObject $current) {
            return (0 < $current->offsetGet('amount'));
        });

        uasort($data, function ($a, $b) {
            return ($b->amount - $a->amount);
        });

        return new CustomerPayableContainer($data);
    }
}