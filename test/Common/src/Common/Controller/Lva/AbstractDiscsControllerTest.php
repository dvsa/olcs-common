<?php

namespace CommonTest\Controller\Lva;

use \Mockery as m;

class AbstractDiscsControllerTest extends AbstractLvaControllerTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->mockController('\Common\Controller\Lva\AbstractDiscsController');
    }

    public function testGetIndexAction()
    {
        $form = $this->createMockForm('Lva\PsvDiscs');

        $this->sut->shouldReceive('getLicenceId')
            ->andReturn(7);

        $this->mockEntity('Licence', 'getPsvDiscs')
            ->with(7)
            ->andReturn([]);

        $this->getMockFormHelper()
            ->shouldReceive('populateFormTable');

        $this->setService(
            'Table',
            m::mock()
            ->shouldReceive('prepareTable')
            ->with('lva-psv-discs', [])
            ->andReturn(m::mock('\Common\Service\Table\TableBuilder'))
            ->getMock()
        );

        $this->mockRender();

        $form->shouldReceive('get')
            ->with('table')
            ->andReturn(m::mock('\Zend\Form\Fieldset'))
            ->shouldReceive('setData')
            ->with(
                [
                    'data' => [
                        'validDiscs' => 0,
                        'pendingDiscs' => 0
                    ]
                ]
            )
            ->andReturn($form);

        $this->sut->indexAction();

        $this->assertEquals('discs', $this->view);
    }

    /*
    public function testPostWithValidData()
    {
    }
    */
}
