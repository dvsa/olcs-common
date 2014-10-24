<?php

/**
 *
 */
namespace Common\Controller\Lva\Traits;

/**
 */
trait LicenceOperatingCentresControllerTrait
{
    protected function getTrafficArea($identifier = null)
    {
        if ($identifier === null) {
            $identifier = $this->getIdentifier();
        }
        return $this->getServiceLocator()
            ->get('Entity\LicenceTrafficArea')
            ->getTrafficArea($identifier);
    }
}
