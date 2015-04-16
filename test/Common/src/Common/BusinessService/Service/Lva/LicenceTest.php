<?php

/**
 * Licence Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\BusinessService\Service\Lva;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use CommonTest\Bootstrap;
use Common\BusinessService\Service\Lva\Licence;
use Common\BusinessService\Response;

/**
 * Licence Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LicenceTest extends MockeryTestCase
{
    protected $sut;

    protected $sm;

    public function setUp()
    {
        $this->sut = new Licence();

        $this->sm = Bootstrap::getServiceManager();

        $this->sut->setServiceLocator($this->sm);
    }

    public function testProcess()
    {
        $params = [
            'id' => 111,
            'data' => ['data' => ['foo' => 'bar']]
        ];

        // Mocks
        $mockLicence = m::mock();
        $this->sm->setService('Entity\Licence', $mockLicence);

        // Expectations
        $mockLicence->shouldReceive('save')
            ->with(['id' => 111, 'foo' => 'bar']);

        $response = $this->sut->process($params);

        $this->assertInstanceOf('Common\BusinessService\Response', $response);
        $this->assertEquals(Response::TYPE_SUCCESS, $response->getType());
    }
}
