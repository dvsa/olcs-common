<?php

namespace OlcsTest\Service\Data;

use Common\Service\Data\TransportManager;

/**
 * Class TransportManagerTest
 * @package OlcsTest\Service\Data
 */
class TransportManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testGetBundle()
    {
        $sut = new TransportManager();
        $this->assertInternalType('array', $sut->getBundle());
    }

    public function testSetId()
    {
        $sut = new TransportManager();
        $sut->setId(78);
        $this->assertEquals(78, $sut->getId());
    }

    public function testGetId()
    {
        $sut = new TransportManager();
        $this->assertNull($sut->getId());
    }

    public function testFetchTmData()
    {
        $tmData = ['id' => 78];

        $mockRestClient = $this->getMock('\Common\Util\RestClient', [], [], '', false);
        $mockRestClient->expects($this->once())
            ->method('get')
            ->with($this->equalTo('/78'), $this->isType('array'))
            ->willReturn($tmData);

        $sut = new TransportManager();
        $sut->setRestClient($mockRestClient);

        $this->assertEquals($tmData, $sut->fetchTmData(78));
        //test data is cached
        $this->assertEquals($tmData, $sut->fetchTmData(78));
    }
}
