<?php

/**
 * Business Service Interface
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\BusinessService;

/**
 * Business Service Interface
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
interface BusinessServiceAwareInterface
{
    public function setBusinessServiceManager(BusinessServiceManager $businessServiceManager);

    public function getBusinessServiceManager();
}
