<?php

namespace CommonTest\Form\View\Helper;

use Zend\View\HelperPluginManager;
use Zend\Form\View\Helper as ZendHelper;
use Common\Form\View\Helper as CommonHelper;
use Mockery as m;

/**
 * GenericConfirmationTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class GenericConfirmationTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
{
    /**
     * @var \Common\Form\GenericConfirmation
     */
    private $sut;

    public function setUp()
    {
        $this->sut = m::mock(\Common\Form\GenericConfirmation::class)->makePartial();
        parent::setUp();
    }

    public function testSetSubmitLabel()
    {
        $fieldset = m::mock();
        $element = m::mock();

        $this->sut->shouldReceive('get')->with('form-actions')->once()->andReturn($fieldset);
        $fieldset->shouldReceive('get')->with('submit')->once()->andReturn($element);
        $element->shouldReceive('setLabel')->with('LABEL')->once();

        $this->sut->setSubmitLabel('LABEL');
    }

    public function testRemoveCancel()
    {
        $fieldset = m::mock();

        $this->sut->shouldReceive('get')->with('form-actions')->once()->andReturn($fieldset);
        $fieldset->shouldReceive('remove')->with('cancel')->once();

        $this->sut->removeCancel();
    }

    public function testSetMessage()
    {
        $fieldset = m::mock();
        $element = m::mock();

        $this->sut->shouldReceive('get')->with('messages')->once()->andReturn($fieldset);
        $fieldset->shouldReceive('get')->with('message')->once()->andReturn($element);
        $element->shouldReceive('setLabel')->with('MESSAGE')->once();

        $this->sut->setMessage('MESSAGE');
    }
}
