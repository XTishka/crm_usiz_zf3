<?php

namespace Manufacturing\Controller;

use Contractor\Entity\ContractorCompany;
use Contractor\Service\ContractorCompanyManager;
use Contractor\Service\ContractorPlantManager;
use Manufacturing\Domain\SkipCommonEntity;
use Manufacturing\Domain\SkipMaterialEntity;
use Manufacturing\Form;
use Manufacturing\Service\FurnaceManager;
use Manufacturing\Service\SkipManager;
use Zend\Http\Request;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class SkipController extends AbstractActionController {

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var FurnaceManager
     */
    protected $furnaceManager;

    /**
     * @var SkipManager
     */
    protected $skipManager;

    /**
     * @var Form\SkipCommon
     */
    protected $skipCommonForm;

    /**
     * @var ContractorCompanyManager
     */
    protected $companyManager;

    /**
     * SkipController constructor.
     * @param FurnaceManager           $furnaceManager
     * @param SkipManager              $skipManager
     * @param Form\SkipCommon          $skipCommonForm
     * @param ContractorCompanyManager $companyManager
     */
    public function __construct(FurnaceManager $furnaceManager,
                                SkipManager $skipManager,
                                Form\SkipCommon $skipCommonForm,
                                ContractorCompanyManager $companyManager) {
        $this->furnaceManager = $furnaceManager;
        $this->skipManager = $skipManager;
        $this->skipCommonForm = $skipCommonForm;
        $this->companyManager = $companyManager;
    }

    /**
     * @return ViewModel
     * @throws \Contractor\Exception\ErrorException
     */
    public function indexAction() {
        $companyId = $this->params()->fromRoute('id');
        /** @var ContractorCompany $company */
        $company = $this->companyManager->getContractorById($companyId);
        $date = $this->params()->fromQuery('date');
        $furnaces = $this->skipManager->getFurnaceLogByPlantId($company->getPlantId(), $date);
        $viewModel = new ViewModel();
        $viewModel->setVariable('furnaces', $furnaces);
        $viewModel->setVariable('company', $company);
        $viewModel->setTerminal(true);
        return $viewModel;
    }

    public function editAction() {

        $form = $this->skipCommonForm;
        $messenger = $this->plugin('FlashMessenger');
        $error = false;
        if ($this->request->isPost()) {

            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                /** @var SkipCommonEntity $object */
                $object = $form->getObject();

                $testUgol = false;
                $testIzvest = false;
                if ($materials = $object->getMaterials()) {

                    /** @var SkipMaterialEntity $material */
                    foreach ($materials as $material) {
                        /*
                        if (1 == $material->getMaterialId() || 3 == $material->getMaterialId())
                            $testIzvest = true;
                        if (2 == $material->getMaterialId())
                            $testUgol = true;
                        */

                        if ($material->getDropout())
                            $testIzvest = true;
                        if (!$material->getDropout())
                            $testUgol = true;
                    }
                }
                if ($testUgol && $testIzvest) {
                    $result = $this->skipManager->saveSkip($object);
                    $messenger->addMessage($result->getMessage(), $result->getStatus());
                    $furnace = $this->furnaceManager->getFurnaceById($object->getFurnaceId());
                    //return $this->plugin('Redirect')->toRoute('dashboard', ['id' => $furnace->getPlantId()]);
                    return $this->plugin('Redirect')->toRoute('dashboard', ['company' => $this->params()->fromQuery('company')]);
                } else {
                    $error = 'Be sure to load coal and lime';
                }
            }
        } else if ($skipId = $this->params()->fromRoute('id')) {
            $object = $this->skipManager->getOneSkipById($skipId);
            $form->bind($object);
            //$form->populateValues(['company_id' => $this->params()->fromQuery('company') ?? $this->params()->fromPost('company_id')]);
        } else {
            $form->populateValues([
                'company_id' => $this->params()->fromQuery('company') ?? $this->params()->fromPost('company_id'),
                'furnace_id' => $this->params()->fromQuery('furnace') ?? $this->params()->fromPost('furnace'),
                'date'       => $this->params()->fromQuery('date') ?? $this->params()->fromPost('date', date('d.m.Y')),
            ]);
        }

        $viewModel = new ViewModel();
        $viewModel->setVariable('companyId', $this->params()->fromQuery('company'));
        $viewModel->setVariable('form', $form);
        $viewModel->setVariable('messenger', $messenger);
        $viewModel->setVariable('errorMessage', $error);
        return $viewModel;

    }

    public function deleteAction() {
        $messenger = $this->plugin('FlashMessenger');
        $companyId = $this->params()->fromQuery('company');
        $skipId = $this->params()->fromRoute('id');
        $result = $this->skipManager->deleteSkip($skipId);
        $messenger->addMessage($result->getMessage(), $result->getStatus());
        return $this->plugin('Redirect')->toRoute('dashboard', ['company' => $companyId]);
    }

}