<?php

namespace Application\Model\Finance;

use Interop\Container\ContainerInterface;
use Zend\Db\Adapter\Adapter as DatabaseAdapter;
use Zend\ServiceManager\Factory\AbstractFactoryInterface;

class AbstractServiceFactory implements AbstractFactoryInterface {

    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @return bool
     */
    public function canCreate(ContainerInterface $container, $requestedName) {
        return is_subclass_of($requestedName, AbstractService::class);
    }

    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return AbstractService|object
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        /** @var DatabaseAdapter $db */
        $db = $container->get(DatabaseAdapter::class);

        /** @var AbstractService $service */
        $service = new $requestedName($db);

        return $service;
    }

}