<?php

/**
 * Publication service
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
namespace Common\Service\Data;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Common\Util\RestClient;

/**
 * Publication service
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class Publication extends AbstractData implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    protected $serviceName = 'Publication';

    public function fetchPublicationData($params)
    {
        return $this->getRestClient()->get($params);
    }
}
