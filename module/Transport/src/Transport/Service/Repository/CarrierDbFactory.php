<?php

namespace Transport\Service\Repository;

use Application\Domain\EmailValueObject;
use Application\Domain\PersonValueObject;
use Application\Domain\PhoneValueObject;
use Application\Hydrator\Strategy\IntegerStrategy;
use Interop\Container\ContainerInterface;
use Transport\Domain;
use Zend\Db\Adapter\Adapter as DbAdapter;
use Zend\Hydrator;
use Zend\ServiceManager\Factory\FactoryInterface;

class CarrierDbFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        /** @var DbAdapter $dbAdapter */
        $dbAdapter = $container->get(DbAdapter::class);

        /** @var Hydrator\ClassMethods $hydrator */
        $hydrator = $container->get('HydratorManager')->get(Hydrator\ClassMethods::class);
        $hydrator->addStrategy('carrier_id', new IntegerStrategy());

        $hydrator->addStrategy('person', new Hydrator\Strategy\ClosureStrategy(
            function (PersonValueObject $object) {
                return $object->toJson();
            },
            function (string $json) {
                return PersonValueObject::factory($json);
            }
        ));

        $hydrator->addStrategy('emails', new Hydrator\Strategy\ClosureStrategy(
            function (array $data) {
                $haystack = array_map(function (EmailValueObject $object) {
                    return $object->toArray();
                }, $data);
                return json_encode($haystack);
            },
            function ($json) {
                return array_map(function (array $data) {
                    return EmailValueObject::factory($data);
                }, json_decode($json, true));
            }
        ));

        $hydrator->addStrategy('phones', new Hydrator\Strategy\ClosureStrategy(
            function (array $data) {
                $haystack = array_map(function (PhoneValueObject $object) {
                    return $object->toArray();
                }, $data);
                return json_encode($haystack);
            },
            function ($json) {
                return array_map(function (array $data) {
                    return PhoneValueObject::factory($data);
                }, json_decode($json, true));
            }
        ));

        $hydrator->addStrategy('updated', new Hydrator\Strategy\DateTimeFormatterStrategy('Y-m-d H:i:s'));
        $hydrator->addStrategy('created', new Hydrator\Strategy\DateTimeFormatterStrategy('Y-m-d H:i:s'));

        /** @var Domain\CarrierEntity $prototype */
        $prototype = new Domain\CarrierEntity();

        return new CarrierDb($dbAdapter, $hydrator, $prototype);
    }

}