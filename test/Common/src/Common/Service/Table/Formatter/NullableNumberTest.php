<?php

namespace CommonTest\Common\Service\Table\Formatter;

use Common\Service\Table\Formatter\NullableNumber;
use Mockery\Adapter\Phpunit\MockeryTestCase;

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
        $this->assertEquals($data['expected'], (new NullableNumber())->format($data, ['name' => 'permitsRequired']));
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
