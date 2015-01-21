<?php

namespace CommonTest\Controller\Lva;

use Mockery as m;
use CommonTest\Bootstrap;

/**
 * Test Abstract Financial Evidence Controller
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class AbstractFinancialEvidenceControllerTest extends AbstractLvaControllerTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->mockController('\Common\Controller\Lva\AbstractFinancialEvidenceController');
    }

    /**
     * @todo These tests require a real service manager to run, as they are not mocking all dependencies,
     * these tests should be addresses
     */
    protected function getServiceManager()
    {
        return Bootstrap::getRealServiceManager();
    }

    public function testGetIndexAction()
    {
        $form = $this->createMockForm('Lva\FinancialEvidence');

        $form->shouldReceive('setData')
            ->with(
                [
                ]
            );

        $table = m::mock()
            ->shouldReceive('getColumn')
            ->shouldReceive('setColumn')
            ->getMock();

        // @NOTE: this data is currently hard-coded in the controller
        // as it's a placeholder page
        $tableData = [
            [
                'id' => 1,
                'fileName' => 'Amber_taxis_accounts_2012-2013.xls',
                'type' => 'Accounts'
            ]
        ];
        $this->mockService('Table', 'prepareTable')
            ->with('lva-financial-evidence', $tableData)
            ->andReturn($table);

        $tableElement = m::mock()
            ->shouldReceive('setTable')
            ->with($table)
            ->getMock();

        $tableFieldset = m::mock()
            ->shouldReceive('get')
            ->andReturn($tableElement)
            ->getMock();

        $form->shouldReceive('get')
            ->with('table')
            ->andReturn($tableFieldset);

        $this->mockRender();

        $this->sut->indexAction();

        $this->assertEquals('financial_evidence', $this->view);
    }

    public function testPostWithValidData()
    {
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
