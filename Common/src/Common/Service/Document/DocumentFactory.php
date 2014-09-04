<?php

namespace Common\Service\Document;

use RuntimeException;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class DocumentFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $contentStore = $serviceLocator->get('ContentStore');

        $service = new Document();
        $service->setContentStore($contentStore);

        return $service;
    }
}
