<?php

/**
 * Helper Service Aware
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Util;

/**
 * Helper Service Aware
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
trait HelperServiceAware
{
    /**
     * Get a helper service
     *
     * @param string $service
     * @return type
     */
    protected function getHelperService($service)
    {
        return $this->getServiceLocator()->get('HelperService')->getHelperService($service);
    }
}
