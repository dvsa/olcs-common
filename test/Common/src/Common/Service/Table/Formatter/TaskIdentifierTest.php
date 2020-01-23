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
class TaskIdentifierTest extends \PHPUnit\Framework\TestCase
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

        $sm = $this->createPartialMock('\stdClass', array('get'));

        $mockUrlHelper = $this->createPartialMock('\stdClass', array('fromRoute'));

        $mockUrlHelper->expects($this->any())
            ->method('fromRoute')
            ->with($routeName, $routeParams)
            ->will($this->returnValue('correctUrl'));

        $sm->expects($this->any())
            ->method('get')
            ->with('Helper\Url')
            ->will($this->returnValue($mockUrlHelper));

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
                ),
                array(),
                'lva-licence/overview',
                'licence',
                '<a href="correctUrl">P1234</a>'
            ),
            3 => array(
                array(
                    'linkDisplay' => 'P1234',
                    'linkType' => 'Licence',
                    'linkId' => 1,
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
                ),
                array(),
                'transport-manager/details',
                'transportManager',
                '<a href="correctUrl">1234</a>'
            ),
            10 => array(
                array(
                    'linkDisplay' => '1234',
                    'linkType' => '',
                    'linkId' => 1,
                ),
                array(),
                'transport-manager/details',
                'transportManager',
                '<a href="#">1234</a>'
            ),
            // Bus Registration
            11 => array(
                array(
                    'linkDisplay' => 'Unlinked',
                    'linkType' => 'Bus Registration',
                    'linkId' => null,
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
                ),
                array(),
                'case',
                'case',
                '<a href="#">1234</a>',
            ),
            // IRFO Organisation
            17 => array(
                array(
                    'linkDisplay' => 'Unlinked',
                    'linkType' => 'IRFO Organisation',
                    'linkId' => null,
                ),
                array(),
                'operator/business-details',
                'organisation',
                'Unlinked'
            ),
            18 => array(
                array(
                    'linkDisplay' => '1234',
                    'linkType' => 'IRFO Organisation',
                    'linkId' => 99,
                ),
                array(),
                'operator/business-details',
                'organisation',
                '<a href="correctUrl">1234</a>',
            ),
            19 => array(
                array(
                    'linkDisplay' => '1234',
                    'linkType' => '',
                    'linkId' => 99,
                ),
                array(),
                'operator/business-details',
                'organisation',
                '<a href="#">1234</a>',
            ),
            // Submission
            20 => array(
                array(
                    'linkDisplay' => 'Unlinked',
                    'linkType' => 'Submission',
                    'linkId' => null,
                    'caseId' => 5
                ),
                array(),
                'submission',
                'submission',
                'Unlinked',
                array('case' => 5, 'action' => 'details')
            ),
            21 => array(
                array(
                    'linkDisplay' => '1234/5',
                    'linkType' => 'Submission',
                    'linkId' => 99,
                    'caseId' => 5
                ),
                array(),
                'submission',
                'submission',
                '<a href="correctUrl">1234/5</a>',
                array('case' => 5, 'action' => 'details')
            ),
            22 => array(
                array(
                    'linkDisplay' => '1234/5',
                    'linkType' => '',
                    'linkId' => 99,
                    'caseId' => 5
                ),
                array(),
                'submission',
                'submission',
                '<a href="#">1234/5</a>',
                array('case' => 5, 'action' => 'details')
            ),
            // Permits
            23 => array(
                array(
                    'linkDisplay' => 'OG4569803/6',
                    'linkType' => 'ECMT Permit Application',
                    'linkId' => 6,
                    'licenceId' => 106,
                ),
                array(),
                'licence/irhp-application/application',
                'irhpAppId',
                '<a href="correctUrl">OG4569803/6</a>',
                array(
                    'irhpAppId' => 6,
                    'licence' => 106,
                    'action' => 'edit'
                )
            ),
        );
    }
}
