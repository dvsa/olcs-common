<?php

namespace CommonTest\Service\Data;

use Common\Service\Data\ContactDetails;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;

/**
 * Class ContactDetails Test
 * @package CommonTest\Service
 */
class ContactDetailsTest extends MockeryTestCase
{
    public function testGetServiceName()
    {
        $sut = new ContactDetails();
        $this->assertEquals('ContactDetails', $sut->getServiceName());
    }

    public function testFormatData()
    {
        $source = $this->getSingleSource();
        $expected = $this->getSingleExpected();

        $sut = new ContactDetails();

        $this->assertEquals($expected, $sut->formatData($source));
    }

    /**
     * @dataProvider provideFetchListOptions
     * @param $input
     * @param $expected
     * @param $useGroups
     */
    public function testFetchListOptions($input, $expected, $useGroups)
    {
        $sut = new ContactDetails();
        $sut->setData('ContactDetails', $input);

        $this->assertEquals($expected, $sut->fetchListOptions('', $useGroups));
    }

    public function provideFetchListOptions()
    {
        return [
            [$this->getSingleSource(), $this->getSingleExpected(), false],
            [false, [], false],
            [$this->getSingleSource(), $this->getSingleExpected(), true],
        ];
    }

    /**
     * @dataProvider provideFetchListData
     * @param $data
     * @param $expected
     */
    public function testFetchListData($data, $expected)
    {
        $mockRestClient = m::mock('Common\Util\RestClient');
        $mockRestClient->shouldReceive('get')
            ->once()
            ->with('', ['limit' => 1000, 'bundle' => [], 'contactType'=>'ct_partner'])
            ->andReturn($data);

        $sut = new ContactDetails();
        $sut->setRestClient($mockRestClient);

        $this->assertEquals($expected, $sut->fetchListData('ct_partner'));
        $sut->fetchListData('ct_partner'); //ensure data is cached
    }

    public function provideFetchListData()
    {
        return [
            [false, false],
            [['Results' => $this->getSingleSource()], $this->getSingleSource()],
            [['some' => 'data'],  false]
        ];
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
    protected function getGroupsExpected()
    {
        $expected = [
            'B' => [
                'label' => 'Bee',
                'options' => [
                    'val-1' => 'Value 1',
                ],
            ],
            'A' => [
                'label' => 'Aye',
                'options' => [
                    'val-2' => 'Value 2',
                ],
            ],
            'C' => [
                'label' => 'Cee',
                'options' => [
                    'val-3' => 'Value 3',
                ],
            ]
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
            ['id' => 'val-3', 'description' => 'Value 3']
        ];
        return $source;
    }
}
