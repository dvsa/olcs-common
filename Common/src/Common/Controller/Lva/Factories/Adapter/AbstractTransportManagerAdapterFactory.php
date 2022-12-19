<?php

namespace Common\Controller\Lva\Factories\Adapter;

use Interop\Container\ContainerInterface;
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

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /** @var \Dvsa\Olcs\Transfer\Util\Annotation\AnnotationBuilder $transferAnnotationBuilder */
        $transferAnnotationBuilder = $container->get('TransferAnnotationBuilder');
        /** @var \Common\Service\Cqrs\Query\CachingQueryService $querySrv */
        $querySrv = $container->get('QueryService');
        /** @var \Common\Service\Cqrs\Command\CommandService $commandSrv */
        $commandSrv = $container->get('CommandService');

        return new $this->adapterClass($transferAnnotationBuilder, $querySrv, $commandSrv);
    }

    public function createService(ServiceLocatorInterface $sl)
    {
        return $this->__invoke($sl, null);
    }
}
