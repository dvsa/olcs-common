<?php

namespace CommonTest\Form\View\Helper;

use Common\Service\Qa\TextFactory;
use Common\Service\Qa\TextFieldsetPopulator;
use Common\Service\Qa\TranslateableTextHandler;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\Form\Element\Text;
use Zend\Form\Fieldset;

/**
 * TextFieldsetPopulatorTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class TextFieldsetPopulatorTest extends MockeryTestCase
{
    public function testPopulateWithHint()
    {
        $textValue = 'textValue';

        $hintOptions = [
            'key' => 'hintKey',
            'parameters' => [
                'hintParam1',
                'hintParam2'
            ],
        ];

        $translatedHint = 'translatedHint';

        $labelOptions = [
            'key' => 'labelKey',
            'parameters' => [
                'labelParam1',
                'labelParam2'
            ],
        ];

        $translatedLabel = 'translatedLabel';

        $options = [
            'value' => $textValue,
            'label' => $labelOptions,
            'hint' => $hintOptions,
        ];

        $expectedTextOptions = [
            'hint' => $translatedHint,
            'hint-class' => 'govuk-hint'
        ];

        $text = m::mock(SingleText::class);
        $text->shouldReceive('setLabel')
            ->with($translatedLabel)
            ->once();
        $text->shouldReceive('setOptions')
            ->with($expectedTextOptions)
            ->once();
        $text->shouldReceive('setValue')
            ->with($textValue)
            ->once();

        $textFactory = m::mock(TextFactory::class);
        $textFactory->shouldReceive('create')
            ->once()
            ->andReturn($text);

        $translateableTextHandler = m::mock(TranslateableTextHandler::class);
        $translateableTextHandler->shouldReceive('translate')
            ->with($labelOptions)
            ->andReturn($translatedLabel);
        $translateableTextHandler->shouldReceive('translate')
            ->with($hintOptions)
            ->andReturn($translatedHint);

        $fieldset = m::mock(Fieldset::class);
        $fieldset->shouldReceive('add')
            ->with($text)
            ->once();

        $sut = new TextFieldsetPopulator($textFactory, $translateableTextHandler);
        $sut->populate($fieldset, $options);
    }

    public function testPopulateWithoutHint()
    {
        $textValue = 'textValue';

        $labelOptions = [
            'key' => 'labelKey',
            'parameters' => [
                'labelParam1',
                'labelParam2'
            ],
        ];

        $translatedLabel = 'translatedLabel';

        $options = [
            'value' => $textValue,
            'label' => $labelOptions
        ];

        $text = m::mock(SingleText::class);
        $text->shouldReceive('setLabel')
            ->with($translatedLabel)
            ->once();
        $text->shouldReceive('setValue')
            ->with($textValue)
            ->once();

        $textFactory = m::mock(TextFactory::class);
        $textFactory->shouldReceive('create')
            ->once()
            ->andReturn($text);

        $translateableTextHandler = m::mock(TranslateableTextHandler::class);
        $translateableTextHandler->shouldReceive('translate')
            ->with($labelOptions)
            ->andReturn($translatedLabel);

        $fieldset = m::mock(Fieldset::class);
        $fieldset->shouldReceive('add')
            ->with($text)
            ->once();

        $sut = new TextFieldsetPopulator($textFactory, $translateableTextHandler);
        $sut->populate($fieldset, $options);
    }
}
