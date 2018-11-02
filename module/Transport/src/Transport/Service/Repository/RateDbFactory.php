<?php

namespace Transport\Service\Repository;

use Application\Domain\AbstractValueObject;
use Application\Hydrator\Strategy\IntegerStrategy;
use Interop\Container\ContainerInterface;
use ReflectionClass;
use Transport\Domain;
use Zend\Db\Adapter\Adapter as DbAdapter;
use Zend\Hydrator;
use Zend\Hydrator\Filter\MethodMatchFilter;
use Zend\Hydrator\Filter\FilterComposite;
use Zend\Hydrator\Strategy\ClosureStrategy;
use Zend\Hydrator\Strategy\DateTimeFormatterStrategy;
use Zend\ServiceManager\Factory\FactoryInterface;

class RateDbFactory implements FactoryInterface {

    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     * @return object|RateDb
     * @throws \Exception
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        /** @var DbAdapter $dbAdapter */
        $dbAdapter = $container->get(DbAdapter::class);

        /** @var Hydrator\ClassMethods $hydrator */
        $hydrator = $container->get('HydratorManager')->get(Hydrator\ClassMethods::class);

        $hydrator->addFilter('plant_name', new MethodMatchFilter('getPlantName'), FilterComposite::CONDITION_AND);
        $hydrator->addFilter('company_name', new MethodMatchFilter('getCompanyName'), FilterComposite::CONDITION_AND);
        $hydrator->addFilter('station_name', new MethodMatchFilter('getStationName'), FilterComposite::CONDITION_AND);
        $hydrator->addFilter('carrier_name', new MethodMatchFilter('getCarrierName'), FilterComposite::CONDITION_AND);
        $hydrator->addFilter('carrier_type', new MethodMatchFilter('getCarrierType'), FilterComposite::CONDITION_AND);
        //$hydrator->addFilter('multiple_weight', new MethodMatchFilter('getMultipleWeight'), FilterComposite::CONDITION_AND);

        $hydrator->addStrategy('station_id', new IntegerStrategy());
        $hydrator->addStrategy('is_deleted', new Hydrator\Strategy\BooleanStrategy('1', '0'));
        $hydrator->addStrategy('updated', new DateTimeFormatterStrategy('Y-m-d H:i:s'));
        $hydrator->addStrategy('created', new DateTimeFormatterStrategy('Y-m-d H:i:s'));
        $hydrator->addStrategy('period_begin', new DateTimeFormatterStrategy('Y-m-d'));
        $hydrator->addStrategy('period_end', new DateTimeFormatterStrategy('Y-m-d'));
        $hydrator->addStrategy('values', new ClosureStrategy(null,
            function ($json) {
                $json = preg_replace(['/\\\"/', '/\["/', '/"\]/'], ['"', '[', ']'], $json);
                $values = json_decode($json);
                if (JSON_ERROR_NONE !== json_last_error())
                    return [];
                foreach ($values as &$value) {
                    $object = new Domain\RateValueEntity();
                    $object->setValueId(intval($value->value_id));
                    $object->setPrice(floatval($value->price));
                    $object->setWeight($value->weight);
                    $value = $object;
                }
                return $values;
            }
        ));

        /** @var Domain\RateEntity $prototype */
        $prototype = new Domain\RateEntity();

        return new RateDb($dbAdapter, $hydrator, $prototype);
    }

}