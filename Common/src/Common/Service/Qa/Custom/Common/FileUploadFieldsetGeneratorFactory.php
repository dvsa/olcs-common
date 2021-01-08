<?php

namespace Common\Service\Qa\Custom\Common;

use Laminas\Form\Factory as FormFactory;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class FileUploadFieldsetGeneratorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return FileUploadFieldsetGenerator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new FileUploadFieldsetGenerator(
            new FormFactory(),
            $serviceLocator->get('FormAnnotationBuilder')
        );
    }
}
