<?php

namespace Document\Controller;

use Contractor\Entity\ContractorCompany;
use Contractor\Entity\ContractorPlant;
use Contractor\Exception\ErrorException;
use Contractor\Service\ContractorCompanyManager;
use Contractor\Service\ContractorPlantManager;
use Document\Domain\TransactionEntity;
use Document\Form;
use Document\Service;
use Zend\Http\Request;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\Plugin\FlashMessenger\FlashMessenger;
use Zend\View\Model\ViewModel;

class TransactionController extends AbstractActionController {

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Form\TransactionAdditional
     */
    protected $transactionAdditionalForm;

    /**
     * @var Form\TransactionCarrier
     */
    protected $transactionCarrierForm;

    /**
     * @var Form\TransactionCorporate
     */
    protected $transactionCorporateForm;

    /**
     * @var Form\TransactionCustomer
     */
    protected $transactionCustomerForm;

    /**
     * @var Form\TransactionProvider
     */
    protected $transactionProviderForm;

    /**
     * @var Form\FinanceTransactionImport
     */
    protected $financeTransactionImportForm;

    /**
     * @var Service\FinanceManager
     */
    protected $financeManager;

    /**
     * @var Form\TransactionCompany
     */
    protected $transactionCompanyForm;

    /**
     * @var Form\TransactionPlant
     */
    protected $transactionPlantForm;

    /**
     * @var ContractorCompanyManager
     */
    protected $contractorCompanyManager;

    /**
     * @var ContractorPlantManager
     */
    protected $contractorPlantManager;

    /**
     * TransactionController constructor.
     * @param Form\TransactionAdditional $transactionAdditionalForm
     * @param Form\TransactionCarrier $transactionCarrierForm
     * @param Form\TransactionCorporate $transactionCorporateForm
     * @param Form\TransactionCustomer $transactionCustomerForm
     * @param Form\TransactionProvider $transactionProviderForm
     * @param Form\TransactionCompany $transactionCompanyForm
     * @param Form\TransactionPlant $transactionPlantForm
     * @param Service\FinanceManager $financeManager
     * @param ContractorCompanyManager $contractorCompanyManager
     * @param ContractorPlantManager $contractorPlantManager
     */
    public function __construct(Form\TransactionAdditional $transactionAdditionalForm,
                                Form\TransactionCarrier $transactionCarrierForm,
                                Form\TransactionCorporate $transactionCorporateForm,
                                Form\TransactionCustomer $transactionCustomerForm,
                                Form\TransactionProvider $transactionProviderForm,
                                Form\TransactionCompany $transactionCompanyForm,
                                Form\TransactionPlant $transactionPlantForm,
                                Form\FinanceTransactionImport $financeTransactionImportForm,
                                Service\FinanceManager $financeManager,
                                ContractorCompanyManager $contractorCompanyManager,
                                ContractorPlantManager $contractorPlantManager) {
        $this->transactionAdditionalForm = $transactionAdditionalForm;
        $this->transactionCarrierForm = $transactionCarrierForm;
        $this->transactionCorporateForm = $transactionCorporateForm;
        $this->transactionCustomerForm = $transactionCustomerForm;
        $this->transactionProviderForm = $transactionProviderForm;
        $this->transactionCompanyForm = $transactionCompanyForm;
        $this->transactionPlantForm = $transactionPlantForm;
        $this->financeTransactionImportForm = $financeTransactionImportForm;
        $this->financeManager = $financeManager;
        $this->contractorCompanyManager = $contractorCompanyManager;
        $this->contractorPlantManager = $contractorPlantManager;
    }

