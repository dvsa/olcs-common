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
                'complaintDate' => 'complaintDate',
                'complainantContactDetails' => 'complainantContactDetails',
                'description' => 'description',
                'status' => ['id' => 'ecst_closed'],
            ],
            [
                'complaintDate' => 'complaintDate',
                'complainantContactDetails' => 'complainantContactDetails',
                'description' => 'description',
                'status' => ['id' => 'ecst_closed'],
            ],
            [
                'complaintDate' => 'complaintDate',
                'complainantContactDetails' => 'complainantContactDetails',
                'description' => 'description',
                'status' => ['id' => 'ecst_open'],
            ],
        ];
        $expected = [
            $cases[2],
            $cases[0],
            $cases[1],
        ];

        $result = $this->helper->sortCasesOpenClosed($cases);
        $this->assertEquals($expected, $result);
    }
}
