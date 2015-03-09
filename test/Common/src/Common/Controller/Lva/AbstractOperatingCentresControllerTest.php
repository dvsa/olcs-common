<?php

/**
 * Abstract Operating Centres Controller Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Controller\Lva;

use Mockery as m;

/**
 * Abstract Operating Centres Controller Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class AbstractOperatingCentresControllerTest extends AbstractLvaControllerTestCase
{
    protected $adapter;

    public function setUp()
    {
        parent::setUp();

        $this->mockController('\Common\Controller\Lva\AbstractOperatingCentresController');

        $this->adapter = m::mock('\Common\Controller\Lva\Interfaces\AdapterInterface');
        $this->sut->setAdapter($this->adapter);
    }

    public function testIndexAction()
    {
        // Stubbed data
        $id = 1;
        $stubbedData = [
            'foo' => 'bar'
        ];

        // Mocked form
        $mockForm = m::mock()
            ->shouldReceive('setData')
            ->with($stubbedData)
            ->andReturnSelf()
            ->getMock();

        // Expectations
        $this->adapter->shouldReceive('addMessages')
            ->shouldReceive('getOperatingCentresFormData')
            ->with($id)
            ->andReturn($stubbedData)
            ->shouldReceive('alterFormData')
            ->with($id, $stubbedData)
            ->andReturn($stubbedData)
            ->shouldReceive('getMainForm')
            ->andReturn($mockForm)
            ->shouldReceive('attachMainScripts');

        $this->sut->shouldReceive('params')->with('application')->andReturn($id);

        $this->sut->shouldReceive('render')
            ->with('operating_centres', $mockForm)
            ->andReturn('VIEW');

        $this->assertEquals('VIEW', $this->sut->indexAction());
    }

    public function testDeleteAction()
    {
        $form = m::mock();

        $this->sm->setService(
            'Helper\Form',
            m::mock()
                ->shouldReceive('createFormWithRequest')
                ->with('GenericDeleteConfirmation', $this->request)
                ->andReturn($form)
                ->getMock()
        );

        $this->sut
            ->shouldReceive('getDeleteModalMessageKey')
            ->andReturn('review-operating_centres_delete');

        $this->mockRender();

        $this->sut->deleteAction();
    }

    public function testDeletePostAction()
    {
        $this->setPost([]);

        $response = $this->getMock('\Zend\Http\Response');

        $this->sut->shouldReceive('delete')
            ->andReturn($response);

        $this->sut->shouldReceive('getIdentifierIndex')
            ->andReturn('application')
            ->shouldReceive('getIdentifier')
            ->andReturn(1);

        $this->sut->shouldReceive('redirect')
            ->andReturn(
                m::mock()->shouldReceive('toRouteAjax')
                    ->with(null, ['application' => 1])
                    ->andReturn('redirect')
                    ->getMock()
            );

        $this->sut->deleteAction();
    }

    public function testIndexActionPostValid()
    {
        $id = 1;
        $postData = ['table'=> [], 'data' => []];

        $alteredData = ['table'=> ['altered'], 'data' => ['altered']];

        $formData = ['table'=> ['form'], 'data' => ['form']];

        $this->setPost($postData);

        // Mocked form
        $mockForm = m::mock()
            ->shouldReceive('setData')
                ->with($alteredData)
                ->andReturnSelf()
            ->shouldReceive('getData')
                ->andReturn($formData)
            ->shouldReceive('isValid')
                ->andReturn(true)
            ->getMock();

        // Expectations
        $this->adapter->shouldReceive('addMessages')
            ->shouldReceive('alterFormData')
                ->with($id, $postData)
                ->andReturn($alteredData)
            ->shouldReceive('getMainForm')
                ->andReturn($mockForm)
            ->shouldReceive('saveMainFormData')
                ->with($formData);

        $this->sut->shouldReceive('params')->with('application')->andReturn($id);

        $redirect = m::mock();
        $this->sut->shouldReceive('completeSection')
            ->with('operating_centres')
            ->andReturn($redirect);

        $this->assertSame($redirect, $this->sut->indexAction());
    }

    public function testEditActionGet()
    {
        $id = 69;
        $this->sut->shouldReceive('params')->with('child_id')->andReturn($id);

        $addressData = ['addressData'];
        $formData    = ['formData'];

        $mockForm = m::mock()
            ->shouldReceive('setData')
                ->with($formData)
                ->andReturnSelf()
            ->shouldReceive('has')
                ->with('advertisements')
                ->andReturn(false)
            ->getMock();

        $this->adapter
            ->shouldReceive('getAddressData')
                ->with($id)
                ->andReturn($addressData)
            ->shouldReceive('formatCrudDataForForm')
                ->with($addressData, 'edit')
                ->andReturn($formData)
            ->shouldReceive('getActionForm')
                ->with('edit', $this->request)
                ->andReturn($mockForm)
            ->shouldReceive('processAddressLookupForm')
                ->with($mockForm, $this->request)
                ->andReturn(false);

        $this->mockService('Script', 'loadFile')->with('add-operating-centre');

        $this->mockRender();

        $this->assertEquals('edit_operating_centre', $this->sut->editAction());
    }

    public function testEditActionPostValid()
    {
        $id = 69;
        $this->sut->shouldReceive('params')->with('child_id')->andReturn($id);

        $postData    = ['postData'];
        $formData    = ['formData'];

        $this->setPost($postData);

        $mockForm = m::mock()
            ->shouldReceive('setData')
                ->with($formData)
                ->andReturnSelf()
            ->shouldReceive('has')
                ->with('advertisements')
                ->andReturn(false)
            ->shouldReceive('isValid')
                ->andReturn(true)
            ->shouldReceive('getData')
                ->andReturn($formData)
            ->getMock();

        $this->adapter
            ->shouldReceive('alterFormDataOnPost')
                ->with('edit', $postData, 69)
                ->andReturn($formData)
            ->shouldReceive('getActionForm')
                ->with('edit', $this->request)
                ->andReturn($mockForm)
            ->shouldReceive('processAddressLookupForm')
                ->with($mockForm, $this->request)
                ->andReturn(false)
            ->shouldReceive('saveActionFormData')
                ->with('edit', $formData, $formData);

        $redirect = m::mock();

        $this->sut->shouldReceive('handlePostSave')->andReturn($redirect);

        $this->assertSame($redirect, $this->sut->editAction());
    }
}
