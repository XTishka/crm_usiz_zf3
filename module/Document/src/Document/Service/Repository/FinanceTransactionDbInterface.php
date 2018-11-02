<?php

namespace Document\Service\Repository;

use ArrayObject;
use DateTime;
use Document\Domain\TransactionEntity;
use Document\Exception;
use Zend\Paginator\Paginator;

interface FinanceTransactionDbInterface {

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
    public function fetchCompanyBalance(int $companyId, DateTime $date = null);

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
    public function fetchCompanyReceivableSum(int $companyId, DateTime $date = null);

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
    public function fetchCompanyPrepaymentSum(int $companyId, DateTime $date = null);

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
    public function fetchCompanyPayableSum(int $companyId, DateTime $date = null);

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
    public function fetchCustomerPrepaymentSum(int $companyId, DateTime $date = null);

    /**
     * Устаной фонд.
     *
     * Возвращает сумму внутренней кредиторской задолженности предприятия на указанную дату.
     * Если дата не указана, будет возвращена сумма состоянием на текущую дату.
     *
     * @param int $companyId
     * @param DateTime|null $date
     * @return ArrayObject
     * @throws Exception\ErrorException
     */
    public function fetchInternalPayableSum(int $companyId, DateTime $date = null);

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
    public function fetchTransactionPaginator(int $companyId, $contractorType = null);

    /**
     * @param TransactionEntity $transaction
     * @return TransactionEntity
     * @throws Exception\ErrorException
     */
    public function saveTransaction(TransactionEntity $transaction);

    /**
     * @param $transactionId
     * @return void
     * @throws Exception\ErrorException
     */
    public function deleteTransaction($transactionId);

}