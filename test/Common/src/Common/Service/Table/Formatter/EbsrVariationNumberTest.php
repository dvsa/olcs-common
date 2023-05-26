<?php

namespace CommonTest\Service\Table\Formatter;

use Common\Service\Table\Formatter\EbsrVariationNumber;
use Dvsa\Olcs\Utils\Translation\TranslatorDelegator;
use Laminas\View\HelperPluginManager;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Class EbsrVariationNumberTest
 *
 * @package CommonTest\Service\Table\Formatter
 */
class EbsrVariationNumberTest extends MockeryTestCase
{
    protected $translator;
    protected $viewHelperManager;
    protected $sut;

    protected function setUp(): void
    {
        $this->translator = m::mock(TranslatorDelegator::class);
        $this->viewHelperManager = m::mock(HelperPluginManager::class);
        $this->sut = new EbsrVariationNumber($this->viewHelperManager, $this->translator);
    }

    protected function tearDown(): void
    {
        m::close();
    }

    /**
     * Tests empty string returned if there's no variation number set
     */
    public function testFormatWithNoVariationNumber()
    {
        $this->assertEquals('', $this->sut->format([]));
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
        $this->assertEquals(1234, $this->sut->format($data));
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
        $statusLabel = 'status label';

        $statusArray = [
            'colour' => 'orange',
            'value' => $statusLabel
        ];

        $statusHelper = m::mock(Status::class);

        $this->viewHelperManager->shouldReceive('get')
            ->with('status')
            ->andReturn($statusHelper);

        $statusHelper->shouldReceive('__invoke')
            ->once()
            ->with($statusArray)
            ->andReturn($statusLabel);

        $this->translator->shouldReceive('translate')
            ->once()
            ->with(EbsrVariationNumber::SN_TRANSLATION_KEY)
            ->andReturn($statusLabel);

        $expected = 1234 . $statusLabel;

        $this->assertEquals($expected, $this->sut->format($data, []));
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
