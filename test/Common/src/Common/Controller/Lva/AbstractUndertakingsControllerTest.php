<?php

namespace CommonTest\Controller\Lva;

use \Mockery as m;

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

    public function testGetIndexAction()
    {
        $form = $this->createMockForm('Lva\Undertakings');

        $applicationId = '123';

        $this->sut->shouldReceive('getApplicationId')->andReturn($applicationId);

        $applicationData = [
            'licenceType' => ['id' => 'ltype_sn'],
            'goodsOrPsv' => ['id' => 'lcat_gv'],
            'niFlag' => 'N',
            'declarationConfirmation' => 'N',
            'version' => 1,
            'id' => $applicationId,
        ];
        $this->sm->shouldReceive('get')->with('Entity\Application')
            ->andReturn(
                m::mock()
                ->shouldReceive('getDataForUndertakings')
                    ->once()
                    ->with($applicationId)
                    ->andReturn($applicationData)
                ->getMock()
            );

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

        $form->shouldReceive('setData')->once()->with($formData);

        $this->mockRender();

        $this->sut->indexAction();

        $this->assertEquals('undertakings', $this->view);
    }

    public function testPostWithValidData()
    {
        $data = [
            'declarationsAndUndertakings' => [
                'declarationConfirmation' => 'Y'
            ]
        ];

        $this->setPost($data);
        $form = $this->createMockForm('Lva\Undertakings');
        $form->shouldReceive('setData')->with($data)->andReturnSelf();
        $form->shouldReceive('isValid')->andReturn(true);

        $this->sm->shouldReceive('get')->with('Entity\Application')
            ->andReturn(
                m::mock()
                ->shouldReceive('save')
                    ->once()
                    ->with(['declarationConfirmation' => 'Y'])
                ->getMock()
            );

        $this->sut->shouldReceive('postSave')
            ->with('undertakings')
            ->shouldReceive('completeSection')
            ->with('undertakings')
            ->andReturn('complete');

        $this->assertEquals(
            'complete',
            $this->sut->indexAction()
        );
    }

    public function testPostWithInvalidData()
    {
        $data = [
            'declarationsAndUndertakings' => [
                'declarationConfirmation' => 'N'
            ]
        ];

        $this->setPost($data);

        $form = $this->createMockForm('Lva\Undertakings');
        $form->shouldReceive('setData')->once()->with($data)->andReturnSelf();
        $form->shouldReceive('isValid')->andReturn(false);

        $applicationId = '123';
        $this->sut->shouldReceive('getApplicationId')->andReturn($applicationId);
        $applicationData = [
            'licenceType' => ['id' => 'ltype_sn'],
            'goodsOrPsv' => ['id' => 'lcat_gv'],
            'niFlag' => 'N',
            'declarationConfirmation' => 'N',
            'version' => 1,
            'id' => $applicationId,
        ];
        $this->sm->shouldReceive('get')->with('Entity\Application')
            ->andReturn(
                m::mock()
                ->shouldReceive('getDataForUndertakings')
                    ->once()
                    ->with($applicationId)
                    ->andReturn($applicationData)
                ->getMock()
            );

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
        $form->shouldReceive('setData')->once()->with($formData)->andReturnSelf();

        $this->mockRender();

        $this->sut->indexAction();

        $this->assertEquals('undertakings', $this->view);
    }
}
