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


    public function testGetUndertakingsDataError()
    {
        $this->markTestIncomplete();

        $mockTransferAnnotationBuilder = m::mock();
        $this->setService('TransferAnnotationBuilder', $mockTransferAnnotationBuilder);

        $mockQueryService = m::mock();
        $this->setService('QueryService', $mockQueryService);

        $mockFlashMessenger = m::mock();
        $this->setService('Helper\FlashMessenger', $mockFlashMessenger);

        $mockResponse = m::mock();

        $this->sut->shouldReceive('getIdentifier')->andReturn(12);

        $mockTransferAnnotationBuilder->shouldReceive('createQuery')
            ->with(m::type('Dvsa\Olcs\Transfer\Query\Application\Declaration'))->once()->andReturn('QUERY');

        $mockQueryService->shouldReceive('send')->with('QUERY')->once()->andReturn($mockResponse);

        $mockResponse->shouldReceive('isOk')->andReturn(false);

        $mockFlashMessenger->shouldReceive('addErrorMessage')->with('unknown-error')->once();

        $this->sut->getUndertakingsData();
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

    public function testCreateUpdateDeclarationDto()
    {
        $this->markTestIncomplete();

        $formData = [
            'id' => 3242,
            'declarationsAndUndertakings' => [
                'version' => 4,
                'declarationConfirmation' => 'Y',
            ],
            'interim' => [
                'goodsApplicationInterim' => 'Y',
                'goodsApplicationInterimReason' => 'SOME REASON',
            ]
        ];

        $this->sut->shouldReceive('getIdentifier')->andReturn(545);

        /* @var $dto \Dvsa\Olcs\Transfer\Command\Application\UpdateDeclaration */
        $dto = $this->sut->createUpdateDeclarationDto($formData);

        $expected = [
            'id' => 545,
            'version' => 4,
            'declarationConfirmation' => 'Y',
            'interimRequested' => 'Y',
            'interimReason' => 'SOME REASON',
        ];

        $this->assertSame($expected, $dto->getArrayCopy());
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

    public function testGetIndexAction()
    {
        $this->markTestIncomplete();

        $form = m::mock('\Common\Form\Form');
        $this->sut->shouldReceive('getForm')->andReturn($form);

        $applicationId = '123';

        $applicationData = $this->mockGetUndertakingsData($applicationId);

        $formData = [
            'declarationsAndUndertakings' => [
                'declarationConfirmation' => 'N',
                'version' => 1,
                'id' => $applicationId,
                'undertakings' => 'markup-undertakings-sample',
                'declarations' => 'markup-declarations-sample',
            ]
        ];

        $this->sut->shouldReceive('formatDataForForm')
            ->once()
            ->with($applicationData)
            ->andReturn($formData);

        $this->sut->shouldReceive('updateForm')->once();

        $form->shouldReceive('setData')->once()->with($formData);

        $this->mockRender();

        $this->sut->indexAction();

        $this->assertEquals('undertakings', $this->view);
    }

    public function testPostWithValidData()
    {
        $this->markTestIncomplete();

        $applicationId = '123';
        $this->mockGetUndertakingsData($applicationId);

        $mockTranslator = m::mock();
        $this->sm->setService('Helper\Translation', $mockTranslator);

        $mockTranslator->shouldReceive('translate')
            ->with('view-full-application')
            ->andReturn('View full application');

        $this->sut->shouldReceive('url->fromRoute')
            ->with('lva-/review', [], [], true)
            ->andReturn('URL');

        $data = [
            'declarationsAndUndertakings' => [
                'declarationConfirmation' => 'Y'
            ]
        ];

        $this->setPost($data);

        $form = m::mock('\Common\Form\Form');
        $this->sut->shouldReceive('getForm')->andReturn($form);

        $form->shouldReceive('setData')->with($data)->andReturnSelf();
        $form->shouldReceive('getData')->with()->once()->andReturn(['FORM_DATA']);
        $form->shouldReceive('isValid')->andReturn(true);

        $form->shouldReceive('get->get->setAttribute')
            ->with('value', '<p><a href="URL" target="_blank">View full application</a></p>');

        $this->sut->shouldReceive('save')->with(['FORM_DATA']);

        $this->sut->shouldReceive('completeSection')
            ->with('undertakings')
            ->andReturn('complete');

        $this->sut->shouldReceive('handleFees');

        $this->assertEquals(
            'complete',
            $this->sut->indexAction()
        );
    }

    public function testPostWithInvalidData()
    {
        $this->markTestIncomplete();

        $data = [
            'declarationsAndUndertakings' => [
                'declarationConfirmation' => 'N'
            ],
            'interim' => [
                'goodsApplicationInterim' => 'Y',
                'goodsApplicationInterimReason' => 'new reason',
            ],
        ];

        $this->setPost($data);

        $form = m::mock('\Common\Form\Form');
        $this->sut->shouldReceive('getForm')->andReturn($form);

        $form->shouldReceive('setData')->once()->with($data)->andReturnSelf();
        $form->shouldReceive('isValid')->andReturn(false);

        $applicationId = '123';
        $applicationData = $this->mockGetUndertakingsData($applicationId);

        $formData = [
            'declarationsAndUndertakings' => [
                'declarationConfirmation' => 'N',
                'version' => 1,
                'id' => $applicationId,
                'undertakings' => 'markup-undertakings-sample',
                'declarations' => 'markup-declarations-sample',
            ],
            'interim' => [
                'goodsApplicationInterim' => 'Y',
                'goodsApplicationInterimReason' => 'new reason',
            ],
        ];

        $this->sut->shouldReceive('formatDataForForm')
            ->once()
            ->with($applicationData)
            ->andReturn($formData);
        $form->shouldReceive('populateValues')->once()->with($formData)->andReturnSelf();

        $this->mockRender();

        $mockTranslator = m::mock();
        $this->sm->setService('Helper\Translation', $mockTranslator);

        $mockTranslator->shouldReceive('translate')
            ->with('view-full-application')
            ->andReturn('View full application');

        $this->sut->shouldReceive('url->fromRoute')
            ->with('lva-/review', [], [], true)
            ->andReturn('URL');

        $form->shouldReceive('get->get->setAttribute')
            ->with('value', '<p><a href="URL" target="_blank">View full application</a></p>');

        $this->sut->indexAction();

        $this->assertEquals('undertakings', $this->view);
    }

    public function testGetPartialPrefix()
    {
        $this->assertEquals('gv', $this->sut->getPartialPrefix('lcat_gv'));
        $this->assertEquals('psv', $this->sut->getPartialPrefix('lcat_psv'));
    }
}
