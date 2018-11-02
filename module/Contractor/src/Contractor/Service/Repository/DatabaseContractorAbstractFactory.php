<?php

namespace Contractor\Service\Repository;

use Application\Domain\AddressValueObject;
use Application\Domain\EmailValueObject;
use Application\Domain\PersonValueObject;
use Application\Domain\PhoneValueObject;
use Application\Hydrator\Strategy\StringTrimStrategy;
use Contractor\Entity;
use Contractor\InputFilter;
use Interop\Container\ContainerInterface;
use Zend\Db\Adapter\Adapter;
use Zend\Hydrator\ClassMethods;
use ReflectionClass;
use Zend\Hydrator\Strategy\ClosureStrategy;
use Zend\Hydrator\Strategy\DateTimeFormatterStrategy;
use Zend\ServiceManager\Factory\AbstractFactoryInterface;

class DatabaseContractorAbstractFactory implements AbstractFactoryInterface {

    public function canCreate(ContainerInterface $container, $requestedName) {
        return is_subclass_of($requestedName, DatabaseContractorAbstract::class);
    }

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {

        /** @var Adapter $adapter */
        $adapter = $container->get(Adapter::class);

        /** @var ClassMethods $hydrator */
        $hydrator = $container->get('HydratorManager')->get(ClassMethods::class);

        $hydrator->addStrategy('address', new ClosureStrategy(
            function ($object) {
                return ($object instanceof AddressValueObject) ? $object->toJson() : $object;
            },
            function ($json) {
                return AddressValueObject::factory($json);
            }
        ));

        $hydrator->addStrategy('emails', new ClosureStrategy(
            function ($data) {
                if (!is_array($data))
                    return $data;
                $haystack = array_map(function ($object) {
                    return ($object instanceof EmailValueObject) ? $object->toArray() : $object;
                }, $data);
                return json_encode($haystack);
            },
            function ($json) {
                $data = json_decode($json, true);
                if (JSON_ERROR_NONE !== json_last_error() && is_array($data)) {
                    return array_map([EmailValueObject::class, 'factory'], $data);
                }
                return [];
            }
        ));

        $hydrator->addStrategy('phones', new ClosureStrategy(
            function (array $data) {
                $haystack = array_map(function (PhoneValueObject $object) {
                    return $object->toArray();
                }, $data);
                return json_encode($haystack);
            },
            function ($json) {
                $data = json_decode($json, true);
                if (JSON_ERROR_NONE !== json_last_error() && is_array($data)) {
                    return array_map([PhoneValueObject::class, 'factory'], $data);
                }
                return [];
            }
        ));

        $hydrator->addStrategy('person', new ClosureStrategy(
            function (PersonValueObject $object) {
                return $object->toJson();
            },
            function ($json) {
                return PersonValueObject::factory($json);
            }
        ));

        $hydrator->addStrategy('description', new StringTrimStrategy());
        $hydrator->addStrategy('register_code', new StringTrimStrategy());
        $hydrator->addStrategy('bank_account', new StringTrimStrategy());
        $hydrator->addStrategy('updated', new DateTimeFormatterStrategy('Y-m-d H:i:s'));
        $hydrator->addStrategy('created', new DateTimeFormatterStrategy('Y-m-d H:i:s'));

        $type = preg_replace('/^Database([A-Za-z]+)$/', '${1}', (new ReflectionClass($requestedName))->getShortName());
        /** @var Entity\ContractorAbstract $object */
        $object = Entity\ContractorFactory::build($type);

        /** @var DatabaseContractorAbstract $repository */
        $repository = new $requestedName($adapter, $hydrator, $object);

        return $repository;
    }


}