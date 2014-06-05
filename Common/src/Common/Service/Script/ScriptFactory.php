<?php

namespace Common\Service\Script;

use Zend\Form\Factory;

class ScriptFactory extends Factory
{
    protected $config = [];

    protected $tokens = [];

    public function __construct($config)
    {
        $this->config = $config;
    }

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

    public function loadFile($file)
    {
        if (!$this->exists($file)) {

            throw new \Exception('Attempted to load invalid script file "'. $file . '"');
        }

        $data = $this->load($file);

        return $this->replaceTokens($data, $this->tokens);
    }

    protected function exists($file)
    {
        return file_exists($this->getFilePath($file));
    }

    protected function load($file)
    {
        return file_get_contents($this->getFilePath($file));
    }

    protected function getFilePath($file)
    {
        return $this->config['local_scripts_path'] . $file . '.js';
    }

    protected function replaceTokens($content, $tokens)
    {
        // no-op at the moment
        return $content;
    }
}
