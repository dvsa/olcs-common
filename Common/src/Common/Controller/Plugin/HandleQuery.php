<?php

namespace Common\Controller\Plugin;

use Common\Service\Cqrs\Query\QueryServiceInterface;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Transfer\Util\Annotation\AnnotationBuilder as TransferAnnotationBuilder;
use Laminas\Mvc\Controller\Plugin\AbstractPlugin;

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

    public function __construct(TransferAnnotationBuilder $annotationBuilder, QueryServiceInterface $queryService)
    {
        $this->queryService = $queryService;
        $this->annotationBuilder = $annotationBuilder;
    }

    /**
     * @return \Common\Service\Cqrs\Response
     */
    public function __invoke(QueryInterface $query)
    {
        $query = $this->annotationBuilder->createQuery($query);
        return $this->queryService->send($query);
    }
}
