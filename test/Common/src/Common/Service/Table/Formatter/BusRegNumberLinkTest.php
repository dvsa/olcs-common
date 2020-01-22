<?php

namespace CommonTest\Service\Table\Formatter;

use Common\Service\Helper\UrlHelperService;
use Common\Service\Table\Formatter\BusRegNumberLink;
use Common\View\Helper\Status;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\Mvc\I18n\Translator;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\HelperPluginManager;

/**
 * @covers \Common\Service\Table\Formatter\BusRegNumberLink
 */
class BusRegNumberLinkTest extends MockeryTestCase
{
    /**
     * Tests the formatting for the different possible input array formats
     *
     * @dataProvider dpFormat
     */
    public function testFormat($isTxcApp, $expectedOutputStatus)
    {
        $sut = new BusRegNumberLink();

        $id = 1234;
        $translatedLabel = 'translated status label';
        $url = 'http://url';

        $inputData = [
            'id' => $id,
            'regNo' => '"5678',
            'isTxcApp' => $isTxcApp,
        ];

        $sm = m::mock(ServiceLocatorInterface::class);

        $translationHelper = m::mock(Translator::class);
        $translationHelper->shouldReceive('translate')
            ->times($isTxcApp)
            ->with(BusRegNumberLink::LABEL_TRANSLATION_KEY)
            ->andReturn($translatedLabel);

        $statusInput = [
            'colour' => BusRegNumberLink::LABEL_COLOUR,
            'value' => $translatedLabel,
        ];

        $statusHelper = m::mock(Status::class);
        $statusHelper->shouldReceive('__invoke')->times($isTxcApp)->with($statusInput)->andReturn($expectedOutputStatus);

        $viewHelperManager = m::mock(HelperPluginManager::class);
        $viewHelperManager->shouldReceive('get')->with('status')->times($isTxcApp)->andReturn($statusHelper);

        $urlHelperService = m::mock(UrlHelperService::class);
        $urlHelperService->expects('fromRoute')
            ->with(BusRegNumberLink::URL_ROUTE, ['busRegId' => $id], [], true)
            ->andReturn($url);

        $sm->shouldReceive('get')->times($isTxcApp)->with('ViewHelperManager')->andReturn($viewHelperManager);
        $sm->shouldReceive('get')->times($isTxcApp)->with('translator')->andReturn($translationHelper);
        $sm->expects('get')->with('Helper\Url')->andReturn($urlHelperService);

        $expected = '<a href="'. $url . '">&quot;5678</a>' . $expectedOutputStatus;
        $this->assertEquals($expected, $sut::format($inputData, [], $sm));
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
