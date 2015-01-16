<?php

namespace OlcsTest\Service\Data;

use Common\Service\Data\Licence;

/**
 * Class LicenceTest
 * @package OlcsTest\Service\Data
 */
class LicenceTest extends \PHPUnit_Framework_TestCase
{
    public function testGetBundle()
    {
        $sut = new Licence();
        $this->assertInternalType('array', $sut->getBundle());
    }

    public function testSetId()
    {
        $sut = new Licence();
        $sut->setId(78);
        $this->assertEquals(78, $sut->getId());
    }

    public function testGetId()
    {
        $sut = new Licence();
        $this->assertNull($sut->getId());
    }

    public function testFetchLicenceData()
    {
        $licence = ['id' => 78, 'isNi' => true, 'trafficArea' => ['id' => 'B']];

        $mockRestClient = $this->getMock('\Common\Util\RestClient', [], [], '', false);
        $mockRestClient->expects($this->once())
            ->method('get')
            ->with($this->equalTo('/78'), $this->isType('array'))
            ->willReturn($licence);

        $sut = new Licence();
        $sut->setRestClient($mockRestClient);

        $this->assertEquals($licence, $sut->fetchLicenceData(78));
        //test data is cached
        $this->assertEquals($licence, $sut->fetchLicenceData(78));
    }

    public function testGetAddressBundle()
    {
        $sut = new Licence();
        $addressBundle = $sut->getAddressBundle();
        $this->assertArrayHasKey('correspondenceCd', $addressBundle['children']);
        $this->assertArrayHasKey(
            'address',
            $addressBundle['children']['correspondenceCd']['children']
        );

        $this->assertInternalType('array', $addressBundle);
    }

    public function testFetchAddressListData()
    {
        $licence = [
            'id' => 110,
            'correspondenceCd' => [
                'address' => 'c_address'
            ],
            'establishmentCd' => [
                'address' => 'e_address'
            ],
            'transportConsultantCd' => [
                'address' => 'tc_address'
            ]
        ];

        $sut = new Licence();

        $mockRestClient = $this->getMock('\Common\Util\RestClient', [], [], '', false);
        $mockRestClient->expects($this->once())
            ->method('get')
            ->with($this->equalTo('/110'), $this->isType('array'))
            ->willReturn($licence);

        $sut->setRestClient($mockRestClient);

        $result = $sut->fetchAddressListData(110);

        $this->assertCount(3, $result);
    }

    public function testFetchOperatingCentreData()
    {
        $licence = [
            'id' => 110
        ];

        $sut = new Licence();

        $mockRestClient = $this->getMock('\Common\Util\RestClient', [], [], '', false);
        $mockRestClient->expects($this->once())
            ->method('get')
            ->with($this->equalTo('/110'), $this->isType('array'))
            ->willReturn($licence);

        $sut->setRestClient($mockRestClient);
        $result = $sut->fetchOperatingCentreData(110);

        $this->assertEquals($result, $licence);
    }
}
