<?php

/**
 * Business Service Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\BusinessService;

/**
 * Business Service Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
trait BusinessServiceAwareTrait
{
    protected $businessServiceManager;

    public function setBusinessServiceManager(BusinessServiceManager $businessServiceManager)
    {
        $this->businessServiceManager = $businessServiceManager;
    }

    public function getBusinessServiceManager()
    {
        return $this->businessServiceManager;
    }
}
