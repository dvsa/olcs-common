<?php

/**
 * External Application Financial History Section Service Test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace CommonTest\Controller\Service\PreviousHistory;

use Common\Controller\Service\PreviousHistory\ExternalApplicationFinancialHistorySectionService;
use CommonTest\Controller\Service\AbstractSectionServiceTestCase;

/**
 * External Application Financial History Section Service Test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class ExternalApplicationFinancialHistorySectionServiceTest extends AbstractSectionServiceTestCase
{
    protected $sut;

    protected function setUp()
    {
        $this->sut = $this->getMock(
            '\Common\Controller\Service\PreviousHistory\ExternalApplicationFinancialHistorySectionService'
        );

        $this->sut = new ExternalApplicationFinancialHistorySectionService();

        parent::setUp();
    }

    public function testAlterFormWithGetReturnsForm()
    {
        $response = array(
            'documents' => array()
        );

        $this->attachRestHelperMock();
        $this->mockRestHelper->expects($this->once())
            ->method('makeRestCall')
            ->willReturn($response);

        $this->mockHelperService('UrlHelper', $this->getMock('\stdClass'));

        $form = $this->buildForm();

        $this->sut->setRequest(new \Zend\Http\Request());
        $result = $this->sut->alterForm($form);

        $this->assertEquals($form, $result);
    }

    public function testMakeFormAlterationsWithReviewFlagRemovesInsolvencyDetails()
    {
        $this->mockHelperService('UrlHelper', $this->getMock('\stdClass'));

        $form = $this->buildForm();

        $options = array(
            'isReview' => true,
            'data' => array('documents' => array()),
            'fieldset' => 'data'
        );

        $this->assertTrue(
            $form->get('data')->has('insolvencyDetails')
        );

        $this->sut->makeFormAlterations($form, $options);

        $this->assertFalse(
            $form->get('data')->has('insolvencyDetails')
        );
    }

    public function testProcessLoadReturnsNamespacedArray()
    {
        $processed = $this->sut->processLoad(array('foo' => 'bar'));

        $this->assertEquals(
            array(
                'data' => array('foo' => 'bar'),
            ),
            $processed
        );
    }

    public function testProcessFinancialFileUpload()
    {
        // @NOTE: we have to mock absolutely *tons* of low-level things here
        // when all we really want to do is assert we've called $this-uploadFile()
        // with some criteria. Once the file upload stuff has been servicized this
        // won't have to reach so deep
        $mockCategoryService = $this->getMock('\stdClass', ['getCategoryByDescription']);

        $mockCategoryService->expects($this->at(0))
            ->method('getCategoryByDescription')
            ->willReturn(['id' => 1]);

        $mockCategoryService->expects($this->at(1))
            ->method('getCategoryByDescription')
            ->willReturn(['id' => 2]);

        $mockFileUploaderService = $this->getMock('\stdClass', ['getUploader']);
        $mockUploadService = $this->getMock('\stdClass', ['upload', 'setFile']);

        $mockFileUploaderService->expects($this->once())
            ->method('getUploader')
            ->willReturn($mockUploadService);

        $file = $this->getMock(
            '\stdClass',
            ['getName', 'getIdentifier', 'getSize', 'getExtension']
        );

        $file->expects($this->once())
            ->method('getName')
            ->willReturn('filename');

        $file->expects($this->once())
            ->method('getSize')
            ->willReturn(1234);

        $file->expects($this->once())
            ->method('getIdentifier')
            ->willReturn('abc123');

        $file->expects($this->once())
            ->method('getExtension')
            ->willReturn('rtf');

        $mockUploadService->expects($this->once())
            ->method('upload')
            ->willReturn($file);

        $mockApplicationSectionService = $this->getMock(
            '\Common\Controller\Service\ApplicationSectionService',
            ['getLicenceSectionService']
        );

        $mockLicenceSectionService = $this->getMock('\stdClass', ['getLicenceData']);

        $mockLicenceSectionService->expects($this->once())
            ->method('getLicenceData')
            ->willReturn(['id' => 1212]);

        $mockApplicationSectionService->expects($this->once())
            ->method('getLicenceSectionService')
            ->willReturn($mockLicenceSectionService);

        $expectedData = [
            'application' => 4321,
            'filename' => 'filename',
            'identifier' => 'abc123',
            'size' => 1234,
            'fileExtension' => 'doc_rtf',
            'licence' => 1212,
            'category' => 1,
            'documentSubCategory' => 2,
            'description' => 'Insolvency document'
        ];

        $this->attachRestHelperMock();
        $this->mockRestHelper->expects($this->once())
            ->method('makeRestCall')
            ->with('Document', 'POST', $expectedData);

        $this->mockSectionService('Application', $mockApplicationSectionService);
        $this->serviceManager->setService('category', $mockCategoryService);
        $this->serviceManager->setService('FileUploader', $mockFileUploaderService);

        $this->sut->setIdentifier(4321);
        $this->sut->processFinancialFileUpload(array());
    }

    private function buildForm()
    {
        $formName = 'application_previous-history_financial-history';
        $form = $this->serviceManager->get('OlcsCustomForm')->createForm($formName);
        return $form;
    }
}
