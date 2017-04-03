<?php

namespace CommonTest\Service\Data;

use Common\Service\Data\ContactDetails;
use Common\Service\Entity\Exceptions\UnexpectedResponseException;
use Dvsa\Olcs\Transfer\Query\ContactDetail\ContactDetailsList as Qry;
use Mockery as m;

/**
 * @covers \Common\Service\Data\ContactDetails
 */
class ContactDetailsTest extends AbstractDataServiceTestCase
{
    /** @var ContactDetails  */
    private $sut;

    public function setUp()
    {
        $this->sut = new ContactDetails();

    }

    public function testFetchListData()
    {
        $results = ['results' => 'results'];
        $params = [
            'sort'  => 'description',
            'order' => 'ASC',
            'page'  => null,
            'limit' => null,
            'contactType' => 'unit_ContactType',
        ];

        $mockTransferAnnotationBuilder = m::mock()
            ->shouldReceive('createQuery')->once()->andReturnUsing(
                function (Qry $dto) use ($params) {
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
            ->shouldReceive('isOk')->andReturn(true)->once()
            ->shouldReceive('getResult')->andReturn($results)->once()
            ->getMock();

        $this->mockHandleQuery($this->sut, $mockTransferAnnotationBuilder, $mockResponse);

        $this->assertEquals($results['results'], $this->sut->fetchListData('unit_ContactType'));
    }

    public function testSetters()
    {
        static::assertNull($this->sut->getContactType());

        $this->sut->setContactType('unit_ContactType');

        static::assertEquals('unit_ContactType', $this->sut->getContactType());
    }

    public function testFetchListDataCache()
    {
        $data = [
            [
                'id' => 9999,
                'description'=> 'EXPECTED'
            ],
        ];
        $this->sut->setData('ContactDetails', $data);

        static::assertEquals([9999 => 'EXPECTED'], $this->sut->fetchListOptions());
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

        $this->mockHandleQuery($this->sut, $mockTransferAnnotationBuilder, $mockResponse);

        $this->sut->setContactType('unit_ContactType');
        $this->sut->fetchListData([]);
    }
}
