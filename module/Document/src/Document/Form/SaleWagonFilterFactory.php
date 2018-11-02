<?php

namespace Document\Form;

use Interop\Container\ContainerInterface;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Sql;
use Zend\Http\Request;
use Zend\Router\Http\TreeRouteStack;
use Zend\ServiceManager\Factory\FactoryInterface;

class SaleWagonFilterFactory implements FactoryInterface {

    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return mixed|object
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {

        /** @var TreeRouteStack $router */
        $router = $container->get('Router');
        /** @var Request $request */
        $request = $container->get('Request');

        $contractId = $router->match($request)->getParam('id');

        /** @var Adapter $adapter */
        $adapter = $container->get(Adapter::class);

        $sql = new Sql($adapter);
        $carrierSelect = $sql->select(['a' => 'carriers']);
        $carrierSelect->join(['b' => 'sale_wagons'], 'a.carrier_id = b.carrier_id', []);
        $carrierSelect->columns(['label' => 'carrier_name', 'value' => 'carrier_id']);
        $carrierSelect->group('a.carrier_id');
        $carrierSelect->where->equalTo('b.contract_id', $contractId);

        $carrierDataSource = $sql->prepareStatementForSqlObject($carrierSelect)->execute();
        $carrierResultSet = new ResultSet('array');
        $carrierResultSet->initialize($carrierDataSource);

        $loadingDateSelect = $sql->select('sale_wagons');
        $loadingDateSelect->columns([
            'min' => new Expression('MIN(loading_date)'),
            'max' => new Expression('MAX(loading_date)'),
        ]);
        $loadingDateSelect->where->equalTo('contract_id', $contractId);

        $loadingDateDataSource = $sql->prepareStatementForSqlObject($loadingDateSelect)->execute();
        $loadingDateResultSet = new ResultSet();
        $loadingDateResultSet->initialize($loadingDateDataSource);

        $form = new SaleWagonFilter([
            'carriers'         => $carrierResultSet->toArray(),
            'minLoadingDate'   => $loadingDateResultSet->current()->offsetGet('min'),
            'maxLoadingDate'   => $loadingDateResultSet->current()->offsetGet('max'),
        ]);


        return $form;
    }

}