<?php

/**
 * Abstract Controller Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Controller\Lva;

use CommonTest\Bootstrap;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Abstract Controller Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class AbstractControllerTest extends MockeryTestCase
{
    protected $sut;

    protected $sm;

    public function setUp()
    {
        $this->markTestSkipped();
        $this->sm = Bootstrap::getServiceManager();

        $this->sut = new Stubs\LvaControllerStub();
        $this->sut->setServiceLocator($this->sm);
    }

    public function testPreDispatch()
    {
        $this->assertEquals(null, $this->sut->getLoggedInUser());

        $this->sut->callPreDispatch();

        $this->assertEquals(1, $this->sut->getLoggedInUser());
    }

    public function testSetLoggedInUser()
    {
        $this->assertEquals(null, $this->sut->getLoggedInUser());

        $this->sut->setLoggedInUser(7);

        $this->assertEquals(7, $this->sut->getLoggedInUser());
    }

    public function testIsButtonPressedWithoutRequest()
    {
        // Mocks
        $request = m::mock();
        $this->sut->setRequest($request);

        // Expectations
        $request->shouldReceive('isPost')
            ->andReturn(false);

        $this->assertFalse($this->sut->callIsButtonPressed('foo'));
    }

    public function testIsButtonPressedWithoutPress()
    {
        // Data
        $post = [];

        // Mocks
        $request = m::mock();
        $this->sut->setRequest($request);

        // Expectations
        $request->shouldReceive('isPost')
            ->andReturn(true)
            ->shouldReceive('getPost')
            ->andReturn($post);

        $this->assertFalse($this->sut->callIsButtonPressed('foo'));
    }

    public function testIsButtonPressedWithPress()
    {
        // Data
        $post = [
            'form-actions' => ['foo' => 1]
        ];

        // Mocks
        $request = m::mock();
        $this->sut->setRequest($request);

        // Expectations
        $request->shouldReceive('isPost')
            ->andReturn(true)
            ->shouldReceive('getPost')
            ->andReturn($post);

        $this->assertTrue($this->sut->callIsButtonPressed('foo'));
    }

    public function testHasConditions()
    {
        // Mocks
        $mockLicence = m::mock();
        $this->sm->setService('Entity\Licence', $mockLicence);

        $mockParams = m::mock('\Zend\Mvc\Controller\Plugin\Params');

        $pm = m::mock('\Zend\Mvc\Controller\PluginManager')->makePartial();
        $pm->setService('params', $mockParams);

        $this->sut->setPluginManager($pm);

        // Expectations
        $mockLicence->shouldReceive('hasApprovedUnfulfilledConditions')
            ->with(111)
            ->andReturn(true);

        $mockParams->shouldReceive('setController')
            ->with($this->sut)
            ->shouldReceive('__invoke')
            ->with('licence')
            ->andReturn(111);

        $this->sut->setLva('licence');
        $this->assertTrue($this->sut->callHasConditions());
    }

    public function testGetTypeOfLicenceData()
    {
        // Mocks
        $mockLicence = m::mock();
        $this->sm->setService('Entity\Licence', $mockLicence);

        $mockParams = m::mock('\Zend\Mvc\Controller\Plugin\Params');

        $pm = m::mock('\Zend\Mvc\Controller\PluginManager')->makePartial();
        $pm->setService('params', $mockParams);

        $this->sut->setPluginManager($pm);

        // Expectations
        $mockLicence->shouldReceive('getTypeOfLicenceData')
            ->with(111)
            ->andReturn(['foo' => 'bar']);

        $mockParams->shouldReceive('setController')
            ->with($this->sut)
            ->shouldReceive('__invoke')
            ->with('licence')
            ->andReturn(111);

        $this->sut->setLva('licence');
        $this->assertEquals(['foo' => 'bar'], $this->sut->callGetTypeOfLicenceData());
    }

    public function testGetTypeOfLicenceDataApplication()
    {
        // Mocks
        $mockApplication = m::mock();
        $this->sm->setService('Entity\Application', $mockApplication);

        $mockParams = m::mock('\Zend\Mvc\Controller\Plugin\Params');

        $pm = m::mock('\Zend\Mvc\Controller\PluginManager')->makePartial();
        $pm->setService('params', $mockParams);

        $this->sut->setPluginManager($pm);

        // Expectations
        $mockApplication->shouldReceive('getTypeOfLicenceData')
            ->with(222)
            ->andReturn(['foo' => 'bar']);

        $mockParams->shouldReceive('setController')
            ->with($this->sut)
            ->shouldReceive('__invoke')
            ->with('application')
            ->andReturn(222);

        $this->sut->setLva('application');
        $this->assertEquals(['foo' => 'bar'], $this->sut->callGetTypeOfLicenceData());
    }

    public function testGetTypeOfLicenceDataVariation()
    {
        // Mocks
        $mockApplication = m::mock();
        $this->sm->setService('Entity\Application', $mockApplication);

        $mockParams = m::mock('\Zend\Mvc\Controller\Plugin\Params');

        $pm = m::mock('\Zend\Mvc\Controller\PluginManager')->makePartial();
        $pm->setService('params', $mockParams);

        $this->sut->setPluginManager($pm);

        // Expectations
        $mockApplication->shouldReceive('getTypeOfLicenceData')
            ->with(222)
            ->andReturn(['foo' => 'bar']);

        $mockParams->shouldReceive('setController')
            ->with($this->sut)
            ->shouldReceive('__invoke')
            ->with('application')
            ->andReturn(222);

        $this->sut->setLva('variation');
        $this->assertEquals(['foo' => 'bar'], $this->sut->callGetTypeOfLicenceData());
    }

    public function testAlterFormForLva()
    {
        $form = m::mock('\Zend\Form\Form');

        $this->assertNull($this->sut->callAlterFormForLva($form));
    }

    public function testPostSave()
    {
        $this->assertNull($this->sut->callPostSave('foo'));
    }

    public function testAddCurrentMessage()
    {
        // Mocks
        $mockFlashMessenger = m::mock('\Zend\Mvc\Controller\Plugin\FlashMessenger');

        $pm = m::mock('\Zend\Mvc\Controller\PluginManager')->makePartial();
        $pm->setService('FlashMessenger', $mockFlashMessenger);

        $this->sut->setPluginManager($pm);

        // Expectations
        $mockFlashMessenger->shouldReceive('setController')
            ->with($this->sut)
            ->shouldReceive('setNamespace')
            ->with('default')
            ->andReturnSelf()
            ->shouldReceive('addMessage')
            ->with('foo')
            ->andReturnSelf()
            ->shouldReceive('addMessage')
            ->with('bar')
            ->andReturnSelf();

        $this->sut->callAddCurrentMessage('foo');
        $this->sut->callAddCurrentMessage('bar');
        $this->sut->callAttachCurrentMessages();
    }

    public function testReload()
    {
        // Mocks
        $mockRedirect = m::mock('\Common\Controller\Plugin\Redirect');

        $pm = m::mock('\Zend\Mvc\Controller\PluginManager')->makePartial();
        $pm->setService('redirect', $mockRedirect);

        $this->sut->setPluginManager($pm);

        // Expectations
        $mockRedirect->shouldReceive('setController')
            ->with($this->sut)
            ->shouldReceive('refreshAjax')
            ->andReturn('RESPONSE');

        $this->assertEquals('RESPONSE', $this->sut->callReload());
    }

    public function testGetAccessibleSections()
    {
        // Params
        $keysOnly = false;
        $typeOfLicence = [
            'goodsOrPsv' => 'foo',
            'licenceType' => 'bar'
        ];
        $sectionConfig = [
            'foo' => 'bar'
        ];
        $expectedAccessKeys = [
            '',
            'licence',
            'foo',
            'bar',
            'hasConditions'
        ];
        $sections = ['sections' => 'foo'];

        // Mocks
        $mockSectionConfig = m::mock();
        $mockAccessHelper = m::mock();
        $mockLicence = m::mock();
        $this->sm->setService('SectionConfig', $mockSectionConfig);
        $this->sm->setService('Entity\Licence', $mockLicence);
        $this->sm->setService('Helper\Access', $mockAccessHelper);

        $mockParams = m::mock('\Zend\Mvc\Controller\Plugin\Params');

        $pm = m::mock('\Zend\Mvc\Controller\PluginManager')->makePartial();
        $pm->setService('params', $mockParams);

        $this->sut->setPluginManager($pm);

        // Expectations
        $mockLicence->shouldReceive('getTypeOfLicenceData')
            ->with(111)
            ->andReturn($typeOfLicence)
            ->shouldReceive('hasApprovedUnfulfilledConditions')
            ->with(111)
            ->andReturn(true);

        $mockParams->shouldReceive('setController')
            ->with($this->sut)
            ->shouldReceive('__invoke')
            ->with('licence')
            ->andReturn(111);

        $mockSectionConfig->shouldReceive('getAll')
            ->andReturn($sectionConfig);

        $mockAccessHelper->shouldReceive('setSections')
            ->with($sectionConfig)
            ->andReturnSelf()
            ->shouldReceive('getAccessibleSections')
            ->with($expectedAccessKeys)
            ->andReturn($sections);

        $this->sut->setLva('licence');

        $return = $this->sut->callGetAccessibleSections($keysOnly);

        $this->assertEquals(['sections' => 'foo'], $return);
    }

    public function testGetAccessibleSectionsKeysOnly()
    {
        // Params
        $keysOnly = true;
        $typeOfLicence = [
            'goodsOrPsv' => 'foo',
            'licenceType' => 'bar'
        ];
        $sectionConfig = [
            'foo' => 'bar'
        ];
        $expectedAccessKeys = [
            '',
            'licence',
            'foo',
            'bar',
            'hasConditions'
        ];
        $sections = ['sections' => 'foo'];

        // Mocks
        $mockSectionConfig = m::mock();
        $mockAccessHelper = m::mock();
        $mockLicence = m::mock();
        $this->sm->setService('SectionConfig', $mockSectionConfig);
        $this->sm->setService('Entity\Licence', $mockLicence);
        $this->sm->setService('Helper\Access', $mockAccessHelper);

        $mockParams = m::mock('\Zend\Mvc\Controller\Plugin\Params');

        $pm = m::mock('\Zend\Mvc\Controller\PluginManager')->makePartial();
        $pm->setService('params', $mockParams);

        $this->sut->setPluginManager($pm);

        // Expectations
        $mockLicence->shouldReceive('getTypeOfLicenceData')
            ->with(111)
            ->andReturn($typeOfLicence)
            ->shouldReceive('hasApprovedUnfulfilledConditions')
            ->with(111)
            ->andReturn(true);

        $mockParams->shouldReceive('setController')
            ->with($this->sut)
            ->shouldReceive('__invoke')
            ->with('licence')
            ->andReturn(111);

        $mockSectionConfig->shouldReceive('getAll')
            ->andReturn($sectionConfig);

        $mockAccessHelper->shouldReceive('setSections')
            ->with($sectionConfig)
            ->andReturnSelf()
            ->shouldReceive('getAccessibleSections')
            ->with($expectedAccessKeys)
            ->andReturn($sections);

        $this->sut->setLva('licence');

        $return = $this->sut->callGetAccessibleSections($keysOnly);

        $this->assertEquals(['sections'], $return);
    }
}
