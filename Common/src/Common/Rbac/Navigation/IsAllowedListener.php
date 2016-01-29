<?php

namespace Common\Rbac\Navigation;

use Zend\Navigation\Page\Mvc;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZfcRbac\Exception;
use ZfcRbac\Guard\GuardInterface;
use ZfcRbac\Service\AuthorizationServiceInterface;
use ZfcRbac\Guard\ProtectionPolicyTrait;
use Zend\EventManager\Event;

/**
 * Class IsAllowedListener
 * @package Common\Rbac\Navigation
 */
class IsAllowedListener implements FactoryInterface
{
    use ProtectionPolicyTrait;
    /**
     * @var AuthorizationServiceInterface
     */
    protected $authorizationService;
    /**
     * Route guard rules
     * Those rules are an associative array that map a rule with one or multiple permissions
     * @var array
     */
    protected $rules = [];

    /**
     * @param \ZfcRbac\Service\AuthorizationServiceInterface $authorizationService
     */
    public function setAuthorizationService($authorizationService)
    {
        $this->authorizationService = $authorizationService;
    }

    /**
     * @return \ZfcRbac\Service\AuthorizationServiceInterface
     */
    public function getAuthorizationService()
    {
        return $this->authorizationService;
    }

    /**
     * Set the rules (it overrides any existing rules)
     *
     * @param array $rules
     * @return void
     */
    public function setRules(array $rules)
    {
        $this->rules = [];
        foreach ($rules as $key => $value) {
            if (is_int($key)) {
                $routeRegex = $value;
                $permissions = [];
            } else {
                $routeRegex = $key;
                $permissions = (array) $value;
            }
            $this->rules[$routeRegex] = $permissions;
        }
    }

    /**
     * @param Mvc $page
     * @return bool
     */
    public function isGranted(Mvc $page)
    {
        $matchedRouteName = $page->getRoute();
        $allowedPermissions = null;
        foreach (array_keys($this->rules) as $routeRule) {
            if (fnmatch($routeRule, $matchedRouteName, FNM_CASEFOLD)) {
                $allowedPermissions = $this->rules[$routeRule];
                break;
            }
        }
        // If no rules apply, it is considered as granted or not based on the protection policy
        if (null === $allowedPermissions) {
            return $this->protectionPolicy === GuardInterface::POLICY_ALLOW;
        }
        if (in_array('*', $allowedPermissions)) {
            return true;
        }
        foreach ($allowedPermissions as $permission) {
            if (!$this->authorizationService->isGranted($permission)) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param Event $event
     * @return bool
     */
    public function accept(Event $event)
    {
        $event->stopPropagation();

        $accepted = true;

        $params = $event->getParams();
        /** @var \Zend\Navigation\Page\Mvc $page */
        $page = $params['page'];

        if ($page instanceof Mvc) {
            $accepted = $this->isGranted($page);
        }

        return $accepted;
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $this->setAuthorizationService($serviceLocator->get('ZfcRbac\Service\AuthorizationService'));

        $options = $serviceLocator->get('ZfcRbac\Options\ModuleOptions');

        $this->setProtectionPolicy($options->getProtectionPolicy());

        $guardsOptions = $options->getGuards();

        if (isset($guardsOptions['ZfcRbac\Guard\RoutePermissionsGuard'])) {
            $this->setRules($guardsOptions['ZfcRbac\Guard\RoutePermissionsGuard']);
        }

        return $this;
    }
}
