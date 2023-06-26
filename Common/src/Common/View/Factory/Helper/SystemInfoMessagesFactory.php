<?php

namespace Common\View\Factory\Helper;

use Common\View\Helper\SystemInfoMessages;
use Laminas\Mvc\Controller\ControllerManager;
use Laminas\Mvc\Controller\PluginManager;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Laminas\ServiceManager\ServiceManager;
use Interop\Container\ContainerInterface;

/**
 * Factory for @see Common\View\Helper\SystemInfoMessages
 */
class SystemInfoMessagesFactory implements FactoryInterface
{
    /**
     * @param PluginManager $sl
     *
     * @return SystemInfoMessages
     */
    public function createService(ServiceLocatorInterface $sl): SystemInfoMessages
    {
        return $this->__invoke($sl, SystemInfoMessages::class);
    }

    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return SystemInfoMessages
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): SystemInfoMessages
    {
        if (method_exists($container, 'getServiceLocator') && $container->getServiceLocator()) {
            $container = $container->getServiceLocator();
        }

        /** @var \Dvsa\Olcs\Transfer\Util\Annotation\AnnotationBuilder $annotationBuilder */
        $annotationBuilder = $container->get('TransferAnnotationBuilder');
        /** @var \Common\Service\Cqrs\Query\CachingQueryService $queryService */
        $queryService = $container->get('QueryService');
        return new SystemInfoMessages($annotationBuilder, $queryService);
    }
}
