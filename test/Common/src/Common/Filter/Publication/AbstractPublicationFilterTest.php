<?php

namespace CommonTest\Filter\Publication;

use Common\Filter\Publication\AbstractPublicationFilter;
use Common\Data\Object\Publication;
use Mockery as m;

/**
 * Class AbstractPublicationFilterTest
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class AbstractPublicationFilterTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Tests the filter
     */
    public function testFilter()
    {
        $value = 'value';
        $sut = new AbstractPublicationFilter();
        $this->assertEquals($value, $sut->filter($value));
    }

    /**
     * Tests the merge data function
     */
    public function testMergeData()
    {
        $existingData = [
            'test1' => 'test1 data',
        ];

        $newData = [
            'test2' => 'test2 data',
            'test3' => 'test3 data'
        ];

        $expectedData = [
            'test1' => 'test1 data',
            'test2' => 'test2 data',
            'test3' => 'test3 data'
        ];

        $publication = new Publication($existingData);
        $sut = new AbstractPublicationFilter();
        $output = $sut->mergeData($publication, $newData);

        $this->assertEquals($expectedData, $output->getArrayCopy());
    }
}
