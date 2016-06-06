<?php

namespace CommonTest\Data\Object\Search;

/**
 * Class PublicationSelfserveTest
 * @package CommonTest\Data\Object\Search
 */
class PublicationSelfserveTest extends SearchAbstractTest
{
    protected $class = 'Common\Data\Object\Search\PublicationSelfserve';

    private $sut;

    public function setUp()
    {
        $this->sut = new $this->class();

        parent::setUp();
    }

    public function testGetDateRanges()
    {
        $dateRanges = $this->sut->getDateRanges();

        $this->assertCount(2, $dateRanges);

        $this->assertInstanceOf('Common\Data\Object\Search\Aggregations\DateRange\PublishedDateFrom', $dateRanges[0]);
        $this->assertInstanceOf('Common\Data\Object\Search\Aggregations\DateRange\PublishedDateTo', $dateRanges[1]);
    }

    public function testGetFilters()
    {
        $filters = $this->sut->getFilters();

        $this->assertCount(5, $filters);

        $this->assertInstanceOf('Common\Data\Object\Search\Aggregations\Terms\LicenceType', $filters[0]);
        $this->assertInstanceOf('Common\Data\Object\Search\Aggregations\Terms\TrafficArea', $filters[1]);
        $this->assertInstanceOf('Common\Data\Object\Search\Aggregations\Terms\PublicationType', $filters[2]);
        $this->assertInstanceOf('Common\Data\Object\Search\Aggregations\Terms\DocumentStatus', $filters[3]);
        $this->assertInstanceOf('Common\Data\Object\Search\Aggregations\Terms\PublicationSection', $filters[4]);

    }
}
