<?php

/**
 * Application Vehicle Goods Adapter Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace CommonTest\Controller\Lva\Adapters;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;
use Common\Controller\Lva\Adapters\ApplicationVehicleGoodsAdapter;
use CommonTest\Bootstrap;
use Common\Service\Entity\LicenceEntityService;

/**
 * Application Vehicle Goods Adapter Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class ApplicationVehicleGoodsAdapterTest extends MockeryTestCase
{
    protected $sut;

    protected function setUp()
    {
        $this->sut = new ApplicationVehicleGoodsAdapter();
    }

    /**
     * Test populate form from entity
     * 
     * @group applicationVehicleGoodsAdapter
     */
    public function testPopulateFormFromEntity()
    {
        $data = [
            'version' => 1,
            'hasEnteredReg' => 'Y'
        ];
        $form = m::mock()
            ->shouldReceive('setData')
            ->with(['data' => $data])
            ->getMock();

        $request = m::mock()
            ->shouldReceive('isPost')
            ->andReturn(false)
            ->getMock();

        $this->assertSame($this->sut->populateForm($request, $data, $form), $form);
    }

    /**
     * Test populate form from post
     * 
     * @group applicationVehicleGoodsAdapter
     */
    public function testPopulateFormFromPost()
    {
        $data = [
            'version' => 1,
            'hasEnteredReg' => 'Y'
        ];
        $form = m::mock()
            ->shouldReceive('setData')
            ->with(['data' => $data])
            ->getMock();

        $request = m::mock()
            ->shouldReceive('isPost')
            ->andReturn(true)
            ->shouldReceive('getPost')
            ->andReturn(['data' => $data])
            ->getMock();

        $this->assertSame($this->sut->populateForm($request, $data, $form), $form);
    }
}
