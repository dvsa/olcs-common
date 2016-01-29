<?php

/**
 * Submission Test
 */
namespace CommonTest\BusinessService\Service\Cases\Submission;

use CommonTest\Bootstrap;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\BusinessService\Service\Cases\Submission\Submission;
use Common\Service\Data\CategoryDataService;

/**
 * Submission Test
 */
class SubmissionTest extends MockeryTestCase
{
    protected $sut;

    protected $sm;

    protected $bsm;

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();

        $this->bsm = m::mock('\Common\BusinessService\BusinessServiceManager')->makePartial();

        $this->sut = new Submission();
        $this->sut->setServiceLocator($this->sm);
        $this->sut->setBusinessServiceManager($this->bsm);
    }

    /**
     * Tests existing id used from existing submission
     * @dataProvider processDataProvider
     */
    public function testProcessExistingSubmission($params, $expectedTaskData)
    {
        $submissionData = [
            'id' => 999,
            'caseId' => 143,
        ];

        // Mocks
        $mockDataServiceManager = m::mock();
        $this->sm->setService('DataServiceManager', $mockDataServiceManager);

        $mockSubmissionEntityService = m::mock('Entity\Submission');
        $mockSubmissionEntityService->shouldReceive('save')->once()->with(m::type('array'))->andReturnNull();
        $this->sm->setService('Entity\Submission', $mockSubmissionEntityService);

        $mockSubmissionDataService = m::mock('Olcs\Service\Data\Submission');
        $mockSubmissionDataService->shouldReceive('fetchData')->once()->with(m::type('integer'))
            ->andReturn($submissionData);
        $this->sm->setService('Olcs\Service\Data\Submission', $mockSubmissionDataService);

        $mockTaskService = m::mock('\Common\BusinessService\BusinessServiceInterface');

        $this->bsm->setService('Cases\Submission\SubmissionAssignmentTask', $mockTaskService);

        $mockBusinessResponse = m::mock('\Common\BusinessService\Response');
        $mockBusinessResponse->shouldReceive('isOk')
            ->once()
            ->andReturn(true);

        // Expectations
        $mockTaskService->shouldReceive('process')
            ->once()
            ->with($expectedTaskData)
            ->andReturn($mockBusinessResponse);
        $response = $this->sut->process($params);

        $this->assertInstanceOf('\Common\BusinessService\Response', $response);
    }

    /**
     * Tests branch where id is returned for adding a new submission
     * @dataProvider processDataProvider
     */
    public function testProcessNewSubmission($params, $expectedTaskData)
    {
        $submissionData = [
            'caseId' => 143,
        ];

        // Mocks
        $mockDataServiceManager = m::mock();
        $this->sm->setService('DataServiceManager', $mockDataServiceManager);

        $mockSubmissionEntityService = m::mock('Entity\Submission');
        $mockSubmissionEntityService->shouldReceive('save')->once()->with(m::type('array'))->andReturn(['id' => 999]);

        $this->sm->setService('Entity\Submission', $mockSubmissionEntityService);

        $mockSubmissionDataService = m::mock('Olcs\Service\Data\Submission');
        $mockSubmissionDataService->shouldReceive('fetchData')->once()->with(m::type('integer'))
            ->andReturn($submissionData);
        $this->sm->setService('Olcs\Service\Data\Submission', $mockSubmissionDataService);

        $mockTaskService = m::mock('\Common\BusinessService\BusinessServiceInterface');

        $this->bsm->setService('Cases\Submission\SubmissionAssignmentTask', $mockTaskService);

        $mockBusinessResponse = m::mock('\Common\BusinessService\Response');
        $mockBusinessResponse->shouldReceive('isOk')
            ->once()
            ->andReturn(true);

        // Expectations
        $mockTaskService->shouldReceive('process')
            ->once()
            ->with($expectedTaskData)
            ->andReturn($mockBusinessResponse);
        $response = $this->sut->process($params);

        $this->assertInstanceOf('\Common\BusinessService\Response', $response);
    }

    /**
     * Tests where submission saves, but tasks fail to be added
     * @dataProvider processDataProvider
     */
    public function testProcessFailedTask($params, $expectedTaskData)
    {
        $submissionData = [
            'caseId' => 143,
        ];

        // Mocks
        $mockDataServiceManager = m::mock();
        $this->sm->setService('DataServiceManager', $mockDataServiceManager);

        $mockSubmissionEntityService = m::mock('Entity\Submission');
        $mockSubmissionEntityService->shouldReceive('save')->once()->with(m::type('array'))->andReturn(['id' => 999]);

        $this->sm->setService('Entity\Submission', $mockSubmissionEntityService);

        $mockSubmissionDataService = m::mock('Olcs\Service\Data\Submission');
        $mockSubmissionDataService->shouldReceive('fetchData')->once()->with(m::type('integer'))
            ->andReturn($submissionData);
        $this->sm->setService('Olcs\Service\Data\Submission', $mockSubmissionDataService);

        $mockTaskService = m::mock('\Common\BusinessService\BusinessServiceInterface');

        $this->bsm->setService('Cases\Submission\SubmissionAssignmentTask', $mockTaskService);

        $mockBusinessResponse = m::mock('\Common\BusinessService\Response');
        $mockBusinessResponse->shouldReceive('isOk')
            ->once()
            ->andReturn(false);

        // Expectations
        $mockTaskService->shouldReceive('process')
            ->once()
            ->with($expectedTaskData)
            ->andReturn($mockBusinessResponse);
        $response = $this->sut->process($params);

        $this->assertInstanceOf('\Common\BusinessService\Response', $response);
    }

    public function processDataProvider()
    {
        return [
            [
                [
                    'data' => [
                        'id' => 999,
                        'recipientUser' => 333,
                        'urgent' => 'N'
                    ]
                ],
                [
                    'caseId' => 143,
                    'subCategory' => CategoryDataService::TASK_SUB_CATEGORY_ASSIGNMENT,
                    'submissionId' => 999,
                    'recipientUser' => 333,
                    'urgent' => 'N',
                ]
            ],
        ];
    }
}
