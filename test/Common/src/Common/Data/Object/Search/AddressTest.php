<?php

namespace CommonTest\Data\Object\Search;

use Mockery as m;

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
    public function testLicenceFormatter($expected, $row, $route, $routeParams)
    {
        $column = [];
        $serviceLocator = m::mock();

        $serviceLocator->shouldReceive('get')->with('Helper\Url')->andReturn(
            m::mock()->shouldReceive('fromRoute')->with($route, $routeParams)->once()
                ->andReturn('http://URL')->getMock()
        );

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
                '<a href="http://URL">AB12345 / 33</a>',
                array_merge($data, ['appId' => 33]),
                'lva-application',
                ['application' => 33]
            ],
            ['<a href="http://URL">AB12345</a>', $data, 'licence', ['licence' => 123]],
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
            ['<a href="http://URL">ACME Ltd</a>', $data, 'operator', ['organisation' => 452]],
        ];
    }
}
