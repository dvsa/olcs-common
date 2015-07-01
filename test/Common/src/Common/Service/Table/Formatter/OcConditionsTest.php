<?php

/**
 * OcConditionsTest.php
 */
namespace CommonTest\Service\Table\Formatter;

use Common\Service\Entity\ConditionUndertakingEntityService;
use Common\Service\Table\Formatter\OcConditions;
use Mockery as m;

/**
 * Class OcComplaintsTest
 *
 * Formatter test.
 *
 * @package CommonTest\Service\Table\Formatter
 */
class OcConditionsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider testFormatDataProvider
     */
    public function testFormat($data, $conditions)
    {
        $this->assertEquals(OcConditions::format($data), $conditions);
    }

    public function testFormatDataProvider()
    {
        return array(
            array(
                array(
                    'conditions' => array(
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
