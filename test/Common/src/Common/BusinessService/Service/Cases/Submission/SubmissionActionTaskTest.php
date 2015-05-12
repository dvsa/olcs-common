<?php

/**
 * Submission Action Task Test
 */
namespace CommonTest\BusinessService\Service\Cases\Submission;

use CommonTest\Bootstrap;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\BusinessService\Service\Cases\Submission\SubmissionActionTask;
use Common\Service\Data\CategoryDataService;

/**
 * Submission Action Task Test
 */
class SubmissionActionTaskTest extends MockeryTestCase
{
    protected $sut;

    protected $sm;

    protected $bsm;

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();

        $this->bsm = m::mock('\Common\BusinessService\BusinessServiceManager')->makePartial();

        $this->sut = new SubmissionActionTask();
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
        $mockRefData->shouldReceive('getDescription')->once()->andReturn('translated description');
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
            // decision
            [
                [
                    'caseId' => 111,
                    'submissionId' => 222,
                    'recipientUser' => 333,
                    'actionTypes' => 'action-status',
                    'subCategory' => CategoryDataService::TASK_SUB_CATEGORY_DECISION,
                    'urgent' => 'N',
                ],
                [
                    'category' => CategoryDataService::CATEGORY_SUBMISSION,
                    'subCategory' => CategoryDataService::TASK_SUB_CATEGORY_DECISION,
                    'description' => 'Licence 10 Case 111 Submission 222 Decision: translated description',
                    'actionDate' => '2015-04-10',
                    'assignedToUser' => 987,
                    'assignedToTeam' => 876,
                    'isClosed' => 'N',
                    'urgent' => 'N',
                    'assignedByUser' => 456,
                    'case' => 111,
                ]
            ],
            // recommendation
            [
                [
                    'caseId' => 112,
                    'submissionId' => 223,
                    'recipientUser' => 333,
                    'actionTypes' => 'action-status',
                    'subCategory' => CategoryDataService::TASK_SUB_CATEGORY_RECOMMENDATION,
                    'urgent' => 'Y',
                ],
                [
                    'category' => CategoryDataService::CATEGORY_SUBMISSION,
                    'subCategory' => CategoryDataService::TASK_SUB_CATEGORY_RECOMMENDATION,
                    'description' => 'Licence 10 Case 112 Submission 223 Recommendations: translated description',
                    'actionDate' => '2015-04-10',
                    'assignedToUser' => 987,
                    'assignedToTeam' => 876,
                    'isClosed' => 'N',
                    'urgent' => 'Y',
                    'assignedByUser' => 456,
                    'case' => 112,
                ]
            ],
        ];
    }
}
