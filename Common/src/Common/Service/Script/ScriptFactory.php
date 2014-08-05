<?php

/**
 * Inline JavaScript loading service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */

namespace Common\Service\Script;

use Zend\Form\Factory;

/**
 * Inline JavaScript loading service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class ScriptFactory extends Factory
{
    /**
     * Hold the application configuration
     *
     * @var array
     */
    protected $config = [];

    /**
     * Hold an array of tokens we'll search for and replace in the
     * loaded script file. This is currently not used but may be
     * in future
     */
    protected $tokens = [];

    /**
     * Constructor
     *
     * @param array $config - application configuration
     *
     * @return void
     */
    public function __construct($config)
    {
        $this->config = $config;
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
        // it'd be nicer to use array_map here, but throwing an
        // exception inside a closure causes more headaches than
        // it's worth
        $scripts = [];
        foreach ($files as $file) {
            $scripts[] = $this->loadFile($file);
        }
        return $scripts;
    }

    /**
     * load a single file
     *
     * @param string $file - the file to load
     * @throws \Exception
     *
     * @return string
     */
    public function loadFile($file)
    {
        foreach ($this->getFilePaths() as $path) {
            $fullPath = $path . $file . '.js';
            if ($this->exists($fullPath)) {
                $data = $this->load($fullPath);
                return $this->replaceTokens($data, $this->tokens);
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
     * get the available file system paths across all modules
     *
     * @return string
     */
    protected function getFilePaths()
    {
        return $this->config['local_scripts_path'];
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
}
