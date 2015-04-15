<?php

/**
 * Delete Other Licence Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\BusinessService\Service\Lva;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use CommonTest\Bootstrap;
use Common\BusinessService\Service\Lva\DeleteOtherLicence;

/**
 * Delete Other Licence Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class DeleteOtherLicenceTest extends MockeryTestCase
{
    protected $sut;

    protected $sm;

    public function setUp()
    {
        $this->sut = new DeleteOtherLicence();

        $this->sm = Bootstrap::getServiceManager();

        $this->sut->setServiceLocator($this->sm);
    }

    public function testProcess()
    {
        $params = ['ids' => [111, 222]];

        // Mocks
        $mockOtherLicence = m::mock();
        $this->sm->setService('Entity\OtherLicence', $mockOtherLicence);

        // Expectations
        $mockOtherLicence->shouldReceive('delete')
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
