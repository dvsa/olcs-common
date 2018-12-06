<?php

/**
 * Opposition Helper Service Test
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace CommonTest\Service\Helper;

use Common\Service\Helper\OppositionHelperService;
use PHPUnit_Framework_TestCase;

/**
 * Opposition Helper Service Test
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class OppositionHelperServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * Setup the helper
     */
    public function setUp()
    {
        $this->helper = new OppositionHelperService();
    }

    /**
     * test sortCasesOpenClosed
     */
    public function testSortCasesOpenClosed()
    {
        $oppositions = [
            ['case' => ['closedDate' => null]],
            ['case' => ['closedDate' => '2015-03-27']],
            ['case' => ['closedDate' => null]],
            ['case' => ['closedDate' => '2015-03-25']],
        ];

        $result = $this->helper->sortOpenClosed($oppositions);
        $this->assertEquals(
            [$oppositions[0], $oppositions[2], $oppositions[1], $oppositions[3]],
            $result
        );
    }
}
