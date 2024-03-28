<?php

namespace Common\View\Helper;

use Laminas\View\Helper\AbstractHelper;

/**
 * Class return Config to view
 */
class Config extends AbstractHelper
{
    /** @var array */
    private $config;

    /**
     * Create service instance
     *
     *
     * @return Config
     */
    public function __construct(
        array $config
    ) {
        $this->config = $config;
    }

    /**
     * Return config
     *
     * @return array
     */
    public function __invoke()
    {
        return $this->config;
    }
}
