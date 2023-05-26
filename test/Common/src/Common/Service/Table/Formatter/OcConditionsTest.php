<?php

/**
 * OcConditionsTest.php
 */

namespace CommonTest\Service\Table\Formatter;

use Common\RefData;
use Common\Service\Table\Formatter\OcConditions;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;

/**
 * Class OcConditionsTest
 *
 * Formatter test.
 *
 * @package CommonTest\Service\Table\Formatter
 */
class OcConditionsTest extends TestCase
{
    /**
     * @dataProvider dpFormatDataProvider
     */
    public function testFormat($data, $conditions)
    {
        $this->assertEquals((new OcConditions())->format($data), $conditions);
    }

    public function dpFormatDataProvider()
    {
        return [
            [
                [
                    'conditions' => [
                        ['licence' => null, 'conditionType' => ['id' => RefData::TYPE_UNDERTAKING]],
                        ['licence' => null, 'conditionType' => ['id' => RefData::TYPE_UNDERTAKING]],
                        ['licence' => 1, 'conditionType' => ['id' => RefData::TYPE_CONDITION]],
                        ['licence' => 1, 'conditionType' => ['id' => RefData::TYPE_CONDITION]]
                    ]
                ],
                2
            ]
        ];
    }
}
