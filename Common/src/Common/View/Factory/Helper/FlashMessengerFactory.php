<?php

namespace Common\View\Factory\Helper;

use Common\View\Helper\FlashMessenger;
use Common\Service\Helper\FlashMessengerHelperService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceManager;

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
