<?php

/**
 * OcConditionsTest.php
 */
namespace CommonTest\Service\Table\Formatter;

use Common\RefData;
use Common\Service\Table\Formatter\OcUndertakings;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;

/**
 * Class  OcUndertakingsTest
 *
 * Formatter test.
 *
 * @package CommonTest\Service\Table\Formatter
 */
class OcUndertakingsTest extends TestCase
{
    /**
     * @dataProvider dpFormatDataProvider
     */
    public function testFormat($data, $conditions)
    {
        $this->assertEquals(OcUndertakings::format($data), $conditions);
    }

    public function dpFormatDataProvider()
    {
        return array(
            array(
                array(
                    'undertakings' => array(
                        array('licence' => 1, 'conditionType' => ['id' => RefData::TYPE_UNDERTAKING]),
                        array('licence' => 1, 'conditionType' => ['id' => RefData::TYPE_UNDERTAKING]),
                        array('licence' => null, 'conditionType' => ['id' => RefData::TYPE_CONDITION]),
                        array('licence' => null, 'conditionType' => ['id' => RefData::TYPE_CONDITION])
                    )
                ),
                2
            )
        );
    }
}
