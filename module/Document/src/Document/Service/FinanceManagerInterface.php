<?php

namespace Document\Service;

use DateTime;
use Document\Domain\TransactionEntity;
use Document\Exception;
use Zend\Paginator\Paginator;

interface FinanceManagerInterface {

    /**
     * Баланс предприятия.
     *
     * Возвращает баланс предприятия состоянием на указанную дату.
     * Если дата не указана, будет возвращен баланс состоянием на текущую дату.
     *
     * @param int $companyId
     * @param DateTime $date
     * @return double
     */
    public function getCompanyBalance(int $companyId, DateTime $date = null);

    /**
     * Дебиторская задолженность.
     *
     * Возвращает дебиторскую задолженность предприятию состоянием на указанную дату.
     * Если дата не указана, будет возвращена сумма состоянием на текущую дату.
     *
     * @param int $companyId
     * @param DateTime|null $date
     * @return double
     */
    public function getCompanyReceivableSum(int $companyId, DateTime $date = null);

    /**
     * Предоплата предприятия.
     *
     * Возвращает сумму предоплат предприятия состоянием на указанную дату.
     * Если дата не указана, будет возвращена сумма состоянием на текущую дату.
     *
     * @param int $companyId
     * @param DateTime|null $date
     * @return double
     */
    public function getCompanyPrepaymentSum(int $companyId, DateTime $date = null);

    /**
     * Кредиторская задолженность.
     *
     * Возвращает сумму кредиторской задолженности предприятия на указанную дату.
     * Если дата не указана, будет возвращена сумма состоянием на текущую дату.
     *
     * @param int $companyId
     * @param DateTime|null $date
     * @return double
     */
    public function getCompanyPayableSum(int $companyId, DateTime $date = null);

    /**
     * Предоплата покупателей.
     *
     * Возвращает сумму предоплаты покупателей предприятию состоянием на указанную дату.
     * Если дата не указана, будет возвращена сумма состоянием на текущую дату.
     *
     * @param int $companyId
     * @param DateTime|null $date
     * @return double
     */
    public function getCustomerPrepaymentSum(int $companyId, DateTime $date = null);

    /**
     * Устаной фонд.
     *
     * Возвращает сумму внутренней кредиторской задолженности предприятия на указанную дату.
     * Если дата не указана, будет возвращена сумма состоянием на текущую дату.
     *
     * @param int $companyId
     * @param DateTime|null $date
     * @return double
     */
    public function getInternalPayableSum(int $companyId, DateTime $date = null);

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
    public function getTransactionPaginator(int $companyId, $contractorType = null);

    /**
     * @param TransactionEntity $receivable
     * @return TransactionEntity
     * @throws Exception\ErrorException
     */
    public function saveTransaction(TransactionEntity $receivable);

    /**
     * @param $transactionId
     * @return void
     * @throws Exception\ErrorException
     */
    public function deleteTransaction($transactionId);

}