<?php

namespace Transport\Form;

use Transport\Domain;
use Transport\InputFilter;
use Interop\Container\ContainerInterface;
use Zend\Hydrator;
use Zend\ServiceManager\Factory\FactoryInterface;

class RateFactory implements FactoryInterface {

    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     * @return object|Rate
     * @throws \Exception
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        /** @var Hydrator\ClassMethods $hydrator */
        $hydrator = $container->get('HydratorManager')->get(Hydrator\ClassMethods::class);

        $hydrator->addStrategy('period_begin', new Hydrator\Strategy\ClosureStrategy(
            function (\DateTime $date) {
                return $date->format('d.m.Y');
            },
            function ($dateString) {
                $date = \DateTime::createFromFormat('d.m.Y', $dateString);
                return $date;
            }
        ));

        $hydrator->addStrategy('period_end', new Hydrator\Strategy\ClosureStrategy(
            function (\DateTime $date) {
                return $date->format('d.m.Y');
            },
            function ($dateString) {
                $date = \DateTime::createFromFormat('d.m.Y', $dateString);
                return $date;
            }
        ));

        $form = new Rate();
        $form->setHydrator($hydrator);
        $form->setInputFilter($container->get('InputFilterManager')->get(InputFilter\Rate::class));
        $form->setObject(new Domain\RateEntity());
        return $form;
    }

}