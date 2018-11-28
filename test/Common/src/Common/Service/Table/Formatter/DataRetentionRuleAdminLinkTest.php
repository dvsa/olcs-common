<?php

namespace CommonTest\Service\Table\Formatter;

use Common\Service\Table\Formatter\DataRetentionRuleAdminLink;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;

/**
 * DataRetentionRuleAdminLink test
 */
class DataRetentionRuleAdminLinkTest extends TestCase
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
                        'admin-dashboard/admin-data-retention/rule-admin',
                        ['action' => 'edit','id' => 1]
                    )
                    ->andReturn('DATA_RETENTION_RULE_EDIT_URL')
                    ->getMock()
            )
            ->getMock();

        $this->assertEquals(
            '<a href="DATA_RETENTION_RULE_EDIT_URL" class="js-modal-ajax">Test</a>',
            DataRetentionRuleAdminLink::format($data, [], $sm)
        );
    }
}
