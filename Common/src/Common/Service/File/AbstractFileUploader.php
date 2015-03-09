<?php

/**
 * Abstract File Uploader
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\File;

use Zend\ServiceManager\ServiceLocatorInterface;

use Common\Exception\BadRequestException;

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

        return $this;
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

    public function populateFile()
    {
        $this->file->setContent(
            $this->readFile()
        );
        return $this;
    }

    /**
     * Create a file object
     *
     * @param array $data
     * @return \Common\Service\File\File
     */
    protected function createFileFromData(array $data = array())
    {
        $file = new File();
        $file->fromData($data);
        return $file;
    }

    /**
     * Generate a random sha
     *
     * @return string
     */
    protected function generateKey()
    {
        return str_replace(
            ['+', '/', '='],
            ['_', '-', ''],
            base64_encode(hash('sha256', openssl_random_pseudo_bytes(64), true))
        );
    }

    protected function getPath($identifier, $namespace = null)
    {
        if ($namespace === null) {
            $namespace = $this->getConfig()['location'];
        }

        return rtrim($namespace, '/') . '/' . $identifier;
    }

    /**
     * Builds a file upload path e.g. gb/publications/2015/03
     *
     * Will retrieve the list of variables needed for the required path and check they are all available.
     * If extra parameters are passed then they will be appended to the end of the URL
     *
     * @link https://wiki.i-env.net/pages/viewpage.action?spaceKey=olcs&title=Document+Repository+Layout
     * @param array $params
     * @param string $path
     * @throws BadRequestException
     * @return string
     */
    public function buildPathNamespace($params, $path = 'defaultPath')
    {
        $path = $this->getConfig()[$path];

        preg_match_all("/(\\[.*?\\])/is", $path, $matches);

        foreach ($matches[0] as $key => $match) {
            $matches[0][$key] = str_replace(['[', ']'], '', $match);
        }

        foreach ($matches[0] as $matchString) {
            if (!isset($params[$matchString])) {
                return new BadRequestException('Missing ' . $matchString . ' URL parameter');
            }

            $path = str_replace('[' . $matchString . ']', $params[$matchString], $path);
            unset($params[$matchString]);
        }

        //check if we have any additional params to be appended
        foreach ($params as $param) {
            $path .= '/' . $param;
        }

        return $path;
    }

    protected function readFile()
    {
        return file_get_contents($this->file->getPath());
    }
}
