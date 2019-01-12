<?php
/**
 * @author    Bogdan Tereshchenko <development.sites@gmail.com>
 */

namespace Application\Model\Finance;

use Contractor\Entity\ContractorCorporate;
use Contractor\Service\Repository\DatabaseContractorAbstract;
use Document\Domain\TransactionEntity;
use Document\Service\Repository\FinanceTransactionDb;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Sql;

/**
 * Сервис получает записи уставного фонда
 *
 * Class CharterCapitalService
 * @package Application\Model\Finance
 */
class CharterCapitalService extends AbstractService {

    /**
     * Возвращает набор записей уставного фонда
     *
     * @param $companyId
     * @return ContainerInterface
     * @throws \Exception
     */
    function getRecords($companyId): ContainerInterface {
        $date = $this->getDate();
        $date->setTime(23, 59, 59);

        $sql = new Sql($this->db);

        $select = $sql->select(['tsn' => FinanceTransactionDb::TABLE_FINANCE_TRANSACTIONS]);
        $select->columns([
            'credit',
            'debit',
            'company_id',
            'contractor_id',
            'contractor_type',
            'transaction_type',
        ]);
        $select->join(['con' => DatabaseContractorAbstract::TABLE_CONTRACTORS],
            'tsn.contractor_id = con.contractor_id AND tsn.contractor_type = con.contractor_type',
            ['contractor_name']);
        $select->where->equalTo('tsn.company_id', $companyId);
        $select->where->equalTo('tsn.contractor_type', ContractorCorporate::TYPE_CORPORATE);
        $select->where->lessThanOrEqualTo('tsn.created', $this->date->format('Y-m-d H:i:s'));
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

            switch (true) {
                case TransactionEntity::TRANSACTION_DEBT == $item->transaction_type && 0 < $item->debit:
                    $data[$index]->amount = bcsub($data[$index]->amount, $item->debit, 2);
                    break;
                case TransactionEntity::TRANSACTION_PAYMENT == $item->transaction_type && 0 < $item->debit:
                    $data[$index]->amount = bcadd($data[$index]->amount, $item->debit, 2);
                    break;
                case TransactionEntity::TRANSACTION_DEBT == $item->transaction_type && 0 < $item->credit:
                    $data[$index]->amount = bcadd($data[$index]->amount, $item->credit, 2);
                    break;
                case TransactionEntity::TRANSACTION_PAYMENT == $item->transaction_type && 0 < $item->credit:
                    $data[$index]->amount = bcsub($data[$index]->amount, $item->credit, 2);
                    break;
                default:
                    throw new \Exception('Invalid transaction type given');
            }

        }

        $data = array_filter($data, function ($record) {
            return (0 < $record->amount);
        });

        uasort($data, function ($a, $b) {
            return ($b->amount - $a->amount);
        });

        return new CharterCapitalContainer($data);
    }

}