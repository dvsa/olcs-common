<?php

/**
 * CRUD Table Trait Test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace CommonTest\Controller\Lva\Traits;

use CommonTest\Bootstrap;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * CRUD Table Trait Test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class CrudTableTraitTest extends MockeryTestCase
{
    protected $sut;

    protected $sm;

    protected function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();

        $this->sut = m::mock('CommonTest\Controller\Lva\Traits\Stubs\CrudTableTraitStub')
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $this->sut->setServiceLocator($this->sm);
    }

    public function testHandlePostSaveWithAddAnother()
    {
        $redirectMock = m::mock()
            ->shouldReceive('toRoute')
            ->with(
                null,
                [
                    'application' => 123,
                    'action' => 'add'
                ]
            )
            ->andReturn('redirect')
            ->getMock();

        $this->sut->shouldReceive('postSave')
            ->shouldReceive('getIdentifierIndex')
            ->andReturn('application')
            ->shouldReceive('getIdentifier')
            ->andReturn(123)
            ->shouldReceive('isButtonPressed')
            ->with('addAnother')
            ->andReturn(true)
            ->shouldReceive('redirect')
            ->andReturn($redirectMock)
            ->shouldReceive('params')
            ->with('action')
            ->andReturn('add');

        $this->sm->setService(
            'Helper\FlashMessenger',
            m::mock()
            ->shouldReceive('addSuccessMessage')
            ->with('section.add.fake-section')
            ->getMock()
        );

        $this->assertEquals(
            'redirect',
            $this->sut->callHandlePostSave()
        );
    }

    public function testHandlePostSave()
    {
        $redirectMock = m::mock()
            ->shouldReceive('toRouteAjax')
            ->with(null, ['application' => 123])
            ->andReturn('redirect')
            ->getMock();

        $this->sut->shouldReceive('postSave')
            ->shouldReceive('getIdentifierIndex')
            ->andReturn('application')
            ->shouldReceive('getIdentifier')
            ->andReturn(123)
            ->shouldReceive('isButtonPressed')
            ->with('addAnother')
            ->andReturn(false)
            ->shouldReceive('redirect')
            ->andReturn($redirectMock)
            ->shouldReceive('params')
            ->with('action')
            ->andReturn('add');

        $this->sm->setService(
            'Helper\FlashMessenger',
            m::mock()
            ->shouldReceive('addSuccessMessage')
            ->with('section.add.fake-section')
            ->getMock()
        );

        $this->assertEquals(
            'redirect',
            $this->sut->callHandlePostSave()
        );
    }
}
