<?php

/**
 * Licence Processing Service Test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace CommonTest\Service\Processing;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use CommonTest\Bootstrap;
use Common\Service\Processing\LicenceProcessingService;
use Common\Service\Entity\LicenceEntityService;

/**
 * Licence Processing Service Test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class LicenceProcessingServiceTest extends MockeryTestCase
{
    protected $sut;
    protected $sm;

    public function setUp()
    {
        $this->sut = new LicenceProcessingService();
        $this->sm = m::mock('Zend\ServiceManager\ServiceLocatorInterface');

        $this->sut->setServiceLocator($this->sm);
    }

    /**
     * @dataProvider generateDocumentProvider
     */
    public function testGenerateDocument($goodsOrPsv, $licenceType, $template, $description, $filename)
    {
        $entityData = [
            'goodsOrPsv' => [
                'id' => $goodsOrPsv
            ],
            'licenceType' => [
                'id' => $licenceType
            ]
        ];

        $this->setService(
            'Entity\Licence',
            m::mock()
            ->shouldReceive('getOverview')
            ->with(1)
            ->andReturn($entityData)
            ->getMock()
        );

        $file = m::mock();

        $this->setService(
            'Helper\DocumentGeneration',
            m::mock()
            ->shouldReceive('generateAndStore')
            ->with($template, $description, ['licence' => 1])
            ->andReturn($file)
            ->getMock()
        );

        $this->setService(
            'Helper\DocumentDispatch',
            m::mock()
            ->shouldReceive('process')
            ->with(
                $file,
                [
                    'description' => $description,
                    'filename' => $filename,
                    'licence' => 1,
                    'category' => 1,
                    'subCategory' => 79,
                    'isReadOnly' => true,
                    'isExternal' => false
                ]
            )
            ->getMock()
        );

        $this->sut->generateDocument(1);
    }

    /**
     * @dataProvider generateInterimDocumentProvider
     */
    public function testGenerateInterimDocument($isVariation, $template, $description, $filename)
    {
        $entityData = [
            'isVariation' => $isVariation,
            'licence' => [
                'id' => 10
            ]
        ];

        $this->setService(
            'Entity\Application',
            m::mock()
                ->shouldReceive('getDataForProcessing')
                ->with(1)
                ->andReturn($entityData)
                ->getMock()
        );

        $file = m::mock();

        $this->setService(
            'Helper\DocumentGeneration',
            m::mock()
                ->shouldReceive('generateAndStore')
                ->with($template, $description, ['application' => 1, 'licence' => 10])
                ->andReturn($file)
                ->getMock()
        );

        $this->setService(
            'PrintScheduler',
            m::mock()
                ->shouldReceive('enqueueFile')
                ->with($file, $description)
                ->getMock()
        );

        $this->setService(
            'Helper\DocumentDispatch',
            m::mock()
                ->shouldReceive('process')
                ->with(
                    $file,
                    [
                        'description' => $description,
                        'filename' => $filename,
                        'application' => 1,
                        'licence' => 10,
                        'category' => 1,
                        'subCategory' => 79,
                        'isExternal' => false,
                        'isScan' => false
                    ]
                )
                ->getMock()
        );

        $this->sut->generateInterimDocument(1);
    }

    public function generateDocumentProvider()
    {
        return [
            [
                LicenceEntityService::LICENCE_CATEGORY_GOODS_VEHICLE,
                LicenceEntityService::LICENCE_TYPE_STANDARD_NATIONAL,
                'GV_LICENCE_V1',
                'GV Licence',
                'GV_Licence.rtf'
            ], [
                LicenceEntityService::LICENCE_CATEGORY_PSV,
                LicenceEntityService::LICENCE_TYPE_STANDARD_NATIONAL,
                'PSV_LICENCE_V1',
                'PSV Licence',
                'PSV_Licence.rtf'
            ], [
                LicenceEntityService::LICENCE_CATEGORY_PSV,
                LicenceEntityService::LICENCE_TYPE_SPECIAL_RESTRICTED,
                'PSVSRLicence',
                'PSV-SR Licence',
                'PSV-SR_Licence.rtf'
            ]

        ];
    }

    public function generateInterimDocumentProvider()
    {
        return [
            [
                true,
                'GV_INT_DIRECTION_V1',
                'GV Interim Direction',
                'GV_Interim_Direction.rtf'
            ], [
                false,
                'GV_INT_LICENCE_V1',
                'GV Interim Licence',
                'GV_Interim_Licence.rtf'
            ], [
                true,
                'GV_INT_DIRECTION_V1',
                'GV Interim Direction',
                'GV_Interim_Direction.rtf'
            ], [
                false,
                'GV_INT_LICENCE_V1',
                'GV Interim Licence',
                'GV_Interim_Licence.rtf'
            ]
        ];
    }

    private function setService($service, $mock)
    {
        $this->sm->shouldReceive('get')
            ->with($service)
            ->andReturn($mock);
    }

    public function testVoidAllDiscs()
    {
        $mockLicenceService = m::mock();
        $this->setService('Entity\Licence', $mockLicenceService);

        $mockLicenceStatusHelper = m::mock();
        $this->setService('Helper\LicenceStatus', $mockLicenceStatusHelper);

        $mockLicenceService->shouldReceive('getRevocationDataForLicence')->with(1966)->once()->andReturn(['DATA']);

        $mockLicenceStatusHelper->shouldReceive('ceaseDiscs')->with(['DATA'])->once();

        $this->sut->voidAllDiscs(1966);
    }

    public function testCreateDiscsGoods()
    {
        $mockLicenceService = m::mock();
        $this->setService('Entity\Licence', $mockLicenceService);

        $mockGoodsDiscService = m::mock();
        $this->setService('Entity\GoodsDisc', $mockGoodsDiscService);

        $mockLicenceService->shouldReceive('getRevocationDataForLicence')->with(1966)->once()
            ->andReturn(['goodsOrPsv' => ['id' => 'lcat_gv'], 'licenceVehicles' => ['DATA']]);

        $mockGoodsDiscService->shouldReceive('createForVehicles')->with(['DATA'])->once();

        $this->sut->createDiscs(1966);
    }

    public function testCreateDiscsPsv()
    {
        $mockLicenceService = m::mock();
        $this->setService('Entity\Licence', $mockLicenceService);

        $mockPsvDiscService = m::mock();
        $this->setService('Entity\PsvDisc', $mockPsvDiscService);

        $mockLicenceService->shouldReceive('getRevocationDataForLicence')->with(1966)->once()
            ->andReturn(['goodsOrPsv' => ['id' => 'lcat_psv'], 'licenceVehicles' => ['DATA']]);

        $mockPsvDiscService->shouldReceive('requestBlankDiscs')->with(1966, 76)->once();

        $this->sut->createDiscs(1966, 76);
    }

    public function testVoidAllCommunityLicences()
    {
        $mockApplicationProcessing = m::mock();
        $this->setService('Processing\Application', $mockApplicationProcessing);

        $mockApplicationProcessing->shouldReceive('voidCommunityLicencesForLicence')->with(1966)->once();

        $this->sut->voidAllCommunityLicences(1966);
    }

    public function testCreateCommunityLicences()
    {
        $mockLicenceCommunityLicenceAdapter = m::mock();
        $this->setService('LicenceCommunityLicenceAdapter', $mockLicenceCommunityLicenceAdapter);

        $mockLicenceService = m::mock();
        $this->setService('Entity\Licence', $mockLicenceService);

        $mockLicenceCommunityLicenceAdapter->shouldReceive('addCommunityLicences')
            ->with(1966, 65, null)
            ->once();

        $mockLicenceService->shouldReceive('updateCommunityLicencesCount')->with(1966)->once();

        $this->sut->createCommunityLicences(1966, 65);
    }

    public function testCreateCommunityLicenceOfficeCopy()
    {
        $mockLicenceCommunityLicenceAdapter = m::mock();
        $this->setService('LicenceCommunityLicenceAdapter', $mockLicenceCommunityLicenceAdapter);

        $mockLicenceCommunityLicenceAdapter->shouldReceive('addOfficeCopy')
            ->with(1966, null)
            ->once();

        $this->sut->createCommunityLicenceOfficeCopy(1966);
    }
}
