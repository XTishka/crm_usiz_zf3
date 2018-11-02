<?php

namespace Document\Controller;

use Document\Domain\FinanceLogEntity;
use Document\Form\AddCarrierBalance;
use Document\Form\AddCompanyBalance;
use Document\Form\AddCustomerBalance;
use Document\Form\AddProviderBalance;
use Document\Service\FinanceLogManager;
use Zend\Http\Request;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\Plugin\FlashMessenger\FlashMessenger;
use Zend\View\Model\ViewModel;

class BalanceController extends AbstractActionController {

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var FinanceLogManager
     */
    protected $financeLogManager;

    /**
     * @var AddCompanyBalance
     */
    protected $addCompanyBalanceForm;

    /**
     * @var AddCarrierBalance
     */
    protected $addCarrierBalanceForm;

    /**
     * @var AddCustomerBalance
     */
    protected $addCustomerBalanceForm;

    /**
     * @var AddProviderBalance
     */
    protected $addProviderBalance;

    public function __construct(FinanceLogManager $financeLogManager,
                                AddCompanyBalance $addCompanyBalanceForm,
                                AddCarrierBalance $addCarrierBalanceForm,
                                AddCustomerBalance $addCustomerBalanceForm,
                                AddProviderBalance $addProviderBalance) {
        $this->financeLogManager = $financeLogManager;
        $this->addCompanyBalanceForm = $addCompanyBalanceForm;
        $this->addCarrierBalanceForm = $addCarrierBalanceForm;
        $this->addCustomerBalanceForm = $addCustomerBalanceForm;
        $this->addProviderBalance = $addProviderBalance;
    }

    public function companyAction() {
        $form = $this->addCompanyBalanceForm;
        /** @var FlashMessenger $messenger */
        $messenger = $this->plugin('FlashMessenger');
        if ($this->request->isPost()) {

            $data = $this->params()->fromPost();

            $form->setData($data);
            if ($form->isValid()) {
                $data = $form->getData();
                $financeLog = new FinanceLogEntity();
                $financeLog->setContractorType($financeLog::CONTRACTOR_COMPANY);
                $financeLog->setPrice($data['amount']);
                $financeLog->setContractorId($data['company_id']);
                $financeLog->setComment('Пополнение счета предприятия');

                $this->financeLogManager->debit($financeLog);

                $messenger->addMessage(sprintf('Счет предприятия успешно пополнен на %s грн.', $financeLog->getPrice()), 'success');

                $this->plugin('Redirect')->toRoute('balance/company');
            }

        }

        $viewModel = new ViewModel();
        $viewModel->setVariable('form', $form);
        $viewModel->setVariable('messenger', $messenger);
        return $viewModel;
    }


    public function carrierAction() {
        $form = $this->addCarrierBalanceForm;
        /** @var FlashMessenger $messenger */
        $messenger = $this->plugin('FlashMessenger');
        if ($this->request->isPost()) {

            $data = $this->params()->fromPost();

            $form->setData($data);
            if ($form->isValid()) {
                $data = $form->getData();
                $financeLog = new FinanceLogEntity();
                $financeLog->setContractorType($financeLog::CONTRACTOR_CARRIER);
                $financeLog->setPrice($data['amount']);
                $financeLog->setContractorId($data['carrier_id']);
                $financeLog->setComment('Пополнение счета перевозчика');

                $this->financeLogManager->debit($financeLog);

                $messenger->addMessage(sprintf('Счет перевозчика успешно пополнен на %s грн.', $financeLog->getPrice()), 'success');

                $this->plugin('Redirect')->toRoute('balance/carrier');
            }

        }

        $viewModel = new ViewModel();
        $viewModel->setVariable('form', $form);
        $viewModel->setVariable('messenger', $messenger);
        return $viewModel;
    }

    public function customerAction() {
        $form = $this->addCustomerBalanceForm;
        /** @var FlashMessenger $messenger */
        $messenger = $this->plugin('FlashMessenger');
        if ($this->request->isPost()) {

            $data = $this->params()->fromPost();

            $form->setData($data);
            if ($form->isValid()) {
                $data = $form->getData();
                $financeLog = new FinanceLogEntity();
                $financeLog->setContractorType($financeLog::CONTRACTOR_CUSTOMER);
                $financeLog->setPrice($data['amount']);
                $financeLog->setContractorId($data['customer_id']);
                $financeLog->setComment('Пополнение счета покупателя');

                $this->financeLogManager->debit($financeLog);

                $messenger->addMessage(sprintf('Счет покупателя успешно пополнен на %s грн.', $financeLog->getPrice()), 'success');

                $this->plugin('Redirect')->toRoute('balance/customer');
            }

        }

        $viewModel = new ViewModel();
        $viewModel->setVariable('form', $form);
        $viewModel->setVariable('messenger', $messenger);
        return $viewModel;
    }

    public function providerAction() {
        $form = $this->addProviderBalance;
        /** @var FlashMessenger $messenger */
        $messenger = $this->plugin('FlashMessenger');
        if ($this->request->isPost()) {

            $data = $this->params()->fromPost();

            $form->setData($data);
            if ($form->isValid()) {
                $data = $form->getData();
                $financeLog = new FinanceLogEntity();
                $financeLog->setContractorType($financeLog::CONTRACTOR_PROVIDER);
                $financeLog->setPrice($data['amount']);
                $financeLog->setContractorId($data['provider_id']);
                $financeLog->setComment('Пополнение счета поставщика');

                $this->financeLogManager->debit($financeLog);

                $messenger->addMessage(sprintf('Счет поставщика успешно пополнен на %s грн.', $financeLog->getPrice()), 'success');

                $this->plugin('Redirect')->toRoute('balance/provider');
            }

        }

        $viewModel = new ViewModel();
        $viewModel->setVariable('form', $form);
        $viewModel->setVariable('messenger', $messenger);
        return $viewModel;
    }

}