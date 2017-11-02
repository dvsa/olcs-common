<?php


namespace CommonTest\FormService\Form\Lva\People;

use Common\Form\Form;
use Common\Form\Model\Form\Licence\AddPerson;
use Common\FormService\Form\Lva\People\LicenceAddPerson as Sut;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;

class LicenceAddPersonTest extends MockeryTestCase
{
    private $sut;
    private $formHelper;
    private $fsm;

    /**
     *
     */
    public function setUp()
    {
        $this->formHelper = m::mock('\Common\Service\Helper\FormHelperService');
        $this->fsm = m::mock('\Common\FormService\FormServiceManager')->makePartial();

        $this->sut = new Sut();
        $this->sut->setFormHelper($this->formHelper);
        $this->sut->setFormServiceLocator($this->fsm);
        parent::setUp();
    }

    public function testGetForm()
    {
        $form = m::mock(Form::class);
        $this->formHelper->shouldReceive('createForm')->once()
            ->with(AddPerson::class)
            ->andReturn($form);
        $actual = $this->sut->getForm();
        $this->assertSame($form, $actual);
    }
}
