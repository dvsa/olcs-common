<?php

namespace CommonTest\Controller\Lva;

use Mockery as m;

use Dvsa\Olcs\Transfer\Command\Application\UpdateFinancialEvidence;

/**
 * Abstract Financial Evidence Controller Test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class AbstractFinancialEvidenceControllerTest extends AbstractLvaControllerTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->mockController('\Common\Controller\Lva\AbstractFinancialEvidenceController');
    }

    public function testGetIndexAction()
    {
        $id = 123;

        $formData = [
            'id' => $id,
            'version' => 1,
            'evidence' => [
                'uploadNow' => 'Y'
            ],
        ];

        $appData = [
            'id' => $id,
            'financialEvidence' => [
                'requiredFinance' => '1000000',
            ],
        ];

        $this->sut->shouldReceive('getIdentifier')->andReturn($id);

        $mockAdapter = m::mock('Common\Controller\Lva\Adapters\AbstractFinancialEvidenceAdapter')
            ->shouldReceive('alterFormForLva')
            ->once()
            ->shouldReceive('getFormData')
            ->once()
            ->andReturn($formData)
            ->shouldReceive('getData')
            ->once()
            ->andReturn($appData)
            ->getMock();

        $this->sut->setAdapter($mockAdapter);

        $form = $this->createMockForm('Lva\FinancialEvidence');
        $form->shouldReceive('setData')->with($formData)->andReturnSelf();

        $this->mockFileUploadHelper($form, false);

        $this->sm->setService(
            'Script',
            m::mock()
                ->shouldReceive('loadFiles')
                ->with(['financial-evidence'])
                ->getMock()
        );

        $this->mockRender();

        $this->sut->indexAction();

        $this->assertEquals('financial_evidence', $this->view);
    }

    public function testPostWithValidDataNoFiles()
    {
        $id = 123;

        $this->sut->shouldReceive('getIdentifier')->andReturn($id);

        $postData = [
            'id' => $id,
            'version' => '1',
            'evidence' => [
                'uploadNow' => 'N',
                'uploadedFileCount' => '0',
            ],
        ];

        $this->setPost($postData);

        $form = $this->createMockForm('Lva\FinancialEvidence');
        $form->shouldReceive('setData')->with($postData)->andReturnSelf();
        $form->shouldReceive('isValid')->andReturn(true);

        $this->mockFileUploadHelper($form, false);

        $mockAdapter = m::mock('Common\Controller\Lva\Adapters\AbstractFinancialEvidenceAdapter')
            ->shouldReceive('alterFormForLva')
            ->getMock();
        $this->sut->setAdapter($mockAdapter);

        $mockCommand = m::mock();
        $mockResponse = m::mock();
        $mockTab = m::mock();
        $this->setService('TransferAnnotationBuilder', $mockTab);
        $mockCommandService = m::mock();
        $this->setService('CommandService', $mockCommandService);

        $mockTab
            ->shouldReceive('createCommand')
            ->once()
            ->andReturn($mockCommand);
        $mockCommandService
            ->shouldReceive('send')
            ->once()
            ->with($mockCommand)
            ->andReturn($mockResponse);

        $mockResponse
            ->shouldReceive('isOk')
            ->andReturn(true);

        $this->sut->shouldReceive('postSave')
            ->with('financial_evidence')
            ->shouldReceive('completeSection')
            ->with('financial_evidence')
            ->andReturn('complete');

        $this->assertEquals(
            'complete',
            $this->sut->indexAction()
        );
    }

    /**
     * @note the method under test here is fairly trivial and just
     * proxies to the adapter
     */
    public function testGetDocuments()
    {
        $id = 99;
        $this->sut->shouldReceive('getIdentifier')->andReturn($id);

        $this->sut->setAdapter(
            m::mock('Common\Controller\Lva\Adapters\AbstractFinancialEvidenceAdapter')
                ->shouldReceive('getDocuments')
                    ->once()
                    ->with($id)
                    ->andReturn(['some-documents'])
                ->getMock()
        );

        $this->assertEquals(['some-documents'], $this->sut->getDocuments());
    }

    /**
     * @note the method under test here is fairly trivial and just
     * proxies to the adapter and the GenericUpload trait
     */
    public function testProcessFinancialEvidenceFileUpload()
    {
        $id = 99;
        $this->sut->shouldReceive('getIdentifier')->andReturn($id);

        $file = ['test-file'];

        $this->sut->setAdapter(
            m::mock('Common\Controller\Lva\Adapters\AbstractFinancialEvidenceAdapter')
                ->shouldReceive('getUploadMetaData')
                    ->once()
                    ->with($file, $id)
                    ->andReturn(['some' => 'meta-data'])
                ->getMock()
        );

        $this->sut->shouldReceive('isExternal')
            ->andReturn(true)
            // (we're only asserting that the trait method is called, it's tested elsewhere)
            ->shouldReceive('uploadFile')
            ->once()
            ->with(
                $file,
                [
                    'some' => 'meta-data',
                    'isExternal' => true
                ]
            );

        $this->sut->processFinancialEvidenceFileUpload($file);
    }

    /**
     * @param Form $form
     * @param bool $processed
     */
    protected function mockFileUploadHelper($form, $processed)
    {
        $this->setService(
            'Helper\FileUpload',
            m::mock()
                ->shouldReceive('setForm')->with($form)->andReturnSelf()
                ->shouldReceive('setUploadCallback')->andReturnSelf()
                ->shouldReceive('setDeleteCallback')->andReturnSelf()
                ->shouldReceive('setLoadCallback')->andReturnSelf()
                ->shouldReceive('setRequest')->with($this->request)->andReturnSelf()
                ->shouldReceive('setSelector')->with('evidence->files')->andReturnSelf()
                ->shouldReceive('setCountSelector')->with('evidence->uploadedFileCount')->andReturnSelf()
                ->shouldReceive('process')->andReturn($processed)
                ->getMock()
        );
    }
}
