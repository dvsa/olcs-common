<?php

namespace CommonTest\View\Helper;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\View\Helper\Date;
use Laminas\I18n\View\Helper\Translate;

class DateTest extends MockeryTestCase
{
    /**
     * @var Date
     */
    private $sut;

    /**
     * Setup the view helper
     */
    protected function setUp(): void
    {
        $mockTranslator = m::mock(Translate::class);
        $mockTranslator->shouldReceive('__invoke')
            ->andReturnUsing(
                static fn($text) => $text . '-translated'
            );

        $this->sut = new Date($mockTranslator);
    }

    /**
     * @dataProvider provider
     */
    public function testInvoke($timestamp, $format, $altIfNull, $expected): void
    {
        $sut = $this->sut;

        $this->assertEquals($expected, $sut($timestamp, $format, $altIfNull));
    }

    /**
     * Data provider
     */
    public function provider()
    {
        return [
            [
                strtotime('2010-03-20'),
                'd/m/Y',
                'Unknown',
                '20/03/2010'
            ],
            [
                strtotime('2010-03-20'),
                'Y',
                'Unknown',
                '2010'
            ],
            [
                null,
                'd/m/Y',
                'Unknown',
                'Unknown-translated'
            ],
            [
                null,
                'd/m/Y',
                'N/a',
                'N/a-translated'
            ]
        ];
    }
}
