<?php

namespace Common\Service\Api;

use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\Exception;

class Resolver extends AbstractPluginManager
{
    /**
     * Validate the plugin
     *
     * Checks that the filter loaded is either a valid callback or an instance
     * of FilterInterface.
     *
     * @param  mixed $plugin
     * @return void
     * @throws Exception\RuntimeException if invalid
     */
    public function validatePlugin($plugin)
    {
    }

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