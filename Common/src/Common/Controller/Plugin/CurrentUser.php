<?php

namespace Common\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Authentication\AuthenticationServiceInterface;

final class CurrentUser extends AbstractPlugin implements CurrentUserInterface
{
    /**
     * @var AuthenticationServiceInterface
     */
    private $authService;

    public function __construct(AuthenticationServiceInterface $authService)
    {
        $this->authService = $authService;
    }

    private function getIdentity()
    {
        return $this->authService->getIdentity();
    }

    public function getUserData()
    {
        return $this->getIdentity()->getUserData();
    }
}
