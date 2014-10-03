<?php

/**
 * Entity Service Aware
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Util;

/**
 * Entity Service Aware
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
trait EntityServiceAware
{
    /**
     * Wrapper method to get an entity service
     *
     * @param string $service
     * @return object
     */
    protected function getEntityService($service)
    {
        return $this->getServiceLocator()->get('EntityService')->get($service);
    }
}
