<?php

namespace CommonTest\Controller\Lva;

use Mockery as m;
use Common\Service\Entity\LicenceEntityService;
use Common\Service\Entity\ApplicationEntityService;

/**
 * Test Abstract Undertakings Controller
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class AbstractUndertakingsControllerTest extends AbstractLvaControllerTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->mockController('\Common\Controller\Lva\AbstractUndertakingsController');
    }

    public function testSave()
    {
        $mockTransferAnnotationBuilder = m::mock();
        $this->setService('TransferAnnotationBuilder', $mockTransferAnnotationBuilder);

        $mockCommandService = m::mock();
        $this->setService('CommandService', $mockCommandService);

        $this->sut->shouldReceive('createUpdateDeclarationDto')->with(['FORM_DATA'])->once()->andReturn('DTO');

        $mockTransferAnnotationBuilder->shouldReceive('createCommand')
            ->with('DTO')
            ->once()
            ->andReturn('COMMAND');

        $mockResponse = m::mock();
        $mockCommandService->shouldReceive('send')->with('COMMAND')->once()->andReturn($mockResponse);

        $mockResponse->shouldReceive('isOk')->with()->once()->andReturn(false);

        $mockFlashMessenger = m::mock();
        $this->setService('Helper\FlashMessenger', $mockFlashMessenger);

        $mockFlashMessenger->shouldReceive('addErrorMessage')->with('unknown-error')->once();

        $this->sut->save(['FORM_DATA']);
    }

    protected function mockGetUndertakingsData($applicationId)
    {
        $mockTransferAnnotationBuilder = m::mock();
        $this->setService('TransferAnnotationBuilder', $mockTransferAnnotationBuilder);

        $mockQueryService = m::mock();
        $this->setService('QueryService', $mockQueryService);

        $mockResponse = m::mock();

        $mockTransferAnnotationBuilder->shouldReceive('createQuery');
        $mockQueryService->shouldReceive('send')->andReturn($mockResponse);

        $this->sut->shouldReceive('getIdentifier')->andReturn($applicationId);

        $applicationData = [
            'licenceType' => ['id' => LicenceEntityService::LICENCE_TYPE_STANDARD_NATIONAL],
            'goodsOrPsv' => ['id' => LicenceEntityService::LICENCE_CATEGORY_GOODS_VEHICLE],
            'niFlag' => 'N',
            'declarationConfirmation' => 'N',
            'version' => 1,
            'id' => $applicationId,
        ];

        $mockResponse->shouldReceive('isOk')->andReturn(true);
        $mockResponse->shouldReceive('getResult')->andReturn($applicationData);

        return $applicationData;
    }

    public function testGetPartialPrefix()
    {
        $this->assertEquals('gv', $this->sut->getPartialPrefix('lcat_gv'));
        $this->assertEquals('psv', $this->sut->getPartialPrefix('lcat_psv'));
    }
}
