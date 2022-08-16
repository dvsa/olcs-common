<?php

/**
 * ApplicationName view helper
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Common\View\Helper;

use Laminas\View\Helper\AbstractHelper;

/**
 * ApplicationName view helper
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationName extends AbstractHelper
{
    /** @var array */
    private $config;

    /**
     * Create service instance
     *
     * @param array $config
     *
     * @return ApplicationName
     */
    public function __construct(
        array $config
    ) {
        $this->config = $config;
    }

    /**
     * Render the ApplicationName
     *
     * @return string
     */
    public function __invoke()
    {
        return $this->render();
    }

    /**
     * Render the ApplicationName
     *
     * @return string
     */
    public function render()
    {
        return !empty($this->config['application-name']) ? $this->config['application-name'] : '';
    }
}
