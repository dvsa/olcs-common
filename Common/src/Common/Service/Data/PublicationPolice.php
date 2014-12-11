<?php

/**
 * Publication Police service
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
namespace Common\Service\Data;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Common\Data\Object\PublicationPolice as PoliceObject;

/**
 * Publication Police service
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class PublicationPolice extends AbstractData implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * @var string
     */
    protected $serviceName = 'PublicationPoliceData';

    /**
     * @return \Common\Data\Object\PublicationPolice
     */
    public function createEmpty()
    {
        return new PoliceObject();
    }
}
