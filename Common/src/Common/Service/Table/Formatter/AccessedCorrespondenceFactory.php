<?php

namespace Common\Service\Table\Formatter;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class AccessedCorrespondenceFactory implements FactoryInterface
{
    /**
     * @param  ContainerInterface $container
     * @param  $requestedName
     * @param  array|null         $options
     * @return AccessedCorrespondence
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $container = method_exists($container, 'getServiceLocator') ? $container->getServiceLocator() : $container;
        $translator = $container->get('translator');
        $urlHelper = $container->get('Helper\Url');
        return new AccessedCorrespondence($urlHelper, $translator);
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return AccessedCorrespondence
     */
    public function createService(ServiceLocatorInterface $serviceLocator): AccessedCorrespondence
    {
        return $this->__invoke($serviceLocator, AccessedCorrespondence::class);
    }
}
