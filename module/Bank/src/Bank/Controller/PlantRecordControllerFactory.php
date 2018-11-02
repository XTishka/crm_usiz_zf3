<?php

namespace Bank\Controller;

use Bank\Form\RecordForm;
use Bank\Service\RecordManager;
use Contractor\Service\ContractorPlantManager;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class PlantRecordControllerFactory implements FactoryInterface {

    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     * @return RecordController|object
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        /** @var RecordForm $recordForm */
        $recordForm = $container->get('FormElementManager')->get(RecordForm::class);
        /** @var RecordManager $recordManager */
        $recordManager = $container->get(RecordManager::class);
        /** @var ContractorPlantManager $plantManager */
        $plantManager = $container->get(ContractorPlantManager::class);
        /** @var RecordController $controller */
        $controller = new PlantRecordController($recordForm, $recordManager, $plantManager);
        return $controller;
    }

}