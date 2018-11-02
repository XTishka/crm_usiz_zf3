<?php

namespace User\Service;

use Application\Service\Result;
use Zend\Authentication\Adapter\DbTable\CallbackCheckAdapter;
use Zend\Authentication\AuthenticationService;
use Zend\Session\SessionManager;

class AuthManager {

    /**
     * @var AuthenticationService
     */
    protected $authenticationService;

    /**
     * @var SessionManager
     */
    protected $sessionManager;

    public function __construct(AuthenticationService $authenticationService, SessionManager $sessionManager) {
        $this->authenticationService = $authenticationService;
        $this->sessionManager = $sessionManager;
    }

    /**
     * @param array $data
     * @return Result
     * @throws \Zend\Authentication\Exception\ExceptionInterface
     */
    public function login(array $data) {
        /** @var CallbackCheckAdapter $adapter */
        $adapter = $this->authenticationService->getAdapter();
        $adapter->setCredential($data['password']);
        $adapter->setIdentity($data['email']);
        $adapter->setCredentialValidationCallback(function ($hash, $credential) {
            return password_verify($credential, $hash);
        });
        $select = $adapter->getDbSelect();
        $select->where->equalTo('is_active', 1);

        $result = $this->authenticationService->authenticate();

        if (!$result->isValid())
            return new Result(Result::STATUS_ERROR, 'Authorization Error');
        $contents = $adapter->getResultRowObject(['user_id', 'first_name', 'last_name', 'role']);
        $contents->username = sprintf('%s %s', $contents->first_name, $contents->last_name);
        $this->authenticationService->getStorage()->write($contents);
        if (array_key_exists('remember_me', $data) && $data['remember_me'])
            $this->sessionManager->rememberMe(604800);
        return new Result(Result::STATUS_SUCCESS, 'You have been successfully authorized on the site');
    }

    public function logout() {
        $this->authenticationService->clearIdentity();
        $this->sessionManager->forgetMe();
    }

}