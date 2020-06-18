<?php

namespace CommonTest\Service\Table\Type;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Service\Table\Type\OperatingCentreAction;

/**
 * OperatingCentreActionTest Test
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class OperatingCentreActionTest extends MockeryTestCase
{
    protected $sut;
    protected $table;

    public function setUp(): void
    {
        $mockAuthService = m::mock()
            ->shouldReceive('isGranted')
            ->with('internal-user')
            ->andReturn(true)
            ->shouldReceive('isGranted')
            ->with('internal-edit')
            ->andReturn(true)
            ->getMock();

        $this->table = m::mock()
            ->shouldReceive('getAuthService')
            ->andReturn($mockAuthService)
            ->once()
            ->getMock();

        $this->sut = new OperatingCentreAction($this->table);
    }

    public function testRenderNoS4()
    {
        $this->table->shouldReceive('getFieldset')->with()->once()->andReturn(null);

        $data = ['id' => 1];
        $column = ['action' => 'FOO'];

        $this->assertStringNotContainsString('(Schedule 4/1)', $this->sut->render($data, $column));
    }

    public function testRenderWithS4()
    {
        $this->table->shouldReceive('getFieldset')->with()->once()->andReturn(null);

        $data = ['id' => 1, 's4' => 'FOO'];
        $column = ['action' => 'FOO'];

        $this->assertStringContainsString('(Schedule 4/1)', $this->sut->render($data, $column));
    }
}
