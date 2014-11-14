<?php

namespace CommonTest\Controller\Lva;

use \Mockery as m;

class AbstractOperatingCentresControllerTest extends AbstractLvaControllerTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->mockController('\Common\Controller\Lva\AbstractOperatingCentresController');

        $this->sut->shouldReceive('getLvaEntity')
            // in practice this would be "Entity\Application" or "Entity\Licence", but at
            // the abstract level we don't care about that
            ->andReturn('Entity\Stub')
            ->shouldReceive('getLvaOperatingCentreEntity')
            // ditto
            ->andReturn('Entity\StubOperatingCentre');
    }

    public function testGetIndexActionForGoodsLicence()
    {
        $form = $this->createMockForm('Lva\OperatingCentres');

        $this->sut->shouldReceive('getIdentifier')->andReturn(50);

        $this->sut->shouldReceive('getTypeOfLicenceData')
            ->andReturn(
                [
                    'licenceType' => [
                        'id' => 'ltyp_sn'
                    ],
                    'niFlag' => 'N'
                ]
            );

        $this->setService(
            'Entity\Stub',
            m::mock()
            ->shouldReceive('getOperatingCentresData')
            ->with(50)
            ->andReturn([])
            ->getMock()
        );

        $this->setService(
            'Entity\StubOperatingCentre',
            m::mock()
            ->shouldReceive('getAddressSummaryData')
            ->with(50)
            ->andReturn(
                [
                    'Results' => []
                ]
            )
            ->getMock()
        );

        $table = m::mock()
            ->shouldReceive('getColumn')
            ->shouldReceive('setColumn')
            ->getMock();

        $this->setService(
            'Table',
            m::mock()
            ->shouldReceive('prepareTable')
            ->with('authorisation_in_form', [])
            ->andReturn($table)
            ->getMock()
        );

        $tableElement = m::mock()
            ->shouldReceive('setTable')
            ->with($table)
            ->getMock();

        $tableFieldset = m::mock()
            ->shouldReceive('get')
            ->andReturn($tableElement)
            ->getMock();

        $form->shouldReceive('get')
            ->with('table')
            ->andReturn($tableFieldset);

        $removeFields = [
            'totAuthSmallVehicles',
            'totAuthMediumVehicles',
            'totAuthLargeVehicles',
            'totCommunityLicences'
        ];

        $this->getMockFormHelper()
            ->shouldReceive('removeFieldList')
            ->with($form, 'data', $removeFields)
            ->shouldReceive('remove')
            ->with($form, 'dataTrafficArea');

        $form->shouldReceive('setData')
            ->with(
                [
                    'data' => [
                        'noOfOperatingCentres' => 0,
                        'minVehicleAuth' => 0,
                        'maxVehicleAuth' => 0,
                        'minTrailerAuth' => 0,
                        'maxTrailerAuth' => 0,
                        'licenceType' => [
                            'id' => 'ltyp_sn'
                        ]
                    ]
                ]
            )
            ->andReturn($form);

        $this->mockRender();

        $this->sut->indexAction();

        $this->assertEquals('operating_centres', $this->view);
    }

    /*
    public function testPostWithInvalidData()
    {
    }

    public function testPostWithValidData()
    {
    }
    */
}
