<?php

namespace Reports\Controller;

use Interop\Container\ContainerInterface;
use Reports\Form\FinanceFilterForm;
use Zend\Db\Adapter\Adapter;
use Zend\I18n\Translator\Translator;
use Zend\I18n\Translator\TranslatorInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class FinanceControllerFactory
 * @package Reports\Controller
 */
class FinanceControllerFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $form = $container->get('FormElementManager')->get(FinanceFilterForm::class);
        $db = $container->get(Adapter::class);
        $translator = $container->get(TranslatorInterface::class);
        return new FinanceController($form, $db, $translator);
    }

}