<?php

/**
 * UpdateContinuationDetailTest Test
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace CommonTest\BusinessService\Service\Lva;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\BusinessService\Service\Lva\UpdateContinuationDetail;
use CommonTest\Bootstrap;

/**
 * UpdateContinuationDetailTests Test
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class UpdateContinuationDetailTest extends MockeryTestCase
{
    protected $sut;

    protected $sm;

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();

        $this->sut = new UpdateContinuationDetail();
        $this->sut->setServiceLocator($this->sm);
    }

    public function testProcess()
    {
        $params = [
            'data' => [
                'id' => 1966,
                'foo' => 'bar',
            ],

        ];

        $mockEntityService = m::mock();
        $this->sm->setService('Entity\ContinuationDetail', $mockEntityService);

        $mockEntityService->shouldReceive('forceUpdate')->with($params['data']['id'], $params['data'])->once();

        $response = $this->sut->process($params);

        $this->assertTrue($response->isOk());
        $this->assertEquals($params['data']['id'], $response->getData()['id']);
    }
}
