<?php

namespace Document\Form;

use Document\Domain\TransactionEntity;
use Document\Form;
use Document\InputFilter;
use ReflectionClass;
use Interop\Container\ContainerInterface;
use Zend\Hydrator;
use Zend\ServiceManager\Factory\AbstractFactoryInterface;

class TransactionAbstractFactory implements AbstractFactoryInterface {

    public function canCreate(ContainerInterface $container, $requestedName) {
        return is_subclass_of($requestedName, TransactionAbstract::class);
    }

    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     * @return TransactionAbstract|object
     * @throws \Exception
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {

        /** @var Hydrator\ClassMethods $hydrator */
        $hydrator = $container->get('HydratorManager')->get(Hydrator\ClassMethods::class);
        $hydrator->addStrategy('credit', new Hydrator\Strategy\ClosureStrategy(
            function ($value) {
                return floatval($value);
            },
            function ($value) {
                return floatval($value);
            }
        ));

        $hydrator->addStrategy('debit', new Hydrator\Strategy\ClosureStrategy(
            function ($value) {
                return floatval($value);
            },
            function ($value) {
                return floatval($value);
            }
        ));

        $hydrator->addStrategy('created', new Hydrator\Strategy\DateTimeFormatterStrategy('d.m.Y H:i:s'));

        $shortRequestedName = (new ReflectionClass($requestedName))->getShortName();
        $inputFilterClassName = preg_replace('/^([A-Za-z]+)$/', 'Document\InputFilter\\\${1}', $shortRequestedName);

        /** @var InputFilter\TransactionAbstract $contractorAbstractManager */
        $inputFilter = $container->get('InputFilterManager')->get($inputFilterClassName);

        /** @var Form\TransactionAbstract $form */
        $form = new $requestedName();
        $form->setHydrator($hydrator);
        $form->setInputFilter($inputFilter);
        $form->setObject(new TransactionEntity());
        return $form;

    }


}