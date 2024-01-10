<?php

namespace Common\Service\Data;

use Laminas\ServiceManager\AbstractPluginManager;

/**
 * Class PluginManager
 * @package Common\Service\Data
 */
class PluginManager extends AbstractPluginManager
{
    protected $instanceOf = null;

    /**
     * @inheritdoc
     */
    public function __construct($configOrContainerInstance = null, array $v3config = [])
    {
        parent::__construct($configOrContainerInstance, $v3config);

        $this->addInitializer(
            new RestClientAwareInitializer()
        );
    }
}
