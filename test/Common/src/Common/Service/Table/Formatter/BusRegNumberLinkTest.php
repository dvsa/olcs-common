<?php

namespace CommonTest\Service\Table\Formatter;

use Common\Service\Helper\UrlHelperService;
use Common\Service\Table\Formatter\BusRegNumberLink;
use Common\View\Helper\Status;
use Dvsa\Olcs\Utils\Translation\TranslatorDelegator;
use Laminas\View\HelperPluginManager;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * @covers \Common\Service\Table\Formatter\BusRegNumberLink
 */
class BusRegNumberLinkTest extends MockeryTestCase
{
    protected $urlHelper;
    protected $translator;
    protected $viewHelperManager;
    protected $statusHelper;

    protected function setUp(): void
    {
        $this->urlHelper = m:: mock(UrlHelperService::class);
        $this->translator = m::mock(TranslatorDelegator::class);
        $this->viewHelperManager = m::mock(HelperPluginManager::class);
        $this->statusHelper = m::mock(Status::class);
        $this->sut = new BusRegNumberLink($this->translator, $this->viewHelperManager, $this->urlHelper);
    }

    protected function tearDown(): void
    {
        m::close();
    }
    /**
     * Tests the formatting for the different possible input array formats
     *
     * @dataProvider dpFormat
     */
    public function testFormat($isTxcApp, $expectedOutputStatus)
    {
        $id = 1234;
        $translatedLabel = 'translated status label';
        $url = 'http://url';

        $inputData = [
            'id' => $id,
            'regNo' => '"5678',
            'isTxcApp' => $isTxcApp,
        ];

        $this->translator->shouldReceive('translate')
            ->times($isTxcApp)
            ->with(BusRegNumberLink::LABEL_TRANSLATION_KEY)
            ->andReturn($translatedLabel);

        $statusInput = [
            'colour' => BusRegNumberLink::LABEL_COLOUR,
            'value' => $translatedLabel,
        ];

        $this->statusHelper->shouldReceive('__invoke')->times($isTxcApp)->with($statusInput)->andReturn($expectedOutputStatus);
        $this->viewHelperManager->shouldReceive('get')->with('status')->times($isTxcApp)->andReturn($this->statusHelper);
        $this->urlHelper->shouldReceive('fromRoute')
            ->with(BusRegNumberLink::URL_ROUTE, ['busRegId' => $id], [], true)
            ->andReturn($url);

        $expected = '<a class="govuk-link" href="' . $url . '">&quot;5678</a>' . ' ' . $expectedOutputStatus;
        $this->assertEquals($expected, $this->sut->format($inputData, []));
    }

    /**
     * Data provider for testFormat
     *
     * @return array
     */
    public function dpFormat()
    {
        return [
            [1, 'status label'],
            [0, ''],
        ];
    }
}
