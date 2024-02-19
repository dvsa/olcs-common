<?php

namespace CommonTest\Service\Table\Formatter;

use Common\Service\Helper\StackHelperService;
use Common\Service\Table\Formatter\NumberStackValue;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;

/**
 * NumberStackValue formatter test
 */
class NumberStackValueTest extends TestCase
{
    protected $stackHelper;
    protected $sut;

    protected function setUp(): void
    {
        $this->stackHelper = m::mock(StackHelperService::class);
        $this->sut = new NumberStackValue($this->stackHelper);
    }

    public function testFormatWithoutStack()
    {
        $this->expectException('\InvalidArgumentException');
        $data = [];
        $column = [];

        $this->sut->format($data, $column);
    }

    public function testWithThousandFormatter()
    {
        $data = [
            'foo' => [
                'bar' => [
                    'cake' => 12300
                ]
            ]
        ];
        $column = [
            'stack' => 'foo->bar->cake'
        ];
        $expected = '12,300';

        $this->stackHelper->shouldReceive('getStackValue')->once()->with($data, ['foo', 'bar', 'cake'])->andReturn(12300);
        $this->assertEquals($expected, $this->sut->format($data, $column));
    }
}
