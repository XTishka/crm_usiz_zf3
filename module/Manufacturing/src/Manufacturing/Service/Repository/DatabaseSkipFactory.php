<?php

namespace Manufacturing\Service\Repository;

use Interop\Container\ContainerInterface;
use Manufacturing\Domain;
use Zend\Db\Adapter\Adapter as DbAdapter;
use Zend\Hydrator;
use Zend\Hydrator\Strategy;
use Zend\Hydrator\Filter\FilterComposite;
use Zend\Hydrator\Filter\MethodMatchFilter;
use Zend\ServiceManager\Factory\FactoryInterface;

class DatabaseSkipFactory implements FactoryInterface {

    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     * @return DatabaseSkip|object
     * @throws \Exception
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        /** @var DbAdapter $dbAdapter */
        $dbAdapter = $container->get(DbAdapter::class);

        $floatStrategy = new Strategy\ClosureStrategy(
            function ($value) {
                return floatval($value);
            },
            function ($value) {
                return floatval($value);
            }
        );

        /** @var Hydrator\ClassMethods $hydrator */
        $hydrator = $container->get('HydratorManager')->get(Hydrator\ClassMethods::class);
        $hydrator->addStrategy('date', new Strategy\DateTimeFormatterStrategy('Y-m-d'));
        $hydrator->addStrategy('product_weight', $floatStrategy);
        $hydrator->addStrategy('dropout_weight', $floatStrategy);

        /** @var Domain\FurnaceEntity $prototype */
        $prototype = new Domain\SkipCommonEntity();

        return new DatabaseSkip($dbAdapter, $hydrator, $prototype);
    }

}