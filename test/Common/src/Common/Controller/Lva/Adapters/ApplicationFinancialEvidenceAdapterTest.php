<?php

namespace CommonTest\Controller\Lva\Adapters;

use Common\Controller\Lva\Adapters\ApplicationFinancialEvidenceAdapter;
use Common\Service\Cqrs\Query\CachingQueryService;
use Common\Service\Data\CategoryDataService as Category;
use Dvsa\Olcs\Transfer\Util\Annotation\AnnotationBuilder;
use Interop\Container\ContainerInterface;
use Laminas\Form\Form;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class ApplicationFinancialEvidenceAdapterTest extends MockeryTestCase
{
    protected $sut;

    protected $container;

    public function setUp(): void
    {
        $this->container = m::mock(ContainerInterface::class);
        $this->sut = m::mock(ApplicationFinancialEvidenceAdapter::class, [$this->container])->makePartial();
    }

    public function testAlterFormForLva()
    {
        $mockForm = m::mock(Form::class)
            ->shouldReceive('get')
            ->with('finance')
            ->andReturn(
                m::mock()
                    ->shouldReceive('get')
                    ->with('requiredFinance')
                    ->andReturn(
                        m::mock()
                            ->shouldReceive('setValue')
                            ->with('markup-required-finance-application')
                            ->once()
                            ->getMock()
                    )
                    ->once()
                    ->getMock()
            )
            ->once()
            ->getMock();

        $this->assertNull($this->sut->alterFormForLva($mockForm));
    }

    public function testGetDocuments()
    {
        $applicationId = 1;

        $this->sut->shouldReceive('getData')
            ->with($applicationId)
            ->andReturn(['documents' => ['documents']])
            ->once();

        $this->assertEquals(['documents'], $this->sut->getDocuments($applicationId));
    }

    public function testGetUploadMetaData()
    {
        $applicationId = 1;
        $licenceId = 2;
        $file = [
            'name' => 'foo'
        ];

        $this->sut->shouldReceive('getData')
            ->with($applicationId)
            ->andReturn(['licence' => ['id' => $licenceId]])
            ->once();

        $expected = [
            'application' => $applicationId,
            'description' => $file['name'],
            'category'    => Category::CATEGORY_APPLICATION,
            'subCategory' => Category::DOC_SUB_CATEGORY_FINANCIAL_EVIDENCE_DIGITAL,
            'licence'     => $licenceId,
        ];

        $this->assertEquals($expected, $this->sut->getUploadMetaData($file, $applicationId));
    }

    public function testGetData()
    {
        $applicationId = 1;

        $this->container->shouldReceive('get')
            ->with(AnnotationBuilder::class)
            ->andReturn(
                m::mock()
                    ->shouldReceive('createQuery')
                    ->andReturn('query')
                    ->once()
                    ->getMock()
            )
            ->once()
            ->shouldReceive('get')
            ->with(CachingQueryService::class)
            ->andReturn(
                m::mock()
                    ->shouldReceive('send')
                    ->with('query')
                    ->andReturn(
                        m::mock()
                            ->shouldReceive('getResult')
                            ->once()
                            ->andReturn('foo')
                            ->getMock()
                    )
                    ->once()
                    ->getMock()
            )
            ->once()
            ->getMock();

        $this->assertEquals('foo', $this->sut->getData($applicationId, true));
        // testing cache
        $this->assertEquals('foo', $this->sut->getData($applicationId, false));
    }
}
