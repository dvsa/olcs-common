<?php

/**
 * LicenceNumberLinkTest.php
 */
namespace CommonTest\Service\Table\Formatter;

use Mockery as m;

use Common\Service\Table\Formatter\LicenceNumberLink;

/**
 * Class LicenceNumberLinkTest
 *
 * @package CommonTest\Service\Table\Formatter
 */
class LicenceNumberLinkTest extends \PHPUnit_Framework_TestCase
{
    public function testFormat()
    {
        $data = array(
            'licence' => array(
                'id' => 1,
                'licNo' => 0001
            )
        );

        $expected = '<a href="LICENCE_URL">1</a>';

        $sm = m::mock()
            ->shouldReceive('get')
            ->with('Helper\Url')
            ->andReturn(
                m::mock()
                    ->shouldReceive('fromRoute')
                    ->with(
                        'lva-licence',
                        array(
                            'licence' => $data['licence']['id']
                        )
                    )
                    ->andReturn('LICENCE_URL')
                    ->getMock()
            );

        $this->assertEquals($expected, LicenceNumberLink::format($data, array(), $sm->getMock()));
    }
}
