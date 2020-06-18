<?php

namespace CommonTest\View\Helper;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\View\Helper\FlashMessenger;

/**
 * Date Test
 */
class DateTimeTest extends MockeryTestCase
{
    /**
     * @var \Common\View\Helper\DateTime
     */
    private $sut;

    /**
     * Setup the view helper
     */
    public function setUp(): void
    {
        $this->sut = new \Common\View\Helper\DateTime();
    }

    /**
     * @dataProvider provider
     *
     * @param \DateTime $dateTime
     * @param $format
     * @param $expected
     */
    public function testInvoke(\DateTime $dateTime, $format, $expected)
    {
        $sut = $this->sut;
        $this->assertEquals($expected, $sut($dateTime, $format));
    }

    /**
     * Data provider
     */
    public function provider()
    {
        return [
            [
                new \DateTime('2016-06-10 12:00', new \DateTimeZone('UTC')),
                'd/m/Y H:i',
                '10/06/2016 13:00'
            ],
            [
                new \DateTime('2016-12-10 12:00', new \DateTimeZone('UTC')),
                'd/m/Y H:i',
                '10/12/2016 12:00'
            ],
            [
                new \DateTime('2016-06-10 12:00', new \DateTimeZone('Europe/London')),
                'd/m/Y H:i',
                '10/06/2016 12:00'
            ],
        ];
    }
}
