<?php

/**
 * Guidance Helper Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Helper;

use CommonTest\Bootstrap;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Service\Helper\GuidanceHelperService;

/**
 * Guidance Helper Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class GuidanceHelperServiceTest extends MockeryTestCase
{
    protected $sut;
    protected $sm;

    public function setUp()
    {
        $this->sut = new GuidanceHelperService();

        $this->sm = Bootstrap::getServiceManager();
        $this->sut->setServiceLocator($this->sm);
    }

    public function testAppend()
    {
        $message = 'foo';

        // Mocks
        $mockViewHelperManager = m::mock();
        $this->sm->setService('ViewHelperManager', $mockViewHelperManager);

        // Expectations
        $mockViewHelperManager->shouldReceive('get')
            ->with('placeholder')
            ->andReturn(
                m::mock()
                ->shouldReceive('getContainer')
                ->with('guidance')
                ->andReturn(
                    m::mock()
                    ->shouldReceive('append')
                    ->with($message)
                    ->getMock()
                )
                ->getMock()
            );
        $this->sut->append($message);
    }
}
