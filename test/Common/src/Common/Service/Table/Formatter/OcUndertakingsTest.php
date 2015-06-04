<?php

/**
 * OcConditionsTest.php
 */
namespace CommonTest\Service\Table\Formatter;

use Common\Service\Entity\ConditionUndertakingEntityService;
use Common\Service\Table\Formatter\OcUndertakings;
use Mockery as m;

/**
 * Class  OcUndertakingsTest
 *
 * Formatter test.
 *
 * @package CommonTest\Service\Table\Formatter
 */
class OcUndertakingsTest extends \PHPUnit_Framework_TestCase
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
                        array('condition_type' => ConditionUndertakingEntityService::TYPE_UNDERTAKING),
                        array('condition_type' => ConditionUndertakingEntityService::TYPE_UNDERTAKING),
                        array('condition_type' => ConditionUndertakingEntityService::TYPE_CONDITION),
                        array('condition_type' => ConditionUndertakingEntityService::TYPE_CONDITION)
                    )
                ),
                2
            )
        );
    }
}
