<?php

namespace Bank\Form\Element;

use Bank\Service\BankManager;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class BankSelectElementFactory implements FactoryInterface {

    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     * @return BankSelectElement|object
     * @throws \Bank\Exception\NotFoundException
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        /** @var BankManager $bankManager */
        $bankManager = $container->get(BankManager::class);
        $valueOptions = $bankManager->fetchValueOptions();
        $select = new BankSelectElement();
        $select->setValueOptions($valueOptions);
        $select->setEmptyOption('Select bank');
        return $select;
    }

}