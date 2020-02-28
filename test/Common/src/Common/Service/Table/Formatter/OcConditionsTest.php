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
        $this->assertEquals(OcConditions::format($data), $conditions);
    }

    public function dpFormatDataProvider()
    {
        return array(
            array(
                array(
                    'conditions' => array(
                        array('licence' => null, 'conditionType' => ['id' => RefData::TYPE_UNDERTAKING]),
                        array('licence' => null, 'conditionType' => ['id' => RefData::TYPE_UNDERTAKING]),
                        array('licence' => 1, 'conditionType' => ['id' => RefData::TYPE_CONDITION]),
                        array('licence' => 1, 'conditionType' => ['id' => RefData::TYPE_CONDITION])
                    )
                ),
                2
            )
        );
    }
}
