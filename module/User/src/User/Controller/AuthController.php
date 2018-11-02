<?php


namespace User\Controller;


use User\Exception\ErrorException;
use User\Form;
use User\Service;
use Zend\Http;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\Controller\Plugin\Redirect;
use Zend\Mvc\Plugin\FlashMessenger\FlashMessenger;
use Zend\Uri\Uri;
use Zend\View\Model\ViewModel;

class AuthController extends AbstractActionController {

    /**
     * @var Http\Request
     */
    protected $request;

    /**
     * @var Service\AuthManager
     */
    protected $authManager;

    /**
     * @var Form\Login
     */
    protected $loginForm;

    /**
     * AuthController constructor.
     * @param Service\AuthManager $authManager
     * @param Form\Login $loginForm
     */
    public function __construct(Service\AuthManager $authManager, Form\Login $loginForm) {
        $this->authManager = $authManager;
        $this->loginForm = $loginForm;
    }

    /**
     * @return Http\Response|ViewModel
     * @throws ErrorException
     * @throws \Zend\Authentication\Exception\ExceptionInterface
     */
    public function loginAction() {
        $form = $this->loginForm;
        /** @var FlashMessenger $messenger */
        $messenger = $this->plugin('FlashMessenger');
        /** @var Redirect $redirect */
        $redirect = $this->plugin('Redirect');
        if ($this->request->isPost()) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $result = $this->authManager->login($form->getData());
                $messenger->addMessage($result->getMessage(), $result->getStatus());
                if ($result::STATUS_SUCCESS === $result->getStatus()) {
                    $redirectUrl = $this->params()->fromPost('redirect_url', '');
                    if (!empty($redirectUrl)) {
                        // Проверка ниже нужна для предотвращения возможных атак перенаправления
                        // (когда кто-то пытается перенаправить пользователя на другой домен).
                        $uri = new Uri($redirectUrl);
                        if (!$uri->isValid() || $uri->getHost() != null)
                            throw new ErrorException('Incorrect redirect URL: ' . $redirectUrl);
                        return $redirect->toUrl($redirectUrl);
                    }
                    return $redirect->toRoute('home');
                }
                return $redirect->toRoute('login');
            } else {
                $messenger->addMessage('Authentication failed', 'error');
                return $this->plugin('redirect')->toRoute('login');
            }
        }

        $redirectUrl = (string)$this->params()->fromQuery('redirectUrl', '');
        if (strlen($redirectUrl) > 2048)
            throw new ErrorException("Too long redirectUrl argument passed");

        $form->setData(['redirect_url' => $redirectUrl]);

        $viewModel = new ViewModel();
        $viewModel->setTerminal(true);
        $viewModel->setVariable('form', $form);
        $viewModel->setVariable('messenger', $messenger);
        return $viewModel;
    }

    public function logoutAction() {
        $this->authManager->logout();
        return $this->plugin('redirect')->toRoute('login');
    }

}