<?php
/**
 * Created by PhpStorm.
 * User: parthvyas
 * Date: 05/11/2018
 * Time: 11:19
 */

namespace CommonTest\Controller\Traits;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class MethodToggleTraitTest extends MockeryTestCase
{
    protected $sut;

    protected function setUp(): void
    {
        $this->sut = m::mock('CommonTest\Controller\Traits\Stubs\MethodToggleTraitStub')
            ->makePartial();
    }

    public function testTogglableMethodWhenToggleOn()
    {
        $this->sut->shouldReceive('featuresEnabledForMethod')->andReturn(true);
        $this->sut->togglableMethod($this->sut, 'someMethod');

        $this->assertEquals($this->sut->someMethodString, 'method was called');
    }

    public function testTogglableMethodWhenToggleOff()
    {
        $this->sut->shouldReceive('featuresEnabledForMethod')->andReturn(false);
        $this->sut->togglableMethod($this->sut, 'someMethod');

        $this->assertEquals($this->sut->someMethodString, 'method was not called');
    }
}
