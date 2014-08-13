<?php

/**
 * Asset path view helper
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\View\Helper;

use Zend\View\Helper\HelperInterface;
use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Asset path view helper
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class AssetPath extends AbstractHelper implements HelperInterface, ServiceLocatorAwareInterface
{

    /**
     * Render base asset path
     *
     * @return string
     */
    public function __invoke($path = null)
    {
        $config = $this->getServiceLocator()->getServiceLocator()->get('Config');

        $base = isset($config['asset_path']) && !empty($config['asset_path']) ? $config['asset_path'] : '';

        return $base . $this->getView()->basePath($path);
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
