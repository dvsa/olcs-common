<?php

/**
 * Current User view helper
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */

namespace Common\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Current User view helper
 *
 * @todo The implementation of this class is a temporary fix
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class CurrentUser extends AbstractHelper implements ServiceLocatorAwareInterface
{
    protected $userData;

    /**
     * Get full name
     *
     * @return string
     */
    public function getFullName()
    {
        if (!$this->userData) {
            $authService = $this->getServiceLocator()->getServiceLocator()
                ->get(\Zend\Authentication\AuthenticationService::class);

            if (!$authService->getIdentity()) {
                return "Not logged in";
            }

            $this->userData = $authService->getIdentity()->getUserData();
        }
        $name = $this->userData['contactDetails']['person']['forename'] .' '.
            $this->userData['contactDetails']['person']['familyName'];
        if (empty(trim($name))) {
            $name = 'User ID '. $this->userData['id'];
        }

        return $name;
    }

    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * Get service locator
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }
}
