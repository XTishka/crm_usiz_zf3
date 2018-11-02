<?php

namespace Document\Form;

use Application\Hydrator\Strategy\IntegerStrategy;
use Document\Domain;
use Document\InputFilter;
use Interop\Container\ContainerInterface;
use Zend\Hydrator;
use Zend\ServiceManager\Factory\FactoryInterface;

class PurchaseLoadingWagonFactory implements FactoryInterface {

    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     * @return PurchaseLoadingWagon|object
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        /** @var Hydrator\ClassMethods $hydrator */
        $hydrator = $container->get('HydratorManager')->get(Hydrator\ClassMethods::class);

        $hydrator->addStrategy('loading_date', new Hydrator\Strategy\DateTimeFormatterStrategy('d.m.Y'));
        $hydrator->addStrategy('unloading_date', new Hydrator\Strategy\DateTimeFormatterStrategy('d.m.Y'));

        $form = new PurchaseLoadingWagon();
        $form->setHydrator($hydrator);
        $form->setInputFilter($container->get('InputFilterManager')->get(InputFilter\PurchaseWagon::class));
        $form->setObject(new Domain\PurchaseWagonEntity());
        return $form;
    }

}