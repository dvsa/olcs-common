<?php

namespace CommonTest\Service\Table\Formatter;

use Common\Service\Table\Formatter\NullableNumber;
use Olcs\TestHelpers\MockeryTestCase;
/**
 * Class NullableNumberTest
 *
 * Formatter test.
 *
 * @package CommonTest\Service\Table\Formatter
 */
class NullableNumberTest extends MockeryTestCase
{
    /**
     * Test the format method
     *
     * @group Formatters
     * @group NullableNumberFormatter
     *
     * @dataProvider provider
     */
    public function testFormat($data)
    {
        $this->assertEquals($data['expected'], NullableNumber::format($data, ['name' => 'permitsRequired']));
    }

    public function provider()
    {
        return array(
            array(
                [
                    'permitsRequired' => null,
                    'expected' => 0
                ],
            ),
            array(
                [
                    'permitsRequired' => 3,
                    'expected' => 3
                ],
            ),
        );
    }
}
