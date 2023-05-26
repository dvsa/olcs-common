<?php

namespace Common\Service\Table\Formatter;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class EbsrVariationNumberFactory implements FactoryInterface
{
    /**
     * @param  ContainerInterface $container
     * @param  $requestedName
     * @param  array|null         $options
     * @return EbsrVariationNumber
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $container = method_exists($container, 'getServiceLocator') ? $container->getServiceLocator() : $container;
        $viewHelperManager = $container->get('ViewHelperManager');
        $translator = $container->get('translator');
        return new EbsrVariationNumber($viewHelperManager, $translator);
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return EbsrVariationNumber
     */
    public function createService(ServiceLocatorInterface $serviceLocator): EbsrVariationNumber
    {
        return $this->__invoke($serviceLocator, EbsrVariationNumber::class);
    }
}
