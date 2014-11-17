<?php

namespace CommonTest\Controller\Lva;

use \Mockery as m;

/**
 * Test Abstract Vehicles Goods Controller
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class AbstractVehiclesGoodsControllerTest extends AbstractLvaControllerTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->mockController('\Common\Controller\Lva\AbstractVehiclesGoodsController');
    }

    public function testGetIndexAction()
    {
        $form = $this->createMockForm('Lva\GoodsVehicles');

        $form->shouldReceive('setData')
            ->with(
                []
            )
            ->andReturn($form)
            ->shouldReceive('get')
            ->with('table')
            ->andReturn(m::mock('\Zend\Form\Fieldset'));

        $this->getMockFormHelper()
            ->shouldReceive('populateFormTable');

        $this->mockService('Table', 'prepareTable')
            ->with('lva-vehicles', [])
            ->andReturn(m::mock('\Common\Service\Table\TableBuilder'));

        $this->sut->shouldReceive('getLicenceId')
            ->andReturn(123)
            ->shouldReceive('getIdentifier')
            ->andReturn(321)
            ->shouldReceive('getLvaEntityService')
            ->andReturn(
                m::mock()
                ->shouldReceive('getTotalVehicleAuthorisation')
                ->with(321)
                ->getMock()
            );

        $this->mockEntity('Licence', 'getVehiclesData')
            ->with(123)
            ->andReturn([]);

        $this->mockEntity('Licence', 'getVehiclesTotal')
            ->with(123)
            ->andReturn(0);

        $this->mockRender();

        $this->sut->indexAction();

        $this->assertEquals('vehicles', $this->view);
    }
}
