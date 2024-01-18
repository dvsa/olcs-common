<?php

namespace Common\Service\Qa;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

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
}
