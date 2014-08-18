<?php

namespace Common\Form\Element;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Common\Service\RefData as RefDataService;

/**
 * Class DynamicSelectFactory
 * @package Common\Form\Element
 */
class DynamicSelectFactory implements FactoryInterface
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
        /** @var RefDataService $refDataService */
        $refDataService = $serviceLocator->get('Common\Service\RefData');

        $service = new DynamicSelect();

        $service->setRefDataService($refDataService);
        return $service;
    }
}
