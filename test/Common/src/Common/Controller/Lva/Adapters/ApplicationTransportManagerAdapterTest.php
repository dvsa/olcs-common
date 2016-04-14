<?php

namespace CommonTest\Controller\Lva\Adapters;

use Common\Controller\Lva\Adapters\ApplicationTransportManagerAdapter;
use Common\Service\Cqrs\Command\CommandService;
use Common\Service\Cqrs\Query\CachingQueryService;
use Common\Service\Entity\LicenceEntityService;
use Dvsa\Olcs\Transfer\Util\Annotation\AnnotationBuilder as TransferAnnotationBuilder;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Application Transport Manager Adapter Test
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class ApplicationTransportManagerAdapterTest extends MockeryTestCase
{
    /** @var  ApplicationTransportManagerAdapter */
    protected $sut;
    /** @var \Zend\ServiceManager\ServiceManager|\Mockery\MockInterface */
    protected $sm;
    /** @var  \Zend\Mvc\Controller\AbstractController */
    protected $controller;

    public function setUp()
    {
        $this->sm = m::mock(\Zend\ServiceManager\ServiceManager::class)->makePartial();
        $this->sm->setAllowOverride(true);

        $this->controller = m::mock(\Zend\Mvc\Controller\AbstractController::class);

        /** @var TransferAnnotationBuilder $mockAnnotationBuilder */
        $mockAnnotationBuilder = m::mock(TransferAnnotationBuilder::class);
        /** @var CachingQueryService $mockQuerySrv */
        $mockQuerySrv = m::mock(CachingQueryService::class);
        /** @var CommandService $mockCommandSrv */
        $mockCommandSrv = m::mock(CommandService::class);

        $this->sut = new ApplicationTransportManagerAdapter(
            $mockAnnotationBuilder, $mockQuerySrv, $mockCommandSrv
        );
        $this->sut->setServiceLocator($this->sm);
        $this->sut->setController($this->controller);
    }

    public function testGetTableData()
    {
        $this->markTestIncomplete();

        $mockTableService = m::mock();
        $this->sm->setService('Table', $mockTableService);

        $mockTable = m::mock();
        $mockTableService->shouldReceive('prepareTable')->with('lva-transport-manangers')->andReturn($mockTable);

        $mockTmaEntityService = m::mock('StdClass');
        $this->sm->setService('Entity\TransportManagerApplication', $mockTmaEntityService);

        $tmaData = [
            'Results' => [
                [
                    'id' => 333,
                    'transportManager' => [
                        'homeCd' => [
                            'person' => [
                                'name' => 'fred',
                                'birthDate' => '2015-04-02'
                            ],
                            'emailAddress' => 'bob@example.com',
                        ]
                    ],
                    'tmApplicationStatus' => 'status'
                ],
            ]
        ];

        $mockTmaEntityService->shouldReceive('getByApplicationWithHomeContactDetails')
            ->once()
            ->with(44)
            ->andReturn($tmaData);

        $expectedData = [
            [
                'id' => 333,
                'name' => [
                    'name' => 'fred',
                    'birthDate' => '2015-04-02'
                ],
                'status' => 'status',
                'email' => 'bob@example.com',
                'dob' => '2015-04-02',
                'transportManager' => [
                    'homeCd' => [
                        'person' => [
                            'name' => 'fred',
                            'birthDate' => '2015-04-02'
                        ],
                        'emailAddress' => 'bob@example.com',
                    ]
                ],
            ]
        ];

        $this->assertEquals($expectedData, $this->sut->getTableData(44, null));
    }

    /**
     * @dataProvider mustHaveAtLeastOneTmData
     */
    public function testMustHaveAtLeastOneTm($expected, $licenceType)
    {
        $this->markTestIncomplete();

        $mockApplicationEntityService = m::mock();
        $this->sm->setService('Entity\Application', $mockApplicationEntityService);

        $applicationData['licenceType']['id'] = $licenceType;
        $mockApplicationEntityService->shouldReceive('getLicenceType')->once()->with(44)->andReturn($applicationData);

        $this->assertEquals($expected, $this->sut->mustHaveAtLeastOneTm());
    }

    public function mustHaveAtLeastOneTmData()
    {
        return [
            [false, 'foo'],
            [false, LicenceEntityService::LICENCE_TYPE_RESTRICTED],
            [false, LicenceEntityService::LICENCE_TYPE_SPECIAL_RESTRICTED],
            [true, LicenceEntityService::LICENCE_TYPE_STANDARD_INTERNATIONAL],
            [true, LicenceEntityService::LICENCE_TYPE_STANDARD_NATIONAL],
        ];
    }

    public function testDelete()
    {
        $this->markTestIncomplete();

        $mockBusinessService = m::mock('StdClass');
        $this->sm->shouldReceive('get->get')
            ->once()
            ->with('Lva\DeleteTransportManagerApplication')
            ->andReturn($mockBusinessService);

        $mockBusinessService->shouldReceive('process')->once()->with(['ids' => [4, 7, 5, 234]]);

        $this->sut->delete([4, 7, 5, 234], null);
    }
}
