<?php

namespace User\Controller\Plugin;

use Application\Exception\InvalidArgumentException;
use Zend\Authentication\AuthenticationService;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Mvc\MvcEvent;
use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Resource\GenericResource;
use Zend\Permissions\Acl\Role\GenericRole;
use Zend\View\Helper\Navigation;
use Zend\View\Model\ViewModel;

class AccessManager extends AbstractPlugin {

    protected $acl;

    protected $defaultRole;

    protected $roles = [];

    protected $resources = [];

    /**
     * AccessManager constructor.
     * @param array $config
     * @param AuthenticationService $authenticationService
     * @throws \Zend\Authentication\Exception\ExceptionInterface
     */
    public function __construct(array $config, AuthenticationService $authenticationService) {
        if (!array_key_exists('acl', $config))
            throw new InvalidArgumentException('The ACL config is not defined');
        $this->acl = new Acl();
        $this->defaultRole = $authenticationService->getStorage()->read()->role ?? null;
        if (array_key_exists('roles', $config['acl']))
            $this->setRoles($config['acl']['roles']);
        if (array_key_exists('resources', $config['acl']))
            $this->setResources($config['acl']['resources']);
    }

    public function setRoles(array $roles) {
        foreach ($roles as $role) {
            $name = $role['name'];
            if (array_key_exists('use_as_default', $role) && $role['use_as_default'] && !$this->defaultRole)
                $this->defaultRole = $name;
            $parent = array_key_exists('parent_role', $role) ? $role['parent_role'] : null;
            $this->acl->addRole(new GenericRole($name), $parent);
        }
    }

    public function setResources(array $resources, $parent = null, $allow = null, $deny = null) {
        foreach ($resources as $config) {
            $resourceId = ltrim(join('/', [$parent, $config['route']]), '/');
            $resource = new GenericResource($resourceId);
            $allow = $config['allow'] ?? $allow;
            $deny = $config['deny'] ?? $deny;

            $this->acl->addResource($resource, $parent);
            $this->acl->allow($allow, $resource);
            $this->acl->deny($deny, $resource);

            if (array_key_exists('child_resources', $config) && is_array($config['child_resources'])) {
                $this->setResources($config['child_resources'], $resource, $allow, $deny);
            }
        }
    }

    /**
     * @param MvcEvent $mvcEvent
     * @return mixed
     */
    public function doAuthorization(MvcEvent $mvcEvent) {
        $resource = $mvcEvent->getRouteMatch()->getMatchedRouteName();
        $controller = $mvcEvent->getTarget();

        Navigation::setDefaultAcl($this->acl);
        Navigation::setDefaultRole($this->defaultRole);

        if (!$this->acl->hasResource($resource) ||
            !$this->acl->hasRole($this->defaultRole) ||
            !$this->acl->isAllowed($this->defaultRole, $resource)) {

            $uri = $mvcEvent->getApplication()->getRequest()->getUri();
            // Make the URL relative (remove scheme, user info, host name and port)
            // to avoid redirecting to other domain by a malicious user.
            $uri->setScheme(null)->setHost(null)->setPort(null)->setUserInfo(null);
            $redirectUrl = $uri->toString();

            // Redirect the user to the "Login" page.
            return $controller->redirect()->toRoute('login', [], [
                'query' => [
                    'redirectUrl' => $redirectUrl,
                ],
            ]);

            $viewModel = $mvcEvent->getViewModel()->setTemplate('layout/layout')->setTerminal(true);
            $viewModel->addChild((new ViewModel())->setTemplate('error/403'), 'content');
            $mvcEvent->getResponse()->setStatusCode(403);
            $mvcEvent->stopPropagation(true);
        }
    }

}