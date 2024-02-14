<?php

namespace CommonTest\Common\Controller\Lva\Adapters;

use Common\Controller\Lva\Adapters\AbstractTransportManagerAdapter;
use Common\Service\Cqrs\Command\CommandService;
use Common\Service\Cqrs\Query\CachingQueryService;
use Dvsa\Olcs\Transfer\Util\Annotation\AnnotationBuilder as TransferAnnotationBuilder;
use Psr\Container\ContainerInterface;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Abstract Transport Manager Adapter Test
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class AbstractTransportManagerAdapterTest extends MockeryTestCase
{
    /** @var  \CommonTest\Common\Controller\Lva\Adapters\StubAbstractTransportManagerAdapter */
    protected $sut;
    /** @var  ContainerInterface|\Mockery\MockInterface */
    protected $container;

    protected function setUp(): void
    {
        $this->container = m::mock(ContainerInterface::class);

        /** @var TransferAnnotationBuilder $mockAnnotationBuilder */
        $mockAnnotationBuilder = m::mock(TransferAnnotationBuilder::class);
        /** @var CachingQueryService $mockQuerySrv */
        $mockQuerySrv = m::mock(CachingQueryService::class);
        /** @var CommandService $mockCommandSrv */
        $mockCommandSrv = m::mock(CommandService::class);

        $this->sut = new StubAbstractTransportManagerAdapter(
            $mockAnnotationBuilder,
            $mockQuerySrv,
            $mockCommandSrv,
            $this->container
        );
    }

    public function testGetNumberOfRows()
    {
        $this->assertEquals(2, $this->sut->getNumberOfRows(888, 999));
    }

    public function testGetTable()
    {
        $mockTable = m::mock(\stdClass::class);
        $this->container->shouldReceive('get->prepareTable')->once()->with('template')->andReturn($mockTable);

        static::assertEquals($mockTable, $this->sut->getTable('template'));
    }

    public function testMustHaveAtLeastOneTm()
    {
        static::assertFalse($this->sut->mustHaveAtLeastOneTm());
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testAddMessages()
    {
        // no assertion as its a no op
        $this->sut->addMessages(99);
    }

    /**
     * @dataProvider dataProviderTestMapResultForTable
     */
    public function testMapResultForTable($licTms, $appTms, $expect)
    {
        $actual = $this->sut->mapResultForTable($appTms, $licTms);

        static::assertEquals($expect, $actual);
    }

    public function dataProviderTestMapResultForTable()
    {
        $expectedName = [
            'familyName' => 'unit_FamilyName',
            'forename' => 'unit_Forename',
        ];
        $expectedStatus = [
            'id' => 'unit_TmAppStatusId',
            'description' => 'unit_TmAppStatusDesc'
        ];
        return [
            [
                'licTms' => [
                    [
                        'id' => 201,
                        'tmid' => 70001,
                        'birthDate' => 'unit_BirthDate',
                        'forename' => 'unit_Forename',
                        'familyName' => 'unit_FamilyName',
                        'emailAddress' => 'unit_LicEmail'
                    ],
                ],
                'appTms' => [
                    [
                        'id' => 101,
                        'action' => 'unit_Action',
                        'tmid' => 80001,
                        'tmasid' => 'unit_TmAppStatusId',
                        'tmasdesc' => 'unit_TmAppStatusDesc',
                        'birthDate' => 'unit_BirthDate',
                        'forename' => 'unit_Forename',
                        'familyName' => 'unit_FamilyName',
                        'emailAddress' => 'unit_AppEmail'
                    ],
                ],
                'expect' => [
                    '80001a' => [
                        'id' => 101,
                        'name' => $expectedName,
                        'status' => $expectedStatus,
                        'email' => 'unit_AppEmail',
                        'dob' => 'unit_BirthDate',
                        'transportManager' => ['id' => 80001],
                        'action' => 'unit_Action',
                    ],
                    '70001' => [
                        'id' => 'L201',
                        'name' => $expectedName,
                        'status' => null,
                        'email' => 'unit_LicEmail',
                        'dob' => 'unit_BirthDate',
                        'transportManager' => ['id' => 70001],
                        'action' => 'E',
                    ],
                ],
            ],
            //  test status of existing manager in licence is changed to 'C'
            [
                'licTms' => [
                    [
                        'id' => 301,
                        'tmid' => 8888,
                        'birthDate' => 'unit_BirthDate',
                        'forename' => 'unit_Forename',
                        'familyName' => 'unit_FamilyName',
                        'emailAddress' => 'unit_LicEmail'
                    ],
                ],
                'appTms' => [
                    [
                        'id' => 101,
                        'action' => 'unit_Action',
                        'tmid' => 8888,
                        'tmasid' => 'unit_TmAppStatusId',
                        'tmasdesc' => 'unit_TmAppStatusDesc',
                        'birthDate' => 'unit_BirthDate',
                        'forename' => 'unit_Forename',
                        'familyName' => 'unit_FamilyName',
                        'emailAddress' => 'unit_AppEmail',
                        'action' => 'U'
                    ],
                ],
                'expect' => [
                    '8888' => [
                        'id' => 'L301',
                        'name' => $expectedName,
                        'status' => null,
                        'email' => 'unit_LicEmail',
                        'dob' => 'unit_BirthDate',
                        'transportManager' => ['id' => 8888],
                        'action' => 'C',
                    ],
                    '8888a' => [
                        'id' => 101,
                        'name' => $expectedName,
                        'status' => $expectedStatus,
                        'email' => 'unit_AppEmail',
                        'dob' => 'unit_BirthDate',
                        'transportManager' => ['id' => 8888],
                        'action' => 'U',
                    ],
                ],
            ],
            //  test remove original if status of manager in application is 'D'
            [
                'licTms' => [
                    [
                        'id' => 301,
                        'tmid' => 8888,
                        'birthDate' => 'unit_BirthDate',
                        'forename' => 'unit_Forename',
                        'familyName' => 'unit_FamilyName',
                        'emailAddress' => 'unit_LicEmail'
                    ],
                ],
                'appTms' => [
                    [
                        'id' => 101,
                        'action' => 'unit_Action',
                        'tmid' => 8888,
                        'tmasid' => 'unit_TmAppStatusId',
                        'tmasdesc' => 'unit_TmAppStatusDesc',
                        'birthDate' => 'unit_BirthDate',
                        'forename' => 'unit_Forename',
                        'familyName' => 'unit_FamilyName',
                        'emailAddress' => 'unit_AppEmail',
                        'action' => 'D'
                    ],
                ],
                'expect' => [
                    '8888a' => [
                        'id' => 101,
                        'name' => $expectedName,
                        'status' => $expectedStatus,
                        'email' => 'unit_AppEmail',
                        'dob' => 'unit_BirthDate',
                        'transportManager' => ['id' => 8888],
                        'action' => 'D',
                    ],
                ],
            ]
        ];
    }

    /**
     * @dataProvider dataProviderTestSortResultForTable
     */
    public function testSortResultForTable($method, $data, $expect)
    {
        $actual = $this->sut->sortResultForTable($data, $method);

        static::assertEquals($expect, $actual);
    }

    public function dataProviderTestSortResultForTable()
    {
        return [
            //  test sorting method by Last and First name
            [
                'method' => AbstractTransportManagerAdapter::SORT_LAST_FIRST_NAME,
                'data' => [
                    [
                        'name' => [
                            'familyName' => 'Xlast',
                            'forename' => 'Afirst',
                        ],
                    ],
                    [
                        'name' => [
                            'familyName' => 'Alast',
                            'forename' => 'Bfirst',
                        ],
                    ],
                    [
                        'name' => [
                            'familyName' => 'Alast',
                            'forename' => 'Cfirst',
                        ],
                    ],
                ],
                'expect' => [
                    [
                        'name' => [
                            'familyName' => 'Alast',
                            'forename' => 'Bfirst',
                        ],
                    ],
                    [
                        'name' => [
                            'familyName' => 'Alast',
                            'forename' => 'Cfirst',
                        ],
                    ],
                    [
                        'name' => [
                            'familyName' => 'Xlast',
                            'forename' => 'Afirst',
                        ],
                    ],
                ],
            ],
            //  test sorting method by Last and First name, and all new items to the end and sorted by add date
            [
                'method' => AbstractTransportManagerAdapter::SORT_LAST_FIRST_NAME_NEW_AT_END,
                'data' => [
                    [
                        'name' => [
                            'familyName' => 'Xlast',
                            'forename' => 'Afirst',
                        ],
                        'action' => 'A',
                    ],
                    [
                        'name' => [
                            'familyName' => 'Alast',
                            'forename' => 'Bfirst',
                        ],
                        'action' => 'A',
                    ],
                    [
                        'name' => [
                            'familyName' => 'Alast',
                            'forename' => 'Cfirst',
                        ],
                        'action' => 'X',
                    ],
                ],
                'expect' => [
                    [
                        'name' => [
                            'familyName' => 'Alast',
                            'forename' => 'Cfirst',
                        ],
                        'action' => 'X',
                    ],
                    [
                        'name' => [
                            'familyName' => 'Alast',
                            'forename' => 'Bfirst',
                        ],
                        'action' => 'A',
                    ],
                    [
                        'name' => [
                            'familyName' => 'Xlast',
                            'forename' => 'Afirst',
                        ],
                        'action' => 'A',
                    ],
                ],
            ],
        ];
    }
}
