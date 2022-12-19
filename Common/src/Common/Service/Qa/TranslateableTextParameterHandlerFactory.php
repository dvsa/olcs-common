<?php

namespace Common\Service\Qa;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class TranslateableTextParameterHandlerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): TranslateableTextParameterHandler
    {
        $handler = new TranslateableTextParameterHandler();

        $handler->registerFormatter(
            'currency',
            $container->get('ViewHelperManager')->get('currencyFormatter')
        );

        return $handler;
    }

    /**
     * @deprecated
     */
    public function createService(ServiceLocatorInterface $serviceLocator): TranslateableTextParameterHandler
    {
        return $this->__invoke($serviceLocator, TranslateableTextParameterHandler::class);
    }
}
