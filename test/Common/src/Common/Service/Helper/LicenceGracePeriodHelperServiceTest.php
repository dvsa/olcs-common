<?php

/**
 * LicenceGracePeriodHelperServiceTest.php
 */
namespace CommonTest\Service\Helper;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;

use CommonTest\Bootstrap;

use Common\Service\Helper\LicenceGracePeriodHelperService;

/**
 * Class LicenceGracePeriodHelperServiceTest
 *
 * LicenceGracePeriodHelperService test.
 *
 * @package CommonTest\Service\Helper
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
class LicenceGracePeriodHelperServiceTest extends MockeryTestCase
{
    protected $sut = null;

    protected $sm = null;

    protected function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();
        $this->sut = new LicenceGracePeriodHelperService();
        $this->sut->setServiceLocator($this->sm);
    }

    /**
     * @dataProvider isActiveProvider
     */
    public function testIsActive($data, $expected)
    {
        $this->sm->shouldReceive('get')->with('Helper\Date')->andReturn(
            m::mock()
                ->shouldReceive('getDateObject')
                ->andReturn(
                    new \DateTime($data['todaysDate']),
                    new \DateTime($data['startDate']),
                    new \DateTime($data['endDate'])
                )
                ->getMock()
        );

        $this->assertEquals($expected, $this->sut->isActive($data));
    }

    public function testIsActiveThrowsException()
    {
        $this->setExpectedException('InvalidArgumentException');

        $this->sut->isActive(array());
    }

    // PROVIDERS

    public function isActiveProvider()
    {
        return array(
            array(
                array(
                'todaysDate' => '2015-02-01',
                'startDate' => '2015-01-01',
                'endDate' => '2015-03-01'
                ),
                true
            ),
            array(
                array(
                    'todaysDate' => '2015-04-01',
                    'startDate' => '2015-01-01',
                    'endDate' => '2015-03-01'
                ),
                false
            )
        );
    }
}
