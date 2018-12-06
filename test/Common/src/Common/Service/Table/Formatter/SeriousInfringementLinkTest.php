<?php

/**
 * SeriousInfringementLinkTest.php
 */
namespace CommonTest\Service\Table\Formatter;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;

use Common\Service\Table\Formatter\SeriousInfringementLink;

/**
 * Class SeriousInfringementLinkTest
 *
 * @package CommonTest\Service\Table\Formatter
 */
class SeriousInfringementLinkTest extends TestCase
{
    public function testFormat()
    {
        $id = 69;
        $inputData = ['id' => $id];
        $route = 'case_penalty_applied';
        $routeParams = [
            'si' => $id,
            'action' => 'index'
        ];

        $sm = m::mock()
            ->shouldReceive('get')
            ->with('Helper\Url')
            ->andReturn(
                m::mock()
                    ->shouldReceive('fromRoute')
                    ->with($route, $routeParams, [], true)
                    ->andReturn('URL')
                    ->getMock()
            )
            ->getMock();

        $this->assertEquals(
            '<a href="URL">' . $id . '</a>',
            SeriousInfringementLink::format($inputData, [], $sm)
        );
    }
}
