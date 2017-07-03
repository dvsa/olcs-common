<?php

namespace CommonTest\Controller\Lva\Adapters;

use Common\Controller\Lva\Adapters\LicenceTransportManagerAdapter;
use Common\Service\Cqrs\Command\CommandService;
use Common\Service\Cqrs\Query\CachingQueryService;
use Dvsa\Olcs\Transfer\Util\Annotation\AnnotationBuilder as TransferAnnotationBuilder;
use Dvsa\Olcs\Transfer\Command\CommandContainer;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\ServiceManager\ServiceManager;
use Zend\Http\Response as HttpResponse;
use Dvsa\Olcs\Transfer\Command\TransportManagerLicence\Delete;

/**
 * Licence Transport Manager Adapter Test
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class LicenceTransportManagerAdapterTest extends MockeryTestCase
{
    /** @var LicenceTransportManagerAdapter */
    protected $sut;
    /** @var  ServiceManager|\Mockery\MockInterface */
    protected $sm;
    /** @var TransferAnnotationBuilder $mockAnnotationBuilder */
    protected $mockAnnotationBuilder;
    /** @var CachingQueryService $mockQuerySrv */
    protected $mockQuerySrv;
    /** @var CommandService $mockCommandSrv */
    protected $mockCommandSrv;

    protected function setUp()
    {
        $this->sm = m::mock(ServiceManager::class)->makePartial();
        $this->sm->setAllowOverride(true);

        $this->mockAnnotationBuilder = m::mock(TransferAnnotationBuilder::class);
        $this->mockQuerySrv = m::mock(CachingQueryService::class);
        $this->mockCommandSrv = m::mock(CommandService::class);

        $this->sut = new LicenceTransportManagerAdapter(
            $this->mockAnnotationBuilder, $this->mockQuerySrv, $this->mockCommandSrv
        );

        $this->sut->setServiceLocator($this->sm);
    }

    public function testDelete()
    {
        $responseIsOk = true;
        $httpResponse = m::mock(HttpResponse::class);
        $httpResponse->shouldReceive('isOk')->once()->withNoArgs()->andReturn($responseIsOk);
        $commandContainer = m::mock(CommandContainer::class);

        $this->mockAnnotationBuilder
            ->shouldReceive('createCommand')
            ->with(m::type(Delete::class))
            ->once()
            ->andReturn($commandContainer);

        $this->mockCommandSrv->shouldReceive('send')->with($commandContainer)->once()->andReturn($httpResponse);

        $this->assertEquals($responseIsOk, $this->sut->delete([111, 222], 333));
    }
}
