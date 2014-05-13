<?php

/**
 * ApplicationName view helper
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Common\View\Helper;

use Zend\View\Helper\HelperInterface;
use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * ApplicationName view helper
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationName extends AbstractHelper implements HelperInterface, ServiceLocatorAwareInterface
{

    /**
     * Render the ApplicationName
     *
     * @return string
     */
    public function __invoke()
    {
        return $this->render();
    }

    /**
     * Render the ApplicationName
     *
     * @return string
     */
    public function render()
    {
        $config = $this->getServiceLocator()->getServiceLocator()->get('Config');

        return (isset($config['application-name']) && !empty($config['application-name'])
            ? $config['application-name'] : '');
    }

    /**
     * Getter for service locator
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    /**
     * Setter for service locator
     *
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
        return $this;
    }
}
