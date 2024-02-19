<?php

namespace CommonTest\Service\Table\Formatter;

use Common\Service\Table\Formatter\DashboardTmApplicationStatus;
use Laminas\View\HelperPluginManager;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class DashboardTmApplicationStatusTest extends MockeryTestCase
{
    protected $sut;
    protected $viewHelperManager;

    protected function setUp(): void
    {
        $this->viewHelperManager = m::mock(HelperPluginManager::class);
        $this->sut = new DashboardTmApplicationStatus($this->viewHelperManager);
    }

    public function testFormat()
    {
        $tmHelper = m::mock();
        $this->viewHelperManager->shouldReceive('get')->with('transportManagerApplicationStatus')->once()->andReturn($tmHelper);

        $tmHelper->shouldReceive('render')
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

        $this->assertEquals($expected, $this->sut->format($data, $column));
    }
}
