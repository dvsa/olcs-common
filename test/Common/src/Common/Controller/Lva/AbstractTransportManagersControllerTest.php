<?php

namespace CommonTest\Controller\Lva;

use \Mockery as m;

class AbstractTransportManagersControllerTest extends AbstractLvaControllerTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->mockController('\Common\Controller\Lva\AbstractTransportManagersController');
    }

    public function testGetIndexAction()
    {
        $form = $this->createMockForm('Lva\TransportManagers');

        $this->mockRender();

        $this->sut->indexAction();

        $this->assertEquals('transport_managers', $this->view);
    }

    public function testPostWithValidData()
    {
        $this->setPost();
        $this->sut->shouldReceive('postSave')
            ->with('transport_managers')
            ->shouldReceive('completeSection')
            ->with('transport_managers')
            ->andReturn('complete');

        $this->assertEquals(
            'complete',
            $this->sut->indexAction()
        );
    }
}
