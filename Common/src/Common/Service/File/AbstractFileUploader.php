<?php

/**
 * Abstract File Uploader
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\File;

use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Abstract File Uploader
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractFileUploader implements FileUploaderInterface
{
    /**
     * Holds the file
     *
     * @var File
     */
    protected $file;

    /**
     * Holds the config array
     *
     * @var array
     */
    protected $config;

    /**
     * Holds the service locator
     *
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * Setter for file
     *
     * @param mixed $file
     */
    public function setFile($file)
    {
        if (is_array($file)) {
            $file = $this->createFileFromData($file);
        }

        $this->file = $file;
    }

    /**
     * Getter for file
     *
     * @return File
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set the config
     *
     * @param array $config
     */
    public function setConfig(array $config = array())
    {
        $this->config = $config;
    }

    /**
     * Get the config
     *
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Set service locator
     *
     * @param ServiceLocatorInterface $serviceLocator
     */
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

    /**
     * Create a file object
     *
     * @param array $data
     */
    protected function createFileFromData(array $data = array())
    {
        $file = new File();
        $file->fromData($data);
        return $file;
    }
}
