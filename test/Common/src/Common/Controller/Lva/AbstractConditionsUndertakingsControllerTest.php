<?php

namespace CommonTest\Controller\Lva;

use \Mockery as m;

class AbstractConditionsUndertakingsControllerTest extends AbstractLvaControllerTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->mockController('\Common\Controller\Lva\AbstractConditionsUndertakingsController');
    }

    public function testGetIndexAction()
    {
        $form = $this->createMockForm('Lva\ConditionsUndertakings');

        $this->mockRender();

        $this->sut->indexAction();

        $this->assertEquals('conditions_undertakings', $this->view);
    }

    public function testPostWithValidData()
    {
        $this->setPost();
        $this->sut->shouldReceive('postSave')
            ->with('conditions_undertakings')
            ->shouldReceive('completeSection')
            ->with('conditions_undertakings')
            ->andReturn('complete');

        $this->assertEquals(
            'complete',
            $this->sut->indexAction()
        );
    }
}
