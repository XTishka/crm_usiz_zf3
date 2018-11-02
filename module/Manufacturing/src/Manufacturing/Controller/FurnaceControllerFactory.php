<?php

namespace Manufacturing\Controller;

use Manufacturing\Form;
use Manufacturing\Service\FurnaceManager;
use Interop\Container\ContainerInterface;
use Manufacturing\Service\FurnaceSkipManager;
use Zend\ServiceManager\Factory\FactoryInterface;

class FurnaceControllerFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $furnaceManager = $container->get(FurnaceManager::class);
        $furnaceSkipManager = $container->get(FurnaceSkipManager::class);
        $furnaceForm = $container->get('FormElementManager')->get(Form\Furnace::class);
        $furnaceSkipForm = $container->get('FormElementManager')->get(Form\FurnaceSkip::class);
        return new FurnaceController($furnaceManager, $furnaceSkipManager, $furnaceForm, $furnaceSkipForm);
    }


}