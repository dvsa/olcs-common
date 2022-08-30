<?php

namespace Common\Service\Data;

use Common\Service\Cqrs\Command\CommandService;
use Common\Service\Cqrs\Query\CachingQueryService as QueryService;
use Dvsa\Olcs\Transfer\Util\Annotation\AnnotationBuilder as TransferAnnotationBuilder;

/**
 * AbstractDataServiceServices
 */
class AbstractDataServiceServices
{
    /** @var TransferAnnotationBuilder */
    protected $transferAnnotationBuilder;

    /** @var QueryService */
    protected $queryService;

    /** @var CommandService */
    protected $commandService;

    /**
     * Create service instance
     *
     * @param TransferAnnotationBuilder $transferAnnotationBuilder
     * @param QueryService $queryService
     * @param CommandService $commandService
     *
     * @return AbstractDataServiceServices
     */
    public function __construct(
        TransferAnnotationBuilder $transferAnnotationBuilder,
        QueryService $queryService,
        CommandService $commandService
    ) {
        $this->transferAnnotationBuilder = $transferAnnotationBuilder;
        $this->queryService = $queryService;
        $this->commandService = $commandService;
    }

    /**
     * Return the transfer annotation builder service
     *
     * @return TransferAnnotationBuilder
     */
    public function getTransferAnnotationBuilder(): TransferAnnotationBuilder
    {
        return $this->transferAnnotationBuilder;
    }

    /**
     * Return the query service
     *
     * @return QueryService
     */
    public function getQueryService(): QueryService
    {
        return $this->queryService;
    }

    /**
     * Return the command service
     *
     * @return CommandService
     */
    public function getCommandService(): CommandService
    {
        return $this->commandService;
    }
}
