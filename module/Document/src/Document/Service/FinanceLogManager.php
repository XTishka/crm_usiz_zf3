<?php

namespace Document\Service;

use Application\Service\Result;
use Document\Domain\FinanceLogEntity;
use Document\Exception;
use Document\Service\Repository;

class FinanceLogManager {

    /**
     * @var Repository\FinanceLogDb
     */
    protected $financeLogDbRepository;

    public function getTransactionLogPaginator() {
        return $this->financeLogDbRepository->fetchLogWithPaginator();
    }

    /**
     * FinanceLogManager constructor.
     * @param Repository\FinanceLogDb $financeLogDbRepository
     */
    public function __construct(Repository\FinanceLogDb $financeLogDbRepository) {
        $this->financeLogDbRepository = $financeLogDbRepository;
    }

    public function getCarrierBalances() {
        return $this->financeLogDbRepository->fetchCarrierBalances();
    }

    public function getCompanyBalances($companyId = null) {
        return $this->financeLogDbRepository->fetchCompanyBalances($companyId);
    }

    public function getCustomerBalances() {
        return $this->financeLogDbRepository->fetchCustomerBalances();
    }

    public function getProviderBalances() {
        return $this->financeLogDbRepository->fetchProviderBalances();
    }

    public function debit(FinanceLogEntity $object) {
        try {
            $object->setOperationType($object::TYPE_DEBIT);
            $object = $this->financeLogDbRepository->saveLog($object);
        } catch (Exception\ErrorException $exception) {
            return new Result('error', $exception->getMessage());
        }
        return new Result('success', 'Debit transaction successfully saved', $object);
    }

    public function credit(FinanceLogEntity $object) {
        try {
            $object->setOperationType($object::TYPE_CREDIT);
            $object = $this->financeLogDbRepository->saveLog($object);
        } catch (Exception\ErrorException $exception) {
            return new Result('error', $exception->getMessage());
        }
        return new Result('success', 'Credit transaction successfully saved', $object);
    }

    public function deleteLogByWagonId($wagonId) {
        try {
            $this->financeLogDbRepository->deleteLogByWagonId($wagonId);
        } catch (Exception\ErrorException $exception) {
            return new Result('error', $exception->getMessage());
        }
        return new Result('success', 'Finance transaction successfully deleted.');
    }

}