<?php

namespace Common\Service\Api;

use Laminas\ServiceManager\AbstractPluginManager;

/**
 * Class Resolver
 * @package Common\Service\Api
 */
class Resolver extends AbstractPluginManager
{
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
