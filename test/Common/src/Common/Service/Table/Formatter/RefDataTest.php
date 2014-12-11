<?php


namespace CommonTest\Service\Table\Formatter;

use PHPUnit_Framework_TestCase as TestCase;
use Common\Service\Table\Formatter\RefData;

/**
 * Class RefDataTest
 * @package CommonTest\Service\Table\Formatter
 */
class RefDataTest extends TestCase
{
    public function testFormat()
    {
        $sut = new RefData();
        $result = $sut->format(
            ['statusField' => ['id' => 'status_unknown', 'description' => 'Unknown']],
            ['name' => 'statusField', 'formatter' => 'RefData']
        );

        $this->assertEquals('Unknown', $result);
    }
}
