<?php

/**
 * Version view helper
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\View\Helper;

use Laminas\View\Helper\AbstractHelper;

/**
 * Version view helper
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Version extends AbstractHelper
{
    protected $markup = '<div class="version-header">
    <p class="environment">Environment: <span class="environment-marker">%s</span></p>
    <p class="version">PHP: <span>%s</span></p>
    <p class="version">Description: <span>%s</span></p>
    <p class="version">Version: <span>%s</span></p>
</div>';

    /** @var array */
    private $config;

    /**
     * Create service instance
     *
     * @param array $config
     *
     * @return Version
     */
    public function __construct(
        array $config
    ) {
        $this->config = $config;
    }

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
        if (!isset($this->config['version']) || !is_array($this->config['version'])) {
            return '';
        }

        $environment = $this->valOrAlt($this->config['version'], 'environment');
        $description = $this->valOrAlt($this->config['version'], 'description', 'NA');
        $release = $this->valOrAlt($this->config['version'], 'release');

        return sprintf($this->markup, $environment, phpversion(), $description, $release);
    }

    protected function valOrAlt($array, $index, $alt = 'unknown')
    {
        return (!empty($array[$index]) ? $array[$index] : $alt);
    }
}
