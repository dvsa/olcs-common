<?php
namespace CommonTest\FormService\Form\Lva\People;

use Common\Form\Form;
use Common\Form\Model\Form\Licence\AddPerson;
use Common\FormService\Form\Lva\People\LicenceAddPerson as Sut;
use Common\FormService\FormServiceManager;
use Common\Service\Helper\FormHelperService;
use Common\Service\Helper\TranslationHelperService;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\Di\ServiceLocator;
use Zend\Form\Element;
use Zend\Form\Fieldset;

class LicenceAddPersonTest extends MockeryTestCase
{
    private $sut;
    private $formHelper;
    private $fsm;

    public function setUp()
    {
        $this->formHelper = m::mock(FormHelperService::class);
        $this->fsm = m::mock(FormServiceManager::class)->makePartial();

        $translator = m::mock(TranslationHelperService::class);
        $translator
            ->shouldReceive('translate')
            ->andReturnUsing(function ($string) {
            return 'Welsh' . $string;
        });

        $mockServiceLocator = m::mock(ServiceLocator::class);
        $mockServiceLocator
            ->shouldReceive('get')
            ->with('Helper\Translation')
            ->andReturn($translator);

        $this->fsm
            ->shouldReceive('getServiceLocator')
            ->andReturn($mockServiceLocator);

        $this->sut = new Sut();
        $this->sut->setFormHelper($this->formHelper);
        $this->sut->setFormServiceLocator($this->fsm);

        parent::setUp();
    }

    public function testGetForm()
    {
        $targetElement = m::mock(AddPerson::class);
        $targetElement
            ->shouldReceive('add')
            ->with(anInstanceOf(Element::class), hasKeyValuePair('priority', integerValue()));

        $fieldset = m::mock(Fieldset::class);
        $fieldset
            ->shouldReceive('getTargetElement')
            ->andReturn($targetElement);

        $fieldset
            ->shouldReceive('getAttribute')
            ->with('class')
            ->andReturn('existingClass');

        $fieldset
            ->shouldReceive('setAttribute')
            ->once()
            ->with('class', 'existingClass add-another-director-change');

        $fieldsets = ['data' => $fieldset];

        $form = m::mock(Form::class);
        $form
            ->shouldReceive('getFieldsets')
            ->andReturn($fieldsets);

        $this->formHelper->shouldReceive('createForm')->once()
            ->with(AddPerson::class)
            ->andReturn($form);

        $actual = $this->sut->getForm();
        $this->assertSame($form, $actual);
    }
}
