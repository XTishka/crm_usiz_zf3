<?php

namespace Transport\Form\Element;

use Interop\Container\ContainerInterface;
use Transport\Service\RateManager;
use Zend\ServiceManager\Factory\FactoryInterface;

class RateSelectFactory implements FactoryInterface {

    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     * @return object|RateSelect
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        /** @var RateManager $rateManager */
        $rateManager = $container->get(RateManager::class);

        $element = new RateSelect();
        $element->setValueOptions($rateManager->getRatesValueOptions(null, true));

        return $element;
    }

}