<?php

namespace Common\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use ZfcRbac\Service\AuthorizationServiceInterface;

/**
 * Class CurrentUser
 * @package Common\Controller\Plugin
 */
final class CurrentUser extends AbstractPlugin implements CurrentUserInterface
{
    /**
     * @var \ZfcRbac\Service\AuthorizationService
     */
    private $authService;

    /**
     * Constructor
     *
     * @param AuthorizationServiceInterface $authService Auth service
     */
    public function __construct(AuthorizationServiceInterface $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Get zf identity
     * 
     * @return \Common\Rbac\User
     */
    public function getIdentity()
    {
        return $this->authService->getIdentity();
    }

    /**
     * Get user data
     *
     * @return array
     */
    public function getUserData()
    {
        return $this->getIdentity()->getUserData();
    }
}
