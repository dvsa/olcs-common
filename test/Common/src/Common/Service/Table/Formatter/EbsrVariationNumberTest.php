<?php

namespace CommonTest\Service\Table\Formatter;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Service\Table\Formatter\EbsrVariationNumber;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Class EbsrVariationNumberTest
 *
 * @package CommonTest\Service\Table\Formatter
 */
class EbsrVariationNumberTest extends MockeryTestCase
{
    /**
     * Tests empty string returned if there's no variation number set
     */
    public function testFormatWithNoVariationNumber()
    {
        $sut = new EbsrVariationNumber();
        $this->assertEquals('', $sut->format([]));
    }

    /**
     * Tests that the variation number is returned as is, when the record is not short notice
     *
     * @param array $data data
     *
     * @dataProvider dpNotShortNoticeProvider
     */
    public function testFormatNotShortNotice($data)
    {
        $sut = new EbsrVariationNumber();
        $this->assertEquals(1234, $sut->format($data));
    }

    /**
     * data provider for testFormatNotShortNotice
     *
     * @return array
     */
    public function dpNotShortNoticeProvider()
    {
        $notShortNotice = [
            'isShortNotice' => 'N',
            'variationNo' => 1234
        ];

        $shortNoticeNotKnown = [
            'variationNo' => 1234
        ];

        return [
            [$notShortNotice],
            [$shortNoticeNotKnown],
            [['busReg' => $notShortNotice]],
            [['busReg' => $shortNoticeNotKnown]],
        ];
    }

    /**
     * Tests format with short notice
     *
     * @param array $data data
     *
     * @dataProvider dpShortNoticeProvider
     */
    public function testFormatWithShortNotice($data)
    {
        $sut = new EbsrVariationNumber();

        $statusLabel = 'status label';

        $statusArray = [
            'colour' => 'orange',
            'value' => $statusLabel
        ];

        $sm = m::mock(ServiceLocatorInterface::class);

        $sm->shouldReceive('get->get->__invoke')
            ->once()
            ->with($statusArray)
            ->andReturn($statusLabel);

        $sm->shouldReceive('get->translate')
            ->once()
            ->with(EbsrVariationNumber::SN_TRANSLATION_KEY)
            ->andReturn($statusLabel);

        $expected = 1234 . $statusLabel;

        $this->assertEquals($expected, $sut->format($data, [], $sm));
    }

    /**
     * data provider for testFormatWithShortNotice
     *
     * @return array
     */
    public function dpShortNoticeProvider()
    {
        $data = [
            'isShortNotice' => 'Y',
            'variationNo' => 1234
        ];

        return [
            [$data],
            [['busReg' => $data]]
        ];
    }
}
