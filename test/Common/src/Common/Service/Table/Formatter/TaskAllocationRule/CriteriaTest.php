<?php

namespace CommonTest\Service\Table\Formatter\TaskAllocationRule;

use Common\Service\Table\Formatter\TaskAllocationRule\Criteria;

/**
 * Criteria test
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class CriteriaTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test the format method
     *
     * @dataProvider provider
     */
    public function testFormat($expected, $data)
    {
        $sut = new Criteria();

        $this->assertSame($expected, $sut->format($data));
    }

    /**
     * Data provider
     *
     * @return array
     */
    public function provider()
    {
        return [
            // expected, data
            ['Goods, MLH', ['goodsOrPsv' => ['id' => 'lcat_gv'], 'isMlh' => true]],
            ['Goods, Non-MLH', ['goodsOrPsv' => ['id' => 'lcat_gv'], 'isMlh' => false]],
            ['N/A', ['goodsOrPsv' => ['id' => 'lcat_psv'], 'isMlh' => true]],
            ['N/A', ['goodsOrPsv' => ['id' => 'XXXX'], 'isMlh' => true]],
        ];
    }
}
