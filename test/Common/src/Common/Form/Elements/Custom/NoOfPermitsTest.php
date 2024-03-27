<?php

/**
 * No of Permits Test
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */

namespace CommonTest\Form\Elements\Custom;

use Common\Form\Elements\Custom\NoOfPermits;
use PHPUnit\Framework\TestCase;

/**
 * No of Permits Test
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class NoOfPermitsTest extends TestCase
{
    /**
     * @dataProvider dpTestHasNonZeroValue
     */
    public function testHasNonZeroValue($value, $expectedResult): void
    {
        $sut = new NoOfPermits();
        $sut->setValue($value);

        $this->assertEquals($expectedResult, $sut->hasNonZeroValue());
    }

    public function dpTestHasNonZeroValue()
    {
        return [
            [0, false],
            [1, true],
            [2, true],
        ];
    }
}
