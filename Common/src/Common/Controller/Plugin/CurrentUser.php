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
     * @var AuthorizationServiceInterface
     */
    private $authService;

    /**
     * @param AuthorizationServiceInterface $authService
     */
    public function __construct(AuthorizationServiceInterface $authService)
    {
        $this->authService = $authService;
    }

    /**
     * @return mixed|null
     */
    private function getIdentity()
    {
        return $this->authService->getIdentity();
    }

    /**
     * @return array
     */
    public function getUserData()
    {
        return $this->getIdentity()->getUserData();
    }
}
