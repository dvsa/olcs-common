<?php

/**
 * Version view helper
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Common\View\Helper;

use Zend\View\Helper\HelperInterface;
use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
/**
 * Version view helper
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Version extends AbstractHelper implements HelperInterface, ServiceLocatorAwareInterface
{

    /**
     * Render the version
     *
     * @return string
     */
    public function __invoke()
    {
        return $this->render();
    }

    /**
     * Render the version
     *
     * @return string
     */
    public function render()
    {
        $config = $this->getServiceLocator()->getServiceLocator()->get('Config');

        return (isset($config['version']) && !empty($config['version']) ? 'V' . $config['version'] : '');
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
    }
}
