<?php

/**
 * Date Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\View\Helper;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\View\Helper\Date;
use Laminas\I18n\View\Helper\Translate;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Date Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class DateTest extends MockeryTestCase
{
    /**
     * @var Date
     */
    private $sut;

    /**
     * Setup the view helper
     */
    public function setUp(): void
    {
        $mockTranslator = m::mock(Translate::class);
        $mockTranslator->shouldReceive('__invoke')
            ->andReturnUsing(
                function ($text) {
                    return $text . '-translated';
                }
            );

        $sm = m::mock(ServiceLocatorInterface::class);
        $sm->shouldReceive('get')->with('translate')->andReturn($mockTranslator);

        $this->sut = new Date();
        $this->sut->createService($sm);
    }

    /**
     * @dataProvider provider
     */
    public function testInvoke($timestamp, $format, $altIfNull, $expected)
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
