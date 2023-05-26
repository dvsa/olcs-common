<?php

namespace Common\Service\Table\Formatter;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class IssuedPermitLicencePermitReferenceFactory implements FactoryInterface
{
    /**
     * @param  ContainerInterface $container
     * @param  $requestedName
     * @param  array|null         $options
     * @return IssuedPermitLicencePermitReference
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $container = method_exists($container, 'getServiceLocator') ? $container->getServiceLocator() : $container;
        $urlHelper = $container->get('Helper\Url');
        return new IssuedPermitLicencePermitReference($urlHelper);
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return IssuedPermitLicencePermitReference
     */
    public function createService(ServiceLocatorInterface $serviceLocator): IssuedPermitLicencePermitReference
    {
        return $this->__invoke($serviceLocator, IssuedPermitLicencePermitReference::class);
    }
}
