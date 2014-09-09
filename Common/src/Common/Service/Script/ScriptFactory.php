<?php

/**
 * Inline JavaScript loading service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */

namespace Common\Service\Script;

use Zend\ServiceManager\FactoryInterface;
use \Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Inline JavaScript loading service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class ScriptFactory implements FactoryInterface
{
    /**
     * Hold the application configuration
     *
     * @var array
     */
    protected $filePaths = [];

    /**
     * Hold an array of tokens we'll search for and replace in the
     * loaded script file. This is currently not used but may be
     * in future
     */
    protected $tokens = [];

    /**
     * Contains the view helper manager! :)
     *
     * @var unknown
     */
    protected $viewHelperManager = null;


    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $this->setViewHelperManager($serviceLocator->get('ViewHelperManager'));

        $config = $serviceLocator->get('Config');

        if (!isset($config['local_scripts_path'])) {
            throw new \LogicException('local_scripts_path was not set in the module config');
        }
        $this->setFilePaths($config['local_scripts_path']);

        return $this;
    }

    /**
     * load an array of files
     *
     * @param array $files - the files to load
     *
     * @return array
     */
    public function loadFiles($files = [])
    {
        foreach ($files as $file) {
            $this->getViewHelperManager()->get('inlineScript')->appendScript($this->loadFile($file));
        }
        return $this;
    }

    /**
     * load a single file, will check multiple paths depending on the number of available modules
     *
     * @param string $file - the file to load
     * @throws \Exception
     *
     * @return string
     */
    public function loadFile($file)
    {
        $paths = $this->getFilePaths();

        if (is_array($paths)) {
            foreach ($this->getFilePaths() as $path) {
                $fullPath = $path . $file . '.js';
                if ($this->exists($fullPath)) {
                    $data = $this->load($fullPath);
                    return $this->replaceTokens($data, $this->tokens);
                }
            }
        }

        throw new \Exception('Attempted to load invalid script file "'. $file . '"');
    }

    /**
     * check to see if a file exists
     *
     * @param string file - the file to check
     *
     * @return bool
     */
    protected function exists($file)
    {
        return file_exists($file);
    }

    /**
     * load the data from a file
     *
     * @param string $file - the file to load
     *
     * @return string
     */
    protected function load($file)
    {
        return file_get_contents($file);
    }

    /**
     * replace any {{tokens}} found in the content string
     * currently a no-op identity method; may be used in future
     *
     * @param string $content - the string of content to search through
     * @param array  $tokens  - the array of tokens to search for and replace
     *
     * @return string
     */
    protected function replaceTokens($content, $tokens)
    {
        // no-op at the moment
        return $content;
    }

    /**
     * get the available file system paths across all modules
     *
     * @return string
     */
    protected function getFilePaths()
    {
        return $this->filePaths;
    }

    public function setFilePaths(array $filePaths)
    {
        $this->filePaths = $filePaths;
        return $this;
    }

    public function getViewHelperManager()
    {
        return $this->viewHelperManager;
    }

    public function setViewHelperManager(\Zend\View\HelperPluginManager $viewHelperManager)
    {
        $this->viewHelperManager = $viewHelperManager;
        return $this;
    }

}
