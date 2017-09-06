<?php

namespace CommonTest\Service\Table\Formatter;

use Common\Service\Table\Formatter\DataRetentionRecordCheckbox;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Data Retention Record Checkbox Formatter Test
 */
class DataRetentionRecordCheckboxTest extends MockeryTestCase
{
    /**
     * Test formatter
     *
     * @dataProvider formatProvider
     */
    public function testFormat($data, $expected)
    {
        $this->assertEquals($expected, DataRetentionRecordCheckbox::format($data));
    }

    public function formatProvider()
    {
        return [
            'action_confirmation is set to true' => [
                ['actionConfirmation' => true, 'id' => 1],
                '<input type="checkbox" value="1" name="id[]" checked>'
            ],
            'action_confirmation is set to false' => [
                ['actionConfirmation' => false, 'id' => 1],
                '<input type="checkbox" value="1" name="id[]" >'
            ],
            'no action_confirmation available' => [
                ['id' => 1],
                '<input type="checkbox" value="1" name="id[]" >'
            ],
        ];
    }
}
