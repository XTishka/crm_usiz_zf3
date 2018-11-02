<?php

namespace User\Form;

use User\Entity;
use User\InputFilter;
use Interop\Container\ContainerInterface;
use Zend\Hydrator;
use Zend\ServiceManager\Factory\FactoryInterface;

class AccountFactory implements FactoryInterface {

    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return object|Account
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {

        /** @var Hydrator\ClassMethods $hydrator */
        $hydrator = $container->get('HydratorManager')->get(Hydrator\ClassMethods::class);

        /** @var InputFilter\Account $inputFilter */
        $inputFilter = $container->get('InputFilterManager')->get(InputFilter\Account::class);

        /** @var Entity\Account $prototype */
        $prototype = new Entity\Account();

        $form = new Account();
        $form->setHydrator($hydrator);
        $form->setInputFilter($inputFilter);
        $form->setObject($prototype);
        return $form;
    }


}