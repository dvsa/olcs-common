<?php

namespace CommonTest\Controller\Lva\Adapters;

use Common\Controller\Lva\Adapters\AbstractTransportManagerAdapter;
use Common\Service\Cqrs\Command\CommandService;
use Common\Service\Cqrs\Query\CachingQueryService;
use Dvsa\Olcs\Transfer\Util\Annotation\AnnotationBuilder as TransferAnnotationBuilder;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\ServiceManager\ServiceManager;

/**
 * Abstract Transport Manager Adapter Test
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class AbstractTransportManagerAdapterTest extends MockeryTestCase
{
    /** @var  TestClass */
    protected $sut;
    /** @var  ServiceManager|\Mockery\MockInterface */
    protected $sm;

    protected function setUp()
    {
        $this->sm = m::mock(ServiceManager::class)->makePartial();
        $this->sm->setAllowOverride(true);

        /** @var TransferAnnotationBuilder $mockAnnotationBuilder */
        $mockAnnotationBuilder = m::mock(TransferAnnotationBuilder::class);
        /** @var CachingQueryService $mockQuerySrv */
        $mockQuerySrv = m::mock(CachingQueryService::class);
        /** @var CommandService $mockCommandSrv */
        $mockCommandSrv = m::mock(CommandService::class);

        $this->sut = new TestClass(
            $mockAnnotationBuilder, $mockQuerySrv, $mockCommandSrv
        );
        $this->sut->setServiceLocator($this->sm);
    }

    public function testGetTable()
    {
        $mockTable = m::mock(\stdClass::class);
        $this->sm->shouldReceive('get->prepareTable')->once()->with('template')->andReturn($mockTable);

        static::assertEquals($mockTable, $this->sut->getTable('template'));
    }

    public function testMustHaveAtLeastOneTm()
    {
        static::assertFalse($this->sut->mustHaveAtLeastOneTm());
    }

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
        $person = [
            'familyname' => 'unit_FamilyName',
            'foreName' => 'unit_ForeName',
            'birthDate' => 'unit_BirthDate',
        ];

        $appMng = [
            'id' => 80001,
            'homeCd' => [
                'person' => $person,
                'emailAddress' => 'unit_AppEmail',
            ],
        ];

        $licMng = [
            'id' => 70001,
            'homeCd' => [
                'person' => $person,
                'emailAddress' => 'unit_LicEmail',
            ],
        ];

        return [
            [
                'licTms' => [
                    [
                        'id' => 201,
                        'transportManager' => $licMng,
                    ],
                ],
                'appTms' => [
                    [
                        'id' => 101,
                        'transportManager' => $appMng,
                        'action' => 'unit_Action',
                        'tmApplicationStatus' => 'unit_TmAppStatus',
                    ],
                ],
                'expect' => [
                    '80001a' => [
                        'id' => 101,
                        'name' => $person,
                        'status' => 'unit_TmAppStatus',
                        'email' => 'unit_AppEmail',
                        'dob' => 'unit_BirthDate',
                        'transportManager' => $appMng,
                        'action' => 'unit_Action',
                    ],
                    '70001' => [
                        'id' => 'L201',
                        'name' => $person,
                        'status' => null,
                        'email' => 'unit_LicEmail',
                        'dob' => 'unit_BirthDate',
                        'transportManager' => $licMng,
                        'action' => 'E',
                    ],
                ],
            ],
            //  test status of existing manager in licence is changed to 'C'
            [
                'licTms' => [
                    [
                        'id' => 301,
                        'transportManager' => ['id' => 8888] + $licMng,
                    ],
                ],
                'appTms' => [
                    [
                        'id' => 101,
                        'transportManager' => ['id' => 8888] + $appMng,
                        'action' => 'U',
                        'tmApplicationStatus' => 'unit_TmAppStatus',
                    ],
                ],
                'expect' => [
                    '8888' => [
                        'id' => 'L301',
                        'name' => $person,
                        'status' => null,
                        'email' => 'unit_LicEmail',
                        'dob' => 'unit_BirthDate',
                        'transportManager' => ['id' => 8888] + $licMng,
                        'action' => 'C',
                    ],
                    '8888a' => [
                        'id' => 101,
                        'name' => $person,
                        'status' => 'unit_TmAppStatus',
                        'email' => 'unit_AppEmail',
                        'dob' => 'unit_BirthDate',
                        'transportManager' => ['id' => 8888] + $appMng,
                        'action' => 'U',
                    ],
                ],
            ],
            //  test remove original if status of manager in application is 'D'
            [
                'licTms' => [
                    [
                        'id' => 301,
                        'transportManager' => ['id' => 8888] + $licMng,
                    ],
                ],
                'appTms' => [
                    [
                        'id' => 101,
                        'transportManager' => ['id' => 8888] + $appMng,
                        'action' => 'D',
                        'tmApplicationStatus' => 'unit_TmAppStatus',
                    ],
                ],
                'expect' => [
                    '8888a' => [
                        'id' => 101,
                        'name' => $person,
                        'status' => 'unit_TmAppStatus',
                        'email' => 'unit_AppEmail',
                        'dob' => 'unit_BirthDate',
                        'transportManager' => ['id' => 8888] + $appMng,
                        'action' => 'D',
                    ],
                ],
            ],
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

/**
 * Test Class for testing abstract class
 */
class TestClass extends AbstractTransportManagerAdapter
{
    protected $tableSortMethod = null;

    public function mapResultForTable(array $applicationTms, array $licenceTms = [])
    {
        return parent::mapResultForTable($applicationTms, $licenceTms);
    }

    public function sortResultForTable(array $data, $method = null)
    {
        return parent::sortResultForTable($data, $method);
    }

    public function getTableData($applicationId, $licenceId)
    {
    }

    public function delete(array $ids, $applicationId)
    {
    }
}
