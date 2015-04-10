<?php

/**
 * Decision Test
 */
namespace CommonTest\BusinessService\Service\Cases\Submission;

use CommonTest\Bootstrap;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\BusinessService\Service\Cases\Submission\Decision;
use Common\BusinessService\Response;
use Common\Service\Data\CategoryDataService;

/**
 * Decision Test
 */
class DecisionTest extends MockeryTestCase
{
    protected $sut;

    protected $sm;

    protected $bsm;

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();

        $this->bsm = m::mock('\Common\BusinessService\BusinessServiceManager')->makePartial();

        $this->sut = new Decision();
        $this->sut->setServiceLocator($this->sm);
        $this->sut->setBusinessServiceManager($this->bsm);
    }

    /**
     * @dataProvider processDataProvider
     */
    public function testProcess($params, $expectedTaskData)
    {
        // Mocks
        $mockSubmissionAction = m::mock();
        $mockSubmissionAction->shouldReceive('save')
            ->once()
            ->with($params['data']);
        $this->sm->setService('Entity\SubmissionAction', $mockSubmissionAction);

        $mockTaskService = m::mock('\Common\BusinessService\BusinessServiceInterface');

        $this->bsm->setService('Cases\Submission\SubmissionActionTask', $mockTaskService);

        $mockBusinessResponse = m::mock('\Common\BusinessService\Response');
        $mockBusinessResponse->shouldReceive('isOk')
            ->times(empty($params['id']) ? 1 : 0)
            ->andReturn(true);

        // Expectations
        $mockTaskService->shouldReceive('process')
            ->times(empty($params['id']) ? 1 : 0)
            ->with($expectedTaskData)
            ->andReturn($mockBusinessResponse);

        $response = $this->sut->process($params);

        $this->assertEquals(Response::TYPE_SUCCESS, $response->getType());
    }

    public function processDataProvider()
    {
        return [
            // add
            [
                [
                    'submissionId' => 222,
                    'caseId' => 111,
                    'data' => [
                        'urgent' => 'N',
                        'submissionActionStatus' => 'action-status',
                        'recipientUser' => 333,
                    ],
                ],
                [
                    'submissionId' => 222,
                    'caseId' => 111,
                    'subCategory' => CategoryDataService::TASK_SUB_CATEGORY_DECISION,
                    'urgent' => 'N',
                    'submissionActionStatus' => 'action-status',
                    'recipientUser' => 333,
                ]
            ],
            // edit
            [
                [
                    'id' => 1,
                    'submissionId' => 222,
                    'caseId' => 111,
                    'data' => [
                        'id' => 1,
                        'urgent' => 'N',
                        'submissionActionStatus' => 'action-status',
                        'recipientUser' => 333,
                    ],
                ],
                [
                    'submissionId' => 222,
                    'caseId' => 111,
                    'subCategory' => CategoryDataService::TASK_SUB_CATEGORY_DECISION,
                    'urgent' => 'N',
                    'submissionActionStatus' => 'action-status',
                    'recipientUser' => 333,
                ]
            ],
        ];
    }
}
