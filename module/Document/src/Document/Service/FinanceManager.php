<?php

namespace Document\Service;

use Application\Service;
use ArrayObject;
use Contractor\Entity\ContractorCompany;
use Contractor\Service\Repository\DatabaseContractorAbstract;
use Contractor\Service\Repository\DatabaseContractorCarrier;
use DateTime;
use Document\Domain\TransactionEntity;
use Document\Exception;
use Document\Service\Repository\FinanceTransactionDb;
use PhpOffice\PhpSpreadsheet\Shared\Date as PhpSpreadsheetSharedDate;
use Transport\Service\Repository\CarrierDb;
use Zend\Db\Sql\Sql;
use Zend\Paginator\Paginator;

class FinanceManager implements FinanceManagerInterface {

    /**
     * @var FinanceTransactionDb
     */
    protected $financeTransactionDbRepository;

    /**
     * FinanceManager constructor.
     * @param FinanceTransactionDb $financeTransactionDbRepository
     */
    public function __construct(FinanceTransactionDb $financeTransactionDbRepository) {
        $this->financeTransactionDbRepository = $financeTransactionDbRepository;
    }

    /**
     * Баланс предприятия.
     *
     * Возвращает баланс предприятия состоянием на указанную дату.
     * Если дата не указана, будет возвращен баланс состоянием на текущую дату.
     *
     * @param int $companyId
     * @param DateTime|null $date
     * @return ArrayObject|float
     * @throws Exception\ErrorException
     */
    public function getCompanyBalance(int $companyId, DateTime $date = null) {
        return $this->financeTransactionDbRepository->fetchCompanyBalance($companyId, $date);
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
     */
    public function getCompanyReceivableSum(int $companyId, DateTime $date = null) {
        return $this->financeTransactionDbRepository->fetchCompanyReceivableSum($companyId, $date);
    }

    /**
     * Предоплата предприятия.
     *
     * Возвращает сумму предоплат предприятия состоянием на указанную дату.
     * Если дата не указана, будет возвращена сумма состоянием на текущую дату.
     *
     * @param int $companyId
     * @param DateTime|null $date
     * @return ArrayObject|float
     * @throws Exception\ErrorException
     */
    public function getCompanyPrepaymentSum(int $companyId, DateTime $date = null) {
        return $this->financeTransactionDbRepository->fetchCompanyPrepaymentSum($companyId, $date);
    }

    /**
     * @param int $plantId
     * @param DateTime|null $date
     * @return ArrayObject
     * @throws Exception\ErrorException
     */
    public function getPlantPrepaymentSum(int $plantId, DateTime $date = null) {
        return $this->financeTransactionDbRepository->fetchPlantPrepaymentSum($plantId, $date);
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
     */
    public function getCompanyPayableSum(int $companyId, DateTime $date = null) {
        return $this->financeTransactionDbRepository->fetchCompanyPayableSum($companyId, $date);
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
     */
    public function getCustomerPrepaymentSum(int $companyId, DateTime $date = null) {
        return $this->financeTransactionDbRepository->fetchCustomerPrepaymentSum($companyId, $date);
    }

    /**
     * @param int $plantId
     * @param DateTime|null $date
     * @return array|ArrayObject|null
     * @throws Exception\ErrorException
     */
    public function getPlantFromCompanyPrepaymentSum(int $plantId, DateTime $date = null) {
        return $this->financeTransactionDbRepository->fetchPlantFromCompanyPrepaymentSum($plantId, $date);
    }

    /**
     * @param int $plantId
     * @param DateTime|null $date
     * @return array|ArrayObject|null
     * @throws Exception\ErrorException
     */
    public function getCompanyToPlantPrepaymentSum(int $plantId, DateTime $date = null) {
        return $this->financeTransactionDbRepository->fetchCompanyToPlantPrepaymentSum($plantId, $date);
    }

    /**
     * Устаной фонд.
     *
     * Возвращает сумму внутренней кредиторской задолженности предприятия на указанную дату.
     * Если дата не указана, будет возвращена сумма состоянием на текущую дату.
     *
     * @param int $companyId
     * @param DateTime|null $date
     * @return ArrayObject
     */
    public function getInternalPayableSum(int $companyId, DateTime $date = null) {
        return $this->financeTransactionDbRepository->fetchInternalPayableSum($companyId, $date);
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
    public function getTransactionPaginator(int $companyId, $contractorType = null) {
        // TODO: Implement getTransactionPaginator() method.
    }

    public function getTransactionById($transactionId) {
        return $this->financeTransactionDbRepository->fetchTransactionById($transactionId);
    }

    /**
     * @param TransactionEntity $transaction
     * @return TransactionEntity
     * @throws Exception\ErrorException
     */
    public function saveTransaction(TransactionEntity $transaction) {
        return $this->financeTransactionDbRepository->saveTransaction($transaction);
    }

    /**
     * @param $transactionId
     * @return void
     * @throws Exception\ErrorException
     */
    public function deleteTransaction($transactionId) {
        $this->financeTransactionDbRepository->deleteTransaction($transactionId);
    }

    /**
     * @param $wagonId
     * @param string $contractType
     */
    public function deleteTransactionByWagonId($wagonId, $contractType = null) {
        $wagonId = intval($wagonId);
        return $this->financeTransactionDbRepository->deleteTransactionByWagonId($wagonId, $contractType);
    }

    /**
     * @param ContractorCompany $company
     * @param $file
     * @return Service\Result
     * @throws Exception\ErrorException
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function importTransactions(ContractorCompany $company, $file) {

        try {
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            $spreadsheet = $reader->load($file['tmp_name']);
        } catch (\PhpOffice\PhpSpreadsheet\Exception $exception) {
            return new Service\Result('error', sprintf('The wagon data was not imported. (%s)', $exception->getMessage()));
        } catch (\InvalidArgumentException $exception) {
            return new Service\Result('error', sprintf('The wagon data was not imported. (%s)', $exception->getMessage()));
        }

        $errors = [];


        $adapter = $this->financeTransactionDbRepository->getDbAdapter();
        $sql = new Sql($adapter);

        $selectContractors = $sql->select(DatabaseContractorAbstract::TABLE_CONTRACTORS);
        $selectContractors->columns(['contractor_id', 'contractor_name', 'contractor_type']);

        $stmt = $sql->prepareStatementForSqlObject($selectContractors);
        $contractors = $stmt->execute();

        $contractorsIndex = [];

        foreach ($contractors as $key => $contractor) {
            $hash = md5($contractor['contractor_name']);
            $contractorsIndex[$hash] = [
                'contractor_id'   => $contractor['contractor_id'],
                'contractor_name' => $contractor['contractor_name'],
                'contractor_type' => $contractor['contractor_type'],
            ];
        }

        $selectCarriers = $sql->select(CarrierDb::TABLE_CARRIERS);
        $selectCarriers->columns(['carrier_id', 'carrier_name']);

        $stmt = $sql->prepareStatementForSqlObject($selectCarriers);
        $carriers = $stmt->execute();

        foreach ($carriers as $key => $carrier) {
            $hash = md5($carrier['carrier_name']);
            $contractorsIndex[$hash] = [
                'contractor_id'   => $carrier['carrier_id'],
                'contractor_name' => $carrier['carrier_name'],
                'contractor_type' => ContractorCompany::TYPE_CARRIER,
            ];
        }

        $sheet = $spreadsheet->getActiveSheet();

        $this->financeTransactionDbRepository->beginTransaction();

        foreach ($sheet->getRowIterator() as $key => $row) {

            $rowIndex = $row->getRowIndex();
            if ($rowIndex < 2) {
                continue;
            }

            $hash = md5(trim($sheet->getCell(sprintf('A%d', $rowIndex))->getValue()));
            $credit = $sheet->getCell(sprintf('B%d', $rowIndex))->getValue();
            $debit = $sheet->getCell(sprintf('C%d', $rowIndex))->getValue();
            $transactionType = $sheet->getCell(sprintf('D%d', $rowIndex))->getValue();
            $date = $sheet->getCell(sprintf('E%d', $rowIndex))->getValue();
            $comment = $sheet->getCell(sprintf('F%d', $rowIndex))->getValue();

            if ($transactionType == TransactionEntity::TRANSACTION_DEBT || mb_strtolower($transactionType) == 'задолженность') {
                $transactionType = TransactionEntity::TRANSACTION_DEBT;
            } elseif (($transactionType == TransactionEntity::TRANSACTION_PAYMENT) || (mb_strtolower($transactionType) == 'платеж')) {
                $transactionType = TransactionEntity::TRANSACTION_PAYMENT;
            } else {
                $errors[] = sprintf('Указан недопустимый тип транзакции в строке %d', $rowIndex);
            }

            if (preg_match('/^\d{2}\.\d{2}\.\d{4}$/', $date)) {
                $date = \DateTime::createFromFormat('d.m.Y', $date);
            } else {
                $date = PhpSpreadsheetSharedDate::excelToDateTimeObject($date);
            }
            $date->setTime(23, 59, 59);

            if (!key_exists($hash, $contractorsIndex)) {
                $errors[] = sprintf('Указан недопустимый контрагент в строке %d', $rowIndex);
                continue;
            }

            $contractorId = $contractorsIndex[$hash]['contractor_id'];
            $contractorType = $contractorsIndex[$hash]['contractor_type'];

            $transaction = new TransactionEntity($transactionType, $contractorType);
            $transaction->setCreated($date);
            $transaction->setCompanyId($company->getContractorId());
            $transaction->setContractorId($contractorId);
            $transaction->setDebit(floatval($debit));
            $transaction->setCredit(floatval($credit));
            $transaction->setComment($comment);
            $transaction->setIsManual(1);

            $this->saveTransaction($transaction);

        }

        if (0 < count($errors)) {
            $this->financeTransactionDbRepository->rollback();
            return new Service\Result('warning', 'Some finance transaction data was not imported.', $errors);
        }
        $this->financeTransactionDbRepository->commit();
        return new Service\Result('success', 'All finance transactions data was successfully imported.');
    }

}