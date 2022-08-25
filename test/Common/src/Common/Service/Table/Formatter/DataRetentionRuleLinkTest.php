<?php

namespace CommonTest\Service\Table\Formatter;

use Common\Service\Table\Formatter\DataRetentionRuleLink;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;

/**
 * DataRetentionRule Link test
 */
class DataRetentionRuleLinkTest extends TestCase
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
                        'admin-dashboard/admin-data-retention/review/records',
                        ['dataRetentionRuleId' => 1]
                    )
                    ->andReturn('DATA_RETENTION_EDIT_URL')
                    ->getMock()
            )
            ->getMock();

        $this->assertEquals(
            '<a class="govuk-link" href="DATA_RETENTION_EDIT_URL" target="_self">Test</a>',
            DataRetentionRuleLink::format($data, [], $sm)
        );
    }
}
