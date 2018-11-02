<?php

namespace User\Service;

use Application\Service\Result;
use User\Entity;
use User\Exception\ErrorException;

class AccountManager {

    /**
     * @var Repository\DatabaseAccount
     */
    protected $databaseAccountRepository;

    /**
     * AccountManager constructor.
     * @param Repository\DatabaseAccount $databaseAccountRepository
     */
    public function __construct(Repository\DatabaseAccount $databaseAccountRepository) {
        $this->databaseAccountRepository = $databaseAccountRepository;
    }

    /**
     * @return \Zend\Paginator\Paginator
     */
    public function getAccountWithPaginator() {
        return $this->databaseAccountRepository->fetchAccountsWithPaginator();
    }

    /**
     * @param $userId
     * @return \User\Entity\Account
     * @throws \User\Exception\ErrorException
     */
    public function getOneAccountById($userId) {
        return $this->databaseAccountRepository->fetchOneAccountById((int)$userId);
    }

    /**
     * @param Entity\Account $account
     * @return Result
     */
    public function saveAccount(Entity\Account $account) {
        try {
            $account = $this->databaseAccountRepository->saveAccount($account);
        } catch (ErrorException $errorException) {
            return new Result('error', $errorException->getMessage());
        }
        return new Result('success', 'The user account was successfully saved.', $account);
    }

    /**
     * @param $userId
     * @return Result
     */
    public function deleteAccountById($userId) {
        try {
            $this->databaseAccountRepository->deleteAccountById((int)$userId);
        } catch (ErrorException $errorException) {
            return new Result('error', $errorException->getMessage());
        }
        return new Result('success', 'The user account was successfully deleted.');
    }

}