    /**
     * @return ViewModel
     * @throws \Document\Exception\ErrorException
     */
    public function additionalEditAction() {
        $form = $this->transactionAdditionalForm;
        /** @var FlashMessenger $messenger */
        $messenger = $this->plugin('FlashMessenger');
        if ($this->request->isPost()) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                /** @var TransactionEntity $transaction */
                $transaction = $form->getObject();
                $transaction = $this->financeManager->saveTransaction($transaction);
                $messenger->addMessage('Transaction was fully completed.', 'success');
                $this->plugin('Redirect')->toRoute('dashboard/finance', ['company' => $transaction->getCompanyId()]);
            }
        } elseif ($transactionId = $this->params()->fromRoute('id')) {
            $transaction = $this->financeManager->getTransactionById($transactionId);
            $form->bind($transaction);
            $form->get('direction')->setValue($transaction->getCredit() ? 'credit' : 'debit');
        } elseif ($companyId = $this->params()->fromQuery('company')) {
            $form->get('company_id')->setValue($companyId);
        }
        $viewModel = new ViewModel();
        $viewModel->setVariable('form', $form);
        $viewModel->setVariable('messenger', $messenger);
        return $viewModel;
    }

    /**
     * @throws \Document\Exception\ErrorException
     */
    public function additionalDeleteAction() {
        $transactionId = $this->params()->fromRoute('id');
        $transaction = $this->financeManager->getTransactionById($transactionId);
        $companyId = $transaction->getCompanyId();
        $this->financeManager->deleteTransaction($transactionId);
        $this->plugin('FlashMessenger')->addMessage('Transaction was successfully canceled.', 'success');
        $this->plugin('Redirect')->toRoute('dashboard/finance', ['company' => $companyId]);
    }

    /**
     * @return ViewModel
     * @throws \Contractor\Exception\ErrorException
     * @throws \Document\Exception\ErrorException
     */
    public function carrierEditAction() {
        $viewModel = new ViewModel();
        $form = $this->transactionCarrierForm;
        /** @var FlashMessenger $messenger */
        $messenger = $this->plugin('FlashMessenger');
        if ($this->request->isPost()) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                /** @var TransactionEntity $transaction */
                $transaction = $form->getObject();

                try {
                    /** @var ContractorCompany $company */
                    $company = $this->contractorCompanyManager->getContractorById($transaction->getCompanyId());
                } catch (ErrorException $errorException) {
                    /** @var ContractorPlant $company */
                    $company = $this->contractorPlantManager->getContractorById($transaction->getCompanyId());
                }

                // Создание задолженности завода перед перевозчиком
                $carrierTransaction = clone $transaction;
                $carrierTransaction->setContractorType(ContractorCompany::TYPE_CARRIER);
                $this->financeManager->saveTransaction($carrierTransaction);


                /*
                // Создание задолженности предприятия перед заводом (отображение у компании)
                $companyTransaction = clone $transaction;
                $companyTransaction->setCompanyId($company->getContractorId());
                $companyTransaction->setContractorType($company::TYPE_PLANT);
                $companyTransaction->setContractorId($company->getPlantId());
                $this->financeManager->saveTransaction($companyTransaction);

                // Создание задолженности предприятия перед заводом (отображение у завода)
                $plantTransaction = clone $transaction;
                $plantTransaction->setCompanyId($company->getPlantId());
                $plantTransaction->setContractorType($company::TYPE_COMPANY);
                $plantTransaction->setContractorId($company->getContractorId());
                if ($plantTransaction->getCredit()) {
                    $plantTransaction->setDebit($plantTransaction->getCredit());
                    $plantTransaction->setCredit(0);
                } else {
                    $plantTransaction->setCredit($plantTransaction->getDebit());
                    $plantTransaction->setDebit(0);
                }
                $this->financeManager->saveTransaction($plantTransaction);
                */

                $messenger->addMessage('Transaction was fully completed.', 'success');
                if ($company::TYPE_PLANT == $company->getContractorType()) {
                    $this->plugin('Redirect')->toRoute('plantDashboard', ['plant' => $transaction->getCompanyId()]);
                } else {
                    $this->plugin('Redirect')->toRoute('dashboard/finance', ['company' => $transaction->getCompanyId()]);
                }
            }
        } elseif ($transactionId = $this->params()->fromRoute('id')) {
            $transaction = $this->financeManager->getTransactionById($transactionId);
            $form->bind($transaction);
            $form->get('direction')->setValue($transaction->getCredit() ? 'credit' : 'debit');
        }

        if ($companyId = $this->params()->fromQuery('company')) {
            $viewModel->setVariable('company_id', $companyId);
            $form->get('company_id')->setValue($companyId);
        }
        $viewModel->setVariable('form', $form);
        $viewModel->setVariable('messenger', $messenger);
        return $viewModel;
    }

    /**
     * @throws ErrorException
     * @throws \Document\Exception\ErrorException
     */
    public function carrierDeleteAction() {
        $transactionId = $this->params()->fromRoute('id');
        $transaction = $this->financeManager->getTransactionById($transactionId);

        try {
            /** @var ContractorCompany $company */
            $company = $this->contractorCompanyManager->getContractorById($transaction->getCompanyId());
        } catch (ErrorException $errorException) {
            /** @var ContractorPlant $company */
            $company = $this->contractorPlantManager->getContractorById($transaction->getCompanyId());
        }

        $this->financeManager->deleteTransaction($transactionId);
        $this->plugin('FlashMessenger')->addMessage('Transaction was successfully canceled.', 'success');
        if ($company::TYPE_PLANT == $company->getContractorType()) {
            $this->plugin('Redirect')->toRoute('plantDashboard', ['plant' => $transaction->getCompanyId()]);
        } else {
            $this->plugin('Redirect')->toRoute('dashboard/finance', ['company' => $transaction->getCompanyId()]);
        }
    }

    /**
     * @return ViewModel
     * @throws \Document\Exception\ErrorException
     */
    public function corporateEditAction() {
        $form = $this->transactionCorporateForm;
        /** @var FlashMessenger $messenger */
        $messenger = $this->plugin('FlashMessenger');
        if ($this->request->isPost()) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                /** @var TransactionEntity $transaction */
                $transaction = $form->getObject();
                $transaction = $this->financeManager->saveTransaction($transaction);
                $messenger->addMessage('Transaction was fully completed.', 'success');
                $this->plugin('Redirect')->toRoute('dashboard/finance', ['company' => $transaction->getCompanyId()]);
            }
        } elseif ($transactionId = $this->params()->fromRoute('id')) {
            $transaction = $this->financeManager->getTransactionById($transactionId);
            $form->bind($transaction);
            $form->get('direction')->setValue($transaction->getCredit() ? 'credit' : 'debit');
        } elseif ($companyId = $this->params()->fromQuery('company')) {
            $form->get('company_id')->setValue($companyId);
        }
        $viewModel = new ViewModel();
        $viewModel->setVariable('form', $form);
        $viewModel->setVariable('messenger', $messenger);
        return $viewModel;
    }

    /**
     * @throws \Document\Exception\ErrorException
     */
    public function corporateDeleteAction() {
        $transactionId = $this->params()->fromRoute('id');
        $transaction = $this->financeManager->getTransactionById($transactionId);
        $companyId = $transaction->getCompanyId();
        $this->financeManager->deleteTransaction($transactionId);
        $this->plugin('FlashMessenger')->addMessage('Transaction was successfully canceled.', 'success');
        $this->plugin('Redirect')->toRoute('dashboard/finance', ['company' => $companyId]);
    }

    /**
     * @return ViewModel
     * @throws \Document\Exception\ErrorException
     */
    public function customerEditAction() {
        $form = $this->transactionCustomerForm;
        /** @var FlashMessenger $messenger */
        $messenger = $this->plugin('FlashMessenger');
        if ($this->request->isPost()) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                /** @var TransactionEntity $transaction */
                $transaction = $form->getObject();
                $transaction = $this->financeManager->saveTransaction($transaction);
                $messenger->addMessage('Transaction was fully completed.', 'success');
                $this->plugin('Redirect')->toRoute('dashboard/finance', ['company' => $transaction->getCompanyId()]);
            }
        } elseif ($transactionId = $this->params()->fromRoute('id')) {
            $transaction = $this->financeManager->getTransactionById($transactionId);
            $form->bind($transaction);
            $form->get('direction')->setValue($transaction->getCredit() ? 'credit' : 'debit');
        } elseif ($companyId = $this->params()->fromQuery('company')) {
            $form->get('company_id')->setValue($companyId);
        }
        $viewModel = new ViewModel();
        $viewModel->setVariable('form', $form);
        $viewModel->setVariable('messenger', $messenger);
        return $viewModel;
    }

    /**
     * @throws \Document\Exception\ErrorException
     */
    public function customerDeleteAction() {
        $transactionId = $this->params()->fromRoute('id');
        $transaction = $this->financeManager->getTransactionById($transactionId);
        $companyId = $transaction->getCompanyId();
        $this->financeManager->deleteTransaction($transactionId);
        $this->plugin('FlashMessenger')->addMessage('Transaction was successfully canceled.', 'success');
        $this->plugin('Redirect')->toRoute('dashboard/finance', ['company' => $companyId]);
    }

    /**
     * @return ViewModel
     * @throws \Document\Exception\ErrorException
     */
    public function plantEditAction() {
        $form = $this->transactionPlantForm;
        /** @var FlashMessenger $messenger */
        $messenger = $this->plugin('FlashMessenger');
        if ($this->request->isPost()) {
            /** @var TransactionEntity $transaction */
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                /**
                 * @var TransactionEntity $transactionCompany
                 * @var TransactionEntity $transactionPlant
                 */
                $transactionCompany = $form->getObject();
                $transactionPlant = clone $transactionCompany;

                // Инверсия транзакции для компании
                $transactionPlant->setContractorType($transactionCompany::CONTRACTOR_COMPANY);
                $transactionPlant->setCompanyId($transactionCompany->getContractorId());
                $transactionPlant->setContractorId($transactionCompany->getCompanyId());
                $transactionPlant->setCredit($transactionCompany->getDebit());
                $transactionPlant->setDebit($transactionCompany->getCredit());

                $transactionCompany = $this->financeManager->saveTransaction($transactionCompany);
                $this->financeManager->saveTransaction($transactionPlant);

                $messenger->addMessage('Transaction was fully completed.', 'success');
                $this->plugin('Redirect')->toRoute('dashboard/finance', ['company' => $transactionCompany->getCompanyId()]);
            }
        } elseif ($transactionId = $this->params()->fromRoute('id')) {
            $transaction = $this->financeManager->getTransactionById($transactionId);
            $form->bind($transaction);
            $form->get('direction')->setValue($transaction->getCredit() ? 'credit' : 'debit');
        } elseif ($companyId = $this->params()->fromQuery('company')) {
            $form->get('company_id')->setValue($companyId);
        }
        $viewModel = new ViewModel();
        $viewModel->setVariable('form', $form);
        $viewModel->setVariable('messenger', $messenger);
        return $viewModel;
    }

    /**
     * @throws \Document\Exception\ErrorException
     */
    public function plantDeleteAction() {
        $transactionId = $this->params()->fromRoute('id');
        $transaction = $this->financeManager->getTransactionById($transactionId);
        $companyId = $transaction->getCompanyId();
        $this->financeManager->deleteTransaction($transactionId);
        $this->plugin('FlashMessenger')->addMessage('Transaction was successfully canceled.', 'success');
        $this->plugin('Redirect')->toRoute('dashboard/finance', ['company' => $companyId]);
    }

    /**
     * @return ViewModel
     * @throws \Document\Exception\ErrorException
     */
    public function providerEditAction() {
        $form = $this->transactionProviderForm;
        /** @var FlashMessenger $messenger */
        $messenger = $this->plugin('FlashMessenger');
        if ($this->request->isPost()) {
            /** @var TransactionEntity $transaction */
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $transaction = $form->getObject();
                $transaction = $this->financeManager->saveTransaction($transaction);
                $messenger->addMessage('Transaction was fully completed.', 'success');
                $this->plugin('Redirect')->toRoute('dashboard/finance', ['company' => $transaction->getCompanyId()]);
            }
        } elseif ($transactionId = $this->params()->fromRoute('id')) {
            $transaction = $this->financeManager->getTransactionById($transactionId);
            $form->bind($transaction);
            $form->get('direction')->setValue($transaction->getCredit() ? 'credit' : 'debit');
        } elseif ($companyId = $this->params()->fromQuery('company')) {
            $form->get('company_id')->setValue($companyId);
        }
        $viewModel = new ViewModel();
        $viewModel->setVariable('form', $form);
        $viewModel->setVariable('messenger', $messenger);
        return $viewModel;
    }

    /**
     * @throws \Document\Exception\ErrorException
     */
    public function providerDeleteAction() {
        $transactionId = $this->params()->fromRoute('id');
        $transaction = $this->financeManager->getTransactionById($transactionId);
        $companyId = $transaction->getCompanyId();
        $this->financeManager->deleteTransaction($transactionId);
        $this->plugin('FlashMessenger')->addMessage('Transaction was successfully canceled.', 'success');
        $this->plugin('Redirect')->toRoute('dashboard/finance', ['company' => $companyId]);
    }

    /**
     * @return ViewModel
     * @throws \Document\Exception\ErrorException
     */
    public function companyEditAction() {
        $form = $this->transactionCompanyForm;
        /** @var FlashMessenger $messenger */
        $messenger = $this->plugin('FlashMessenger');
        if ($this->request->isPost()) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                /**
                 * @var TransactionEntity $transactionPlant
                 * @var TransactionEntity $transactionCompany
                 */
                $transactionPlant = $form->getObject();
                $transactionCompany = clone $transactionPlant;

                // Инверсия транзакции для компании
                $transactionCompany->setContractorType($transactionCompany::CONTRACTOR_PLANT);
                $transactionCompany->setCompanyId($transactionPlant->getContractorId());
                $transactionCompany->setContractorId($transactionPlant->getCompanyId());
                $transactionCompany->setCredit($transactionPlant->getDebit());
                $transactionCompany->setDebit($transactionPlant->getCredit());

                $transactionPlant = $this->financeManager->saveTransaction($transactionPlant);
                $this->financeManager->saveTransaction($transactionCompany);

                $messenger->addMessage('Transaction was fully completed.', 'success');
                $this->plugin('Redirect')->toRoute('plantDashboard/finance', ['plant' => $transactionPlant->getCompanyId()]);
            }
        } elseif ($transactionId = $this->params()->fromRoute('id')) {
            $transaction = $this->financeManager->getTransactionById($transactionId);
            $form->bind($transaction);
            $form->get('direction')->setValue($transaction->getCredit() ? 'credit' : 'debit');
        } elseif ($companyId = $this->params()->fromQuery('company')) {
            $form->get('company_id')->setValue($companyId);
        }
        $viewModel = new ViewModel();
        $viewModel->setVariable('form', $form);
        $viewModel->setVariable('messenger', $messenger);
        return $viewModel;
    }

    /**
     * @throws \Document\Exception\ErrorException
     */
    public function companyDeleteAction() {
        $transactionId = $this->params()->fromRoute('id');
        $transaction = $this->financeManager->getTransactionById($transactionId);
        $companyId = $transaction->getCompanyId();
        $this->financeManager->deleteTransaction($transactionId);
        $this->plugin('FlashMessenger')->addMessage('Transaction was successfully canceled.', 'success');
        $this->plugin('Redirect')->toRoute('dashboard/finance', ['company' => $companyId]);
    }

    /**
     * @return ViewModel
     * @throws ErrorException
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function importAction() {
        $companyId = $this->params()->fromRoute('company');
        /** @var ContractorCompany $company */
        $company = $this->contractorCompanyManager->getContractorById($companyId);
        $form = $this->financeTransactionImportForm;
        if ($this->request->isPost()) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $tmpFile = $this->params()->fromFiles('file');
                $result = $this->financeManager->importTransactions($company, $tmpFile);
                $messenger = $this->plugin('FlashMessenger');
                if ($result::STATUS_WARNING == $result->getStatus()) {
                    foreach ($result->getSource() as $message) {
                        $messenger->addMessage($message, $result->getStatus());
                    }
                } else {
                    $messenger->addMessage($result->getMessage(), $result->getStatus());
                }
                return $this->plugin('Redirect')->toRoute('dashboard/finance', ['company' => $companyId]);
            }
        }
        $view = new ViewModel();
        $view->setVariable('form', $form);
        $view->setVariable('company', $company);
        return $view;
    }

}