<?php

namespace Manufacturing\Controller;

use Manufacturing\Domain\FurnaceSkipEntity;
use Manufacturing\Form;
use Manufacturing\Service\FurnaceManager;
use Manufacturing\Service\FurnaceSkipManager;
use Zend\Http;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class FurnaceController extends AbstractActionController {

    /**
     * @var Http\Request
     */
    protected $request;

    /**
     * @var FurnaceManager
     */
    protected $furnaceManager;

    /**
     * @var FurnaceSkipManager
     */
    protected $furnaceSkipManager;

    /**
     * @var Form\Furnace
     */
    protected $furnaceForm;

    /**
     * @var Form\FurnaceSkip
     */
    protected $furnaceSkipForm;

    /**
     * FurnaceController constructor.
     * @param FurnaceManager $furnaceManager
     * @param FurnaceSkipManager $furnaceSkipManager
     * @param Form\Furnace $furnaceForm
     * @param Form\FurnaceSkip $furnaceSkipForm
     */
    public function __construct(FurnaceManager $furnaceManager,
                                FurnaceSkipManager $furnaceSkipManager,
                                Form\Furnace $furnaceForm,
                                Form\FurnaceSkip $furnaceSkipForm) {
        $this->furnaceManager = $furnaceManager;
        $this->furnaceSkipManager = $furnaceSkipManager;
        $this->furnaceForm = $furnaceForm;
        $this->furnaceSkipForm = $furnaceSkipForm;
    }

    public function indexAction() {
        $messenger = $this->plugin('FlashMessenger');
        $pageNumber = $this->params()->fromQuery('page');
        $paginator = $this->furnaceManager->getFurnacesPaginator();
        $paginator->setCurrentPageNumber($pageNumber);
        $paginator->setItemCountPerPage(20);
        $viewModel = new ViewModel();
        $viewModel->setVariable('messenger', $messenger);
        $viewModel->setVariable('paginator', $paginator);
        return $viewModel;
    }

    public function loadingAction() {
        $form = $this->furnaceSkipForm;
        $messenger = $this->plugin('FlashMessenger');
        $error = false;
        if ($this->request->isPost()) {
            $post = $this->params()->fromPost();
            $form->setData($post);
            if ($form->isValid()) {
                /** @var FurnaceSkipEntity $object */
                $object = $form->getObject();
                $testUgol = false;
                $testIzvest = false;
                if ($materials = $object->getMaterials()) {

                    /** @var \ArrayObject $material */
                    foreach ($materials as $material) {
                        if (1 == $material->offsetGet('material_id') || 3 == $material->offsetGet('material_id'))
                            $testIzvest = true;
                        if (2 == $material->offsetGet('material_id'))
                            $testUgol = true;
                    }
                }
                if ($testUgol && $testIzvest) {
                    $result = $this->furnaceSkipManager->loading($object);
                    $messenger->addMessage($result->getMessage(), $result->getStatus());
                    $furnace = $this->furnaceManager->getFurnaceById($object->getFurnaceId());
                    return $this->plugin('Redirect')->toRoute('dashboard', ['id' => $furnace->getPlantId()]);
                } else {
                    $error = 'Be sure to load coal and lime';
                }
            }
        }

        $form->populateValues([
            'furnace_id' => $this->params()->fromQuery('furnace') ?? $this->params()->fromPost('furnace'),
            'date'       => $this->params()->fromQuery('date') ?? $this->params()->fromPost('date', date('d.m.Y')),
        ]);

        $viewModel = new ViewModel();
        $viewModel->setVariable('form', $form);
        $viewModel->setVariable('messenger', $messenger);
        $viewModel->setVariable('errorMessage', $error);
        return $viewModel;
    }

    public function unloadingAction() {

    }

    public function editAction() {
        $form = $this->furnaceForm;
        $messenger = $this->plugin('FlashMessenger');
        if ($this->request->isPost()) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $object = $form->getObject();
                $result = $this->furnaceManager->saveFurnace($object);
                $messenger->addMessage($result->getMessage(), $result->getStatus());
                if ($this->params()->fromPost('save_and_remain'))
                    return $this->plugin('Redirect')->toRoute('furnace/edit', ['id' => $result->getSource()->getFurnaceId()]);
                return $this->plugin('Redirect')->toRoute('furnace');
            }
        } elseif ($furnaceId = $this->params()->fromRoute('id')) {
            $object = $this->furnaceManager->getFurnaceById($furnaceId);
            $form->bind($object);
        }
        $viewModel = new ViewModel();
        $viewModel->setVariable('form', $form);
        $viewModel->setVariable('messenger', $messenger);
        return $viewModel;
    }

    public function deleteAction() {
        $furnaceId = $this->params()->fromRoute('id');
        $result = $this->furnaceManager->deleteFurnaceById($furnaceId);
        $this->plugin('FlashMessenger')->addMessage($result->getMessage(), $result->getStatus());
        $this->plugin('Redirect')->toRoute('furnace');
    }

}