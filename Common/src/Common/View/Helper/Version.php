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

    /**
     * Create service instance
     *
     *
     * @return Version
     */
    public function __construct(private array $config)
    {
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

    /**
     * @psalm-param 'description'|'environment'|'release' $index
     * @psalm-param 'NA'|'unknown' $alt
     */
    protected function valOrAlt(array $array, string $index, string $alt = 'unknown')
    {
        return (empty($array[$index]) ? $alt : $array[$index]);
    }
}
