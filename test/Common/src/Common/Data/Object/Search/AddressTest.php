<?php

namespace CommonTest\Data\Object\Search;

use Mockery as m;
use Common\Service\Helper\UrlHelperService;

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
     * @dataProvider dpTestLicenceFormatter
     */
    public function testLicenceFormatter($expected, $row, $appTimes)
    {
        $column = [];
        $serviceLocator = m::mock();

        $urlHelperService = m::mock(UrlHelperService::class);
        $urlHelperService->shouldReceive('fromRoute')->with('licence', ['licence' => 123])->once()
            ->andReturn('http://licURL');
        $urlHelperService->shouldReceive('fromRoute')->with('lva-application', ['application' => 33])->times($appTimes)
            ->andReturn('http://appURL');

        $serviceLocator->shouldReceive('get')->with('Helper\Url')->andReturn($urlHelperService);

        $columns = $this->sut->getColumns();
        $this->assertSame($expected, $columns[0]['formatter']($row, $column, $serviceLocator));
    }

    public function dpTestLicenceFormatter()
    {
        $data = [
            'licId' => 123,
            'licNo' => 'AB12345',
            'orgId' => '452',
            'orgName' => 'ACME Ltd',
        ];

        return [
            // expected, row, route, routeParams
            [
                '<a href="http://licURL">AB12345</a> / <a href="http://appURL">33</a>',
                array_merge($data, ['appId' => 33]),
                1
            ],
            ['<a href="http://licURL">AB12345</a>', $data, 0],
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
        $this->assertSame($expected, $columns[2]['formatter']($row, $column, $serviceLocator));
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
