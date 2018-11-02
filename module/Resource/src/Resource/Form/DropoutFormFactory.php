<?php

namespace Resource\Form;

use Resource\Domain\DropoutEntity;
use Interop\Container\ContainerInterface;
use Zend\Hydrator\ClassMethods;
use Zend\Hydrator\Strategy\ClosureStrategy;
use Zend\ServiceManager\Factory\FactoryInterface;

class DropoutFormFactory implements FactoryInterface {

    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     * @return DropoutForm|object
     * @throws \Exception
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        /** @var ClassMethods $hydrator */
        $hydrator = $container->get('HydratorManager')->get(ClassMethods::class);

        $hydrator->addStrategy('period_begin', new ClosureStrategy(
            function (\DateTime $date) {
                return $date->format('d.m.Y');
            },
            function ($dateString) {
                $date = \DateTime::createFromFormat('d.m.Y', $dateString);
                return $date;
            }
        ));

        $hydrator->addStrategy('period_end', new ClosureStrategy(
            function (\DateTime $date) {
                return $date->format('d.m.Y');
            },
            function ($dateString) {
                $date = \DateTime::createFromFormat('d.m.Y', $dateString);
                return $date;
            }
        ));

        /** @var DropoutEntity $object */
        $object = new DropoutEntity();
        /** @var DropoutForm $form */
        $form = new DropoutForm();
        $form->setHydrator($hydrator);
        $form->setObject($object);
        return $form;

    }

}