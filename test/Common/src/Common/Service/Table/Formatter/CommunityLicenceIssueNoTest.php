<?php

/**
 * Community Licence Issue No Formetter Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace CommonTest\Service\Table\Formatter;

use Common\Service\Table\Formatter\CommunityLicenceIssueNo;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;

/**
 * Community Licence Issue No Formetter Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CommunityLicenceIssueNoTest extends MockeryTestCase
{
    /**
     * @dataProvider formatProvider
     */
    public function testFormat($data, $column, $expected)
    {
        $sm = m::mock();

        $this->assertEquals($expected, CommunityLicenceIssueNo::format($data, $column, $sm));
    }

    public function formatProvider()
    {
        return [
            [
                [
                    'issueNo' => 0
                ],
                [
                    'name' => 'issueNo'
                ],
                '0 (Office copy)'
            ],
            [
                [
                    'issueNo' => 1
                ],
                [
                    'name' => 'issueNo'
                ],
                '1'
            ],
            [
                [
                    'foo' => 0
                ],
                [
                    'name' => 'foo'
                ],
                '0 (Office copy)'
            ]
        ];
    }
}
