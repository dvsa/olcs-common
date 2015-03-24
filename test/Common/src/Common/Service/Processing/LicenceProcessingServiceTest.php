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
    public function testGenerateDocument($niFlag, $goodsOrPsv, $licenceType, $template, $description, $filename)
    {
        $entityData = [
            'niFlag' => $niFlag,
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
        $content = m::mock();

        $this->setService(
            'Helper\DocumentGeneration',
            m::mock()
            ->shouldReceive('generateFromTemplate')
            ->with($template, ['licence' => 1])
            ->andReturn($content)
            ->shouldReceive('uploadGeneratedContent')
            ->with($content, 'documents', $description)
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
            'Entity\Document',
            m::mock()
            ->shouldReceive('createFromFile')
            ->with(
                $file,
                [
                    'description' => $description,
                    'filename' => $filename,
                    'fileExtension' => 'doc_rtf',
                    'licence' => 1,
                    'category' => 1,
                    'subCategory' => 79,
                    'isReadOnly' => true
                ]
            )
            ->getMock()
        );

        $this->sut->generateDocument(1);
    }

    /**
     * @dataProvider generateInterimDocumentProvider
     */
    public function testGenerateInterimDocument($niFlag, $isVariation, $template, $description, $filename)
    {
        $entityData = [
            'niFlag' => $niFlag,
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
        $content = m::mock();

        $this->setService(
            'Helper\DocumentGeneration',
            m::mock()
                ->shouldReceive('generateFromTemplate')
                ->with($template, ['application' => 1, 'licence' => 10])
                ->andReturn($content)
                ->shouldReceive('uploadGeneratedContent')
                ->with($content, 'documents', $description)
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
            'Entity\Document',
            m::mock()
                ->shouldReceive('createFromFile')
                ->with(
                    $file,
                    [
                        'description' => $description,
                        'filename' => $filename,
                        'fileExtension' => 'doc_rtf',
                        'application' => 1,
                        'licence' => 10,
                        'category' => 1,
                        'subCategory' => 79,
                        'isDigital' => false,
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
                'N',
                LicenceEntityService::LICENCE_CATEGORY_GOODS_VEHICLE,
                LicenceEntityService::LICENCE_TYPE_STANDARD_NATIONAL,
                'GB/GV_LICENCE_V1',
                'GV Licence',
                'GV_Licence.rtf'
            ], [
                'N',
                LicenceEntityService::LICENCE_CATEGORY_PSV,
                LicenceEntityService::LICENCE_TYPE_STANDARD_NATIONAL,
                'GB/PSV_LICENCE_V1',
                'PSV Licence',
                'PSV_Licence.rtf'
            ], [
                'N',
                LicenceEntityService::LICENCE_CATEGORY_PSV,
                LicenceEntityService::LICENCE_TYPE_SPECIAL_RESTRICTED,
                'GB/PSVSRLicence',
                'PSV-SR Licence',
                'PSV-SR_Licence.rtf'
            ], [
                'Y',
                LicenceEntityService::LICENCE_CATEGORY_GOODS_VEHICLE,
                LicenceEntityService::LICENCE_TYPE_STANDARD_INTERNATIONAL,
                'NI/GV_LICENCE_V1',
                'GV Licence',
                'GV_Licence.rtf'
            ]

        ];
    }

    public function generateInterimDocumentProvider()
    {
        return [
            [
                'N',
                true,
                'GB/GV_INT_DIRECTION_V1',
                'GV Interim Direction',
                'GV_Interim_Direction.rtf'
            ], [
                'N',
                false,
                'GB/GV_INT_LICENCE_V1',
                'GV Interim Licence',
                'GV_Interim_Licence.rtf'
            ], [
                'Y',
                true,
                'NI/GV_INT_DIRECTION_V1',
                'GV Interim Direction',
                'GV_Interim_Direction.rtf'
            ], [
                'Y',
                false,
                'NI/GV_INT_LICENCE_V1',
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
}
