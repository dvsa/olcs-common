<?php

namespace CommonTest\Service\Entity;

use Common\Service\Entity\LicenceStatusRuleEntityService;

/**
 * Class LicenceStatusRuleEntityServiceTest
 * @package CommonTest\Service\Entity
 */
class LicenceStatusRuleEntityServiceTest extends AbstractEntityServiceTestCase
{
    protected $sut = null;

    protected function setUp()
    {
        $this->sut = new LicenceStatusRuleEntityService();

        parent::setUp();
    }

    public function testCreateStatusForLicence()
    {
        $this->expectOneRestCall(
            'LicenceStatusRule',
            'POST',
            array(
                'licence' => 1,
                'licenceStatus' => null,
                'startDate' => null,
                'endDate' => null,
                'startProcessedDate' => null,
                'endProcessedDate' => null,
            )
        )->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->createStatusForLicence(1));
    }

    public function testGetStatusesForLicence()
    {
        $this->expectOneRestCall(
            'LicenceStatusRule',
            'GET',
            array(
                'licenceStatus' => array()
            )
        )->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->getStatusesForLicence());
    }

    public function testRemoveStatusesForLicence()
    {
        $this->expectOneRestCall(
            'LicenceStatusRule',
            'DELETE',
            array(
                'id' => null
            )
        )->will($this->returnValue('RESPONSE'));

        $this->assertEquals(null, $this->sut->removeStatusesForLicence());
    }

    public function testGetLicencesToRevokeCurtailSuspend()
    {
        $mockDate = \Mockery::mock('StdClass');
        $mockDate->shouldReceive('getDate')->andReturn('2015-03-25');
        $this->sm->setService('Helper\Date', $mockDate);

        $this->restHelper->expects($this->once())
            ->method('makeRestCall')
            ->will($this->returnValue(['Results' => [1,2,3]]));

        $results = $this->sut->getLicencesToRevokeCurtailSuspend();
        $this->assertEquals([1,2,3], $results);
    }

    public function testGetLicencesToValid()
    {
        $mockDate = \Mockery::mock('StdClass');
        $mockDate->shouldReceive('getDate')->andReturn('2015-03-25');
        $this->sm->setService('Helper\Date', $mockDate);

        $this->restHelper->expects($this->once())
            ->method('makeRestCall')
            ->will($this->returnValue(['Results' => [1,2,3]]));

        $results = $this->sut->getLicencesToValid();
        $this->assertEquals([1,2,3], $results);
    }
}
