<?php
/**
 * Created by Bogdan Tereshchenko <development.sites@gmail.com>
 * Copyright: 2006-2019 Bogdan Tereshchenko
 * Link: https://zelliengroup.com/
 */

namespace Contractor\Form\Element;

use Contractor\Service\Repository\DatabaseContractorAbstract;
use Interop\Container\ContainerInterface;
use Transport\Service\Repository\CarrierDb;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Sql;
use Zend\ServiceManager\Factory\FactoryInterface;

class ContractorAllSelectFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {

        $db = $container->get(Adapter::class);

        $sql = new Sql($db);

        $selectA = $sql->select(DatabaseContractorAbstract::TABLE_CONTRACTORS);
        $selectA->columns([
            'value' => new Expression('CONCAT("con_", contractor_id)'),
            'label' => 'contractor_name',
            'type'  => 'contractor_type',
        ]);

        $selectB = $sql->select(CarrierDb::TABLE_CARRIERS);
        $selectB->columns([
            'value' => new Expression('CONCAT("car_", carrier_id)'),
            'label' => 'carrier_name',
            'type'  => new Expression('TRIM("carrier")'),
        ]);

        $select = $selectA->combine($selectB, 'union', 'all');

        $dataSource = $sql->prepareStatementForSqlObject($select)->execute();

        $options = [];
        foreach ($dataSource as $contractor) {
            $options[] = [
                'value'      => $contractor['value'],
                'label'      => $contractor['label'],
                'attributes' => [
                    'data-type' => $contractor['type'],
                ],
            ];
        }
        $element = new ContractorAllSelect();
        $element->setValueOptions($options);

        return $element;
    }

}