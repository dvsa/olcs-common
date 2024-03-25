<?php

/**
 * Task checkbox formatter tests
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */

namespace CommonTest\Service\Table\Formatter;

use Common\Service\Table\Formatter\TaskCheckbox;
use Common\Service\Table\TableBuilder;
use Mockery as m;

/**
 * Task checkbox formatter tests
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class TaskCheckboxTest extends \PHPUnit\Framework\TestCase
{
    protected $tableBuilder;

    protected $sut;

    protected function setUp(): void
    {
        $this->tableBuilder = m::mock(TableBuilder::class);
        $this->sut = new TaskCheckbox($this->tableBuilder);
    }

    /**
     * @dataProvider notClosedProvider
     */
    public function testFormatNotClosed($data): void
    {
        $column = [];

        $this->tableBuilder->shouldReceive('replaceContent')
            ->with('{{[elements/checkbox]}}', $data)
            ->andReturn('checkbox markup');

        $this->assertEquals('checkbox markup', $this->sut->format($data, $column));
    }

    public function notClosedProvider()
    {
        return [
            'N' => [
                [
                    'id' => 69,
                    'isClosed' => 'N',
                ],
            ],
            'not set' => [
                [
                    'id' => 69,
                ],
            ],
            'null' => [
                [
                    'id' => 69,
                    'isClosed' => null,
                ],
            ],
        ];
    }

    public function testFormatClosed(): void
    {
        $data = [
            'id' => 69,
            'isClosed' => 'Y',
        ];

        $column = [];

        $this->assertEquals('', (new TaskCheckbox($this->tableBuilder))->format($data, $column));
    }
}
