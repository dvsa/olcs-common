<?php

namespace Common\Form\Element;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
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
        /** @var \Zend\Form\FormElementManager $formElementManager */
        $serviceLocator = $formElementManager->getServiceLocator();

        $service = new DynamicMultiCheckbox();

        $service->setServiceLocator($serviceLocator);
        return $service;
    }
}
