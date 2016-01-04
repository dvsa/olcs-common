<?php

namespace CommonTest\Service\Data;

use Common\Service\Data\ContactDetails;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;
use Dvsa\Olcs\Transfer\Query\ContactDetail\ContactDetailsList as Qry;
use Common\Service\Entity\Exceptions\UnexpectedResponseException;

/**
 * Class ContactDetails Test
 * @package CommonTest\Service
 */
class ContactDetailsTest extends AbstractDataServiceTestCase
{
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

    public function testFetchListData()
    {
        $results = ['results' => 'results'];
        $params = [
            'sort'  => 'description',
            'order' => 'ASC',
            'page'  => 1,
            'limit' => 1000,
            'contactType' => 'ct_partner'
        ];
        $dto = Qry::create($params);
        $mockTransferAnnotationBuilder = m::mock()
            ->shouldReceive('createQuery')->once()->andReturnUsing(
                function ($dto) use ($params) {
                    $this->assertEquals($params['sort'], $dto->getSort());
                    $this->assertEquals($params['order'], $dto->getOrder());
                    $this->assertEquals($params['page'], $dto->getPage());
                    $this->assertEquals($params['limit'], $dto->getLimit());
                    $this->assertEquals($params['contactType'], $dto->getContactType());
                    return 'query';
                }
            )
            ->once()
            ->getMock();

        $mockResponse = m::mock()
            ->shouldReceive('isOk')
            ->andReturn(true)
            ->once()
            ->shouldReceive('getResult')
            ->andReturn($results)
            ->twice()
            ->getMock();

        $sut = new ContactDetails();
        $this->mockHandleQuery($sut, $mockTransferAnnotationBuilder, $mockResponse, $results);

        $this->assertEquals($results['results'], $sut->fetchListData('ct_partner'));
    }

    public function testFetchListDataWithException()
    {
        $this->setExpectedException(UnexpectedResponseException::class);
        $mockTransferAnnotationBuilder = m::mock()
            ->shouldReceive('createQuery')->once()->andReturn('query')->getMock();

        $mockResponse = m::mock()
            ->shouldReceive('isOk')
            ->andReturn(false)
            ->once()
            ->getMock();
        $sut = new ContactDetails();
        $this->mockHandleQuery($sut, $mockTransferAnnotationBuilder, $mockResponse, []);

        $sut->fetchListData([]);
    }
}
