<?php

namespace CommonTest\Service\Table\Formatter;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Service\Helper\UrlHelperService as UrlHelper;
use Common\View\Helper\Status as StatusHelper;
use Laminas\View\HelperPluginManager as ViewPluginManager;
use Common\Service\Table\Formatter\LicenceApplication;
use CommonTest\Bootstrap;

/**
 * Licence and application test
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class LicenceApplicationTest extends MockeryTestCase
{
    /**
     * @dataProvider dpTestFormat
     *
     * @param $expected
     * @param $appTimes
     * @param $extraRowData
     */
    public function testFormat($expected, $extraRowData, $appTimes)
    {
        $licStatus = 'lic status';
        $licStatusDesc = 'lic status desc';
        $formattedLicStatus = 'formatted lic status';
        $licUrl = 'http://licURL';
        $licNo = 'OB1234567';
        $licId = 1234;

        $licStatusArray = [
            'id' => $licStatus,
            'description' => $licStatusDesc
        ];

        $appStatus = 'app status';
        $appStatusDesc = 'app status desc';
        $formattedAppStatus = 'formatted app status';
        $appUrl = 'http://appURL';
        $appId = 5678;

        $appStatusArray = [
            'id' => $appStatus,
            'description' => $appStatusDesc
        ];

        $row = [
            'licId' => $licId,
            'licNo' => $licNo,
            'licStatus' => $licStatus,
            'licStatusDesc' => $licStatusDesc,
            'appStatus' => $appStatus,
            'appStatusDesc' => $appStatusDesc
        ];

        $row += $extraRowData;

        $statusService = m::mock(StatusHelper::class);
        $statusService->shouldReceive('__invoke')->with($licStatusArray)->once()->andReturn($formattedLicStatus);
        $statusService->shouldReceive('__invoke')
            ->with($appStatusArray)
            ->times($appTimes)
            ->andReturn($formattedAppStatus);

        $urlHelperService = m::mock(UrlHelper::class);
        $urlHelperService->shouldReceive('fromRoute')
            ->with('licence', ['licence' => $licId])
            ->once()
            ->andReturn($licUrl);
        $urlHelperService->shouldReceive('fromRoute')
            ->with('lva-application', ['application' => $appId])
            ->times($appTimes)
            ->andReturn($appUrl);

        $viewHelperManager = m::mock(ViewPluginManager::class);
        $viewHelperManager->shouldReceive('get')->with('status')->once()->andReturn($statusService);

        $sm = Bootstrap::getServiceManager();
        $sm->setService('Helper\Url', $urlHelperService);
        $sm->setService('ViewHelperManager', $viewHelperManager);

        $this->assertEquals($expected, LicenceApplication::format($row, [], $sm));
    }

    /**
     * data provider for testLicenceApplicationFormatter
     *
     * @return array
     */
    public function dpTestFormat()
    {
        $licenceLink = '<a class="govuk-link" href="http://licURL">OB1234567</a>formatted lic status';
        $appLink = '<a class="govuk-link" href="http://appURL">5678</a>formatted app status';

        return [
            [$licenceLink . '<br />' . $appLink, ['appId' => 5678], 1],
            [$licenceLink, [], 0],
        ];
    }
}
