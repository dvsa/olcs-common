<?php

namespace CommonTest\Controller\Lva;

use Mockery as m;
use CommonTest\Bootstrap;

/**
 * Test Abstract Discs Controller
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class AbstractDiscsControllerTest extends AbstractLvaControllerTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->mockController('\Common\Controller\Lva\AbstractDiscsController');

        $this->mockService('Script', 'loadFiles')->with(['discs', 'forms/filter']);
    }

    /**
     * @group abstractDiscsController
     */
    public function testGetIndexAction()
    {
        $form = $this->createMockForm('Lva\PsvDiscs');

        $this->sut->shouldReceive('getLicenceId')
            ->andReturn(7);

        $this->mockEntity('Licence', 'getPsvDiscs')
            ->with(7)
            ->andReturn([]);

        $this->sut
            ->shouldReceive('params')
            ->andReturn(
                m::mock()
                ->shouldReceive('fromQuery')
                ->with('includeCeased', 0)
                ->andReturn(0)
                ->getMock()
            );

        $this->getMockFormHelper()
            ->shouldReceive('createForm')
            ->with('Lva\DiscFilter')
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

    /**
     * @group abstractDiscsController
     */
    public function testIndexActionWithFilter()
    {
        $form = $this->createMockForm('Lva\PsvDiscs');

        $this->sut->shouldReceive('getLicenceId')
            ->andReturn(7);

        $this->mockEntity('Licence', 'getPsvDiscs')
            ->with(7)
            ->andReturn(
                [
                    ['ceasedDate' => null, 'discNo' => '123', 'issuedDate' => '2014-01-01'],
                    ['ceasedDate' => '2014-01-01', 'discNo' => '456', 'issuedDate' => '2014-01-01']
                ]
            );

        $this->sut
            ->shouldReceive('params')
            ->andReturn(
                m::mock()
                ->shouldReceive('fromQuery')
                ->with('includeCeased', 0)
                ->andReturn(1)
                ->getMock()
            );

        $this->getMockFormHelper()
            ->shouldReceive('createForm')
            ->with('Lva\DiscFilter')
            ->shouldReceive('populateFormTable');

        $this->setService(
            'Table',
            m::mock()
            ->shouldReceive('prepareTable')
            ->with(
                'lva-psv-discs',
                [
                    [
                        'ceasedDate' => null,
                        'discNo' => '123',
                        'issuedDate' => '2014-01-01'
                    ],
                    [
                        'ceasedDate' => '2014-01-01',
                        'discNo' => '456',
                        'issuedDate' => '2014-01-01'
                    ]
                ]
            )
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
                        'validDiscs' => 2,
                        'pendingDiscs' => 0
                    ]
                ]
            )
            ->andReturn($form);

        $this->sut->indexAction();

        $this->assertEquals('discs', $this->view);
    }

    public function testAddAction()
    {
        // Stubbed data
        $licenceId = 3;
        $stubbedPost = [
            'data' => [
                'additionalDiscs' => 1
            ]
        ];

        // Mocks
        $mockForm = m::mock();
        $mockFormHelper = m::mock();
        $this->sm->setService('Helper\Form', $mockFormHelper);
        $mockDiscsEntity = m::mock();
        $this->sm->setService('Entity\PsvDisc', $mockDiscsEntity);

        // Expectations
        $mockDiscsEntity->shouldReceive('requestDiscs')
            ->with(1, ['licence' => $licenceId]);

        $this->sut->shouldReceive('getLicenceId')
            ->andReturn($licenceId)
            ->shouldReceive('postSave')
            ->with('discs')
            ->shouldReceive('getIdentifierIndex')
            ->andReturn('licence')
            ->shouldReceive('getIdentifier')
            ->andReturn($licenceId);

        $mockForm->shouldReceive('get->remove')
            ->with('addAnother');

        $mockForm->shouldReceive('setData')
            ->with($stubbedPost)
            ->andReturnSelf()
            ->shouldReceive('isValid')
            ->andReturn(true);

        $mockFormHelper->shouldReceive('createForm')
            ->with('Lva\PsvDiscsRequest')
            ->andReturn($mockForm);

        $this->request->shouldReceive('isPost')
            ->andReturn(true);

        $this->request->shouldReceive('getPost')
            ->andReturn($stubbedPost);

        $this->sut->shouldReceive('redirect->toRoute')
            ->with(null, ['licence' => $licenceId])
            ->andReturn('REDIRECT');

        $this->mockService('Helper\FlashMessenger', 'addSuccessMessage')
            ->with('psv-discs-requested-successfully');

        $this->assertEquals('REDIRECT', $this->sut->addAction());
    }
}
