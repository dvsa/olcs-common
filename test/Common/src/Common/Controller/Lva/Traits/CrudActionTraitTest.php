<?php

/**
 * CRUD Action Trait Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Controller\Lva\Traits;

use CommonTest\Bootstrap;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * CRUD Action Trait Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CrudActionTraitTest extends MockeryTestCase
{
    /**
     * @var \CommonTest\Controller\Lva\Traits\Stubs\CrudActionTraitStub
     */
    protected $sut;

    protected $sm;

    protected function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();

        $this->sut = m::mock('CommonTest\Controller\Lva\Traits\Stubs\CrudActionTraitStub')
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $this->sut->setServiceLocator($this->sm);
    }

    /**
     * @dataProvider providerGetCrudAction
     */
    public function testGetCrudAction($input, $expected)
    {
        $this->assertEquals($expected, $this->sut->callGetCrudAction($input));
    }

    /**
     * @dataProvider providerGetActionFromCrudAction
     */
    public function testGetActionFromCrudAction($input, $expected)
    {
        $this->assertEquals($expected, $this->sut->callGetActionFromCrudAction($input));
    }

    public function testHandleCrudActionWithoutIdWhenNotRequired()
    {
        $data = [
            'action' => 'add'
        ];
        $rowsNotRequired = ['add'];
        $childIdParamName = 'child_id';
        $route = null;

        $this->sut->shouldReceive('redirect->toRoute')
            ->once()
            ->with(null, ['action' => 'add'], ['query' => null], true)
            ->andReturn('RESPONSE');

        $response = $this->sut->callHandleCrudAction($data, $rowsNotRequired, $childIdParamName, $route);

        $this->assertEquals('RESPONSE', $response);
    }

    public function testHandleCrudActionWithoutIdWhenIdRequired()
    {
        $data = [
            'action' => 'edit'
        ];
        $rowsNotRequired = ['add'];
        $childIdParamName = 'child_id';
        $route = null;

        $mockFm = m::mock();
        $this->sm->setService('Helper\FlashMessenger', $mockFm);

        $mockFm->shouldReceive('addWarningMessage')
            ->once()
            ->with('please-select-row');

        $this->sut->shouldReceive('redirect->refresh')
            ->once()
            ->andReturn('RESPONSE');

        $response = $this->sut->callHandleCrudAction($data, $rowsNotRequired, $childIdParamName, $route);

        $this->assertEquals('RESPONSE', $response);
    }

    public function testHandleCrudActionWithIdWhenIdRequired()
    {
        $data = [
            'id' => 111,
            'action' => 'edit'
        ];
        $rowsNotRequired = ['add'];
        $childIdParamName = 'child_id';
        $route = null;

        $this->sut->shouldReceive('redirect->toRoute')
            ->once()
            ->with(null, ['action' => 'edit', 'child_id' => 111], ['query' => null], true)
            ->andReturn('RESPONSE');

        $response = $this->sut->callHandleCrudAction($data, $rowsNotRequired, $childIdParamName, $route);

        $this->assertEquals('RESPONSE', $response);
    }

    public function testHandleCrudActionWithIdWhenIdRequiredAlternativeDataFormat()
    {
        $data = [
            'action' => ['edit' => [111 => 'foo']]
        ];
        $rowsNotRequired = ['add'];
        $childIdParamName = 'child_id';
        $route = null;

        $this->sut->shouldReceive('redirect->toRoute')
            ->once()
            ->with(null, ['action' => 'edit', 'child_id' => 111], ['query' => null], true)
            ->andReturn('RESPONSE');

        $response = $this->sut->callHandleCrudAction($data, $rowsNotRequired, $childIdParamName, $route);

        $this->assertEquals('RESPONSE', $response);
    }

    public function testHandleCrudActionWithMultipleIdsWhenIdRequired()
    {
        $data = [
            'id' => [111, 222],
            'action' => 'edit'
        ];
        $rowsNotRequired = ['add'];
        $childIdParamName = 'child_id';
        $route = null;

        $this->sut->shouldReceive('redirect->toRoute')
            ->once()
            ->with(null, ['action' => 'edit', 'child_id' => '111,222'], ['query' => null], true)
            ->andReturn('RESPONSE');

        $response = $this->sut->callHandleCrudAction($data, $rowsNotRequired, $childIdParamName, $route);

        $this->assertEquals('RESPONSE', $response);
    }

    public function testHandleCrudActionWithIdWhenNotRequired()
    {
        $data = [
            'id' => 111,
            'action' => 'add'
        ];
        $rowsNotRequired = ['add'];
        $childIdParamName = 'child_id';
        $route = null;

        $this->sut->shouldReceive('redirect->toRoute')
            ->once()
            ->with(null, ['action' => 'add'], ['query' => null], true)
            ->andReturn('RESPONSE');

        $response = $this->sut->callHandleCrudAction($data, $rowsNotRequired, $childIdParamName, $route);

        $this->assertEquals('RESPONSE', $response);
    }

    public function testHandleCrudActionWithIdWhenIdRequiredWithCustomParams()
    {
        $data = [
            'id' => 111,
            'action' => 'edit'
        ];
        $rowsNotRequired = ['add'];
        $childIdParamName = 'some_other_id';
        $route = 'foo/bar';

        $this->sut->shouldReceive('redirect->toRoute')
            ->once()
            ->with('foo/bar', ['action' => 'edit', 'some_other_id' => 111], ['query' => null], true)
            ->andReturn('RESPONSE');

        $response = $this->sut->callHandleCrudAction($data, $rowsNotRequired, $childIdParamName, $route);

        $this->assertEquals('RESPONSE', $response);
    }

    public function providerGetCrudAction()
    {
        return [
            [
                [],
                null
            ],
            [
                [
                    [
                        'foo' => 'bar'
                    ]
                ],
                null
            ],
            [
                [
                    [
                        'action' => 'bar'
                    ]
                ],
                ['action' => 'bar']
            ],
            [
                [
                    [
                        'action' => 'bar'
                    ],
                    [
                        'action' => 'foo'
                    ]
                ],
                ['action' => 'bar']
            ],
            [
                [
                    [
                        'foo' => 'bar'
                    ],
                    [
                        'action' => 'foo'
                    ]
                ],
                ['action' => 'foo']
            ]
        ];
    }

    public function providerGetActionFromCrudAction()
    {
        return [
            [
                ['action' => 'BAR'],
                'bar'
            ],
            [
                ['action' => ['BAR' => 1]],
                'bar'
            ]
        ];
    }
}
