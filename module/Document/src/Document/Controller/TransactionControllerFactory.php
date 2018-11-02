<?php

namespace Document\Controller;

use Contractor\Service\ContractorCompanyManager;
use Contractor\Service\ContractorPlantManager;
use Document\Form;
use Document\Service\FinanceManager;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class TransactionControllerFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $transactionAdditionalForm = $container->get('FormElementManager')->get(Form\TransactionAdditional::class);
        $transactionCarrierForm = $container->get('FormElementManager')->get(Form\TransactionCarrier::class);
        $transactionCorporateForm = $container->get('FormElementManager')->get(Form\TransactionCorporate::class);
        $transactionCustomerForm = $container->get('FormElementManager')->get(Form\TransactionCustomer::class);
        $transactionProviderForm = $container->get('FormElementManager')->get(Form\TransactionProvider::class);
        $transactionCompanyForm = $container->get('FormElementManager')->get(Form\TransactionCompany::class);
        $transactionPlantForm = $container->get('FormElementManager')->get(Form\TransactionPlant::class);
        $financeTransactionImportForm = $container->get('FormElementManager')->get(Form\FinanceTransactionImport::class);
        $financeManager = $container->get(FinanceManager::class);
        $contractorCompanyManager = $container->get(ContractorCompanyManager::class);
        $contractorPlantManager = $container->get(ContractorPlantManager::class);
        return new TransactionController(
            $transactionAdditionalForm,
            $transactionCarrierForm,
            $transactionCorporateForm,
            $transactionCustomerForm,
            $transactionProviderForm,
            $transactionCompanyForm,
            $transactionPlantForm,
            $financeTransactionImportForm,
            $financeManager,
            $contractorCompanyManager,
            $contractorPlantManager
        );
    }

}