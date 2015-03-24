<?php

/**
 * Application Service Trait
 */
namespace Common\Service\Data;

use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Application Service Trait
 */
trait ApplicationServiceTrait
{
    /**
     * @var \Common\Service\Data\Application
     */
    protected $applicationService;

    /**
     * @param \Common\Service\Data\Application $applicationService
     * @return $this
     */
    public function setApplicationService($applicationService)
    {
        $this->applicationService = $applicationService;
        return $this;
    }

    /**
     * @return \Common\Service\Data\Application
     */
    public function getApplicationService()
    {
        return $this->applicationService;
    }

    /**
     * Get Application Goods/Psv information
     *
     * @return array
     */
    protected function getApplicationContext()
    {
        $application = $this->getApplicationService()->fetchApplicationData();

        return [
            'goodsOrPsv' => $application['goodsOrPsv']['id']
        ];
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $this->setApplicationService($serviceLocator->get('\Common\Service\Data\Application'));

        return $this;
    }
}
