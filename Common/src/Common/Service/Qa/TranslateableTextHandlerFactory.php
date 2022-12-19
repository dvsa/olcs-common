<?php

namespace Common\Service\Qa;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class TranslateableTextHandlerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): TranslateableTextHandler
    {
        return new TranslateableTextHandler(
            $container->get('QaFormattedTranslateableTextParametersGenerator'),
            $container->get('Helper\Translation')
        );
    }

    /**
     * @deprecated
     */
    public function createService(ServiceLocatorInterface $serviceLocator): TranslateableTextHandler
    {
        return $this->__invoke($serviceLocator, TranslateableTextHandler::class);
    }
}
