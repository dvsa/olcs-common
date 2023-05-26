<?php

namespace Common\Service\Table\Formatter;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class YesNoFactory implements FactoryInterface
{
    /**
     * @param  ContainerInterface $container
     * @param  $requestedName
     * @param  array|null         $options
     * @return YesNo
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $container = method_exists($container, 'getServiceLocator') ? $container->getServiceLocator() : $container;
        $stackHelper = $container->get('Helper\Stack');
        $translator = $container->get('translator');
        return new YesNo($stackHelper, $translator);
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return YesNo
     */
    public function createService(ServiceLocatorInterface $serviceLocator): YesNo
    {
        return $this->__invoke($serviceLocator, YesNo::class);
    }
}
