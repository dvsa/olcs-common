<?php

namespace Common\Service\Document;

use RuntimeException;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class DocumentFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
        return $this;
    }

    public function getGenerator($mimeType)
    {
        switch ($mimeType) {
        case 'application/rtf':
        case 'application/x-rtf':
            return new RtfGenerator();
        default:
            throw new RuntimeException('No generator found for mime type: ' . $mimeType);
        }
    }
}
