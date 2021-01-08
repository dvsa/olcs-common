<?php

namespace CommonTest\Service\Qa;

use Common\Service\Qa\FieldsetAdder;
use Common\Service\Qa\FieldsetFactory;
use Common\Service\Qa\FieldsetModifier\FieldsetModifier;
use Common\Service\Qa\FieldsetPopulatorInterface;
use Common\Service\Qa\FieldsetPopulatorProvider;
use Common\Service\Qa\UsageContext;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Laminas\Form\Fieldset;
use Laminas\Form\Form;

/**
 * FieldsetAdderTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class FieldsetAdderTest extends MockeryTestCase
{
    private $options;

    private $fieldset;

    private $form;

    private $sut;

    private $shortName = 'Cabotage';

    public function setUp(): void
    {
        $fieldsetName = 'fields123';

        $elementType = 'elementType';

        $elementOptions = [
            'elementProperty1' => 'elementValue1',
            'elementProperty2' => 'elementValue2'
        ];

        $this->options = [
            'fieldsetName' => $fieldsetName,
            'shortName' => $this->shortName,
            'type' => $elementType,
            'element' => $elementOptions
        ];

        $this->fieldset = m::mock(Fieldset::class);

        $qaWrapperFieldset = m::mock(Fieldset::class);
        $qaWrapperFieldset->shouldReceive('add')
            ->with($this->fieldset)
            ->once();

        $this->form = m::mock(Form::class);
        $this->form->shouldReceive('get')
            ->with('qa')
            ->andReturn($qaWrapperFieldset);

        $fieldsetFactory = m::mock(FieldsetFactory::class);
        $fieldsetFactory->shouldReceive('create')
            ->with($fieldsetName, $elementType)
            ->once()
            ->andReturn($this->fieldset);

        $fieldsetPopulator = m::mock(FieldsetPopulatorInterface::class);
        $fieldsetPopulator->shouldReceive('populate')
            ->with($this->form, $this->fieldset, $elementOptions)
            ->once()
            ->globally()
            ->ordered();

        $fieldsetModifier = m::mock(FieldsetModifier::class);
        $fieldsetModifier->shouldReceive('modify')
            ->with($this->fieldset)
            ->once()
            ->globally()
            ->ordered();

        $fieldsetPopulatorProvider = m::mock(FieldsetPopulatorProvider::class);
        $fieldsetPopulatorProvider->shouldReceive('get')
            ->with($elementType)
            ->once()
            ->andReturn($fieldsetPopulator);

        $this->sut = new FieldsetAdder($fieldsetPopulatorProvider, $fieldsetFactory, $fieldsetModifier);
    }

    public function testAddSelfserve()
    {
        $this->sut->add($this->form, $this->options, UsageContext::CONTEXT_SELFSERVE);
    }

    public function testAddInternal()
    {
        $this->fieldset->shouldReceive('setLabel')
            ->with($this->shortName)
            ->once()
            ->globally()
            ->ordered();

        $this->fieldset->shouldReceive('setLabelAttributes')
            ->with([])
            ->once()
            ->globally()
            ->ordered();

        $this->sut->add($this->form, $this->options, UsageContext::CONTEXT_INTERNAL);
    }
}
