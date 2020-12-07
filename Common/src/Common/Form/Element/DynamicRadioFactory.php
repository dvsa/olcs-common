<?php

namespace Common\Form\Element;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Class DynamicRadioFactory
 * @package Common\Form\Element
 */
class DynamicRadioFactory implements FactoryInterface
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

        $service = new DynamicRadio();

        $service->setServiceLocator($serviceLocator->get('DataServiceManager'));
        return $service;
    }
}
