<?php

/**
 * Interim Vehicles Checkbox Formatter Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */

namespace CommonTest\Service\Table\Formatter;

use Common\Service\Table\Formatter\InterimVehiclesCheckbox;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Interim Vehicles Checkbox Formatter Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class InterimVehiclesCheckboxTest extends MockeryTestCase
{
    /**
     * Test formatter
     * 
     * @group interimFormatter
     * @dataProvider formatProvider
     */
    public function testFormat($data, $expected)
    {
        $this->assertEquals($expected, InterimVehiclesCheckbox::format($data));
    }

    public function formatProvider()
    {
        return [
            [
                [
                    'interimApplication' => ['id' => 2],
                    'id' => 1
                ],
                '<input type="checkbox" value="1" name="vehicles[id][]" checked>'
            ],
            [
                [
                    'interimApplication' => [],
                    'id' => 1
                ],
                '<input type="checkbox" value="1" name="vehicles[id][]" >'
            ],
            [
                [
                    'id' => 1
                ],
                '<input type="checkbox" value="1" name="vehicles[id][]" >'
            ],
        ];
    }
}
