<?php

namespace CommonTest\Service\Table\Formatter;

use Common\Service\Table\Formatter\DataRetentionRuleLink;
use Mockery as m;

/**
 * DataRetentionRule Link test
 */
class DataRetentionRuleLinkTest extends \PHPUnit_Framework_TestCase
{
    public function testFormat()
    {
        $data = [
            'id' => 1,
            'description' => 'test',
        ];

        $sm = m::mock()
            ->shouldReceive('get')
            ->with('Helper\Url')
            ->andReturn(
                m::mock()
                    ->shouldReceive('fromRoute')
                    ->with(
                        'admin-dashboard/admin-data-retention/records',
                        ['dataRetentionRuleId' => 1]
                    )
                    ->andReturn('DATA_RETENTION_EDIT_URL')
                    ->getMock()
            )
            ->getMock();

        $this->assertEquals(
            '<a href="DATA_RETENTION_EDIT_URL" target="_self">test</a>',
            DataRetentionRuleLink::format($data, [], $sm)
        );
    }
}
