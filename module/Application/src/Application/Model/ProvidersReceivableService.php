<?php

namespace Application\Model;

use Contractor\Service\Repository\DatabaseContractorAbstract;
use Document\Domain\TransactionEntity;
use Document\Service\Repository\FinanceTransactionDb;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Sql;

class ProvidersReceivableService {

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
     * @return ProvidersReceivableContainer
     */
    public function getRecords($companyId, $dateTime = null) {

        if (!$dateTime instanceof \DateTime) {
            $dateTime = trim($dateTime) ? \DateTime::createFromFormat('d.m.Y', $dateTime) : new \DateTime();
            $dateTime->setTime(23, 59, 59);
        }

        $sql = new Sql($this->db);

        $select = $sql->select(['a' => FinanceTransactionDb::TABLE_FINANCE_TRANSACTIONS]);
        $select->join(['b' => DatabaseContractorAbstract::TABLE_CONTRACTORS], 'a.contractor_id = b.contractor_id AND a.contractor_type = b.contractor_type', ['contractor_name']);
        $select->columns([
            'credit',
            'debit',
            'company_id',
            'contractor_id',
            'contractor_type',
            'transaction_type',
        ]);
        $select->where->equalTo('a.contractor_type', TransactionEntity::CONTRACTOR_PROVIDER);
        $select->where->equalTo('a.company_id', $companyId);
        $select->where->lessThanOrEqualTo('a.created', $dateTime->format('Y-m-d H:i:s'));
        $select->having->nest()->greaterThan('credit', 0)->or->greaterThan('debit', 0);

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

            if (!array_key_exists($index, $data)) {
                $data[$index] = clone $item;
                $data[$index]->amount = 0;
            }

            if ($item->transaction_type == TransactionEntity::TRANSACTION_DEBT && 0 < $item->debit) {
                // Поставщик должен нам (debit)
                $data[$index]->amount = bcadd($data[$index]->amount, $item->debit, 2);
            } elseif ($item->transaction_type == TransactionEntity::TRANSACTION_DEBT && 0 < $item->credit) {
                // Мы должны поставщику (credit)
                $data[$index]->amount = bcsub($data[$index]->amount, $item->credit, 2);
            } elseif ($item->transaction_type == TransactionEntity::TRANSACTION_PAYMENT && 0 < $item->debit) {
                // Поставщик платит нам (debit)
                $data[$index]->amount = bcsub($data[$index]->amount, $item->debit, 2);
            } elseif ($item->transaction_type == TransactionEntity::TRANSACTION_PAYMENT && 0 < $item->credit) {
                // Мы платим поставщику (credit)
                $data[$index]->amount = bcadd($data[$index]->amount, $item->credit, 2);
            }

            /*
            elseif ($item->transaction_type == TransactionEntity::TRANSACTION_PAYMENT && 0 < $item->debit) {
                $data[$index]->amount = bcsub($data[$index]->amount, $item->debit, 2);
            } elseif ($item->transaction_type == TransactionEntity::TRANSACTION_DEBT && 0 < $item->credit) {
                $data[$index]->amount = bcsub($data[$index]->amount, $item->credit, 2);
            }
            */
        }
        
        $data = array_filter($data, function (\ArrayObject $current) {
            return (0 < $current->offsetGet('amount'));
        });

        uasort($data, function ($a, $b) {
            return ($b->amount - $a->amount);
        });

        return new ProvidersReceivableContainer($data);
    }

}