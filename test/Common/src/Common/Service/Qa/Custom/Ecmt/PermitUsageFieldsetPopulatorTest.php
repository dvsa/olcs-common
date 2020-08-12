<?php

namespace CommonTest\Service\Qa\Custom\Ecmt;

use Common\Form\Elements\Types\Html;
use Common\Service\Helper\TranslationHelperService;
use Common\Service\Qa\Custom\Ecmt\PermitUsageFieldsetPopulator;
use Common\Service\Qa\RadioFieldsetPopulator;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\Form\Fieldset;
use Zend\Form\Form;

/**
 * PermitUsageFieldsetPopulatorTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class PermitUsageFieldsetPopulatorTest extends MockeryTestCase
{
    public function testPopulate()
    {
        $options = [
            'key1' => 'value1',
            'key2' => 'value2'
        ];

        $form = m::mock(Form::class);

        $fieldset = m::mock(Fieldset::class);

        $radioFieldsetPopulator = m::mock(RadioFieldsetPopulator::class);
        $radioFieldsetPopulator->shouldReceive('populate')
            ->with($form, $fieldset, $options)
            ->once()
            ->ordered()
            ->globally();

        $translator = m::mock(TranslationHelperService::class);
        $translator->shouldReceive('translate')
            ->with('qanda.ecmt.permit-usage.footer-annotation')
            ->andReturn('We may contact you to verify your application.');

        $expectedAnnotationDefinition = [
            'name' => 'footerAnnotation',
            'type' => Html::class,
            'attributes' => [
                'value' => '<p><br><strong>We may contact you to verify your application.</strong></p>'
            ]
        ];

        $fieldset->shouldReceive('add')
            ->with($expectedAnnotationDefinition)
            ->once()
            ->ordered()
            ->globally();


        $permitUsageFieldsetPopulator = new PermitUsageFieldsetPopulator($radioFieldsetPopulator, $translator);
        $permitUsageFieldsetPopulator->populate($form, $fieldset, $options);
    }
}
