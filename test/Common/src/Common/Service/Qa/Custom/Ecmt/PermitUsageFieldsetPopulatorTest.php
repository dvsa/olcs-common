<?php

namespace CommonTest\Service\Qa\Custom\Ecmt;

use Common\Service\Helper\TranslationHelperService;
use Common\Service\Qa\Custom\Common\HtmlAdder;
use Common\Service\Qa\Custom\Ecmt\PermitUsageFieldsetPopulator;
use Common\Service\Qa\RadioFieldsetPopulator;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Laminas\Form\Fieldset;
use Laminas\Form\Form;

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
            ->globally()
            ->ordered();

        $translator = m::mock(TranslationHelperService::class);
        $translator->shouldReceive('translate')
            ->with('qanda.ecmt.permit-usage.footer-annotation')
            ->andReturn('We may contact you to verify your application.');

        $expectedMarkup = '<p class="govuk-!-margin-top-7 info-box__icon-wrapper info-box__text">' .
            '<i class="info-box__icon selfserve-important"></i>We may contact you to verify your application.</p>';

        $htmlAdder = m::mock(HtmlAdder::class);
        $htmlAdder->shouldReceive('add')
            ->with($fieldset, 'footerAnnotation', $expectedMarkup)
            ->once()
            ->globally()
            ->ordered();

        $permitUsageFieldsetPopulator = new PermitUsageFieldsetPopulator(
            $radioFieldsetPopulator,
            $translator,
            $htmlAdder
        );

        $permitUsageFieldsetPopulator->populate($form, $fieldset, $options);
    }
}
