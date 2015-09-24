<?php

/**
 * Version view helper
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\View\Helper;

use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\View\Helper\HelperInterface;
use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Version view helper
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Version extends AbstractHelper implements HelperInterface, ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    protected $markup = '<div class="version-header">
    <p class="environment">Environment: <span class="environment-marker">%s</span></p>
    <p class="version">Version: <span>%s</span></p>
</div>';

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
        $mainServiceLocator = $this->getServiceLocator()->getServiceLocator();

        $config = $mainServiceLocator->get('Config');

        if (!isset($config['version']) || !is_array($config['version'])) {
            return '';
        }

        $environment = $this->valOrAlt($config['version'], 'environment');
        $release = $this->valOrAlt($config['version'], 'release');

        return sprintf($this->markup, $environment, $release);
    }

    protected function valOrAlt($array, $index, $alt = 'unknown')
    {
        return (!empty($array[$index]) ? $array[$index] : $alt);
    }
}
