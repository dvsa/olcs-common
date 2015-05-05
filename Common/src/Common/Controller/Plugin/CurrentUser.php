<?php

namespace Common\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Authentication\AuthenticationServiceInterface;

/**
 * Class CurrentUser
 * @package Common\Controller\Plugin
 */
final class CurrentUser extends AbstractPlugin implements CurrentUserInterface
{
    /**
     * @var AuthenticationServiceInterface
     */
    private $authService;

    /**
     * @param AuthenticationServiceInterface $authService
     */
    public function __construct(AuthenticationServiceInterface $authService)
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
