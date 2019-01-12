<?php

namespace Application\Controller;

use Application\Model\Finance\Plant\AccountPayableService;
use Bank\Service\RecordManager;
use Contractor\Entity\ContractorPlant;
use Contractor\Service\ContractorPlantManager;
use Document\Service\FinanceManager;
use Document\Service\PurchaseWagonManager;
use Manufacturing\Service\SkipManager;
use Manufacturing\Service\WarehouseLogManager;
use Zend\Http;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class PlantDashboardController extends AbstractActionController {

    /**
     * @var Http\Request
     */
    protected $request;

    /**
     * @var ContractorPlantManager
     */
    protected $plantManager;

    /**
     * @var FinanceManager
     */
    protected $financeManager;

    /**
     * @var WarehouseLogManager
     */
    protected $warehouseLogManager;

    /**
     * @var SkipManager
     */
    protected $furnaceSkipManager;

    /**
     * @var PurchaseWagonManager
     */
    protected $purchaseWagonManager;

    /**
     * @var RecordManager
     */
    protected $recordManager;

    /**
     * @var AccountPayableService
     */
    protected $accountPayableService;

    public function __construct(ContractorPlantManager $plantManager,
                                FinanceManager $financeManager,
                                WarehouseLogManager $warehouseLogManager,
                                SkipManager $furnaceSkipManager,
                                PurchaseWagonManager $purchaseWagonManager,
                                RecordManager $recordManager,
                                AccountPayableService $accountPayableService) {
        $this->plantManager = $plantManager;
        $this->financeManager = $financeManager;
        $this->warehouseLogManager = $warehouseLogManager;
        $this->furnaceSkipManager = $furnaceSkipManager;
        $this->purchaseWagonManager = $purchaseWagonManager;
        $this->recordManager = $recordManager;
        $this->accountPayableService = $accountPayableService;
    }

    public function welcomeAction() {
        $messenger = $this->plugin('FlashMessenger');
        $viewModel = new ViewModel();
        $viewModel->setVariable('messenger', $messenger);
        return $viewModel;
    }

    /**
     * @return ViewModel
     * @throws \Contractor\Exception\ErrorException
     * @throws \Manufacturing\Exception\ErrorException
     */
    public function indexAction() {
        $plantId = $this->params()->fromRoute('plant');

        $plant = $this->plantManager->getContractorById($plantId);

        if ($plant instanceof ContractorPlant) {
            $this->redirect()->toRoute('plantDashboard/finance', ['plant' => $plantId]);
        }

        $expectedWagons = $this->purchaseWagonManager->getExpectedWagonsStatistic($plantId);
        $expectedMaterials = $this->purchaseWagonManager->getExpectedMaterialWeight($plantId);
        $totalMaterials = $this->purchaseWagonManager->getTotalMaterialWeight($plant->getContractorId(), $plantId);

        $messenger = $this->plugin('FlashMessenger');

        $materials = $this->warehouseLogManager->getMaterialBalances($plant->getContractorId());
        $products = $this->warehouseLogManager->getProductBalances($plant->getContractorId());

        $furnaces = $this->furnaceSkipManager->getFurnaceLogByPlantId($plant->getContractorId());

        $viewModel = new ViewModel();
        $viewModel->setVariable('plant', $plant);

        $viewModel->setVariable('materials', $materials);
        $viewModel->setVariable('products', $products);
        $viewModel->setVariable('messenger', $messenger);
        $viewModel->setVariable('furnaces', $furnaces);
        $viewModel->setVariable('plantId', $plantId);
        $viewModel->setVariable('expectedMaterials', $expectedMaterials);
        $viewModel->setVariable('expectedWagons', $expectedWagons);
        $viewModel->setVariable('totalMaterials', $totalMaterials);
        return $viewModel;
    }

    /**
     * @return ViewModel
     * @throws \Bank\Exception\NotFoundException
     * @throws \Contractor\Exception\ErrorException
     * @throws \Document\Exception\ErrorException
     * @throws \Manufacturing\Exception\ErrorException
     */
    public function financeAction() {
        $plantId = $this->params()->fromRoute('plant');
        $plant = $this->plantManager->getContractorById($plantId);
        $messenger = $this->plugin('FlashMessenger');

        $viewModel = new ViewModel();
        $viewModel->setVariable('plant', $plant);
        $viewModel->setVariable('messenger', $messenger);
        $viewModel->setVariable('warehouseBalance', $this->warehouseLogManager->getTotalMaterialBalances($plant->getContractorId()));
        $viewModel->setVariable('expectedMaterials', $this->purchaseWagonManager->getExpectedMaterialWeight($plantId));
        $viewModel->setVariable('plantBalance', $this->financeManager->getCompanyBalance($plantId));
        $viewModel->setVariable('accountPayableContainer', $this->accountPayableService->getRecords($plantId));
        $viewModel->setVariable('plantReceivableSum', $this->financeManager->getCompanyReceivableSum($plantId));

        $viewModel->setVariable('plantPrepaymentSum', $this->financeManager->getPlantPrepaymentSum($plantId));
        $viewModel->setVariable('plantFromCompanyPrepaymentSum', $this->financeManager->getPlantFromCompanyPrepaymentSum($plantId));

        $viewModel->setVariable('internalPayableSum', $this->financeManager->getInternalPayableSum($plantId));
        $viewModel->setVariable('bankTotalSum', $this->recordManager->getCurrentTotalAmount($plantId));

        $viewModel->setVariable('plantId', $plantId);
        return $viewModel;
    }

}