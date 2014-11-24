<?php

namespace Common\Form\Element;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Common\Service\Data\RefData as RefDataService;

/**
 * Class DynamicMultiSelectFactory
 * @package Common\Form\Element
 */
class DynamicMultiSelectFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $formElementManager
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $formElementManager)
    {
        $serviceLocator = $formElementManager->getServiceLocator();

        $service = new DynamicMultiSelect();

        $service->setServiceLocator($serviceLocator->get('DataServiceManager'));
        return $service;
    }
}
