<?php

namespace Document\Service\Repository;

use Application\Hydrator\Strategy\IntegerStrategy;
use Interop\Container\ContainerInterface;
use Document\Domain;
use Zend\Db\Adapter\Adapter as DbAdapter;
use Zend\Hydrator;
use Zend\Hydrator\Filter\MethodMatchFilter;
use Zend\Hydrator\Filter\FilterComposite;
use Zend\Hydrator\Strategy\DateTimeFormatterStrategy;
use Zend\ServiceManager\Factory\FactoryInterface;

class PurchaseContractDbFactory implements FactoryInterface {

    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     * @return PurchaseContractDb|object
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        /** @var DbAdapter $dbAdapter */
        $dbAdapter = $container->get(DbAdapter::class);

        /** @var Hydrator\ClassMethods $hydrator */
        $hydrator = $container->get('HydratorManager')->get(Hydrator\ClassMethods::class);

        $hydrator->addFilter('company_name', new MethodMatchFilter('getCompanyName'), FilterComposite::CONDITION_AND);
        $hydrator->addFilter('country_name', new MethodMatchFilter('getCountryName'), FilterComposite::CONDITION_AND);
        $hydrator->addFilter('station_name', new MethodMatchFilter('getStationName'), FilterComposite::CONDITION_AND);
        $hydrator->addFilter('material_name', new MethodMatchFilter('getMaterialName'), FilterComposite::CONDITION_AND);
        $hydrator->addFilter('provider_name', new MethodMatchFilter('getProviderName'), FilterComposite::CONDITION_AND);
        $hydrator->addFilter('warehouse_name', new MethodMatchFilter('getWarehouseName'), FilterComposite::CONDITION_AND);
        $hydrator->addFilter('loadedWagons', new MethodMatchFilter('getLoadedWagons'), FilterComposite::CONDITION_AND);
        $hydrator->addFilter('unloadedWagons', new MethodMatchFilter('getUnloadedWagons'), FilterComposite::CONDITION_AND);

        $hydrator->addStrategy('purchase_id', new IntegerStrategy());
        $hydrator->addStrategy('company_id', new IntegerStrategy());
        $hydrator->addStrategy('material_id', new IntegerStrategy());
        $hydrator->addStrategy('provider_id', new IntegerStrategy());
        $hydrator->addStrategy('warehouse_id', new IntegerStrategy());
        $hydrator->addStrategy('updated', new DateTimeFormatterStrategy('Y-m-d H:i:s'));
        $hydrator->addStrategy('created', new DateTimeFormatterStrategy('Y-m-d H:i:s'));

        /** @var Domain\PurchaseContractEntity $prototype */
        $prototype = new Domain\PurchaseContractEntity();

        return new PurchaseContractDb($dbAdapter, $hydrator, $prototype);
    }

}