<?php

/**
 * File Uploader factory
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\File;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * File Uploader factory
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class FileUploaderFactory implements FactoryInterface
{
    /**
     * Holds the service locator
     * @var ServiceLocatorInterface
     */
    private $serviceLocator;

    /**
     * Get the instance of the factory
     *
     * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;

        return $this;
    }

    /**
     * Get an instance of uploader
     *
     * @param string $type
     */
    public function getUploader($type = null)
    {
        $config = $this->serviceLocator->get('Config');

        if (is_null($type)) {
            $type = $config['file_uploader']['default'];
        }

        $className = __NAMESPACE__ . '\\' . $type . 'FileUploader';

        $uploader = new $className();
        $uploader->setConfig($config['file_uploader']['config']);
        $uploader->setServiceLocator($this->serviceLocator);

        return $uploader;
    }
}
