<?php

namespace Common\Service\Api;

use Dvsa\Olcs\Utils\Traits\PluginManagerTrait;
use Laminas\ServiceManager\AbstractPluginManager;

/**
 * Class Resolver
 * @package Common\Service\Api
 */
class Resolver extends AbstractPluginManager
{
    use PluginManagerTrait;

    protected $instanceOf = null;

    /**
     * @deprecated
     * @param $api
     * @return object
     */
    public function getClient($api)
    {
        return $this->get('Olcs\\RestService\\' . $api);
    }
}
