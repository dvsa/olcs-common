<?php

namespace CommonTest\Service\Qa;

use Common\Service\Qa\HtmlFieldsetPopulator;
use Common\Service\Qa\RadioFieldsetPopulator;
use Common\Service\Qa\RadioOrHtmlFieldsetPopulator;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\Form\Fieldset;
use Zend\Form\Form;

/**
 * RadioOrHtmlFieldsetPopulatorTest
 */
class RadioOrHtmlFieldsetPopulatorTest extends MockeryTestCase
{
    public function testPopulateForMultipleOptions()
    {
        $options = [
            'options' => [
                [
                    'value' => 'value1',
                    'label' => 'label1',
                ],
                [
                    'value' => 'value2',
                    'label' => 'label2',
                ],
            ]
        ];

        $form = m::mock(Form::class);

        $fieldset = m::mock(Fieldset::class);

        $radioFieldsetPopulator = m::mock(RadioFieldsetPopulator::class);
        $radioFieldsetPopulator->shouldReceive('populate')
            ->with($form, $fieldset, $options)
            ->once();

        $htmlFieldsetPopulator = m::mock(HtmlFieldsetPopulator::class);
        $htmlFieldsetPopulator->shouldReceive('populate')
            ->never();

        $radioOrHtmlFieldsetPopulator = new RadioOrHtmlFieldsetPopulator(
            $radioFieldsetPopulator,
            $htmlFieldsetPopulator
        );
        $radioOrHtmlFieldsetPopulator->populate($form, $fieldset, $options);
    }

    public function testPopulateForSingleOption()
    {
        $options = [
            'options' => [
                [
                    'value' => 'value1',
                    'label' => 'label1',
                ],
            ]
        ];

        $form = m::mock(Form::class);
        $form->shouldReceive('get')
            ->with('Submit')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('get')
            ->with('SubmitButton')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('setValue')
            ->with('permits.button.continue')
            ->once();

        $fieldset = m::mock(Fieldset::class);

        $radioFieldsetPopulator = m::mock(RadioFieldsetPopulator::class);
        $radioFieldsetPopulator->shouldReceive('populate')
            ->never();

        $htmlFieldsetPopulator = m::mock(HtmlFieldsetPopulator::class);
        $htmlFieldsetPopulator->shouldReceive('populate')
            ->with($form, $fieldset, $options)
            ->once();

        $radioOrHtmlFieldsetPopulator = new RadioOrHtmlFieldsetPopulator(
            $radioFieldsetPopulator,
            $htmlFieldsetPopulator
        );
        $radioOrHtmlFieldsetPopulator->populate($form, $fieldset, $options);
    }
}
