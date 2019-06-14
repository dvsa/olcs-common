<?php

namespace Common\Service\Qa;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class TranslateableTextParameterHandlerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return TranslateableTextParameterHandler
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $handler = new TranslateableTextParameterHandler();

        $handler->registerFormatter(
            'currency',
            $serviceLocator->get('ViewHelperManager')->get('currencyFormatter')
        );

        return $handler;
    }
}
