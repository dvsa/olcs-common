<?php

/**
 * Listener Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Controller\Crud;

use Common\Controller\Crud\Listener;
use CommonTest\Bootstrap;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Listener Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ListenerTest extends MockeryTestCase
{
    protected $sut;

    protected $sm;

    public function setUp()
    {
        $this->sut = new Listener();

        $this->sm = Bootstrap::getServiceManager();

        $this->sut->setServiceLocator($this->sm);
    }

    public function testAttach()
    {
        // Parmas
        $events = m::mock('\Zend\EventManager\EventManagerInterface');

        // Expectations
        $events->shouldReceive('attach')
            ->with('dispatch', [$this->sut, 'onDispatch'], 20);

        $this->sut->attach($events);
    }

    public function testOnDispatchWithoutPost()
    {
        // Params
        $mockEvent = m::mock('\Zend\Mvc\MvcEvent');
        $mockController = m::mock();

        // Mocks
        $mockRequest = m::mock();
        $this->sm->setService('Request', $mockRequest);

        // Expectations
        $mockRequest->shouldReceive('isPost')
            ->andReturn(false);

        $this->sut->setController($mockController);
        $this->assertNull($this->sut->onDispatch($mockEvent));
    }

    public function testOnDispatchWithCancel()
    {
        // Params
        $mockEvent = m::mock('\Zend\Mvc\MvcEvent');
        $mockController = m::mock();
        $postData = ['form-actions' => ['cancel' => 1]];

        // Mocks
        $mockRequest = m::mock();
        $this->sm->setService('Request', $mockRequest);
        $mockFlashMessenger = m::mock();
        $this->sm->setService('Helper\FlashMessenger', $mockFlashMessenger);

        // Expectations
        $mockRequest->shouldReceive('isPost')
            ->andReturn(true)
            ->shouldReceive('getPost')
            ->andReturn($postData);

        $mockFlashMessenger->shouldReceive('addInfoMessage')
            ->with('flash-discarded-changes');

        $mockController->shouldReceive('redirect->toRouteAjax')
            ->with(null)
            ->andReturn('REDIRECT');

        $mockEvent->shouldReceive('setResult')
            ->with('REDIRECT');

        $this->sut->setController($mockController);
        $this->assertEquals('REDIRECT', $this->sut->onDispatch($mockEvent));
    }

    /**
     * @dataProvider withoutCrudPostData
     */
    public function testOnDispatchWithoutCrudAction($postData)
    {
        // Params
        $mockEvent = m::mock('\Zend\Mvc\MvcEvent');
        $mockController = m::mock();

        // Mocks
        $mockRequest = m::mock();
        $this->sm->setService('Request', $mockRequest);

        // Expectations
        $mockRequest->shouldReceive('isPost')
            ->andReturn(true)
            ->shouldReceive('getPost')
            ->andReturn($postData);

        $this->sut->setController($mockController);
        $this->assertNull($this->sut->onDispatch($mockEvent));
    }

    /**
     * @dataProvider actionProvider
     */
    public function testOnDispatchWithUnexpectedAction($action)
    {
        // Params
        $mockEvent = m::mock('\Zend\Mvc\MvcEvent');
        $mockController = m::mock();
        $postData = [
            'table' => 'foo',
            'action' => $action
        ];
        $routeName = 'foo/bar';
        $mockConfig = [
            'crud-config' => [
                'foo/bar' => [
                    'expected' => ['requireRows' => true]
                ]
            ]
        ];

        // Mocks
        $mockRequest = m::mock();
        $this->sm->setService('Request', $mockRequest);
        $this->sm->setService('Config', $mockConfig);

        // Expectations
        $mockRequest->shouldReceive('isPost')
            ->andReturn(true)
            ->shouldReceive('getPost')
            ->andReturn($postData);

        $mockEvent->shouldReceive('getRouteMatch->getMatchedRouteName')
            ->andReturn($routeName);

        $this->sut->setController($mockController);
        $this->assertNull($this->sut->onDispatch($mockEvent));
    }

    public function testOnDispatchWithoutId()
    {
        // Params
        $mockEvent = m::mock('\Zend\Mvc\MvcEvent');
        $mockController = m::mock();
        $routeName = 'foo/bar';
        $postData = [
            'table' => 'foo',
            'action' => 'edit'
        ];
        $mockConfig = [
            'crud-config' => [
                'foo/bar' => [
                    'edit' => ['requireRows' => true]
                ]
            ]
        ];

        // Mocks
        $mockRequest = m::mock();
        $this->sm->setService('Request', $mockRequest);
        $this->sm->setService('Config', $mockConfig);
        $mockFlashMessenger = m::mock();
        $this->sm->setService('Helper\FlashMessenger', $mockFlashMessenger);

        // Expectations
        $mockRequest->shouldReceive('isPost')
            ->andReturn(true)
            ->shouldReceive('getPost')
            ->andReturn($postData);

        $mockEvent->shouldReceive('getRouteMatch->getMatchedRouteName')
            ->andReturn($routeName);

        $mockFlashMessenger->shouldReceive('addWarningMessage')
            ->with('please-select-row');

        $mockController->shouldReceive('redirect->refresh')
            ->andReturn('REDIRECT');

        $mockEvent->shouldReceive('setResult')
            ->with('REDIRECT');

        $this->sut->setController($mockController);
        $this->assertEquals('REDIRECT', $this->sut->onDispatch($mockEvent));
    }

    /**
     * @dataProvider postDataProvider
     */
    public function testOnDispatch($postData)
    {
        // Params
        $mockEvent = m::mock('\Zend\Mvc\MvcEvent');
        $mockController = m::mock();
        $routeName = 'foo/bar';
        $mockConfig = [
            'crud-config' => [
                'foo/bar' => [
                    'add' => ['requireRows' => true]
                ]
            ]
        ];

        // Mocks
        $mockRequest = m::mock();
        $this->sm->setService('Request', $mockRequest);
        $this->sm->setService('Config', $mockConfig);

        // Expectations
        $mockRequest->shouldReceive('isPost')
            ->andReturn(true)
            ->shouldReceive('getPost')
            ->andReturn($postData);

        $mockEvent->shouldReceive('getRouteMatch->getMatchedRouteName')
            ->andReturn($routeName);

        $mockController->shouldReceive('redirect->toRoute')
            ->with(null, ['action' => 'add', 'id' => 1], [], true)
            ->andReturn('REDIRECT');

        $mockEvent->shouldReceive('setResult')
            ->with('REDIRECT');

        $this->sut->setController($mockController);
        $this->assertEquals('REDIRECT', $this->sut->onDispatch($mockEvent));
    }

    public function testOnDispatchWithDefaultConfig()
    {
        // Params
        $mockEvent = m::mock('\Zend\Mvc\MvcEvent');
        $mockController = m::mock();
        $routeName = 'foo/bar';
        $postData = [
            'table' => 'foo',
            'action' => 'add'
        ];
        $mockConfig = ['crud-config' => []];

        // Mocks
        $mockRequest = m::mock();
        $this->sm->setService('Request', $mockRequest);
        $this->sm->setService('Config', $mockConfig);

        // Expectations
        $mockRequest->shouldReceive('isPost')
            ->andReturn(true)
            ->shouldReceive('getPost')
            ->andReturn($postData);

        $mockEvent->shouldReceive('getRouteMatch->getMatchedRouteName')
            ->andReturn($routeName);

        $mockController->shouldReceive('redirect->toRoute')
            ->with(null, ['action' => 'add'], [], true)
            ->andReturn('REDIRECT');

        $mockEvent->shouldReceive('setResult')
            ->with('REDIRECT');

        $this->sut->setController($mockController);
        $this->assertEquals('REDIRECT', $this->sut->onDispatch($mockEvent));
    }

    public function postDataProvider()
    {
        return [
            [
                [
                    'table' => 'foo',
                    'action' => [
                        'add' => [
                            1 => 'blah'
                        ]
                    ]
                ]
            ],
            [
                [
                    'table' => 'foo',
                    'action' => 'add',
                    'id' => 1
                ]
            ],
            [
                [
                    'table' => 'foo',
                    'action' => 'add',
                    'id' => [1]
                ]
            ]
        ];
    }

    public function actionProvider()
    {
        return [
            [
                'add'
            ],
            [
                'Add'
            ],
            [
                'add' => ['foo']
            ],
            [
                'Add' => ['foo']
            ]
        ];
    }

    public function withoutCrudPostData()
    {
        return [
            [
                [
                    'action' => 'foo'
                ]
            ],
            [
                [
                    'table' => 'foo'
                ]
            ]
        ];
    }
}
