<?php

namespace Common\Service\Qa\DataTransformer;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class DataTransformerProviderFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): DataTransformerProvider
    {
        $dataTransformerProvider = new DataTransformerProvider();

        $dataTransformerProvider->registerTransformer(
            'number-of-permits-either',
            $container->get('QaEcmtNoOfPermitsSingleDataTransformer')
        );

        $dataTransformerProvider->registerTransformer(
            'number-of-permits-both',
            $container->get('QaEcmtNoOfPermitsSingleDataTransformer')
        );

        return $dataTransformerProvider;
    }
}
