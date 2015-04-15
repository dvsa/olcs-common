<?php

/**
 * Other Licence Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\BusinessService\Service\Lva;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\BusinessService\Service\Lva\OtherLicence;
use Common\BusinessService\Response;
use CommonTest\Bootstrap;

/**
 * Other Licence Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class OtherLicenceTest extends MockeryTestCase
{
    protected $sut;

    protected $sm;

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();

        $this->sut = new OtherLicence();

        $this->sut->setServiceLocator($this->sm);
    }

    public function testProcess()
    {
        // Params
        $params = [
            'data' => [
                'id' => 111,
                'foo' => 'bar'
            ]
        ];
        $expectedData = [
            'id' => 111,
            'foo' => 'bar'
        ];

        // Mocks
        $mockOtherLicence = m::mock();

        $this->sm->setService('Entity\OtherLicence', $mockOtherLicence);

        // Expectations
        $mockOtherLicence->shouldReceive('save')
            ->with($expectedData)
            ->andReturn([]);

        $response = $this->sut->process($params);

        $this->assertInstanceOf('\Common\BusinessService\Response', $response);
        $this->assertEquals(Response::TYPE_SUCCESS, $response->getType());
        $this->assertEquals(['id' => 111], $response->getData());
    }

    public function testProcessWithCreate()
    {
        // Params
        $params = [
            'data' => [
                'foo' => 'bar'
            ]
        ];
        $expectedData = [
            'foo' => 'bar'
        ];

        // Mocks
        $mockOtherLicence = m::mock();

        $this->sm->setService('Entity\OtherLicence', $mockOtherLicence);

        // Expectations
        $mockOtherLicence->shouldReceive('save')
            ->with($expectedData)
            ->andReturn(['id' => 222]);

        $response = $this->sut->process($params);

        $this->assertInstanceOf('\Common\BusinessService\Response', $response);
        $this->assertEquals(Response::TYPE_SUCCESS, $response->getType());
        $this->assertEquals(['id' => 222], $response->getData());
    }
}
