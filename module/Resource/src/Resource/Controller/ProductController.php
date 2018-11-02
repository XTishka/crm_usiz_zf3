<?php

namespace Resource\Controller;

use Resource\Form;
use Resource\Service\ProductManager;
use Zend\Http;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class ProductController extends AbstractActionController {

    /**
     * @var Http\Request
     */
    protected $request;

    /**
     * @var ProductManager
     */
    protected $productManager;

    /**
     * @var Form\Product
     */
    protected $productForm;

    /**
     * ProductController constructor.
     * @param ProductManager $productManager
     * @param Form\Product $productForm
     */
    public function __construct(ProductManager $productManager, Form\Product $productForm) {
        $this->productManager = $productManager;
        $this->productForm = $productForm;
    }

    public function indexAction() {
        $messenger = $this->plugin('FlashMessenger');
        $pageNumber = $this->params()->fromQuery('page');
        $paginator = $this->productManager->getProductsPaginator();
        $paginator->setCurrentPageNumber($pageNumber);
        $viewModel = new ViewModel();
        $viewModel->setVariable('messenger', $messenger);
        $viewModel->setVariable('paginator', $paginator);
        return $viewModel;
    }

    public function editAction() {
        $form = $this->productForm;
        $messenger = $this->plugin('FlashMessenger');
        if ($this->request->isPost()) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $object = $form->getObject();
                $result = $this->productManager->saveProduct($object);
                $messenger->addMessage($result->getMessage(), $result->getStatus());
                if ($this->params()->fromPost('save_and_remain'))
                    return $this->plugin('Redirect')->toRoute('product/edit', ['id' => $result->getSource()->getProductId()]);
                return $this->plugin('Redirect')->toRoute('product');
            }
        } elseif ($productId = $this->params()->fromRoute('id')) {
            $object = $this->productManager->getProductById($productId);
            $form->bind($object);
        }
        $viewModel = new ViewModel();
        $viewModel->setVariable('form', $form);
        $viewModel->setVariable('messenger', $messenger);
        return $viewModel;
    }

    public function deleteAction() {
        $productId = $this->params()->fromRoute('id');
        $result = $this->productManager->deleteProductById($productId);
        $this->plugin('FlashMessenger')->addMessage($result->getMessage(), $result->getStatus());
        $this->plugin('Redirect')->toRoute('product');
    }

}