<?php

namespace Common\Form\Element;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Common\Service\Data\RefData as RefDataService;

/**
 * Class DynamicMultiCheckboxFactory
 * @package Common\Form\Element
 */
class DynamicMultiCheckboxFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $formElementManager
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $formElementManager)
    {
        /** @var \Laminas\Form\FormElementManager $formElementManager */
        $serviceLocator = $formElementManager->getServiceLocator();

        $service = new DynamicMultiCheckbox();

        $service->setServiceLocator($serviceLocator->get('DataServiceManager'));
        return $service;
    }
}
