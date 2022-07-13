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

    public function setUp(): void
    {
        $this->sut = new $this->class;

        parent::setUp();
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
            ['<a class="govuk-link" href="http://URL">ACME Ltd</a>', $data, 'operator/business-details', ['organisation' => 452]],
        ];
    }

    public function testComplaintColumnNo()
    {
        $column = $this->sut->getColumns()[3];
        $row = [
            'complaint' => 'No'
        ];

        $this->assertSame('Complaint', $column['title']);
        $this->assertSame('No', $column['formatter']($row, [], null));
    }

    public function testComplaintColumnYes()
    {
        $mockHelperUrl = m::mock();
        $mockHelperUrl->shouldReceive('fromRoute')->with('licence/opposition', ['licence' => 123])->once()
            ->andReturn('URL');
        $mockServiceLocator = m::mock();
        $mockServiceLocator->shouldReceive('get')->with('Helper\Url')->once()->andReturn($mockHelperUrl);

        $column = $this->sut->getColumns()[3];
        $row = [
            'licId' => 123,
            'complaint' => 'Yes'
        ];

        $this->assertSame('Complaint', $column['title']);
        $this->assertSame('<a class="govuk-link" href="URL">Yes</a>', $column['formatter']($row, [], $mockServiceLocator));
    }

    public function testOppositionColumnNo()
    {
        $column = $this->sut->getColumns()[4];
        $row = [
            'opposition' => 'No'
        ];

        $this->assertSame('Opposition', $column['title']);
        $this->assertSame('No', $column['formatter']($row, [], null));
    }

    public function testOppositionColumnYes()
    {
        $mockHelperUrl = m::mock();
        $mockHelperUrl->shouldReceive('fromRoute')->with('licence/opposition', ['licence' => 123])->once()
            ->andReturn('URL');
        $mockServiceLocator = m::mock();
        $mockServiceLocator->shouldReceive('get')->with('Helper\Url')->once()->andReturn($mockHelperUrl);

        $column = $this->sut->getColumns()[4];
        $row = [
            'licId' => 123,
            'opposition' => 'Yes'
        ];

        $this->assertSame('Opposition', $column['title']);
        $this->assertSame('<a class="govuk-link" href="URL">Yes</a>', $column['formatter']($row, [], $mockServiceLocator));
    }
}
