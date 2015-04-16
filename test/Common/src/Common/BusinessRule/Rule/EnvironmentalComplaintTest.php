<?php

/**
 * Environmental Complaint Test
 */
namespace CommonTest\BusinessRule\Rule;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use CommonTest\Bootstrap;
use Common\BusinessRule\Rule\EnvironmentalComplaint;
use Common\Service\Entity\ComplaintEntityService;

/**
 * Environmental Complaint Test
 */
class EnvironmentalComplaintTest extends MockeryTestCase
{
    protected $sut;

    protected $sm;

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();

        $this->sut = new EnvironmentalComplaint();
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
            ->times(!empty($expected['closedDate']) ? 1: 0)
            ->andReturn('2015-01-01');

        $this->assertEquals($expected, $this->sut->validate($data));
    }

    public function providerValidate()
    {
        return [
            [
                [
                    'status' => ComplaintEntityService::COMPLAIN_STATUS_OPEN
                ],
                [
                    'status' => ComplaintEntityService::COMPLAIN_STATUS_OPEN,
                    'isCompliance' => false,
                    'closedDate' => null
                ]
            ],
            [
                [
                    'status' => ComplaintEntityService::COMPLAIN_STATUS_CLOSED,
                ],
                [
                    'status' => ComplaintEntityService::COMPLAIN_STATUS_CLOSED,
                    'isCompliance' => false,
                    'closedDate' => '2015-01-01'
                ]
            ]
        ];
    }
}
