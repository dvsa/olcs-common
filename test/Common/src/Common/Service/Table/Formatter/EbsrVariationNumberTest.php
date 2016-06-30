<?php

namespace CommonTest\Service\Table\Formatter;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Service\Table\Formatter\EbsrVariationNumber;
use Zend\ServiceManager\ServiceLocatorInterface;

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
     */
    public function testFormatNotShortNotice()
    {
        $variationNumber = 1234;

        $data = [
            'busReg' => [
                'isShortNotice' => 'N',
                'variationNo' => $variationNumber
            ]
        ];

        $sut = new EbsrVariationNumber();
        $this->assertEquals($variationNumber, $sut->format($data));
    }

    /**
     * Tests format with short notice
     */
    public function testFormatWithShortNotice()
    {
        $sut = new EbsrVariationNumber();

        $variationNumber = 1234;
        $statusLabel = 'status label';

        $data = [
            'busReg' => [
                'isShortNotice' => 'Y',
                'variationNo' => $variationNumber
            ]
        ];
        
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

        $expected = $variationNumber . $statusLabel;

        $this->assertEquals($expected, $sut->format($data, [], $sm));
    }
}
