<?php

/**
 * Company Subsidiary Change Task Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\BusinessService\Service\Lva;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\BusinessService\Service\Lva\CompanySubsidiaryChangeTask;
use Common\Service\Data\CategoryDataService;

/**
 * Company Subsidiary Change Task Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CompanySubsidiaryChangeTaskTest extends MockeryTestCase
{
    protected $sut;

    protected $bsm;

    public function setUp()
    {
        $this->bsm = m::mock('\Common\BusinessService\BusinessServiceManager')->makePartial();

        $this->sut = new CompanySubsidiaryChangeTask();

        $this->sut->setBusinessServiceManager($this->bsm);
    }

    public function testProcess()
    {
        // Data
        $params = [
            'action' => 'action',
            'name' => 'name',
            'licenceId' => 111
        ];
        $expectedTaskData = [
            'category' => CategoryDataService::CATEGORY_APPLICATION,
            'subCategory' => CategoryDataService::TASK_SUB_CATEGORY_APPLICATION_SUBSIDIARY_DIGITAL,
            'description' => 'Subsidiary company action - name',
            'licence' => 111
        ];

        // Mocks
        $mockTaskService = m::mock('\Common\BusinessService\BusinessServiceInterface');

        $this->bsm->setService('Lva\Task', $mockTaskService);

        // Expectations
        $mockTaskService->shouldReceive('process')
            ->once()
            ->with($expectedTaskData)
            ->andReturn('RESPONSE');

        $response = $this->sut->process($params);

        $this->assertEquals('RESPONSE', $response);
    }
}
