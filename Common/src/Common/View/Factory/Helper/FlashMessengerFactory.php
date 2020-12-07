<?php

namespace Common\View\Factory\Helper;

use Common\View\Helper\FlashMessenger;
use Common\Service\Helper\FlashMessengerHelperService;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Laminas\ServiceManager\ServiceManager;

/**
 * Factory for @see Common\View\Helper\FlashMessenger
 */
class FlashMessengerFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $sl
     *
     * @return FlashMessenger
     */
    public function createService(ServiceLocatorInterface $sl)
    {
        /** @var ServiceManager $sm */
        $sm = $sl->getServiceLocator();

        /** @var FlashMessengerHelperService $queryService */
        $flashMessengerHelperService = $sm->get('Helper\FlashMessenger');

        return new FlashMessenger($flashMessengerHelperService);
    }
}
