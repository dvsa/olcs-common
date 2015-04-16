<?php

/**
 * Complaints Helper Service Test
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace CommonTest\Service\Helper;

use Common\Service\Helper\ComplaintsHelperService;
use PHPUnit_Framework_TestCase;

/**
 * Complaints Helper Service Test
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class ComplaintsHelperServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * Setup the helper
     */
    public function setUp()
    {
        $this->helper = new ComplaintsHelperService();
    }

    /**
     * test sortCasesOpenClosed
     */
    public function testSortCasesOpenClosed()
    {
        $cases = [
            [
                'id' => 545,
                'complaints' => [
                    [
                        'complaintDate' => 'complaintDate',
                        'complainantContactDetails' => 'complainantContactDetails',
                        'ocComplaints' => 'ocComplaints',
                        'description' => 'description',
                        'status' => ['id' => 'ecst_closed'],
                    ]
                ]
            ],
            [
                'id' => 123,
                'complaints' => [
                    [
                        'complaintDate' => 'complaintDate',
                        'complainantContactDetails' => 'complainantContactDetails',
                        'ocComplaints' => 'ocComplaints',
                        'description' => 'description',
                        'status' => ['id' => 'ecst_open'],
                    ]
                ]
            ],
        ];
        $expected = [
            [
                'caseId' => 123,
                'complaintDate' => 'complaintDate',
                'complainantContactDetails' => 'complainantContactDetails',
                'ocComplaints' => 'ocComplaints',
                'description' => 'description',
                'status' => ['id' => 'ecst_open'],
            ],
            [
                'caseId' => 545,
                'complaintDate' => 'complaintDate',
                'complainantContactDetails' => 'complainantContactDetails',
                'ocComplaints' => 'ocComplaints',
                'description' => 'description',
                'status' => ['id' => 'ecst_closed'],
            ]
        ];

        $result = $this->helper->sortCasesOpenClosed($cases);
        $this->assertEquals($expected, $result);
    }
}
