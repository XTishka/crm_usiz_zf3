<?php

namespace Bank\Controller;

use Bank\Form\FilterForm;
use Bank\Form\ImportForm;
use Bank\Form\RecordForm;
use Bank\Service\RecordManager;
use Contractor\Service\ContractorCompanyManager;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class RecordControllerFactory implements FactoryInterface {

    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     * @return RecordController|object
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        /** @var RecordForm $recordForm */
        $recordForm = $container->get('FormElementManager')->get(RecordForm::class);
        /** @var FilterForm $filterForm */
        $filterForm = $container->get('FormElementManager')->get(FilterForm::class);
        /** @var ImportForm $filterForm */
        $importForm = $container->get('FormElementManager')->get(ImportForm::class);
        /** @var RecordManager $recordManager */
        $recordManager = $container->get(RecordManager::class);
        /** @var ContractorCompanyManager $companyManager */
        $companyManager = $container->get(ContractorCompanyManager::class);
        /** @var RecordController $controller */
        $controller = new RecordController($recordForm, $filterForm, $importForm, $recordManager, $companyManager);
        return $controller;
    }

}