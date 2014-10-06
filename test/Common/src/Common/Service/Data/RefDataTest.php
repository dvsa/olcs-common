<?php

namespace CommonTest\Service\Data;

use Common\Service\Data\RefData;

/**
 * Class RefDataTest
 * @package CommonTest\Service
 */
class RefDataTest extends \PHPUnit_Framework_TestCase
{
    public function testGetServiceName()
    {
        $sut = new RefData();
        $this->assertEquals('RefData', $sut->getServiceName());
    }

    public function testFormatData()
    {
        $source = $this->getSingleSource();
        $expected = $this->getSingleExpected();

        $sut = new RefData();

        $this->assertEquals($expected, $sut->formatData($source));
    }

    public function testFormatDataForGroups()
    {
        $source = $this->getGroupSource();
        $expected = $this->getGroupExpected();

        $sut = new RefData();

        $this->assertEquals($expected, $sut->formatDataForGroups($source));
    }

    public function testfetchListData()
    {
        $mockRestClient = $this->getMock('\Common\Util\RestClient', [], [], '', 0);
        $mockRestClient->expects($this->once())->method('get')->with($this->equalTo('/test'))->willReturn([]);

        $sut = new RefData();
        $sut->setRestClient($mockRestClient);

        $this->assertEquals([], $sut->fetchListData('test'));
    }

    public function testCreateService()
    {
        $mockTranslator = $this->getMock('stdClass', ['getLocale']);
        $mockTranslator->expects($this->once())->method('getLocale')->willReturn('en_GB');

        $mockRestClient = $this->getMock('\Common\Util\RestClient', [], [], '', 0);
        $mockRestClient->expects($this->once())->method('setLanguage')->with($this->equalTo('en_GB'));

        $mockApiResolver = $this->getMock('stdClass', ['getClient']);
        $mockApiResolver
            ->expects($this->once())
            ->method('getClient')
            ->with($this->equalTo('RefData'))
            ->willReturn($mockRestClient);

        $mockSl = $this->getMock('\Zend\ServiceManager\ServiceManager');
        $mockSl->expects($this->any())
               ->method('get')
               ->willReturnMap(
                   [
                       ['translator', true, $mockTranslator],
                       ['ServiceApiResolver', true, $mockApiResolver],
                   ]
               );

        $sut = new RefData();
        $service = $sut->createService($mockSl);

        $this->assertInstanceOf('\Common\Service\Data\RefData', $service);
        $this->assertSame($mockRestClient, $service->getRestClient());
    }

    /**
     * @dataProvider provideFetchListOptions
     */
    public function testFetchListOptions($source, $expected, $useGroups)
    {
        $sut = new RefData();
        $sut->setData('test', $source);

        $this->assertEquals($expected, $sut->fetchListOptions('test', $useGroups));
    }

    public function provideFetchListOptions()
    {
        return [
            [false, [], false],
            [$this->getSingleSource(), $this->getSingleExpected(), false],
            [$this->getGroupSource(), $this->getGroupExpected(), true],
        ];
    }

    /**
     * @return array
     */
    protected function getGroupExpected()
    {
        $expected = array (
            'parent' => array (
                'label' => 'Parent',
                'options' => array (),
            ),
            'p1' => array (
                'options' => array (
                  'val-1' => 'Value 1',
                ),
            ),
            'p2' => array (
                'options' => array (
                  'val-2' => 'Value 2',
                ),
            ),
            'p3' => array (
                'options' => array (
                  'val-3' => 'Value 3',
                ),
            ),
        );
        return $expected;
    }

    /**
     * @return array
     */
    protected function getSingleExpected()
    {
        $expected = [
            'val-1' => 'Value 1',
            'val-2' => 'Value 2',
            'val-3' => 'Value 3',
        ];
        return $expected;
    }

    /**
     * @return array
     */
    protected function getSingleSource()
    {
        $source = [
            ['id' => 'val-1', 'description' => 'Value 1'],
            ['id' => 'val-2', 'description' => 'Value 2'],
            ['id' => 'val-3', 'description' => 'Value 3'],
        ];
        return $source;
    }

    /**
     * @return array
     */
    protected function getGroupSource()
    {
        $source = [
            ['id' => 'parent', 'description' => 'Parent'],
            ['id' => 'val-1', 'description' => 'Value 1', 'parent' => ['id'=>'p1', 'description'=>'d1']],
            ['id' => 'val-2', 'description' => 'Value 2', 'parent' => ['id'=>'p2', 'description'=>'d2']],
            ['id' => 'val-3', 'description' => 'Value 3', 'parent' => ['id'=>'p3', 'description'=>'d3']],
        ];
        return $source;
    }
}
