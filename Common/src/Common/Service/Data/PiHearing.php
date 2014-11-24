<?php

/**
 * Pi Hearing service
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
namespace Common\Service\Data;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Common\Util\RestClient;

/**
 * Pi Hearing service
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class PiHearing extends AbstractData implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    protected $serviceName = 'PiHearing';

    public function fetchPiHearingData($params)
    {
        return $this->getRestClient()->get($params);
    }
}
