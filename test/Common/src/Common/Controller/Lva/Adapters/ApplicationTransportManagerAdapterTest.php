<?php

namespace CommonTest\Controller\Lva\Adapters;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Controller\Lva\Adapters\ApplicationTransportManagerAdapter;
use Common\Service\Entity\LicenceEntityService;

/**
 * Application Transport Manager Adapter Test
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class ApplicationTransportManagerAdapterTest extends MockeryTestCase
{
    protected $sut;
    protected $sm;
    protected $controller;

    public function setUp()
    {
        $this->sm = m::mock('\Zend\ServiceManager\ServiceManager')->makePartial();
        $this->sm->setAllowOverride(true);

        $this->controller = m::mock('\Zend\Mvc\Controller\AbstractController');

        $this->sut = new ApplicationTransportManagerAdapter();
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

        $this->assertEquals($expected, $this->sut->mustHaveAtLeastOneTm(44));
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
