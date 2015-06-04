<?php

namespace Common\Controller\Plugin;

use Common\Service\Cqrs\Query\QueryService;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Dvsa\Olcs\Transfer\Util\Annotation\AnnotationBuilder as TransferAnnotationBuilder;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Class HandleQuery
 * @package Common\Controller\Plugin
 */
class HandleQuery extends AbstractPlugin
{
    /**
     * @var TransferAnnotationBuilder
     */
    private $annotationBuilder;

    /**
     * @var QueryService
     */
    private $queryService;

    /**
     * @param TransferAnnotationBuilder $annotationBuilder
     * @param QueryService $queryService
     */
    public function __construct(TransferAnnotationBuilder $annotationBuilder, QueryService $queryService)
    {
        $this->queryService = $queryService;
        $this->annotationBuilder = $annotationBuilder;
    }

    /**
     * @param QueryInterface $query
     * @return \Common\Service\Cqrs\Response
     */
    public function __invoke(QueryInterface $query)
    {
        $query = $this->annotationBuilder->createQuery($query);
        return $this->queryService->send($query);
    }
}
