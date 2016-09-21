<?php

namespace CommonTest\Service\Table\Formatter;

use Common\Service\Table\Formatter\RefData;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * @covers Common\Service\Table\Formatter\RefData
 */
class RefDataTest extends MockeryTestCase
{
    public function testFormat()
    {
        $mockSm = m::mock(\Zend\ServiceManager\ServiceLocatorInterface::class);
        $mockSm->shouldReceive('get->translate')->with('unit_TextKey')->andReturn('EXPECT');

        $result = RefData::format(
            [
                'statusField' => [
                    'id' => 'status_unknown',
                    'description' => 'unit_TextKey',
                ],
            ],
            [
                'name' => 'statusField',
                'formatter' => 'RefData',
            ],
            $mockSm
        );

        static::assertEquals('EXPECT', $result);
    }
}
