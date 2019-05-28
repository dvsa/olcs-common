<?php

namespace CommonTest\Service\Table\Formatter;

use Common\Service\Table\Formatter\ValidityPeriod;
use CommonTest\Bootstrap;
use IntlDateFormatter;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\I18n\Translator\TranslatorInterface;
use Zend\I18n\View\Helper\DateFormat;
use Zend\View\HelperPluginManager;

/**
 * Validity period formatter test
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class ValidityPeriodTest extends MockeryTestCase
{
    public function testFormat()
    {
        $locale = 'cy_GB';
        $validFromTimestamp = 12345678;
        $validToTimestamp = 87654321;

        $row = [
            'validFromTimestamp' => $validFromTimestamp,
            'validToTimestamp' => $validToTimestamp,
            'year' => '2019',
        ];

        $dateFormatService = m::mock(DateFormat::class);
        $dateFormatService->shouldReceive('__invoke')
            ->with($validFromTimestamp, IntlDateFormatter::MEDIUM, IntlDateFormatter::NONE, $locale)
            ->andReturn('1 Jan 2019');
        $dateFormatService->shouldReceive('__invoke')
            ->with($validToTimestamp, IntlDateFormatter::MEDIUM, IntlDateFormatter::NONE, $locale)
            ->andReturn('31 Dec 2019');

        $translatorService = m::mock(TranslatorInterface::class);
        $translatorService->shouldReceive('getLocale')
            ->andReturn($locale);
        $translatorService->shouldReceive('translate')
            ->with('permits.irhp.fee-breakdown.validity-period.cell')
            ->andReturn('%s to %s');

        $viewHelperManager = m::mock(HelperPluginManager::class);
        $viewHelperManager->shouldReceive('get')
            ->with('DateFormat')
            ->andReturn($dateFormatService);

        $sm = Bootstrap::getServiceManager();
        $sm->setService('Translator', $translatorService);
        $sm->setService('ViewHelperManager', $viewHelperManager);

        $this->assertEquals(
            '1 Jan to 31 Dec',
            ValidityPeriod::format($row, [], $sm)
        );
    }
}
