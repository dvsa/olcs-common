<?php

namespace CommonTest\Controller\Lva;

use \Mockery as m;

class AbstractConvictionsPenaltiesControllerTest extends AbstractLvaControllerTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->mockController('\Common\Controller\Lva\AbstractConvictionsPenaltiesController');
    }

    public function testGetIndexAction()
    {
        $form = $this->createMockForm('Lva\ConvictionsPenalties');

        $this->mockRender();

        $this->sut->indexAction();

        $this->assertEquals('convictions_penalties', $this->view);
    }
}
