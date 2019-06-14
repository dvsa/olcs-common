<?php

/**
 * LicenceNumberLinkTest.php
 */

namespace CommonTest\Service\Table\Formatter;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;

use Common\Service\Table\Formatter\InternalLicenceNumberLink;

/**
 * Class LicenceNumberLinkTest
 *
 * @package CommonTest\Service\Table\Formatter
 */
class InternalLicenceNumberLinkTest extends TestCase
{
    public function testFormat()
    {
        $licence = [
            'licence' => [
                'id' => 1,
                'licNo' => 0001,
            ]
        ];

        $sm = m::mock()
            ->shouldReceive('get')
            ->with('Helper\Url')
            ->andReturn(
                m::mock()
                    ->shouldReceive('fromRoute')
                    ->with(
                        'lva-licence',
                        [
                            'licence' => $licence['licence']['id']
                        ]
                    )
                    ->andReturn('LICENCE_URL')
                    ->getMock()
            );
        $expected = '<a href="LICENCE_URL" title="Licence details for 1">1</a>';
        $this->assertEquals($expected, InternalLicenceNumberLink::format($licence, array(), $sm->getMock()));
    }
}
