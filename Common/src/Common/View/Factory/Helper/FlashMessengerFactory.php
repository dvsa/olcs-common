<?php

namespace Common\View\Factory\Helper;

use Common\View\Helper\FlashMessenger;
use Common\Service\Helper\FlashMessengerHelperService;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Laminas\ServiceManager\ServiceManager;

/**
 * @see Common\View\Helper\FlashMessenger
 */
class FlashMessengerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): FlashMessenger
    {
        if (method_exists($container, 'getServiceLocator') && $container->getServiceLocator()) {
            $container = $container->getServiceLocator();
        }

        /** @var FlashMessengerHelperService $queryService */
        $flashMessengerHelperService = $container->get('Helper\FlashMessenger');

        $flashMessenger = new FlashMessenger($flashMessengerHelperService);

        $flashMessenger->setPluginFlashMessenger($container->get('ControllerPluginManager')->get('FlashMessenger'));

        return $flashMessenger;
    }

    /**
     * @deprecated
     */
    public function createService(ServiceLocatorInterface $sl): FlashMessenger
    {
        return $this->__invoke($sl, FlashMessenger::class);
    }
}
