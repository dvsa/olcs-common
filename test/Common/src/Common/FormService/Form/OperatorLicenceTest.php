<?php
/**
 * Created by PhpStorm.
 * User: shaunhare
 * Date: 2018-12-17
 * Time: 10:00
 */

namespace CommonTest\FormService\Form;

use Common\FormService\Form\Licence\Surrender\OperatorLicence;
use PHPUnit\Framework\TestCase;
use Mockery as m;
use \Olcs\Form\Model\Form\Surrender\OperatorLicence as OperatorLicenceForm;

class OperatorLicenceTest extends TestCase
{
    private $sut;

    public function setUp()
    {
        $this->sut = new OperatorLicence();
        $this->formHelper = m::mock('\Common\Service\Helper\FormHelperService');
        $fsm = m::mock('\Common\FormService\FormServiceManager')->makePartial();
        $this->sut->setFormHelper($this->formHelper);
        $this->sut->setFormServiceLocator($fsm);
    }

    public function testAlterForm()
    {
        $form = m::mock(\Common\Form\Form::class);

        $mockSubmit = m::mock();
        $this->formHelper->shouldReceive('createForm')->once()
            ->with(OperatorLicenceForm::class)
            ->andReturn($form);

        $formActions = m::mock(\Common\Form\Form::class);
        $formActions->shouldReceive('get')->with('submit')->andReturn(
            $mockSubmit
        );

        $mockSubmit->shouldReceive('setLabel')->once()->with('Save and continue');
        $form->shouldReceive('get')->with('form-actions')->andReturn($formActions);

        $form = $this->sut->getForm();
        $this->assertInstanceOf(\Common\Form\Form::class, $form);
    }
}
