<?php

/**
 * Environmental Complaint Task Test
 */
namespace CommonTest\BusinessService\Service\Cases\Complaint;

use CommonTest\Bootstrap;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\BusinessService\Service\Cases\Complaint\EnvironmentalComplaintTask;
use Common\Service\Data\CategoryDataService;

/**
 * Environmental Complaint Task Test
 */
class EnvironmentalComplaintTaskTest extends MockeryTestCase
{
    protected $sut;

    protected $sm;

    protected $bsm;

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();

        $this->bsm = m::mock('\Common\BusinessService\BusinessServiceManager')->makePartial();

        $this->sut = new EnvironmentalComplaintTask();
        $this->sut->setServiceLocator($this->sm);
        $this->sut->setBusinessServiceManager($this->bsm);
    }

    public function testProcess()
    {
        // Data
        $params = [
            'caseId' => 111,
        ];

        $expectedTaskData = [
            'category' => CategoryDataService::CATEGORY_ENVIRONMENTAL,
            'subCategory' => CategoryDataService::TASK_SUB_CATEGORY_REVIEW_COMPLAINT,
            'description' => 'Review complaint',
            'actionDate' => '2015-04-10',
            'assignedToUser' => 456,
            'assignedToTeam' => 654,
            'isClosed' => 'N',
            'urgent' => 'N',
            'assignedByUser' => 456,
            'case' => 111,
        ];

        // Mocks
        $mockDataServiceManager = m::mock();
        $this->sm->setService('DataServiceManager', $mockDataServiceManager);

        $mockCases = m::mock();
        $mockCases->shouldReceive('fetchData')->once()
            ->andReturn(['licence' => ['id' => 10, 'reviewDate' => '2015-04-10']]);
        $mockDataServiceManager->shouldReceive('get')->with('Olcs\Service\Data\Cases')->andReturn($mockCases);

        $mockUser = m::mock();
        $mockUser->shouldReceive('getCurrentUser')
            ->once()
            ->andReturn(['id' => 456, 'team' => ['id' => 654]]);
        $this->sm->setService('Entity\User', $mockUser);

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
}
