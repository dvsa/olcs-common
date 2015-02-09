<?php

namespace CommonTest\Controller\Lva;

use Mockery as m;
use CommonTest\Bootstrap;

/**
 * Test Abstract Financial Evidence Controller
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
        $this->markTestIncomplete('@todo mock file upload stuff');

        $form = $this->createMockForm('Lva\FinancialEvidence');

        $form->shouldReceive('setData');

        $mockAdapter = m::mock('Common\Controller\Lva\Adapters\AbstractFinancialEvidenceAdapter')
            ->shouldReceive('alterFormForLva')
            ->once()
            ->shouldReceive('getFirstVehicleRate')
            ->shouldReceive('getAdditionalVehicleRate')
            ->shouldReceive('getTotalNumberOfAuthorisedVehicles')
            ->shouldReceive('getRequiredFinance')
            ->shouldReceive('getRatesForView')->andReturn([])
            ->getMock();

        $this->sut->setAdapter($mockAdapter);

        $this->sut->shouldReceive('getIdentifier')->andReturn(123);

        $this->setService(
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

    public function testPostWithValidData()
    {
        $this->markTestIncomplete('@todo mock file upload stuff');

        $this->setPost();
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
}
