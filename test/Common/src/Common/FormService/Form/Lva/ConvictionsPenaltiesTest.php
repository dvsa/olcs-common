<?php

/**
 * Convictions & Penalties Form Service Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */

namespace CommonTest\FormService\Form\Lva;

use Common\FormService\Form\Lva\ConvictionsPenalties;
use Common\RefData;
use Zend\Form\Element;
use Zend\Form\Element\Radio;
use Zend\Form\Fieldset;
use Zend\Form\Form;
use Mockery as m;

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

    public function testAlterFormDoesNothingIfParamsNotSet()
    {
        $this->mockedForm->shouldNotReceive('get');
        $this->sut->alterForm($this->mockedForm, []);
    }

    public function testAlterFormChangesLabelsForDirectorVariationType()
    {
        $heading='selfserve-app-subSection-previous-history-criminal-conviction-hasConv';
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
                ->shouldReceive('setLabel')->with($heading.'-'.RefData::ORG_TYPE_RC)
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

        $this->mockedForm->shouldReceive('get')->with('convictionsConfirmation')->andReturn(
            m::mock(Element::class)
                ->shouldReceive('setLabel')
                ->with('I agree to:')->getMock()
                ->shouldReceive('get')->with('convictionsConfirmation')->andReturn(
                    m::mock(Element::class)
                        ->shouldReceive('setLabel')
                        ->with('director-change-convictions-penalties-conformation')->getMock()
                )->getMock()
        )->getMock();

        $this->formHelper
            ->shouldReceive('remove')
            ->with($this->mockedForm, 'form-actions->save')
            ->once()
            ->getMock();

        $params['variationType'] = RefData::VARIATION_TYPE_DIRECTOR_CHANGE;
        $params['organisationType'] = RefData::ORG_TYPE_RC;
        $this->sut->alterForm($this->mockedForm, $params);
    }
}
