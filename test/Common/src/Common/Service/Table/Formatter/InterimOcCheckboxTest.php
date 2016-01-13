<?php

/**
 * Interim OC Checkbox Formatter Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */

namespace CommonTest\Service\Table\Formatter;

use Common\Service\Table\Formatter\InterimOcCheckbox;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Interim OC Checkbox Formatter Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class MockeryTestCaseTest extends MockeryTestCase
{
    /**
     * Test formatter
     * 
     * @group interimFormatter
     * @dataProvider formatProvider
     */
    public function testFormat($data, $expected)
    {
        $this->assertEquals($expected, InterimOcCheckbox::format($data));
    }

    public function formatProvider()
    {
        return [
            [
                [
                    'isInterim' => 'Y',
                    'id' => 1
                ],
                '<input type="checkbox" value="1" name="operatingCentres[id][]" checked>'
            ],
            [
                [
                    'isInterim' => 'N',
                    'id' => 1
                ],
                '<input type="checkbox" value="1" name="operatingCentres[id][]" >'
            ],
            [
                [
                    'id' => 1
                ],
                '<input type="checkbox" value="1" name="operatingCentres[id][]" >'
            ],
        ];
    }
}
