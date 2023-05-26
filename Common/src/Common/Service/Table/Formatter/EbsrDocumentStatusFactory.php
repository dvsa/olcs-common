<?php

namespace Common\Service\Table\Formatter;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class EbsrDocumentStatusFactory implements FactoryInterface
{
    /**
     * @param  ContainerInterface $container
     * @param  $requestedName
     * @param  array|null         $options
     * @return EbsrDocumentStatus
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $container = method_exists($container, 'getServiceLocator') ? $container->getServiceLocator() : $container;
        $viewHelperManager = $container->get('ViewHelperManager');
        return new EbsrDocumentStatus($viewHelperManager);
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return EbsrDocumentStatus
     */
    public function createService(ServiceLocatorInterface $serviceLocator): EbsrDocumentStatus
    {
        return $this->__invoke($serviceLocator, EbsrDocumentStatus::class);
    }
}
