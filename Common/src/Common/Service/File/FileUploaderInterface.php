<?php

/**
 * File Upload Interface
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\File;

use Zend\ServiceManager\ServiceLocatorAwareInterface;

/**
 * File Upload Interface
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
interface FileUploaderInterface extends ServiceLocatorAwareInterface
{
    /**
     * Set the config
     *
     * @param array $config
     */
    public function setConfig(array $config = array());

    /**
     * Get the config
     *
     * @return array
     */
    public function getConfig();

    /**
     * Set the file
     *
     * @param array $file
     */
    public function setFile($file);

    /**
     * Get the file
     *
     * @return File
     */
    public function getFile();

    /**
     * Process the file upload
     */
    public function upload($namespace = null);

    /**
     * Process the file download
     */
    public function download($identifier, $name, $namespace = null);

    /**
     * Process the file removal
     */
    public function remove($identifier, $namespace = null);
}
