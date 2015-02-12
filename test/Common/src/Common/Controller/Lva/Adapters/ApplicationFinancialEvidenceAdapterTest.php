<?php

/**
 * Application Financial Evidence Adapter Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace OlcsTest\Controller\Lva\Adapters;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Controller\Lva\Adapters\ApplicationFinancialEvidenceAdapter;
use Common\Service\Entity\LicenceEntityService as Licence;
use Common\Service\Entity\ApplicationEntityService as Application;
use Common\Service\Data\CategoryDataService;
use CommonTest\Bootstrap;

/**
 * Application Financial Evidence Adapter Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class ApplicationFinancialEvidenceAdapterTest extends MockeryTestCase
{
    protected $sut;
    protected $sm;

    public function setUp()
    {
        $this->sut = new ApplicationFinancialEvidenceAdapter();
        $this->sm = Bootstrap::getServiceManager();
        $this->sut->setServiceLocator($this->sm);
    }

    protected function setUpData()
    {
        $applicationId = 123;

        $applicationData = [
            'id' => $applicationId,
            'totAuthVehicles' => 3,
            'status' => [ 'id' => Application::APPLICATION_STATUS_NOT_SUBMITTED ],
            'licence' => [
                'id' => 234,
                'organisation' => [ 'id' => 99 ],
            ],
            'licenceType' => [ 'id' => Licence::LICENCE_TYPE_STANDARD_INTERNATIONAL ],
            'goodsOrPsv' => [ 'id' => Licence::LICENCE_CATEGORY_GOODS_VEHICLE ],
        ];
        $mockApplicationEntityService = m::mock()
            ->shouldReceive('getDataForFinancialEvidence')
            ->once()
            ->with($applicationId)
            ->andReturn($applicationData)
            ->getMock();

        $this->sm->setService('Entity\Application', $mockApplicationEntityService);

        $licences = [
            [
                'id' => 235,
                'status' => [ 'id' => Licence::LICENCE_STATUS_VALID ],
                'goodsOrPsv' => [ 'id' => Licence::LICENCE_CATEGORY_GOODS_VEHICLE ],
                'licenceType' => [ 'id' => Licence::LICENCE_TYPE_RESTRICTED ],
                'totAuthVehicles' => 3,

            ],
            [
                'id' => 237,
                'status' => [ 'id' => Licence::LICENCE_STATUS_SUSPENDED ],
                'goodsOrPsv' => [ 'id' => Licence::LICENCE_CATEGORY_PSV ],
                'licenceType' => [ 'id' => Licence::LICENCE_TYPE_RESTRICTED ],
                'totAuthVehicles' => 1,

            ],
        ];

        $mockOrganisationEntityService = m::mock()
            ->shouldReceive('getLicencesByStatus')
            ->once()
            ->with(
                99,
                [
                    Licence::LICENCE_STATUS_VALID,
                    Licence::LICENCE_STATUS_SUSPENDED,
                    Licence::LICENCE_STATUS_CURTAILED
                ]
            )
            ->andReturn($licences)
            ->getMock();

        $anotherApplication = [
            'id' => 124,
            'totAuthVehicles' => 0,
            'status' => [ 'id' => Application::APPLICATION_STATUS_UNDER_CONSIDERATION ],
            'licence' => [
                'id' => 237,
                'organisation' => [ 'id' => 99 ],
            ],
            'licenceType' => [ 'id' => Licence::LICENCE_TYPE_STANDARD_INTERNATIONAL ],
            'goodsOrPsv' => [ 'id' => Licence::LICENCE_CATEGORY_GOODS_VEHICLE ],
        ];

        $currentApplications = [
            $applicationData,
            $anotherApplication
        ];

        $mockOrganisationEntityService
            ->shouldReceive('getNewApplicationsByStatus')
            ->once()
            ->with(
                99,
                [
                    Application::APPLICATION_STATUS_UNDER_CONSIDERATION,
                    Application::APPLICATION_STATUS_GRANTED,
                ]
            )
            ->andReturn($currentApplications);

        $this->sm->setService('Entity\Organisation', $mockOrganisationEntityService);
    }

    public function testGetRequiredFinance()
    {
        // For an operator:
        //  * with a goods standard international application with 3 vehicles,
        //    the finance is £7000 + (2 x £3900) = £14,800
        //  * plus a goods restricted licence with 3 vehicles, the finance is (3 x £1700) = £5,100
        //  * plus a psv restricted licence with 1 vehicle, the finance is £2,700
        //  * The total required finance is £14,800 + £5,100 + £2,700 = £22,600
        $expected = 22600;

        $this->setUpData();
        $this->assertEquals($expected, $this->sut->getRequiredFinance(123));
    }

    public function testGetTotalNumberOfAuthorisedVehicles()
    {
        $this->setUpData();
        $this->assertEquals(7, $this->sut->getTotalNumberOfAuthorisedVehicles(123));
    }

    public function testAlterFormForLva()
    {
        $mockElement = m::mock()
            ->shouldReceive('setValue')
            ->once()
            ->with('markup-required-finance-application')
            ->getMock();

        $mockFieldset = m::mock()
            ->shouldReceive('get')
            ->once()
            ->with('requiredFinance')
            ->andReturn($mockElement)
            ->getMock();

        $mockForm = m::mock()
            ->shouldReceive('get')
            ->once()
            ->with('finance')
            ->andReturn($mockFieldset)
            ->getMock();

        $this->sut->alterFormForLva($mockForm);
    }

    public function testGetRatesForView()
    {
        $applicationId = 789;

        $this->sm->setService(
            'Entity\Application',
            m::mock()
                ->shouldReceive('getDataForFinancialEvidence')
                ->with($applicationId)
                ->andReturn(
                    [
                        'id' => $applicationId,
                        'goodsOrPsv' => ['id' => Licence::LICENCE_CATEGORY_PSV],
                    ]
                )
                ->getMock()
        );

        $variables = $this->sut->getRatesForView($applicationId);

        $this->assertInternalType('array', $variables);

        $this->assertArrayHasKey('standardFirst', $variables);
        $this->assertArrayHasKey('standardAdditional', $variables);
        $this->assertArrayHasKey('restrictedFirst', $variables);
        $this->assertArrayHasKey('restrictedAdditional', $variables);
    }

    /**
     * @dataProvider formDataProvider
     * @param string|null $uploaded value from application record ('Y'|'N'|null)
     * @param array $expected expected form data
     */
    public function testGetFormData($uploaded, $expected)
    {
        $applicationId = 123;

        $this->sm->setService(
            'Entity\Application',
            m::mock()
                ->shouldReceive('getDataForFinancialEvidence')
                ->with($applicationId)
                ->andReturn(
                    [
                        'id' => $applicationId,
                        'version' => 1,
                        'goodsOrPsv' => ['id' => Licence::LICENCE_CATEGORY_PSV],
                        'financialEvidenceUploaded' => $uploaded
                    ]
                )
                ->getMock()
        );

        $this->assertEquals(
            $expected,
            $this->sut->getFormData($applicationId)
        );
    }

    /**
     * @return array
     */
    public function formDataProvider()
    {
        return [
            [
                'Y',
                ['id'=>123, 'version'=>1, 'evidence'=>['uploadNow'=>'Y']]
            ],
            [
                'N',
                ['id'=>123, 'version'=>1, 'evidence'=>['uploadNow'=>'N']]
            ],
            [
                null,
                ['id'=>123, 'version'=>1, 'evidence'=>['uploadNow'=>'Y']]
            ],
        ];
    }

    public function testGetDocuments()
    {
        $this->sm->setService(
            'Entity\Application',
            m::mock()
                ->shouldReceive('getDocuments')
                    ->with(
                        123,
                        CategoryDataService::CATEGORY_APPLICATION,
                        CategoryDataService::DOC_SUB_CATEGORY_FINANCIAL_EVIDENCE_DIGITAL
                    )
                    ->andReturn(['array-of-documents'])
                ->getMock()
        );

        $this->assertEquals(['array-of-documents'], $this->sut->getDocuments(123));
    }

    public function testGetDocumentMetaData()
    {
        $applicationId = 123;
        $licenceId = 456;

        $stubFile = [
            'name' => 'the-filename'
        ];

        $this->sm->setService(
            'Entity\Application',
            m::mock()
                ->shouldReceive('getLicenceIdForApplication')
                    ->with($applicationId)
                    ->andReturn($licenceId)
                ->getMock()
        );

        $expected = [
            'application' => $applicationId,
            'licence' => $licenceId,
            'description' => 'the-filename',
            'category' => CategoryDataService::CATEGORY_APPLICATION,
            'subCategory' => CategoryDataService::DOC_SUB_CATEGORY_FINANCIAL_EVIDENCE_DIGITAL,
        ];

        $this->assertEquals($expected, $this->sut->getUploadMetaData($stubFile, $applicationId));
    }
}
