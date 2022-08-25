<?php

namespace CommonTest\Service\Table\Formatter;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Service\Table\Formatter\DashboardTmApplicationStatus;

class DashboardTmApplicationStatusTest extends MockeryTestCase
{
    private $sut;

    /* @var \Mockery\MockInterface */
    private $sm;

    /* @var \Mockery\MockInterface */
    private $mockViewHelper;

    public function setUp(): void
    {
        $this->sut = new DashboardTmApplicationStatus();

        $this->mockViewHelper = m::mock();

        $this->sm = m::mock('StdClass');
        $this->sm->shouldReceive('get->get')
            ->with('transportManagerApplicationStatus')
            ->once()
            ->andReturn($this->mockViewHelper);
    }

    public function testFormat()
    {
        $this->mockViewHelper->shouldReceive('render')
            ->with(656, 'FooBar')
            ->once()
            ->andReturn('HTML');

        $data = [
            'transportManagerApplicationStatus' => [
                'id' => 656,
                'description' => 'FooBar',
            ]
        ];
        $column = [];
        $expected = 'HTML';

        $this->assertEquals($expected, $this->sut->format($data, $column, $this->sm));
    }
}
