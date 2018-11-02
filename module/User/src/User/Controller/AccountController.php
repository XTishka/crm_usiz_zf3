<?php

namespace User\Controller;

use User\Form;
use User\Service\AccountManager;
use Zend\Http;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\Controller\Plugin;
use Zend\Mvc\Plugin\FlashMessenger\FlashMessenger;
use Zend\View\Model\ViewModel;

class AccountController extends AbstractActionController {

    /**
     * @var Http\Request
     */
    protected $request;

    /**
     * @var AccountManager
     */
    protected $accountManager;

    /**
     * @var Form\Account
     */
    protected $accountForm;

    /**
     * AccountController constructor.
     * @param AccountManager $accountManager
     * @param Form\Account $accountForm
     */
    public function __construct(AccountManager $accountManager, Form\Account $accountForm) {
        $this->accountManager = $accountManager;
        $this->accountForm = $accountForm;
    }

    public function indexAction() {
        $messenger = $this->plugin('FlashMessenger');
        $pageNumber = $this->params()->fromQuery('page');
        $paginator = $this->accountManager->getAccountWithPaginator();
        $paginator->setCurrentPageNumber($pageNumber);
        $viewModel = new ViewModel();
        $viewModel->setVariable('messenger', $messenger);
        $viewModel->setVariable('paginator', $paginator);
        return $viewModel;
    }

    /**
     * @throws \User\Exception\ErrorException
     */
    public function editAction() {
        $form = $this->accountForm;
        /** @var FlashMessenger $messenger */
        $messenger = $this->plugin('FlashMessenger');
        if ($this->request->isPost()) {
            if ($userId = $this->params()->fromPost('user_id')) {
                $form->getInputFilter()->get('password')->setRequired(false);
                $form->getInputFilter()->get('confirm_password')->setRequired(false);
            }
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $account = $form->getObject();
                $result = $this->accountManager->saveAccount($account);
                $messenger->addMessage($result->getMessage(), $result->getStatus());
                /** @var Plugin\Redirect $redirect */
                $redirect = $this->plugin('Redirect');
                if ($this->params()->fromPost('save_and_remain')) {
                    return $redirect->toRoute('user/edit', ['id' => $result->getSource()->getUserId()]);
                }
                return $redirect->toRoute('user');
            }
        } elseif ($userId = $this->params()->fromRoute('id')) {
            $account = $this->accountManager->getOneAccountById($userId);
            $form->bind($account);
        }
        $viewModel = new ViewModel();
        $viewModel->setVariable('form', $form);
        $viewModel->setVariable('messenger', $messenger);
        return $viewModel;
    }

    public function deleteAction() {
        /** @var FlashMessenger $messenger */
        $messenger = $this->plugin('FlashMessenger');
        $userId = $this->params()->fromRoute('id');
        $result = $this->accountManager->deleteAccountById($userId);
        $messenger->addMessage($result->getMessage(), $result->getStatus());
        /** @var Plugin\Redirect $redirect */
        $redirect = $this->plugin('Redirect');
        return $redirect->toRoute('user');
    }

}