<?php

namespace Application\Model\Finance;

use Contractor\Entity\ContractorCustomer;
use Contractor\Service\Repository\DatabaseContractorAbstract;
use Document\Domain\TransactionEntity;
use Document\Service\Repository\FinanceTransactionDb;
use Transport\Service\Repository\CarrierDb;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Sql;

class AccountsPayableService extends AbstractService {

    /**
     * @param $companyId
     * @return ContainerInterface
     * @throws \Exception
     */
    function getRecords($companyId): ContainerInterface {
        $sql = new Sql($this->db);

        $selectA = $sql->select(['a' => FinanceTransactionDb::TABLE_FINANCE_TRANSACTIONS]);
        $selectA->join(['b' => DatabaseContractorAbstract::TABLE_CONTRACTORS], 'a.contractor_id = b.contractor_id AND a.contractor_type = b.contractor_type', ['contractor_name']);
        $selectA->columns(['credit', 'debit', 'company_id', 'contractor_id', 'contractor_type', 'transaction_type']);
        $selectA->where->equalTo('a.company_id', $companyId);
        //$selectA->where->equalTo('a.transaction_type', TransactionEntity::TRANSACTION_DEBT);
        $selectA->where->notIn('a.contractor_type', [ContractorCustomer::TYPE_CUSTOMER]);
        $selectA->where->lessThanOrEqualTo('a.created', $this->getDate()->format('Y-m-d H:i:s'));
        $selectA->having->nest()->greaterThan('credit', 0)->or->greaterThan('debit', 0);

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

            if ($item->transaction_type == TransactionEntity::TRANSACTION_DEBT && 0 < $item->debit) {
                $dataA[$index]->amount = bcsub($dataA[$index]->amount, $item->debit, 2);
            } elseif ($item->transaction_type == TransactionEntity::TRANSACTION_PAYMENT && 0 < $item->debit) {
                $dataA[$index]->amount = bcadd($dataA[$index]->amount, $item->debit, 2);
            } elseif ($item->transaction_type == TransactionEntity::TRANSACTION_DEBT && 0 < $item->credit) {
                $dataA[$index]->amount = bcadd($dataA[$index]->amount, $item->credit, 2);
            } elseif ($item->transaction_type == TransactionEntity::TRANSACTION_PAYMENT && 0 < $item->credit) {
                $dataA[$index]->amount = bcsub($dataA[$index]->amount, $item->credit, 2);
            }

        }

        $selectB = $sql->select(['a' => FinanceTransactionDb::TABLE_FINANCE_TRANSACTIONS]);
        $selectB->join(['b' => CarrierDb::TABLE_CARRIERS], 'a.contractor_id = b.carrier_id', ['contractor_name' => 'carrier_name']);
        $selectB->columns([
            'credit',
            'debit',
            'company_id',
            'contractor_id',
            'contractor_type',
            'transaction_type',
        ]);
        $selectB->where->equalTo('a.contractor_type', TransactionEntity::CONTRACTOR_CARRIER);
        $selectB->where->equalTo('a.company_id', $companyId);
        $selectB->where->lessThanOrEqualTo('a.created', $this->getDate()->format('Y-m-d H:i:s'));
        $selectA->having->nest()->greaterThan('credit', 0)->or->greaterThan('debit', 0);

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

            if ($item->transaction_type == TransactionEntity::TRANSACTION_DEBT && 0 < $item->debit) {
                $dataB[$index]->amount = bcsub($dataB[$index]->amount, $item->debit, 2);
            } elseif ($item->transaction_type == TransactionEntity::TRANSACTION_PAYMENT && 0 < $item->debit) {
                $dataB[$index]->amount = bcadd($dataB[$index]->amount, $item->debit, 2);
            } elseif ($item->transaction_type == TransactionEntity::TRANSACTION_DEBT && 0 < $item->credit) {
                $dataB[$index]->amount = bcadd($dataB[$index]->amount, $item->credit, 2);
            } elseif ($item->transaction_type == TransactionEntity::TRANSACTION_PAYMENT && 0 < $item->credit) {
                $dataB[$index]->amount = bcsub($dataB[$index]->amount, $item->credit, 2);
            }
        }

        $data = array_merge($dataA, $dataB);

        $data = array_filter($data, function ($record) {
            return (0 < $record->amount);
        });

        uasort($data, function ($a, $b) {
            return ($b->amount - $a->amount);
        });

        return new AccountsPayableContainer($data);
    }


}