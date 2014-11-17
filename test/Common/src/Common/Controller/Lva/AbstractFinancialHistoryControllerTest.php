<?php

namespace CommonTest\Controller\Lva;

use \Mockery as m;

/**
 * Test Abstract Financial History Controller
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class AbstractFinancialHistoryControllerTest extends AbstractLvaControllerTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->mockController('\Common\Controller\Lva\AbstractFinancialHistoryController');
    }

    public function testGetIndexAction()
    {
        $form = $this->createMockForm('Lva\FinancialHistory');

        $form->shouldReceive('setData')
            ->with(
                [
                    'data' => []
                ]
            )
            ->andReturn($form);

        $this->sut
            ->shouldReceive('getApplicationId')
            ->andReturn(500);

        $this->mockEntity('Application', 'getFinancialHistoryData')
            ->with(500)
            ->andReturn([]);

        $this->setService(
            'Helper\FileUpload',
            m::mock()
            ->shouldReceive('setForm')
            ->with($form)
            ->andReturnSelf()
            ->shouldReceive('setSelector')
            ->with('data->file')
            ->andReturnSelf()
            ->shouldReceive('setUploadCallback')
            ->andReturnSelf()
            ->shouldReceive('setDeleteCallback')
            ->andReturnSelf()
            ->shouldReceive('setLoadCallback')
            ->andReturnSelf()
            ->shouldReceive('setRequest')
            ->with($this->request)
            ->andReturnSelf()
            ->shouldReceive('process')
            ->andReturn(false)
            ->getMock()
        );

        $this->mockRender();

        $this->sut->indexAction();

        $this->assertEquals('financial_history', $this->view);
    }
}
