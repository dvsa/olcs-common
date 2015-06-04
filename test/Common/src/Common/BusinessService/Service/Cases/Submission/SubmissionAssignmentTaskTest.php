<?php

/**
 * Submission Assignment Task Test
 */
namespace CommonTest\BusinessService\Service\Cases\Submission;

use CommonTest\Bootstrap;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\BusinessService\Service\Cases\Submission\SubmissionAssignmentTask;
use Common\Service\Data\CategoryDataService;

/**
 * Submission Assignment Task Test
 */
class SubmissionAssignmentTaskTest extends MockeryTestCase
{
    protected $sut;

    protected $sm;

    protected $bsm;

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();

        $this->bsm = m::mock('\Common\BusinessService\BusinessServiceManager')->makePartial();

        $this->sut = new SubmissionAssignmentTask();
        $this->sut->setServiceLocator($this->sm);
        $this->sut->setBusinessServiceManager($this->bsm);
    }

    /**
     * @dataProvider processDataProvider
     */
    public function testProcess($params, $expectedTaskData)
    {
        // Mocks
        $mockDataServiceManager = m::mock();
        $this->sm->setService('DataServiceManager', $mockDataServiceManager);

        $mockCases = m::mock();
        $mockCases->shouldReceive('fetchData')->once()->andReturn(['licence' => ['id' => 10]]);
        $mockDataServiceManager->shouldReceive('get')->with('Olcs\Service\Data\Cases')->andReturn($mockCases);

        $mockUser = m::mock();
        $mockUser->shouldReceive('getUserDetails')
            ->once()
            ->with(333)
            ->andReturn(['id' => 987, 'team' => ['id' => 876]]);
        $mockUser->shouldReceive('getCurrentUser')
            ->once()
            ->andReturn(['id' => 456]);
        $this->sm->setService('Entity\User', $mockUser);

        $mockDate = m::mock();
        $mockDate->shouldReceive('getDate')->once()->andReturn('2015-04-10');
        $this->sm->setService('Helper\Date', $mockDate);

        $mockRefData = m::mock();
        $mockRefData->shouldReceive('getDescription')->andReturn('translated description');
        $mockDataServiceManager->shouldReceive('get')->with('\Common\Service\Data\RefData')->andReturn($mockRefData);

        $mockTaskService = m::mock('\Common\BusinessService\BusinessServiceInterface');

        $this->bsm->setService('Task', $mockTaskService);

        // Expectations
        $mockTaskService->shouldReceive('process')
            ->once()
            ->with($expectedTaskData)
            ->andReturn('RESPONSE');
        $response = $this->sut->process($params);

        $this->assertEquals('RESPONSE', $response);
    }

    public function processDataProvider()
    {
        return [
            [
                [
                    'caseId' => 111,
                    'submissionId' => 222,
                    'recipientUser' => 333,
                    'subCategory' => CategoryDataService::TASK_SUB_CATEGORY_ASSIGNMENT,
                    'urgent' => 'N',
                ],
                [
                    'category' => CategoryDataService::CATEGORY_SUBMISSION,
                    'subCategory' => CategoryDataService::TASK_SUB_CATEGORY_ASSIGNMENT,
                    'description' => 'Licence 10 Case 111 Submission 222 Assigned',
                    'actionDate' => '2015-04-10',
                    'assignedToUser' => 987,
                    'assignedToTeam' => 876,
                    'isClosed' => 'N',
                    'urgent' => 'N',
                    'assignedByUser' => 456,
                    'case' => 111,
                ]
            ],
            [
                [
                    'caseId' => 112,
                    'submissionId' => 223,
                    'recipientUser' => 333,
                    'subCategory' => CategoryDataService::TASK_SUB_CATEGORY_ASSIGNMENT,
                    'urgent' => 'Y',
                ],
                [
                    'category' => CategoryDataService::CATEGORY_SUBMISSION,
                    'subCategory' => CategoryDataService::TASK_SUB_CATEGORY_ASSIGNMENT,
                    'description' => 'Licence 10 Case 112 Submission 223 Assigned',
                    'actionDate' => '2015-04-10',
                    'assignedToUser' => 987,
                    'assignedToTeam' => 876,
                    'isClosed' => 'N',
                    'urgent' => 'Y',
                    'assignedByUser' => 456,
                    'case' => 112
                ]
            ],
        ];
    }
}
