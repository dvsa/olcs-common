<?php

/**
 * Section Service Factory
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Section Service Factory
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class SectionServiceFactory implements FactoryInterface
{
    /**
     * Holds the service locator
     *
     * @var ServiceLocatorInterface
     */
    private $serviceLocator;

    /**
     * Cache the services
     *
     * @var array
     */
    private $sectionServices = array();

    /**
     * Create the section service factory
     *
     * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     * @return \Common\Controller\Service\SectionServiceFactory
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
        return $this;
    }

    /**
     * Get the instance of the section service
     *
     * @param string $serviceName
     * @return \Common\Controller\Service\SectionServiceInterface
     */
    public function getSectionService($serviceName)
    {
        if (!isset($this->sectionServices[$serviceName])) {
            $this->sectionServices[$serviceName] = $this->createSectionService($serviceName);
        }

        return $this->sectionServices[$serviceName];
    }

    /**
     * Create an brand new instance of the section service
     *
     * @param string $serviceName
     * @return \Common\Controller\Service\SectionServiceInterface
     */
    public function createSectionService($serviceName)
    {
        $className = __NAMESPACE__ . '\\' . $serviceName . 'SectionService';
        $service = new $className();
        $service->setServiceLocator($this->serviceLocator);
        $service->setSectionServiceFactory($this);

        return $service;
    }
}
