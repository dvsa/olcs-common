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
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class TaskIdentifierTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Test link formatter
     * @group taskIdentifier
     * @dataProvider provider
     */
    public function testFormat(
        $data,
        $column,
        $routeName,
        $param,
        $expected,
        $routeParams = array()
    ) {

        $routeParams = array_merge($routeParams, [$param => $data['linkId']]);

        $sm = $this->getMock('\stdClass', array('get'));

        $mockUrlHelper = $this->getMock('\stdClass', array('__invoke'));

        $mockUrlHelper->expects($this->any())
            ->method('__invoke')
            ->with($routeName, $routeParams)
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

        $result = TaskIdentifier::format($data, $column, $sm);

        $this->assertEquals($expected, $result);
    }

    /**
     * Data provider
     *
     * @return array
     */
    public function provider()
    {
        return array(
            // Licence
            0 => array(
                array(
                    'linkDisplay' => 'Unlinked',
                    'linkType' => 'Licence',
                    'linkId' => null
                ),
                array(),
                'lva-licence/overview',
                'licence',
                'Unlinked'
            ),
            1 => array(
                array(
                    'linkDisplay' => 'P1234',
                    'linkType' => 'Licence',
                    'linkId' => 1,
                    'licenceCount' => 1
                ),
                array(),
                'lva-licence/overview',
                'licence',
                '<a href="correctUrl">P1234</a>'
            ),
            2 => array(
                array(
                    'linkDisplay' => 'P1234',
                    'linkType' => 'Licence',
                    'linkId' => 1,
                    'licenceCount' => 2
                ),
                array(),
                'lva-licence/overview',
                'licence',
                '<a href="correctUrl">P1234</a> (MLH)'
            ),
            3 => array(
                array(
                    'linkDisplay' => 'P1234',
                    'linkType' => 'Licence',
                    'linkId' => 1,
                    'licenceCount' => 1
                ),
                array(),
                'lva-licence/overview',
                'licence',
                '<a href="correctUrl">P1234</a>'
            ),
            4 => array(
                array(
                    'linkDisplay' => 'P1234',
                    'linkType' => '',
                    'linkId' => 1,
                    'licenceCount' => 1
                ),
                array(),
                'lva-licence/overview',
                'licence',
                '<a href="#">P1234</a>'
            ),
            // Application
            5 => array(
                array(
                    'linkDisplay' => 'Unlinked',
                    'linkType' => 'Application',
                    'linkId' => null
                ),
                array(),
                'lva-application/overview',
                'application',
                'Unlinked'
            ),
            6 => array(
                array(
                    'linkDisplay' => 'P1234',
                    'linkType' => 'Application',
                    'linkId' => 1,
                    'licenceCount' => 1
                ),
                array(),
                'lva-application/overview',
                'application',
                '<a href="correctUrl">P1234</a>'
            ),
            7 => array(
                array(
                    'linkDisplay' => 'P1234',
                    'linkType' => '',
                    'linkId' => 1,
                    'licenceCount' => 1
                ),
                array(),
                'lva-application/overview',
                'application',
                '<a href="#">P1234</a>'
            ),
            // Transport Manager
            8 => array(
                array(
                    'linkDisplay' => 'Unlinked',
                    'linkType' => 'Transport Manager',
                    'linkId' => null,
                    'licenceCount' => 0
                ),
                array(),
                'lva-application/overview',
                'application',
                'Unlinked'
            ),
            9 => array(
                array(
                    'linkDisplay' => '1234',
                    'linkType' => 'Transport Manager',
                    'linkId' => 1,
                    'licenceCount' => 0
                ),
                array(),
                'transport-manager',
                'transportManager',
                '<a href="correctUrl">1234</a>'
            ),
            10 => array(
                array(
                    'linkDisplay' => '1234',
                    'linkType' => '',
                    'linkId' => 1,
                    'licenceCount' => 0
                ),
                array(),
                'transport-manager',
                'transportManager',
                '<a href="#">1234</a>'
            ),
            // Bus Registration
            11 => array(
                array(
                    'linkDisplay' => 'Unlinked',
                    'linkType' => 'Bus Registration',
                    'linkId' => null,
                    'licenceCount' => 1
                ),
                array(),
                'licence/bus-details',
                'busRegId',
                'Unlinked'
            ),
            12 => array(
                array(
                    'linkDisplay' => 'P1234/123',
                    'linkType' => 'Bus Registration',
                    'linkId' => 99,
                    'licenceCount' => 1,
                    'licenceId' => 110
                ),
                array(),
                'licence/bus-details',
                'busRegId',
                '<a href="correctUrl">P1234/123</a>',
                array('licence' => 110) // additional route param needed
            ),
            13 => array(
                array(
                    'linkDisplay' => 'P1234/123',
                    'linkType' => '',
                    'linkId' => 99,
                    'licenceCount' => 1,
                    'licenceId' => 110
                ),
                array(),
                'licence/bus-details',
                'busRegId',
                '<a href="#">P1234/123</a>',
                array('licence' => 110)
            ),
            // Case
            14 => array(
                array(
                    'linkDisplay' => 'Unlinked',
                    'linkType' => 'Case',
                    'linkId' => null,
                    'licenceCount' => 1
                ),
                array(),
                'case',
                'case',
                'Unlinked'
            ),
            15 => array(
                array(
                    'linkDisplay' => '1234',
                    'linkType' => 'Case',
                    'linkId' => 99,
                    'licenceCount' => 1,
                ),
                array(),
                'case',
                'case',
                '<a href="correctUrl">1234</a>',
            ),
            16 => array(
                array(
                    'linkDisplay' => '1234',
                    'linkType' => '',
                    'linkId' => 99,
                    'licenceCount' => 1,
                ),
                array(),
                'case',
                'case',
                '<a href="#">1234</a>',
            ),
        );
    }
}
