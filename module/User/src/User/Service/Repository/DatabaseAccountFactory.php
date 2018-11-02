<?php

namespace User\Service\Repository;

use Interop\Container\ContainerInterface;
use User\Entity;
use Zend\Db\Adapter\Adapter;
use Zend\Hydrator;
use Zend\ServiceManager\Factory\FactoryInterface;

class DatabaseAccountFactory implements FactoryInterface {

    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return object|DatabaseAccount
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        /** @var Adapter $adapter */
        $adapter = $container->get(Adapter::class);

        /** @var Hydrator\ClassMethods $hydrator */
        $hydrator = $container->get('HydratorManager')->get(Hydrator\ClassMethods::class);
        $hydrator->addStrategy('updated', new Hydrator\Strategy\DateTimeFormatterStrategy('Y-m-d H:i:s'));
        $hydrator->addStrategy('created', new Hydrator\Strategy\DateTimeFormatterStrategy('Y-m-d H:i:s'));
        $hydrator->addStrategy('is_active', new Hydrator\Strategy\BooleanStrategy('1', '0'));

        $hydrator->addFilter('full_name', new Hydrator\Filter\MethodMatchFilter('getFullName'), Hydrator\Filter\FilterComposite::CONDITION_AND);

        /** @var Entity\Account $prototype */
        $prototype = new Entity\Account();

        /** @var DatabaseAccount $repository */
        $repository = new DatabaseAccount($adapter, $hydrator, $prototype);
        return $repository;
    }

}