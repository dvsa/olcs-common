<?php

/**
 * Delete Other Employment Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\BusinessService\Service\Lva;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use CommonTest\Bootstrap;
use Common\BusinessService\Service\Lva\DeleteOtherEmployment;

/**
 * Delete Other Employment Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class DeleteOtherEmploymentTest extends MockeryTestCase
{
    protected $sut;

    protected $sm;

    public function setUp()
    {
        $this->sut = new DeleteOtherEmployment();

        $this->sm = Bootstrap::getServiceManager();

        $this->sut->setServiceLocator($this->sm);
    }

    public function testProcess()
    {
        $params = ['ids' => [111, 222]];

        // Mocks
        $mockOtherEmployment = m::mock();
        $this->sm->setService('Entity\TmEmployment', $mockOtherEmployment);

        // Expectations
        $mockOtherEmployment->shouldReceive('delete')
            ->once()
            ->with(111)
            ->shouldReceive('delete')
            ->once()
            ->with(222);

        $response = $this->sut->process($params);

        $this->assertInstanceOf('Common\BusinessService\Response', $response);
        $this->assertTrue($response->isOk());
    }
}
