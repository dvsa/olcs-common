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
        $data = $this->getData();

        $name = $data['contactDetails']['person']['forename'] .' '.
            $data['contactDetails']['person']['familyName'];
        if (empty(trim($name))) {
            $name = 'User ID '. $this->userData['id'];
        }

        return $this->getView()->escapeHtml($name);
    }

    /**
     * Get Organisation name
     *
     * @return string
     */
    public function getOrganisationName()
    {
        $data = $this->getData();

        if (isset($data['organisationUsers']) && is_array($data['organisationUsers'])) {
            // use array_shift, as the first organisationUser is not always index 0
            $organisationUser = array_shift($data['organisationUsers']);
            $name = $organisationUser['organisation']['name'];
            if (empty(trim($name))) {
                $name = 'Organisation ID '.$organisationUser['organisation']['id'];
            }
        } else {
            $name = 'NO ORGANISATION';
        }

        return $this->getView()->escapeHtml($name);
    }

    /**
     * Get current user data
     *
     * @return array
     */
    private function getData()
    {
        if (!$this->userData) {
            $authService = $this->getServiceLocator()->getServiceLocator()
                ->get(\Zend\Authentication\AuthenticationService::class);

            if (!$authService->getIdentity()) {
                return "Not logged in";
            }

            $this->userData = $authService->getIdentity()->getUserData();
        }

        return $this->userData;
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
