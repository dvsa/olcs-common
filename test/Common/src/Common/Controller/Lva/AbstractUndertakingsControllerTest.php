<?php

namespace CommonTest\Controller\Lva;

use \Mockery as m;

class AbstractUndertakingsControllerTest extends AbstractLvaControllerTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->mockController('\Common\Controller\Lva\AbstractUndertakingsController');
    }

    public function testGetIndexAction()
    {
        $form = $this->createMockForm('Lva\Undertakings');

        $this->mockRender();

        $this->sut->indexAction();

        $this->assertEquals('undertakings', $this->view);
    }

    public function testPostWithValidData()
    {
        $this->setPost();
        $this->sut->shouldReceive('postSave')
            ->with('undertakings')
            ->shouldReceive('completeSection')
            ->with('undertakings')
            ->andReturn('complete');

        $this->assertEquals(
            'complete',
            $this->sut->indexAction()
        );
    }
}
