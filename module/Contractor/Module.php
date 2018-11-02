<?php

namespace Contractor;

use Contractor\Entity\ContractorAbstract;
use Contractor\Entity\ContractorCompany;
use Contractor\Entity\ContractorPlant;
use Contractor\Service\ContractorCompanyManager;
use Contractor\Service\ContractorPlantManager;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\Mvc\MvcEvent;
use Zend\Navigation\Navigation;
use Zend\Navigation\Page\AbstractPage;
use Zend\Navigation\Page\Mvc;

class Module implements AutoloaderProviderInterface, ConfigProviderInterface {

    public function getAutoloaderConfig() {
        return [
            'Zend\Loader\ClassMapAutoloader' => [__DIR__ . '/autoload_classmap.php'],
            'Zend\Loader\StandardAutoloader' => [
                'namespaces' => [
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ],
            ],
        ];
    }

    public function getConfig() {
        return include __DIR__ . '/config/module.config.php';
    }

    public function onBootstrap(MvcEvent $event) {
        /**
         * @var AbstractPage $parent
         */
        $serviceManager = $event->getApplication()->getServiceManager();
        $router = $serviceManager->get('Router');
        // Добавление компаний в боковое меню
        if ($parent = $serviceManager->get('Zend\Navigation\Default')->findOneBy('id', 'companyDashboard')) {
            /** @var ContractorCompanyManager $companyManager */
            $companyManager = $serviceManager->get(ContractorCompanyManager::class);
            $navigation = new Navigation();
            foreach ($companyManager->getContractorsValueOptions() as $valueOption) {
                $page = new Mvc();
                $page->setLabel($valueOption['label']);
                $page->setRoute('dashboard');
                $page->setRouter($router);
                $page->setOptions(['icon' => 'fa fa-building fa-fw']);
                $page->setParams(['company' => $valueOption['value']]);
                $navigation->addPage($page);
            }
            $parent->addPages($navigation);
        }

        // Добавление заводов в боковое меню
        if ($parent = $serviceManager->get('Zend\Navigation\Default')->findOneBy('id', 'plantDashboard')) {
            /** @var ContractorCompanyManager $companyManager */
            $plantManager = $serviceManager->get(ContractorPlantManager::class);

            $navigation = new Navigation();
            foreach ($plantManager->getContractorsValueOptions() as $valueOption) {
                $page = new Mvc();
                $page->setLabel($valueOption['label']);
                $page->setRoute('plantDashboard');
                $page->setRouter($router);
                $page->setOptions(['icon' => 'fa fa-industry fa-fw']);
                $page->setParams(['plant' => $valueOption['value']]);
                $navigation->addPage($page);
            }
            $parent->addPages($navigation);
        }
    }

}