<?php

namespace Common\Service\Cqrs\Query;


use Common\Service\Cqrs\RecoverHttpClientExceptionTrait;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Transfer\Util\Annotation\AnnotationBuilder as TransferAnnotationBuilder;

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

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $this->queryService = $this->getQueryService($serviceLocator);
        $this->annotationBuilder = $serviceLocator->get('TransferAnnotationBuilder');

        return $this;
    }

    /**
     * @param QueryInterface $query
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
     * Grab the appropriate query service from the service locator
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return QueryService
     */
    protected function getQueryService(ServiceLocatorInterface $serviceLocator)
    {
        return $serviceLocator->get('QueryService');
    }
}
