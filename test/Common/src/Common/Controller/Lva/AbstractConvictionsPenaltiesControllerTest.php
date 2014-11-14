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

        $this->sut->shouldReceive('getApplicationId')
            ->andReturn(30);

        $this->mockEntity('Application', 'getConvictionsPenaltiesData')
            ->with(30)
            ->andReturn(
                [
                    'version' => 1,
                    'prevConviction' => 'x',
                    'convictionsConfirmation' => 'y'
                ]
            );

        $this->mockEntity('PreviousConviction', 'getDataForApplication')
            ->with(30)
            ->andReturn([]);

        $this->getMockFormHelper()
            ->shouldReceive('populateFormTable');

        $form->shouldReceive('get->get')->andReturn(m::mock('\Zend\Form\Fieldset'));

        $form->shouldReceive('setData')
            ->with(
                [
                    'data' => [
                        'version' => 1,
                        'question' => 'x',
                    ],
                    'convictionsConfirmation' => [
                        'convictionsConfirmation' => 'y'
                    ]
                ]
            );

        $this->setService(
            'Table',
            m::mock()
            ->shouldReceive('prepareTable')
            ->with('lva-convictions-penalties', [])
            ->andReturn(m::mock('\Common\Service\Table\TableBuilder'))
            ->getMock()
        );

        $this->mockRender();

        $this->sut->indexAction();

        $this->assertEquals('convictions_penalties', $this->view);
    }

    public function testPostWithValidData()
    {
        $form = $this->createMockForm('Lva\ConvictionsPenalties');

        $this->sut->shouldReceive('getApplicationId')
            ->andReturn(30);

        $postData = [
            'data' => [
                'table' => ''
            ],
            'convictionsConfirmation' => [
                'question' => 'y'
            ]
        ];

        $this->setPost($postData);

        $this->getMockFormHelper()
            ->shouldReceive('populateFormTable');

        $form->shouldReceive('setData')
            ->with($postData)
            ->andReturn($form)
            ->shouldReceive('isValid')
            ->andReturn(true)
            ->shouldReceive('get->get')
            ->andReturn(m::mock('\Zend\Form\Fieldset'));

        $this->mockEntity('PreviousConviction', 'getDataForApplication')
            ->with(30)
            ->andReturn([]);

        $this->sut->shouldReceive('getCrudAction')
            ->andReturn(null);

        $this->sut->shouldReceive('completeSection')
            ->andReturn('complete');

        $this->mockEntity('Application', 'save')
            ->with(
                [
                    'table' => '',
                    'id' => 30,
                    'prevConviction' => 'y'
                ]
            );

        $this->setService(
            'Table',
            m::mock()
            ->shouldReceive('prepareTable')
            ->with('lva-convictions-penalties', [])
            ->andReturn(m::mock('\Common\Service\Table\TableBuilder'))
            ->getMock()
        );

        $this->assertEquals(
            'complete',
            $this->sut->indexAction()
        );
    }

    public function testPostWithValidDataAndCrudAction()
    {
        $form = $this->createMockForm('Lva\ConvictionsPenalties');

        $this->sut->shouldReceive('getApplicationId')
            ->andReturn(30);

        $postData = [
            'data' => [
                'table' => ''
            ],
            'convictionsConfirmation' => [
                'question' => 'y'
            ]
        ];

        $this->setPost($postData);

        $this->getMockFormHelper()
            ->shouldReceive('populateFormTable');

        $form->shouldReceive('setData')
            ->with($postData)
            ->andReturn($form)
            ->shouldReceive('isValid')
            ->andReturn(true)
            ->shouldReceive('get->get')
            ->andReturn(m::mock('\Zend\Form\Fieldset'));

        $this->getMockFormHelper()
            ->shouldReceive('disableEmptyValidation')
            ->with($form);

        $this->mockEntity('PreviousConviction', 'getDataForApplication')
            ->with(30)
            ->andReturn([]);

        $this->sut->shouldReceive('getCrudAction')
            ->andReturn('crud-action')
            ->shouldReceive('handleCrudAction')
            ->with('crud-action')
            ->andReturn('crud');

        $this->mockEntity('Application', 'save')
            ->with(
                [
                    'table' => '',
                    'id' => 30,
                    'prevConviction' => 'y'
                ]
            );

        $this->setService(
            'Table',
            m::mock()
            ->shouldReceive('prepareTable')
            ->with('lva-convictions-penalties', [])
            ->andReturn(m::mock('\Common\Service\Table\TableBuilder'))
            ->getMock()
        );

        $this->assertEquals(
            'crud',
            $this->sut->indexAction()
        );
    }

    public function testGetAddAction()
    {
        $form = $this->createMockForm('Lva\PreviousConviction');

        $this->mockRender();

        $form->shouldReceive('setData')
            ->with([]);

        $this->assertEquals(
            'add_convictions_penalties',
            $this->sut->addAction()
        );
    }

    public function testGetEditAction()
    {
        $form = $this->createMockForm('Lva\PreviousConviction');

        $this->mockRender();

        $form->shouldReceive('setData')
            ->with(
                [
                    'data' => [
                        'foo' => 'bar'
                    ]
                ]
            )
            ->andReturn($form);

        $this->sut->shouldReceive('params')
            ->with('child_id')
            ->andReturn(123);

        $this->mockEntity('PreviousConviction', 'getData')
            ->with(123)
            ->andReturn(['foo' => 'bar']);

        $this->shouldRemoveAddAnother($form);

        $this->assertEquals(
            'edit_convictions_penalties',
            $this->sut->editAction()
        );
    }
}
