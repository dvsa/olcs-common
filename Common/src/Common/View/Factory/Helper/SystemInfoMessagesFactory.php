<?php

namespace Common\View\Factory\Helper;

use Common\View\Helper\SystemInfoMessages;
use Laminas\Mvc\Controller\ControllerManager;
use Laminas\Mvc\Controller\PluginManager;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Laminas\ServiceManager\ServiceManager;

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
    public function createService(ServiceLocatorInterface $sl)
    {
        /** @var ServiceManager $sm */
        $sm = $sl->getServiceLocator();

        /** @var \Dvsa\Olcs\Transfer\Util\Annotation\AnnotationBuilder $annotationBuilder */
        $annotationBuilder = $sm->get('TransferAnnotationBuilder');
        /** @var \Common\Service\Cqrs\Query\CachingQueryService $queryService */
        $queryService = $sm->get('QueryService');

        return new SystemInfoMessages($annotationBuilder, $queryService);
    }
}
