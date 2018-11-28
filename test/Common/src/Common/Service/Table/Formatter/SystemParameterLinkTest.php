<?php

/**
 * SystemParameter Link test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace CommonTest\Service\Table\Formatter;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;

use Common\Service\Table\Formatter\SystemParameterLink;

/**
 * SystemParameter Link test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class SystemParameterLinkTest extends TestCase
{

    public function testFormat()
    {
        $data = [
            'id' => 1
        ];

        $sm = m::mock()
            ->shouldReceive('get')
            ->with('Helper\Url')
            ->andReturn(
                m::mock()
                    ->shouldReceive('fromRoute')
                    ->with(
                        'admin-dashboard/admin-system-parameters',
                        [
                            'action' => 'edit',
                            'sp' => 1
                        ]
                    )
                    ->andReturn('SYSTEM_PARAMETER_EDIT_URL')
                    ->getMock()
            )
            ->getMock();

        $this->assertEquals(
            '<a href="SYSTEM_PARAMETER_EDIT_URL" class="js-modal-ajax">1</a>',
            SystemParameterLink::format($data, [], $sm)
        );
    }
}
