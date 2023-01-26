<?php

namespace Common\Service\Cqrs\Query;

use Common\Service\Cqrs\RecoverHttpClientExceptionTrait;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Transfer\Query\FeatureToggle\IsEnabled as IsEnabledQry;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Transfer\Util\Annotation\AnnotationBuilder as TransferAnnotationBuilder;
use Interop\Container\ContainerInterface;

/**
 * Query Sender
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class QuerySender implements FactoryInterface
{
    use RecoverHttpClientExceptionTrait;

    /**
     * @var TransferAnnotationBuilder
     */
    private $annotationBuilder;

    /**
     * @var QueryService
     */
    private $queryService;

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator serviceLocator
     *
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator): QuerySender
    {
        return $this->__invoke($serviceLocator, QuerySender::class);
    }

    /**
     * Send
     *
     * @param QueryInterface $query Query
     *
     * @return \Common\Service\Cqrs\Response
     */
    public function send(QueryInterface $query)
    {
        $query = $this->annotationBuilder->createQuery($query);
        $this->queryService->setRecoverHttpClientException($this->getRecoverHttpClientException());
        return $this->queryService->send($query);
    }

    /**
     * @todo not the right place for this, need to think what's best, but seems like it might be ok for now to avoid duplication
     *
     * @param array $features
     *
     * @return bool
     */
    public function featuresEnabled(array $features)
    {
        return $this->send(IsEnabledQry::create(['ids' => $features]))->getResult()['isEnabled'];
    }

    /**
     * Grab the appropriate query service from the service locator
     *
     * @param ServiceLocatorInterface $serviceLocator serviceLocator
     *
     * @return QueryService
     */
    protected function getQueryService(ServiceLocatorInterface $serviceLocator)
    {
        return $serviceLocator->get('QueryService');
    }

    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return $this
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): QuerySender
    {
        $this->queryService = $this->getQueryService($container);
        $this->annotationBuilder = $container->get('TransferAnnotationBuilder');
        return $this;
    }
}
