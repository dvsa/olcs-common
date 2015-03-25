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
}
