<?php

namespace CommonTest\Service\Table\Formatter;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Service\Table\Formatter\DashboardTmApplicationId;

/**
 * Class DashboardTmApplicationIdTest
 *
 * @package CommonTest\Service\Table\Formatter
 */
class DashboardTmApplicationIdTest extends MockeryTestCase
{
    private $sut;

    /* @var \Mockery\MockInterface */
    private $sm;

    /* @var \Mockery\MockInterface */
    private $mockViewHelper;

    public function setUp(): void
    {
        $this->sut = new DashboardTmApplicationId();

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
            'applicationId' => 323,
            'transportManagerApplicationStatus' => [
                'id' => 656,
                'description' => 'FooBar',
            ]
        ];
        $column = [];
        $expected = '<b>323</b> HTML';

        $this->assertEquals($expected, $this->sut->format($data, $column, $this->sm));
    }
}
