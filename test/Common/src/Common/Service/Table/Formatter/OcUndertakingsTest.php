<?php

/**
 * OcConditionsTest.php
 */
namespace CommonTest\Service\Table\Formatter;

use Common\Service\Entity\ConditionUndertakingEntityService;
use Common\Service\Table\Formatter\OcUndertakings;
use Mockery as m;
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
     * @dataProvider testFormatDataProvider
     */
    public function testFormat($data, $conditions)
    {
        $this->assertEquals(OcUndertakings::format($data), $conditions);
    }

    public function testFormatDataProvider()
    {
        return array(
            array(
                array(
                    'undertakings' => array(
                        array('licence' => 1, 'conditionType' => ['id' => ConditionUndertakingEntityService::TYPE_UNDERTAKING]),
                        array('licence' => 1, 'conditionType' => ['id' => ConditionUndertakingEntityService::TYPE_UNDERTAKING]),
                        array('licence' => null, 'conditionType' => ['id' => ConditionUndertakingEntityService::TYPE_CONDITION]),
                        array('licence' => null, 'conditionType' => ['id' => ConditionUndertakingEntityService::TYPE_CONDITION])
                    )
                ),
                2
            )
        );
    }
}
