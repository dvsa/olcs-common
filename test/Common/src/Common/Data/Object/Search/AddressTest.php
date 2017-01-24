<?php

namespace CommonTest\Data\Object\Search;

use Mockery as m;
use Common\Service\Helper\UrlHelperService as UrlHelper;
use Common\View\Helper\Status as StatusHelper;
use Zend\View\HelperPluginManager as ViewPluginManager;

/**
 * Class AddressTest
 * @package CommonTest\Data\Object\Search
 */
class AddressTest extends SearchAbstractTest
{
    protected $class = 'Common\Data\Object\Search\Address';

    public function setUp()
    {
        $this->sut = new $this->class;

        parent::setUp();
    }

    /**
     * @dataProvider dpTestLicenceApplicationFormatter
     *
     * @param $expected
     * @param $appTimes
     * @param $extraRowData
     */
    public function testLicenceApplicationFormatter($expected, $extraRowData, $appTimes)
    {
        $column = [];
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

        $serviceLocator = m::mock();

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

        $serviceLocator->shouldReceive('get')->with('Helper\Url')->once()->andReturn($urlHelperService);
        $serviceLocator->shouldReceive('get')->with('ViewHelperManager')->once()->andReturn($viewHelperManager);

        $columns = $this->sut->getColumns();
        $this->assertSame($expected, $columns[0]['formatter']($row, $column, $serviceLocator));
    }

    /**
     * data provider for testLicenceApplicationFormatter
     *
     * @return array
     */
    public function dpTestLicenceApplicationFormatter()
    {
        $licenceLink = '<a href="http://licURL">OB1234567</a>formatted lic status';
        $appLink = '<a href="http://appURL">5678</a>formatted app status';

        return [
            [$licenceLink . '<br />' . $appLink, ['appId' => 5678], 1],
            [$licenceLink, [], 0],
        ];
    }

    /**
     * @dataProvider dpTestOperatorFormatter
     */
    public function testOperatorFormatter($expected, $row)
    {
        $column = [];
        $serviceLocator = m::mock();

        $serviceLocator->shouldReceive('get')->with('Helper\Url')->andReturn(
            m::mock()->shouldReceive('fromRoute')->andReturn('http://URL')->getMock()
        );

        $columns = $this->sut->getColumns();
        $this->assertSame($expected, $columns[1]['formatter']($row, $column, $serviceLocator));
    }

    public function dpTestOperatorFormatter()
    {
        $data = [
            'licId' => 123,
            'licNo' => 'AB12345',
            'orgId' => '452',
            'orgName' => 'ACME Ltd',
        ];

        return [
            // expected, row, route, routeParams
            ['<a href="http://URL">ACME Ltd</a>', $data, 'operator/business-details', ['organisation' => 452]],
        ];
    }
}
