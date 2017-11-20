<?php

namespace Common\Service\Cqrs\Query;

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
     * @param bool           $recoverHttpClientException
     *
     * @return \Common\Service\Cqrs\Response
     */
    public function send(QueryInterface $query, $recoverHttpClientException = false)
    {
        $query = $this->annotationBuilder->createQuery($query);
        return $this->queryService->send($query,  $recoverHttpClientException);
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
