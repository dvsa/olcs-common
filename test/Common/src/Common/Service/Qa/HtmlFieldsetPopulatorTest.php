<?php

namespace CommonTest\Service\Qa;

use Common\Form\Elements\Types\Html;
use Common\Service\Helper\TranslationHelperService;
use Common\Service\Qa\HtmlFieldsetPopulator;
use Common\Service\Qa\RadioFieldsetPopulator;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\Form\Element\Hidden;
use Zend\Form\Fieldset;
use Zend\Form\Form;

/**
 * HtmlFieldsetPopulatorTest
 */
class HtmlFieldsetPopulatorTest extends MockeryTestCase
{
    public function testPopulate()
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

        $fieldset = m::mock(Fieldset::class);

        $translator = m::mock(TranslationHelperService::class);
        $translator->shouldReceive('translate')
            ->with('label1')
            ->andReturn('translated label1');

        $fieldset->shouldReceive('add')
            ->with(
                [
                    'name' => 'qaHtml',
                    'type' => Html::class,
                    'attributes' => [
                        'value' => 'translated label1'
                    ]
                ]
            )
            ->once()
            ->shouldReceive('add')
            ->with(
                [
                    'name' => 'qaElement',
                    'type' => Hidden::class,
                    'attributes' => [
                        'value' => 'value1'
                    ]
                ]
            )
            ->once();

        $htmlFieldsetPopulator = new HtmlFieldsetPopulator($translator);
        $htmlFieldsetPopulator->populate($form, $fieldset, $options);
    }
}
