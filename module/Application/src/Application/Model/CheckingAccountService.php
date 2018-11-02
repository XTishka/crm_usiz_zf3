<?php

namespace Application\Model;

use Contractor\Service\Repository\DatabaseContractorAbstract;
use Document\Domain\TransactionEntity;
use Document\Service\Repository\FinanceTransactionDb;
use Transport\Service\Repository\CarrierDb;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Sql;

class CheckingAccountService {

    /** @var Adapter */
    protected $db;

    /**
     * CheckingAccountService constructor.
     * @param Adapter $db
     */
    public function __construct(Adapter $db) {
        $this->db = $db;
    }

    /**
     * @param $companyId
     * @param null $dateTime
     * @return CheckingAccountContainer
     */
    public function getRecords($companyId, $dateTime = null) {

        if (!$dateTime instanceof \DateTime) {
            $dateTime = trim($dateTime) ? \DateTime::createFromFormat('d.m.Y', $dateTime) : new \DateTime();
            $dateTime->setTime(23, 59, 59);
        }

        $sql = new Sql($this->db);

        $selectA = $sql->select(['a' => FinanceTransactionDb::TABLE_FINANCE_TRANSACTIONS]);
        $selectA->join(['b' => DatabaseContractorAbstract::TABLE_CONTRACTORS], 'a.contractor_id = b.contractor_id AND a.contractor_type = b.contractor_type', ['contractor_name']);
        $selectA->columns([
            'debit',
            'credit',
            'company_id',
            'contractor_id',
            'contractor_type',
            'transaction_type',
        ]);
        $selectA->where->equalTo('a.transaction_type', TransactionEntity::TRANSACTION_PAYMENT);
        $selectA->where->equalTo('a.company_id', $companyId);
        $selectA->where->lessThanOrEqualTo('a.created', $dateTime->format('Y-m-d H:i:s'));

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

            if (!array_key_exists($index, $dataA)) {
                $dataA[$index] = clone $item;
                $dataA[$index]->amount = 0;
            }

            if (0 < $item->debit) {
                $dataA[$index]->amount = bcadd($dataA[$index]->amount, $item->debit, 2);
            } elseif (0 < $item->credit) {
                $dataA[$index]->amount = bcsub($dataA[$index]->amount, $item->credit, 2);
            }
        }

        $selectB = $sql->select(['a' => FinanceTransactionDb::TABLE_FINANCE_TRANSACTIONS]);
        $selectB->join(['b' => CarrierDb::TABLE_CARRIERS], 'a.contractor_id = b.carrier_id', ['contractor_name' => 'carrier_name']);
        $selectB->columns([
            'debit',
            'credit',
            'company_id',
            'contractor_id',
            'contractor_type',
            'transaction_type',
        ]);
        $selectB->where->equalTo('a.transaction_type', TransactionEntity::TRANSACTION_PAYMENT);
        $selectB->where->equalTo('a.contractor_type', TransactionEntity::CONTRACTOR_CARRIER);
        $selectB->where->equalTo('a.company_id', $companyId);
        $selectB->where->lessThanOrEqualTo('a.created', $dateTime->format('Y-m-d H:i:s'));

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

            if (!array_key_exists($index, $dataB)) {
                $dataB[$index] = clone $item;
                $dataB[$index]->amount = 0;
            }

            if (0 < $item->debit) {
                $dataB[$index]->amount = bcadd($dataB[$index]->amount, $item->debit, 2);
            } elseif (0 < $item->credit) {
                $dataB[$index]->amount = bcsub($dataB[$index]->amount, $item->credit, 2);
            }
        }

        $data = array_merge($dataA, $dataB);

        uasort($data, function ($a, $b) {
            return ($b->amount - $a->amount);
        });

        return new CheckingAccountContainer($data);
    }

}