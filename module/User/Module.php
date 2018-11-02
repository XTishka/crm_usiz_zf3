<?php

namespace User;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\Mvc\MvcEvent;

class Module implements AutoloaderProviderInterface, ConfigProviderInterface {

    public function onBootstrap(MvcEvent $mvcEvent) {
        $eventManager = $mvcEvent->getApplication()->getEventManager();
        $eventManager->attach('route', [$this, 'loadConfiguration'], 2);
    }

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

    /**
     * @param MvcEvent $e
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function loadConfiguration(MvcEvent $e) {
        $application = $e->getApplication();
        $serviceManager = $application->getServiceManager();
        $sharedManager = $application->getEventManager()->getSharedManager();

        $router = $serviceManager->get('router');
        $request = $serviceManager->get('request');

        $matchedRoute = $router->match($request);

        if (null !== $matchedRoute) {
            $sharedManager->attach('Zend\Mvc\Controller\AbstractActionController', 'dispatch', function ($event) use ($serviceManager) {
                $serviceManager->get('ControllerPluginManager')->get('AccessManager')->doAuthorization($event); //pass to the plugin...
            }, 2);
        }
    }


}