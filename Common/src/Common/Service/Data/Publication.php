<?php

/**
 * Publication service
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
namespace Common\Service\Data;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Publication service
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class PublicationService extends AbstractData implements ServiceLocatorAwareInterface
{
    use Common\Util\RestCallTrait;

    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    protected $serviceName = 'Publication';

    public function createPublication($type, $data = [])
    {

    }

    public function buildPublication($type, $data = [])
    {

    }

    public function getPublicationId($pubType = 'A&D', $trafficArea = 'B', $status = 'pub_s_new')
    {
        $params = [
            'pubType' => $pubType,
            'trafficArea' => $trafficArea,
            'pubStatus' => $status
        ];

        $data = $this->getRestClient()->get('', $params);

        print_r($data);
        die();
    }

    /**
     * Set service locator
     *
     * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * Get service locator
     *
     * @return \Zend\ServiceManager\ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }
}
