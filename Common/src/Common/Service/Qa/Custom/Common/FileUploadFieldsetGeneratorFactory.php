<?php

namespace Common\Service\Qa\Custom\Common;

use Interop\Container\ContainerInterface;
use Laminas\Form\Factory as FormFactory;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class FileUploadFieldsetGeneratorFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): FileUploadFieldsetGenerator
    {
        return new FileUploadFieldsetGenerator(
            new FormFactory(),
            $container->get('FormAnnotationBuilder')
        );
    }

    /**
     * @deprecated
     */
    public function createService(ServiceLocatorInterface $serviceLocator): FileUploadFieldsetGenerator
    {
        return $this->__invoke($serviceLocator, FileUploadFieldsetGenerator::class);
    }
}
