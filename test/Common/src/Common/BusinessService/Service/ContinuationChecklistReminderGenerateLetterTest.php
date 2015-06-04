<?php

/**
 * CContinuationChecklistReminderGenerateLettersTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace CommonTest\BusinessService\Service;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\BusinessService\Service\ContinuationChecklistReminderGenerateLetter;
use CommonTest\Bootstrap;

/**
 * ContinuationChecklistReminderGenerateLettersTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class ContinuationChecklistReminderGenerateLetterTest extends MockeryTestCase
{
    protected $sut;

    protected $sm;

    public function setUp()
    {
        $this->sut = new ContinuationChecklistReminderGenerateLetter();

        $this->sm = Bootstrap::getServiceManager();

        $this->sut->setServiceLocator($this->sm);
    }

    public function testProcessMissingParam()
    {
        $response = $this->sut->process([]);

        $this->assertFalse($response->isOk());
        $this->assertEquals("'continuationDetailId' parameter is missing", $response->getMessage());
    }

    public function testProcessNotAFile()
    {
        $mockContinuationDetailService = m::mock();
        $this->sm->setService('Entity\ContinuationDetail', $mockContinuationDetailService);

        $mockDocumentGenerationService = m::mock();
        $this->sm->setService('Helper\DocumentGeneration', $mockDocumentGenerationService);

        $mockDocumentDispatchService = m::mock();
        $this->sm->setService('Helper\DocumentDispatch', $mockDocumentDispatchService);

        $mockUserService = m::mock();
        $this->sm->setService('Entity\User', $mockUserService);

        $mockFile = m::mock();

        $continuationDetail = [
            'licence' => [
                'id' => 1066,
                'goodsOrPsv' => [
                    'id' => 'lcat_gv',
                ]
            ]
        ];

        $mockContinuationDetailService->shouldReceive('getDetailsForProcessing')->with(454)->once()
            ->andReturn($continuationDetail);

        $mockUserService->shouldReceive('getCurrentUser')->with()->once()->andReturn(['id' => 92]);

        $mockDocumentGenerationService->shouldReceive('generateAndStore')->with(
            'LIC_CONTD_NO_CHECKLIST_GV',
            'Checklist reminder',
            ['licence' => 1066, 'user' => 92]
        )->once()->andReturn('NOT A FILE');

        $response = $this->sut->process(['continuationDetailId' => 454]);

        $this->assertFalse($response->isOk());
    }

    public function testProcessGenerateFileException()
    {
        $mockContinuationDetailService = m::mock();
        $this->sm->setService('Entity\ContinuationDetail', $mockContinuationDetailService);

        $mockDocumentGenerationService = m::mock();
        $this->sm->setService('Helper\DocumentGeneration', $mockDocumentGenerationService);

        $mockDocumentDispatchService = m::mock();
        $this->sm->setService('Helper\DocumentDispatch', $mockDocumentDispatchService);

        $mockUserService = m::mock();
        $this->sm->setService('Entity\User', $mockUserService);

        $mockFile = m::mock('Common\Service\File\File');

        $continuationDetail = [
            'licence' => [
                'id' => 1066,
                'goodsOrPsv' => [
                    'id' => 'lcat_gv',
                ]
            ]
        ];

        $mockContinuationDetailService->shouldReceive('getDetailsForProcessing')->with(454)->once()
            ->andReturn($continuationDetail);

        $mockUserService->shouldReceive('getCurrentUser')->with()->once()->andReturn(['id' => 92]);

        $mockDocumentGenerationService->shouldReceive('generateAndStore')->with(
            'LIC_CONTD_NO_CHECKLIST_GV',
            'Checklist reminder',
            ['licence' => 1066, 'user' => 92]
        )->once()->andThrow('Exception', 'MESSAGE');

        $response = $this->sut->process(['continuationDetailId' => 454]);

        $this->assertFalse($response->isOk());
        $this->assertEquals('Failed to generate file - MESSAGE', $response->getMessage());
    }

    public function testProcessDispatchException()
    {
        $mockContinuationDetailService = m::mock();
        $this->sm->setService('Entity\ContinuationDetail', $mockContinuationDetailService);

        $mockDocumentGenerationService = m::mock();
        $this->sm->setService('Helper\DocumentGeneration', $mockDocumentGenerationService);

        $mockDocumentDispatchService = m::mock();
        $this->sm->setService('Helper\DocumentDispatch', $mockDocumentDispatchService);

        $mockUserService = m::mock();
        $this->sm->setService('Entity\User', $mockUserService);

        $mockFile = m::mock('Common\Service\File\File');

        $continuationDetail = [
            'licence' => [
                'id' => 1066,
                'goodsOrPsv' => [
                    'id' => 'lcat_gv',
                ]
            ]
        ];

        $mockContinuationDetailService->shouldReceive('getDetailsForProcessing')->with(454)->once()
            ->andReturn($continuationDetail);

        $mockUserService->shouldReceive('getCurrentUser')->with()->once()->andReturn(['id' => 92]);

        $mockDocumentGenerationService->shouldReceive('generateAndStore')->with(
            'LIC_CONTD_NO_CHECKLIST_GV',
            'Checklist reminder',
            ['licence' => 1066, 'user' => 92]
        )->once()->andReturn($mockFile);

        $mockDocumentDispatchService->shouldReceive('process')->with($mockFile, [
            'category'    => 1,
            'subCategory' => 74,
            'description' => 'Checklist reminder',
            'filename'    => 'LIC_CONTD_NO_CHECKLIST_GV.rtf',
            'licence'     => 1066,
            'isExternal'  => false,
            'isScan'  => false,
        ])->once()->andThrow('Exception', 'MESSAGE');

        $response = $this->sut->process(['continuationDetailId' => 454]);

        $this->assertFalse($response->isOk());
        $this->assertEquals('Failed to dispatch document - MESSAGE', $response->getMessage());
    }


    public function testProcess()
    {
        $mockContinuationDetailService = m::mock();
        $this->sm->setService('Entity\ContinuationDetail', $mockContinuationDetailService);

        $mockDocumentGenerationService = m::mock();
        $this->sm->setService('Helper\DocumentGeneration', $mockDocumentGenerationService);

        $mockDocumentDispatchService = m::mock();
        $this->sm->setService('Helper\DocumentDispatch', $mockDocumentDispatchService);

        $mockUserService = m::mock();
        $this->sm->setService('Entity\User', $mockUserService);

        $mockFile = m::mock('Common\Service\File\File');

        $continuationDetail = [
            'licence' => [
                'id' => 1066,
                'goodsOrPsv' => [
                    'id' => 'lcat_psv',
                ]
            ]
        ];

        $mockContinuationDetailService->shouldReceive('getDetailsForProcessing')->with(454)->once()
            ->andReturn($continuationDetail);

        $mockUserService->shouldReceive('getCurrentUser')->with()->once()->andReturn(['id' => 92]);

        $mockDocumentGenerationService->shouldReceive('generateAndStore')->with(
            'LIC_CONTD_NO_CHECKLIST_PSV',
            'Checklist reminder',
            ['licence' => 1066, 'user' => 92]
        )->once()->andReturn($mockFile);

        $mockDocumentDispatchService->shouldReceive('process')->with($mockFile, [
            'category'    => 1,
            'subCategory' => 74,
            'description' => 'Checklist reminder',
            'filename'    => 'LIC_CONTD_NO_CHECKLIST_PSV.rtf',
            'licence'     => 1066,
            'isExternal'  => false,
            'isScan'  => false,
        ])->once();

        $response = $this->sut->process(['continuationDetailId' => 454]);

        $this->assertTrue($response->isOk());
    }
}
