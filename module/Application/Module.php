<?php

namespace Application;

use Zend\Db;
use Zend\Form\View\Helper as FormViewHelper;
use Zend\I18n\Filter\NumberFormat;
use Zend\I18n\View\Helper\CurrencyFormat;
use Zend\I18n\View\Helper\DateFormat;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\Mvc\MvcEvent;
use Zend\Paginator\Paginator;
use Zend\View;

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

    public function onBootstrap(MvcEvent $mvcEvent) {
        $sharedEvents = $mvcEvent->getApplication()->getEventManager()->getSharedManager();
        $sharedEvents->attach('Zend\View\View', View\ViewEvent::EVENT_RENDERER_POST, function (View\ViewEvent $viewEvent) {
            $renderer = $viewEvent->getRenderer();
            if ($renderer instanceof View\Renderer\PhpRenderer) {
                /** @var FormViewHelper\FormElementErrors $helper */
                $helper = $renderer->plugin('formElementErrors');
                $helper->setAttributes(['class' => 'form-errors']);
                /** @var DateFormat $helper */
                $helper = $renderer->plugin('dateFormat');
                $helper->setLocale('ru_UK');
                /** @var CurrencyFormat $helper */

                $helper = $renderer->plugin('currencyFormat');
                $helper->setLocale('uk_UA');
                $helper->setCurrencyCode('UAH');
                /** @var NumberFormat $helper */
                $helper = $renderer->plugin('numberFormat');
                $helper->setLocale('uk_UA');
            }
        });

        $serviceManager = $mvcEvent->getApplication()->getServiceManager();
        $databaseAdapter = $serviceManager->get(Db\Adapter\Adapter::class);
        Db\TableGateway\Feature\GlobalAdapterFeature::setStaticAdapter($databaseAdapter);
    }

}