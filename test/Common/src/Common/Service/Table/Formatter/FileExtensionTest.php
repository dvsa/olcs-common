<?php

/**
 * File extension formatter test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */

namespace CommonTest\Service\Table\Formatter;

use Common\Service\Table\Formatter\FileExtension;

/**
 * File extension formatter test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class FileExtensionTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Test the format method
     *
     * @group Formatters
     * @group FileExtensionFormatter
     *
     * @dataProvider provider
     */
    public function testFormat($data, $column, $expected)
    {
        $this->assertEquals($expected, FileExtension::format($data, $column));
    }

    /**
     * Data provider
     *
     * @return array
     */
    public function provider()
    {
        return array(
            array(
                array('filename' => 'foo'), array(), ''
            ),
            array(
                array('filename' => 'foo.txt'), array(), 'TXT'
            ),
            array(
                array('filename' => 'foo.bar.zip'), array(), 'ZIP'
            ),
        );
    }
}
