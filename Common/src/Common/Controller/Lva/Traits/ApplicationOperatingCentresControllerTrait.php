<?php

/**
 *
 */
namespace Common\Controller\Lva\Traits;

/**
 */
trait ApplicationOperatingCentresControllerTrait
{
    protected  function getTrafficArea($identifier = null)
    {
        if ($identifier === null) {
            $identifier = $this->getIdentifier();
        }
        return $this->getServiceLocator()
            ->get('Entity\TrafficArea')
            ->getTrafficArea($identifier);
    }

}
