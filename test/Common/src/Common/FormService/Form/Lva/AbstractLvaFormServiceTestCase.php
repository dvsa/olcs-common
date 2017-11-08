<?php

/**
 * Abstract LVA Form Service Test Case
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace CommonTest\FormService\Form\Lva;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\Form\Form;

/**
 * Abstract LVA Form Service Test Case
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
abstract class AbstractLvaFormServiceTestCase extends MockeryTestCase
{
    protected $classToTest = 'override_me';

    protected $formName = 'override_me_too';

    protected $sut;

    protected $formHelper;

    protected $fsm;

    public function setUp()
    {
        $this->formHelper = m::mock('\Common\Service\Helper\FormHelperService');
        $this->fsm = m::mock('\Common\FormService\FormServiceManager')->makePartial();

        $class = $this->classToTest;
        $this->sut = new $class();
        $this->sut->setFormHelper($this->formHelper);
        $this->sut->setFormServiceLocator($this->fsm);
    }

    public function testGetForm()
    {
        // Mocks
        $mockForm = m::mock(Form::class)->shouldReceive('get')->withAnyArgs()->getMock();

        $this->formHelper->shouldReceive('createForm')
            ->with($this->formName)
            ->andReturn($mockForm);

        $form = $this->sut->getForm();

        $this->assertSame($mockForm, $form);
    }
}
