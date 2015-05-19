<?php

/**
 * CContinuationChecklistReminderGenerateLettersTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace CommonTest\BusinessService\Service;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\BusinessService\Service\ContinuationChecklistReminderQueueLetters;
use CommonTest\Bootstrap;

/**
 * ContinuationChecklistReminderGenerateLettersTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class ContinuationChecklistReminderQueueLettersTest extends MockeryTestCase
{
    protected $sut;

    protected $sm;

    public function setUp()
    {
        $this->sut = new ContinuationChecklistReminderQueueLetters();

        $this->sm = Bootstrap::getServiceManager();

        $this->sut->setServiceLocator($this->sm);
    }

    public function testProcessMissingParam()
    {
        $response = $this->sut->process([]);

        $this->assertFalse($response->isOk());
        $this->assertEquals("'continuationDetailIds' parameter is missing", $response->getMessage());
    }

    public function testProcess()
    {
        $mockQueueEntityService = m::mock();
        $this->sm->setService('Entity\Queue', $mockQueueEntityService);

        $mockQueueEntityService->shouldReceive('multiCreate')->with(
            [
                ['type' => 'que_typ_cont_check_rem_gen_let', 'entityId' => 34, 'status' => 'que_sts_queued'],
                ['type' => 'que_typ_cont_check_rem_gen_let', 'entityId' => 66, 'status' => 'que_sts_queued'],
            ]
        )->once();

        $response = $this->sut->process(['continuationDetailIds' => [34, 66]]);

        $this->assertTrue($response->isOk());
    }
}
