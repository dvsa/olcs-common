<?php

namespace CommonTest\FormService\Form\Lva;

use Common\Form\Elements\InputFilters\ActionLink;
use Common\Form\Model\Form\Lva\Fieldset\ConvictionsPenaltiesData;
use Common\Form\Model\Form\Lva\Fieldset\ConvictionsPenaltiesReadMoreLink;
use Common\FormService\Form\Lva\ConvictionsPenalties;
use Common\RefData;
use Common\Service\Helper\TranslationHelperService;
use Common\Service\Helper\UrlHelperService;
use Laminas\Form\Element;
use Laminas\Form\Element\Radio;
use Laminas\Form\Fieldset;
use Laminas\Form\Form;
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

    /** @var  m\MockInterface|\Laminas\Form\Form */
    private $mockedForm;

    public function setUp(): void
    {
        $this->translator = m::mock(TranslationHelperService::class);
        $this->urlHelper = m::mock(UrlHelperService::class);
        $this->mockedForm = m::mock(Form::class);
        $this->classArgs = [$this->translator, $this->urlHelper];
        parent::setUp();
    }


    public function checkGetForm($guidePath, $guideName)
    {
        $this->translator
            ->shouldReceive('translate')
            ->andReturn($guideName);

        $this->urlHelper
            ->shouldReceive('fromRoute')
            ->with(
                'guides/guide',
                ['guide' => $guideName]
            )
            ->andReturn($guidePath);

        $dataTable = m::mock(ConvictionsPenaltiesData::class);
        $dataTable
            ->shouldReceive('add')
            ->with(
                \Hamcrest\Matchers::anInstanceOf(Element::class),
                \Hamcrest\Matchers::hasKeyValuePair('priority', \Hamcrest\Matchers::integerValue())
            );

        $ConvictionsReadMoreLink = m::mock(ConvictionsPenaltiesReadMoreLink::class);
        $ConvictionsReadMoreLink
            ->shouldReceive('get')
            ->with('readMoreLink')->andReturn(
                m::mock(ActionLink::class)->shouldReceive('setValue')->with($guidePath)->getMock()
            )->getMock();

        $this->mockedForm
            ->shouldReceive('get')->with('data')->andReturn($dataTable)
            ->shouldReceive('get')->with('convictionsReadMoreLink')->andReturn(
                $ConvictionsReadMoreLink
            )->getMock();

        $this->formHelper
            ->shouldReceive('createForm')
            ->once()
            ->with($this->formName)
            ->andReturn($this->mockedForm);

        $actual = $this->sut->getForm();

        $this->assertSame($this->mockedForm, $actual);
    }

    public function checkGetFormNi()
    {
        $this->checkGetForm(
            '/guides/convictions-and-penalties-guidance-ni/',
            'convictions-and-penalties-guidance-ni'
        );
    }

    public function checkGetFormGb()
    {
        $this->checkGetForm(
            '/guides/convictions-and-penalties-guidance-gb/',
            'convictions-and-penalties-guidance-gb'
        );
    }

    public function testGetForm()
    {
        $this->checkGetFormGb();
        $this->checkGetFormNi();
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
                ->shouldReceive('setLabel')->with($heading . '-' . RefData::ORG_TYPE_RC . "-dc")
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
