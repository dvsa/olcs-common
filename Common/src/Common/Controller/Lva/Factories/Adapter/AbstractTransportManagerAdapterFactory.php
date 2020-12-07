<?php

namespace Common\Controller\Lva\Factories\Adapter;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Abstract Factory to create Transport Manager Adapters
 *
 * @author Dmitry Golubev <dmitrij.golubev@valtech.co.uk>
 */
abstract class AbstractTransportManagerAdapterFactory implements FactoryInterface
{
    /** @var \Common\Controller\Lva\Adapters\AbstractTransportManagerAdapter */
    protected $adapterClass;

    public function createService(ServiceLocatorInterface $sl)
    {
        /** @var \Dvsa\Olcs\Transfer\Util\Annotation\AnnotationBuilder $transferAnnotationBuilder */
        $transferAnnotationBuilder = $sl->get('TransferAnnotationBuilder');
        /** @var \Common\Service\Cqrs\Query\CachingQueryService $querySrv */
        $querySrv = $sl->get('QueryService');
        /** @var \Common\Service\Cqrs\Command\CommandService $commandSrv */
        $commandSrv = $sl->get('CommandService');

        return new $this->adapterClass($transferAnnotationBuilder, $querySrv, $commandSrv);
    }
}
