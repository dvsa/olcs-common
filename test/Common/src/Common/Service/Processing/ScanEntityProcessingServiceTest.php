<?php

/**
 * Scan Entity Processing Service Test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace CommonTest\Service\Processing;

use CommonTest\Bootstrap;
use Common\Service\Processing\ScanEntityProcessingService;
use Common\Service\Data\CategoryDataService;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;

/**
 * Scan Entity Processing Service Test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class ScanEntityProcessingServiceTest extends MockeryTestCase
{
    protected $sm;
    protected $sut;

    protected function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();
        $this->sm->setAllowOverride(true);

        $this->sut = new ScanEntityProcessingService();
        $this->sut->setServiceLocator($this->sm);
    }

    public function testFindEntityForCategory()
    {
        $licenceMock = m::mock()
            ->shouldReceive('findByIdentifier')
            ->with(123)
            ->andReturn('FakeEntity')
            ->getMock();

        $this->sm->setService('Entity\Licence', $licenceMock);

        $this->assertEquals(
            'FakeEntity',
            $this->sut->findEntityForCategory(
                1,
                123
            )
        );
    }

    public function testFindEntityForCategoryWithLicenceSubEntity()
    {
        $licenceMock = m::mock()
            ->shouldReceive('findByIdentifier')
            ->with(123)
            ->andReturn(
                [
                    'foo' => 'bar',
                    'licence' => [
                        'licNo' => 1234
                    ]
                ]
            )
            ->getMock();

        $this->sm->setService('Entity\Licence', $licenceMock);

        $this->assertEquals(
            [
                'foo' => 'bar',
                'licence' => [
                    'licNo' => 1234
                ],
                'licNo' => 1234
            ],
            $this->sut->findEntityForCategory(
                1,
                123
            )
        );
    }

    public function testFindEntityNameForCategory()
    {
        $this->assertEquals(
            'Licence',
            $this->sut->findEntityNameForCategory(1)
        );
    }

    /**
     * @dataProvider getChildrenDataProvider
     */
    public function testGetChildrenForCategory($category, $expectedData)
    {
        $entity = [
            'id' => 123,
            'licence' => [
                'id' => 456
            ]
        ];

        $this->assertEquals(
            $expectedData,
            $this->sut->getChildrenForCategory($category, $entity)
        );
    }

    public function getChildrenDataProvider()
    {
        return [
            [CategoryDataService::CATEGORY_APPLICATION, ['licence' => 123]],
            [CategoryDataService::CATEGORY_LICENSING, ['licence' => 123]],
            [CategoryDataService::CATEGORY_ENVIRONMENTAL, ['licence' => 123]],
            [CategoryDataService::CATEGORY_COMPLIANCE, ['case' => 123]],
            [CategoryDataService::CATEGORY_IRFO, ['organisation' => 123]],
            [CategoryDataService::CATEGORY_TRANSPORT_MANAGER, ['transportManager' => 123]],
            [
                CategoryDataService::CATEGORY_BUS_REGISTRATION,
                [
                    'busReg' => 123,
                    'licence' => 456
                ]
            ],
            [123456789, []]
        ];
    }

    public function testExtractChildrenFromEntity()
    {
        $scanMock = m::mock()
            ->shouldReceive('getChildRelations')
            ->andReturn(['foo', 'bar', 'baz'])
            ->getMock();

        $this->sm->setService('Entity\Scan', $scanMock);

        $expected = [
            'foo' => 1,
            'baz' => 3
        ];
        $this->assertEquals(
            $expected,
            $this->sut->extractChildrenFromEntity(
                [
                    'foo' => ['id' => 1],
                    'baz' => ['id' => 3]
                ]
            )
        );
    }
}
