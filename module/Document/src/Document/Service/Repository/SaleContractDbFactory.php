<?php

namespace Document\Service\Repository;

use Application\Hydrator\Strategy\IntegerStrategy;
use Application\Hydrator\Strategy\StringTrimStrategy;
use Interop\Container\ContainerInterface;
use Document\Domain;
use Zend\Db\Adapter\Adapter as DbAdapter;
use Zend\Hydrator;
use Zend\Hydrator\Filter\MethodMatchFilter;
use Zend\Hydrator\Filter\FilterComposite;
use Zend\Hydrator\Strategy\DateTimeFormatterStrategy;
use Zend\ServiceManager\Factory\FactoryInterface;

class SaleContractDbFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        /** @var DbAdapter $dbAdapter */
        $dbAdapter = $container->get(DbAdapter::class);

        /** @var Hydrator\ClassMethods $hydrator */
        $hydrator = $container->get('HydratorManager')->get(Hydrator\ClassMethods::class);

        $hydrator->addFilter('company_name', new MethodMatchFilter('getCompanyName'), FilterComposite::CONDITION_AND);
        $hydrator->addFilter('country_name', new MethodMatchFilter('getCountryName'), FilterComposite::CONDITION_AND);
        $hydrator->addFilter('station_name', new MethodMatchFilter('getStationName'), FilterComposite::CONDITION_AND);
        $hydrator->addFilter('product_name', new MethodMatchFilter('getProductName'), FilterComposite::CONDITION_AND);
        $hydrator->addFilter('customer_name', new MethodMatchFilter('getCustomerName'), FilterComposite::CONDITION_AND);
        $hydrator->addFilter('consignee_name', new MethodMatchFilter('getConsigneeName'), FilterComposite::CONDITION_AND);
        $hydrator->addFilter('warehouse_name', new MethodMatchFilter('getWarehouseName'), FilterComposite::CONDITION_AND);
        $hydrator->addFilter('loaded_wagons', new MethodMatchFilter('getLoadedWagons'), FilterComposite::CONDITION_AND);

        $hydrator->addStrategy('sale_id', new IntegerStrategy());
        $hydrator->addStrategy('company_id', new IntegerStrategy());
        $hydrator->addStrategy('product_id', new IntegerStrategy());
        $hydrator->addStrategy('customer_id', new IntegerStrategy());
        $hydrator->addStrategy('consignee_name', new StringTrimStrategy());
        $hydrator->addStrategy('warehouse_id', new IntegerStrategy());
        $hydrator->addStrategy('updated', new DateTimeFormatterStrategy('Y-m-d H:i:s'));
        $hydrator->addStrategy('created', new DateTimeFormatterStrategy('Y-m-d H:i:s'));

        /** @var Domain\SaleContractEntity $prototype */
        $prototype = new Domain\SaleContractEntity();

        return new SaleContractDb($dbAdapter, $hydrator, $prototype);
    }

}