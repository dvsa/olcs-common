<?php

namespace Common\Service\Data;

use Laminas\ServiceManager\ServiceLocatorInterface;

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
     * Set application service
     *
     * @param \Common\Service\Data\Application $applicationService Application service
     *
     * @return $this
     */
    public function setApplicationService($applicationService)
    {
        $this->applicationService = $applicationService;

        return $this;
    }

    /**
     * Get application service
     *
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
        // fetch application with goods or psv details
        $application = $this->getApplicationService()->fetchApplicationData();

        return [
            'goodsOrPsv' => $application['goodsOrPsv']['id'],
            'isNi' => $application['niFlag']
        ];
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator Service locator
     *
     * @return $this
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $this->setApplicationService($serviceLocator->get('\Common\Service\Data\Application'));

        return $this;
    }
}
