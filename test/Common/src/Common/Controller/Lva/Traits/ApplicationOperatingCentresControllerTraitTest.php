<?php

/**
 * Application Operating Centres Controller Trait Test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace CommonTest\Controller\Lva\Traits;

use CommonTest\Bootstrap;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Application Operating Centres Controller Trait Test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class ApplicationOperatingCentresControllerTraitTest extends MockeryTestCase
{
    protected $sut;

    protected function setUp()
    {
        /*
        $this->sut = m::mock('CommonTest\Controller\Lva\Traits\Stubs\ApplicationOperatingCentresControllerTraitStub')
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();
         */
        $this->sut = new Stubs\ApplicationOperatingCentresControllerTraitStub();
    }

    public function testCheckTrafficAreaAfterCrudActionWithArray()
    {
        $this->assertEquals(
            null,
            $this->sut->callCheckTrafficAreaAfterCrudAction(
                [
                    'action' => ['foo' => 'bar']
                ]
            )
        );
    }
}
