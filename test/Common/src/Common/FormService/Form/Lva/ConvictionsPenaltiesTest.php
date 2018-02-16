<?php

/**
 * Convictions & Penalties Form Service Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */

namespace CommonTest\FormService\Form\Lva;

use Common\Form\Model\Form\Lva\Fieldset\ConvictionsPenaltiesData;
use Common\FormService\Form\Lva\ConvictionsPenalties;
use Common\RefData;
use Zend\Form\Element;
use Zend\Form\Element\Radio;
use Zend\Form\Fieldset;
use Zend\Form\Form;
use Mockery as m;
use Common\Service\Helper\TranslationHelperService;
use Zend\Di\ServiceLocator;
use Common\Service\Helper\FormHelperService;
use Common\FormService\FormServiceManager;

/**
 * Convictions & Penalties Form Service Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class ConvictionsPenaltiesTest extends AbstractLvaFormServiceTestCase
{
    protected $classToTest = ConvictionsPenalties::class;

    protected $formName = 'Lva\ConvictionsPenalties';

    /** @var  m\MockInterface|\Zend\Form\Form */
    private $mockedForm;

    public function setUp()
    {
        $this->mockedForm = m::mock(Form::class);
        parent::setUp();
    }

    public function testGetForm()
    {
        $dataTable = m::mock(ConvictionsPenaltiesData::class);
        $dataTable
            ->shouldReceive('add')
            ->with(anInstanceOf(Element::class), hasKeyValuePair('priority', integerValue()));

        $form = m::mock(Form::class);
        $form
            ->shouldReceive('get')
            ->with('data')
            ->andReturn($dataTable);

        $this->formHelper
            ->shouldReceive('createForm')
            ->once()
            ->with($this->formName)
            ->andReturn($form);

        $actual = $this->sut->getForm();

        $this->assertSame($form, $actual);
    }


    public function testAlterFormDoesNothingIfParamsNotSet()
    {
        $this->mockedForm->shouldNotReceive('get');
        $this->sut->changeFormForDirectorVariation($this->mockedForm, []);
    }

    public function testAlterFormChangesLabelsForDirectorVariationType()
    {
        $heading = 'selfserve-app-subSection-previous-history-criminal-conviction-hasConv';
        $this->mockedForm->shouldReceive('get')->with('data')->andReturn(
            m::mock(Fieldset::class)
                ->shouldReceive('get')
                ->with('question')
                ->andReturn(
                    m::mock(Radio::class)
                        ->shouldReceive('setLabel')
                        ->with('')->getMock()
                )->getMock()
                ->shouldReceive('getLabel')
                ->andReturn($heading)
                ->getMock()
                ->shouldReceive('setLabel')->with($heading . '-' . RefData::ORG_TYPE_RC."-dc")
                ->shouldReceive('getAttribute')->with('class')->andReturn('')
                ->shouldReceive('setAttribute')->with('class', ' five-eights')
                ->getMock()
                ->shouldReceive('getAttribute')->with('class')->andReturn('')
                ->shouldReceive('setAttribute')
                ->with('class', ' director-change')
                ->getMock()
        )->getMock();
        $this->mockedForm->shouldReceive('get')
            ->with('form-actions')
            ->andReturn(
                m::mock(Fieldset::class)
                    ->shouldReceive('get')
                    ->with('saveAndContinue')->andReturn(
                        m::mock(Element::class)->shouldReceive('setLabel')
                            ->with('Submit details')->getMock()
                    )->getMock()
            )->getMock();

        $this->mockedForm->shouldReceive('remove')->with('convictionsConfirmation')
                ->getMock();

        $this->formHelper
            ->shouldReceive('remove')
            ->with($this->mockedForm, 'form-actions->save')
            ->once()
            ->getMock();

        $params['variationType'] = RefData::VARIATION_TYPE_DIRECTOR_CHANGE;
        $params['organisationType'] = RefData::ORG_TYPE_RC;
        $this->sut->changeFormForDirectorVariation($this->mockedForm, $params);
    }

    public function testDirectChangeFalseIfNoParam()
    {
        $this->assertFalse($this->sut->isDirectorChange([]));
    }

    public function testDirectChangeFalseIfNotAppropriateParam()
    {
        $this->assertFalse($this->sut->isDirectorChange(['variationType' => 'inappropriate']));
    }

    public function testDirectChangeTrueIfNotAppropriateVariationType()
    {
        $this->assertTrue($this->sut->isDirectorChange(['variationType' => RefData::VARIATION_TYPE_DIRECTOR_CHANGE]));
    }
}
