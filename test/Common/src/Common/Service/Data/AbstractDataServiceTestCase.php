<?php

namespace CommonTest\Common\Service\Data;

use Common\Service\Cqrs\Command\CommandService;
use Common\Service\Cqrs\Query\CachingQueryService as QueryService;
use Common\Service\Data\AbstractDataServiceServices;
use Dvsa\Olcs\Transfer\Query\QueryContainerInterface;
use Dvsa\Olcs\Transfer\Util\Annotation\AnnotationBuilder as TransferAnnotationBuilder;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Abstract Data Service Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class AbstractDataServiceTestCase extends MockeryTestCase
{
    /** @var  m\MockInterface */
    protected $query;

    /** @var  m\MockInterface */
    protected $transferAnnotationBuilder;

    /** @var  m\MockInterface */
    protected $queryService;

    /** @var  m\MockInterface */
    protected $commandService;

    /** @var  AbstractDataServiceServices */
    protected $abstractDataServiceServices;

    #[\Override]
    protected function setUp(): void
    {
        $this->query = m::mock(QueryContainerInterface::class);

        $this->transferAnnotationBuilder = m::mock(TransferAnnotationBuilder::class);
        $this->queryService = m::mock(QueryService::class);
        $this->commandService = m::mock(CommandService::class);

        $this->abstractDataServiceServices = new AbstractDataServiceServices(
            $this->transferAnnotationBuilder,
            $this->queryService,
            $this->commandService
        );
    }

    public function mockHandleQuery($mockResponse, $query = null): void
    {
        $query ??= $this->query;

        $this->queryService->shouldReceive('send')
            ->with($query)
            ->once()
            ->andReturn($mockResponse);
    }
}
