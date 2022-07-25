<?php

namespace Common\Service\Traits;

use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * GenericFactoryCreateServiceTrait
 *
 * @author Jonathan Thomas
 */
trait GenericFactoryCreateServiceTrait
{
    /**
     * Create service method for Laminas v2 compatibility
     *
     * @param ServiceLocatorInterface $services
     *
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $services)
    {
        // see Laminas\ServiceManager\ServiceManager line 1091
        // additional arguments are passed into this method beyond those defined in the interface
        $args = func_get_args();
        $requestedName = $args[2];

        return $this($services, $requestedName);
    }
}
