<?php

namespace Common\Service\Cqrs\Query;

use Common\Service\Cqrs\RecoverHttpClientExceptionTrait;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Dvsa\Olcs\Transfer\Query\FeatureToggle\IsEnabled as IsEnabledQry;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Transfer\Util\Annotation\AnnotationBuilder as TransferAnnotationBuilder;
use Interop\Container\ContainerInterface;

class QuerySender implements FactoryInterface
{
    use RecoverHttpClientExceptionTrait;

    /**
     * @var TransferAnnotationBuilder
     */
    private $annotationBuilder;
    private QueryService $queryService;

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

    protected function getQueryService(ContainerInterface $serviceLocator): QueryService
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
