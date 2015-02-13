<?php

/**
 * Community Licence Document Helper Service test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace CommonTest\Service\Helper;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;
use Common\Service\Helper\CommunityLicenceDocumentHelperService;
use Common\Service\Entity\LicenceEntityService;
use Common\Service\Printing\PrintSchedulerInterface;

/**
 * Community Licence Document Helper Service test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class CommunityLicenceDocumentHelperServiceTest extends MockeryTestCase
{
    /**
     * @dataProvider generateBatchProvider
     */
    public function testGenerateBatchWithPSVLicence($licence, $template)
    {
        $mockDocument = m::mock();
        $mockFile = m::mock();

        $sm = m::mock('Zend\ServiceManager\ServiceLocatorInterface')
            ->shouldReceive('get')
            ->with('Entity\Licence')
            ->andReturn(
                m::mock()
                ->shouldReceive('getOverview')
                ->with(123)
                ->andReturn($licence)
                ->getMock()
            )
            ->shouldReceive('get')
            ->with('Helper\DocumentGeneration')
            ->andReturn(
                m::mock()
                ->shouldReceive('generateFromTemplate')
                ->with($template, ['licence' => 123, 'communityLic' => 10])
                ->andReturn($mockDocument)
                ->shouldReceive('uploadGeneratedContent')
                ->with($mockDocument, 'documents', 'Community Licence')
                ->andReturn($mockFile)
                ->getMock()
            )
            ->shouldReceive('get')
            ->with('PrintScheduler')
            ->andReturn(
                m::mock()
                ->shouldReceive('enqueueFile')
                ->with($mockFile, 'Community Licence', [PrintSchedulerInterface::OPTION_DOUBLE_SIDED])
                ->getMock()
            )
            ->getMock();

        $helper = new CommunityLicenceDocumentHelperService();
        $helper->setServiceLocator($sm);

        $helper->generateBatch(123, [10]);
    }

    public function generateBatchProvider()
    {
        return [
            [
                [
                    'licenceType' => [
                        'id' => LicenceEntityService::LICENCE_CATEGORY_PSV
                    ],
                    'niFlag' => 'N'
                ],
                'PSV_European_Community_Licence'
            ],
            [
                [
                    'licenceType' => [
                        'id' => LicenceEntityService::LICENCE_CATEGORY_GOODS_VEHICLE
                    ],
                    'niFlag' => 'N'
                ],
                'GV_GB_European_Community_Licence'
            ],
            [
                [
                    'licenceType' => [
                        'id' => LicenceEntityService::LICENCE_CATEGORY_GOODS_VEHICLE
                    ],
                    'niFlag' => 'Y'
                ],
                'GV_NI_European_Community_Licence'
            ]
        ];
    }
}
