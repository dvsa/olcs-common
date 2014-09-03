<?php

/**
 * Task identifier formatter tests
 *
 * @author Nick payne <nick.payne@valtech.co.uk>
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */

namespace CommonTest\Service\Table\Formatter;

use Common\Service\Table\Formatter\TaskIdentifier;

/**
 * Task identifier formatter tests
 *
 * @author Nick payne <nick.payne@valtech.co.uk>
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class TaskIdentifierTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Test link formatter
     * 
     * @dataProvider provider
     */
    public function testFormat($data, $column, $expected)
    {
        $sm = $this->getMock('\stdClass', array('get'));

        $mockUrlHelper = $this->getMock('\stdClass', array('__invoke'));

        $mockUrlHelper->expects($this->any())
            ->method('__invoke')
            ->with('licence/details/overview', array('licence' => $data['linkId']))
            ->will($this->returnValue('correctUrl'));

        $mockViewHelperManager = $this->getMock('\stdClass', array('get'));

        $mockViewHelperManager->expects($this->any())
            ->method('get')
            ->with('url')
            ->will($this->returnValue($mockUrlHelper));

        $sm->expects($this->any())
            ->method('get')
            ->with('viewhelpermanager')
            ->will($this->returnValue($mockViewHelperManager));

        $this->assertEquals($expected, TaskIdentifier::format($data, $column, $sm));
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
                array(
                    'linkDisplay' => 'Unlinked',
                    'linkType' => 'Licence',
                    'linkId' => 1
                ),
                array(),
                'Unlinked'
            ),
            array(
                array(
                    'linkDisplay' => 'P1234',
                    'linkType' => 'Licence',
                    'linkId' => 1,
                    'licenceCount' => 1
                ),
                array(),
                '<a href="correctUrl">P1234</a>'
            ),
            array(
                array(
                    'linkDisplay' => 'P1234',
                    'linkType' => 'Licence',
                    'linkId' => 1,
                    'licenceCount' => 2
                ),
                array(),
                '<a href="correctUrl">P1234</a> (MLH)'
            ),
            array(
                array(
                    'linkDisplay' => 'P1234',
                    'linkType' => 'Licence',
                    'linkId' => 1,
                    'licenceCount' => 1
                ),
                array(),
                '<a href="correctUrl">P1234</a>'
            ),
            array(
                array(
                    'linkDisplay' => 'P1234',
                    'linkType' => '',
                    'linkId' => 1,
                    'licenceCount' => 1
                ),
                array(),
                '<a href="#">P1234</a>'
            )
        );
    }
}
