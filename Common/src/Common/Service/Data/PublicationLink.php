<?php

/**
 * Publication Link service
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
namespace Common\Service\Data;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\Stdlib\ArrayObject;
use Common\Util\RestClient;
use Common\Data\Object\Publication as PublicationObject;

/**
 * Publication Link service
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class PublicationLink extends AbstractData implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    protected $serviceName = 'PublicationLink';

    public function createEmpty()
    {
        return new ArrayObject(new PublicationObject());
    }

    public function save(ArrayObject $publication)
    {
        return $this->getServiceLocator()->get('Helper\Rest')->makeRestCall(
            'PublicationLink',
            'POST',
            $publication->getArrayCopy()
        );
    }

    public function createPublicationLink(ArrayObject $publication, $service)
    {
        $publication = $this->getServiceLocator()->get($service)->filter($publication);
        return $this->save($publication);
    }

    public function fetchPublicationLinkData($params)
    {
        return $this->getRestClient()->get($params);
    }
}