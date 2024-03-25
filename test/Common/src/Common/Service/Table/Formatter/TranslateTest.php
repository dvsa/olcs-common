<?php

namespace CommonTest\Service\Table\Formatter;

use Common\Service\Helper\DataHelperService;
use Common\Service\Table\Formatter\Translate;
use Dvsa\Olcs\Utils\Translation\TranslatorDelegator;
use Mockery as m;

/**
 * @covers Common\Service\Table\Formatter\Translate
 */
class TranslateTest extends \PHPUnit\Framework\TestCase
{
    protected $translator;

    protected $dataHelper;

    protected $sut;

    protected function setUp(): void
    {
        $this->translator = m::mock(TranslatorDelegator::class);
        $this->dataHelper = m::mock(DataHelperService::class);
        $this->sut = new Translate($this->translator, $this->dataHelper);
    }

    protected function tearDown(): void
    {
        m::close();
    }

    /**
     * @dataProvider provider
     */
    public function testFormat($data, $column, $expected): void
    {
        $this->translator->shouldReceive('translate')
            ->andReturnUsing(
                static fn($string) => strtoupper($string)
            );

        $this->dataHelper->shouldReceive('fetchNestedData')->andReturn($expected);

        $this->assertEquals($expected, $this->sut->format($data, $column));
    }

    /**TaskIdentifierTest
     * Data provider
     *
     * @return array
     */
    public function provider()
    {
        return [
            [
                'data' => ['test' => 'foo'],
                'column' => ['name' => 'test'],
                'expect' => 'FOO',
            ],
            [
                'data' => ['test' => 'foo'],
                'column' => ['content' => 'test'],
                'expect' => 'TEST',
            ],
            [
                'data' => ['test' => 'foo'],
                'column' => [],
                'expect' => '',
            ],
            [
                'data' => [
                    'test' => ['foo' => 'bar']
                ],
                'column' => ['name' => 'test->foo'],
                'expect' => 'BAR',
            ],
        ];
    }
}
