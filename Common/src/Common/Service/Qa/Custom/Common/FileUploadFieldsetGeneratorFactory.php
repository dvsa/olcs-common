<?php

namespace Common\Service\Qa\Custom\Common;

use Zend\Form\Factory as FormFactory;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

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
