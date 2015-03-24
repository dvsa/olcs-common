<?php

/**
 * Task Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\BusinessRule\Rule;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use CommonTest\Bootstrap;
use Common\BusinessRule\Rule\Task;

/**
 * Task Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class TaskTest extends MockeryTestCase
{
    protected $sut;

    protected $sm;

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();

        $this->sut = new Task();
        $this->sut->setServiceLocator($this->sm);
    }

    /**
     * @dataProvider providerValidate
     */
    public function testValidate($data, $expected)
    {
        $mockDateHelper = m::mock();
        $this->sm->setService('Helper\Date', $mockDateHelper);
        $mockDateHelper->shouldReceive('getDate')
            ->andReturn('2015-01-01');

        $this->assertEquals($expected, $this->sut->validate($data));
    }

    public function providerValidate()
    {
        return [
            [
                [
                    'foo' => 'bar',
                    'actionDate' => '2013-01-01'
                ],
                [
                    'foo' => 'bar',
                    'actionDate' => '2013-01-01'
                ]
            ],
            [
                [
                    'foo' => 'bar'
                ],
                [
                    'foo' => 'bar',
                    'actionDate' => '2015-01-01'
                ]
            ]
        ];
    }
}
