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
use CommonTest\Traits\MockFinancialStandingRatesTrait;

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

    protected function setUpData($applicationId, $uploaded = null)
    {
        $applicationData = [
            'id' => $applicationId,
            'version' => 1,
            'totAuthVehicles' => 3,
            'status' => [ 'id' => Application::APPLICATION_STATUS_NOT_SUBMITTED ],
            'licence' => [
                'id' => 234,
                'organisation' => [ 'id' => 99 ],
            ],
            'licenceType' => [ 'id' => Licence::LICENCE_TYPE_STANDARD_INTERNATIONAL ],
            'goodsOrPsv' => [ 'id' => Licence::LICENCE_CATEGORY_GOODS_VEHICLE ],
            'financialEvidenceUploaded' => $uploaded,
            'financialEvidence' => [
                'requiredFinance' => 87600,
                'vehicles' => 25,
                'standardFirst' => 6000,
                'standardAdditional' => 2900,
                'restrictedFirst' => 2100,
                'restrictedAdditional' => 700,
            ],
            'documents' => ['array-of-documents'],
        ];

        $mockResponse = m::mock();

        $mockQuery = m::mock();
        $this->sm->setService(
            'TransferAnnotationBuilder',
            m::mock()
                ->shouldReceive('createQuery')
                ->once()
                ->andReturn($mockQuery)
                ->getMock()
        );

        $this->sm->setService(
            'QueryService',
            m::mock()
                ->shouldReceive('send')
                ->with($mockQuery)
                ->andReturn($mockResponse)
                ->getMock()
        );

        $mockResponse->shouldReceive('getResult')->andReturn($applicationData);
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

    /**
     * @dataProvider formDataProvider
     * @param string|null $uploaded value from application record ('Y'|'N'|null)
     * @param array $expected expected form data
     */
    public function testGetFormData($uploaded, $expected)
    {
        $applicationId = 123;

        $this->setUpData($applicationId, $uploaded);

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
        $applicationId = 123;
        $this->setUpData($applicationId);
        $this->assertEquals(['array-of-documents'], $this->sut->getDocuments($applicationId));
    }

    public function testGetDocumentMetaData()
    {
        $applicationId = 123;

        $this->setUpData($applicationId);

        $stubFile = [
            'name' => 'the-filename'
        ];

        $expected = [
            'application' => $applicationId,
            'licence' => 234,
            'description' => 'the-filename',
            'category' => CategoryDataService::CATEGORY_APPLICATION,
            'subCategory' => CategoryDataService::DOC_SUB_CATEGORY_FINANCIAL_EVIDENCE_DIGITAL,
        ];

        $this->assertEquals($expected, $this->sut->getUploadMetaData($stubFile, $applicationId));
    }
}
