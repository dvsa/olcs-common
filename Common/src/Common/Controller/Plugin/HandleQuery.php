<?php

namespace Common\Controller\Plugin;

use Common\Service\Cqrs\Query\QueryServiceInterface;
use Laminas\Mvc\Controller\Plugin\AbstractPlugin;
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
     * @var QueryServiceInterface
     */
    private $queryService;

    /**
     * @param TransferAnnotationBuilder $annotationBuilder
     * @param QueryServiceInterface $queryService
     */
    public function __construct(TransferAnnotationBuilder $annotationBuilder, QueryServiceInterface $queryService)
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
