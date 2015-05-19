<?php

/**
 * ContinueLicenceTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace CommonTest\BusinessService\Service\Lva;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\BusinessService\Service\Lva\ContinueLicence;
use Common\BusinessService\Response;
use CommonTest\Bootstrap;

/**
 * ContinueLicenceTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class ContinueLicenceTest extends MockeryTestCase
{
    protected $sut;

    protected $sm;

    protected $bsm;

    protected $brm;

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();

        $this->sut = new ContinueLicence();
        $this->sut->setServiceLocator($this->sm);
    }

    public function testProcessMissingParam()
    {
        $response = $this->sut->process([]);

        $this->assertFalse($response->isOk());
        $this->assertEquals("'continuationDetailId' parameter is missing.", $response->getMessage());
    }


    public function testProcessPsv()
    {
        $mockContinuationDetailService = \Mockery::mock();
        $this->sm->setService('Entity\ContinuationDetail', $mockContinuationDetailService);

        $mockLicenceService = \Mockery::mock();
        $this->sm->setService('Entity\Licence', $mockLicenceService);

        $mockLicenceProcessingService = \Mockery::mock();
        $this->sm->setService('Processing\Licence', $mockLicenceProcessingService);

        $continuationDetail = [
            'id' => 98,
            'totPsvDiscs' => 23,
            'totCommunityLicences' => 43,
            'totAuthVehicles' => 83,
            'licence' => [
                'id' => 76,
                'version' => 2,
                'expiryDate' => '2015-05-13',
                'reviewDate' => '2015-02-24',
                'goodsOrPsv' => ['id' => 'lcat_psv'],
                'licenceType' => ['id' => 'ltyp_si'],
            ]
        ];

        $mockContinuationDetailService->shouldReceive('getDetailsForProcessing')->with(76)->once()
            ->andReturn($continuationDetail);
        $mockLicenceService->shouldReceive('save')->with(
            [
                'id' => 76,
                'version' => 2,
                'expiryDate' => '2020-05-13',
                'reviewDate' => '2020-02-24',
            ]
        )->once();

        $mockLicenceService->shouldReceive('forceUpdate')->with(76, ['totAuthVehicles' => 83])->once();
        $mockLicenceProcessingService->shouldReceive('voidAllDiscs')->with(76)->once();
        $mockLicenceProcessingService->shouldReceive('createDiscs')->with(76, 23)->once();

        $mockLicenceProcessingService->shouldReceive('voidAllCommunityLicences')->with(76)->once();
        $mockLicenceProcessingService->shouldReceive('createCommunityLicences')->with(76, 43)->once();
        $mockLicenceProcessingService->shouldReceive('createCommunityLicenceOfficeCopy')->with(76)->once();

        $mockLicenceProcessingService->shouldReceive('generateDocument')->with(76)->once();

        $mockContinuationDetailService->shouldReceive('forceUpdate')->with(98, ['status' => 'con_det_sts_complete'])
            ->once();

        $response = $this->sut->process(['continuationDetailId' => 76]);

        $this->assertTrue($response->isOk());
    }

    public function testProcessGoods()
    {
        $mockContinuationDetailService = \Mockery::mock();
        $this->sm->setService('Entity\ContinuationDetail', $mockContinuationDetailService);

        $mockLicenceService = \Mockery::mock();
        $this->sm->setService('Entity\Licence', $mockLicenceService);

        $mockLicenceProcessingService = \Mockery::mock();
        $this->sm->setService('Processing\Licence', $mockLicenceProcessingService);

        $continuationDetail = [
            'id' => 98,
            'totCommunityLicences' => 43,
            'licence' => [
                'id' => 76,
                'version' => 2,
                'expiryDate' => '2015-05-13',
                'reviewDate' => '2015-02-24',
                'goodsOrPsv' => ['id' => 'lcat_gv'],
                'licenceType' => ['id' => 'ltyp_si'],
            ]
        ];

        $mockContinuationDetailService->shouldReceive('getDetailsForProcessing')->with(76)->once()
            ->andReturn($continuationDetail);
        $mockLicenceService->shouldReceive('save')->with(
            [
                'id' => 76,
                'version' => 2,
                'expiryDate' => '2020-05-13',
                'reviewDate' => '2020-02-24',
            ]
        )->once();

        $mockLicenceProcessingService->shouldReceive('voidAllDiscs')->with(76)->once();
        $mockLicenceProcessingService->shouldReceive('createDiscs')->with(76)->once();

        $mockLicenceProcessingService->shouldReceive('voidAllCommunityLicences')->with(76)->once();
        $mockLicenceProcessingService->shouldReceive('createCommunityLicences')->with(76, 43)->once();
        $mockLicenceProcessingService->shouldReceive('createCommunityLicenceOfficeCopy')->with(76)->once();

        $mockLicenceProcessingService->shouldReceive('generateDocument')->with(76)->once();

        $mockContinuationDetailService->shouldReceive('forceUpdate')->with(98, ['status' => 'con_det_sts_complete'])
            ->once();

        $response = $this->sut->process(['continuationDetailId' => 76]);

        $this->assertTrue($response->isOk());
    }
}
