<?php

namespace CommonTest\Service\Qa\Custom\CertRoadworthiness;

use Common\Service\Helper\TranslationHelperService;
use Common\Service\Qa\Custom\Common\DateSelectMustBeBefore;
use Common\Service\Qa\Custom\Common\HtmlAdder;
use Common\Service\Qa\Custom\CertRoadworthiness\MotExpiryDateFieldsetPopulator;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\Form\Fieldset;
use Zend\Form\Form;

/**
 * MotExpiryDateFieldsetPopulatorTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class MotExpiryDateFieldsetPopulatorTest extends MockeryTestCase
{
    public function testPopulate()
    {
        $requestedDate = '2020-03-15';
        $dateMustBeBefore = '2020-05-01';

        $translator = m::mock(TranslationHelperService::class);
        $translator->shouldReceive('translate')
            ->with('qanda.certificate-of-roadworthiness.mot-expiry-date.hint')
            ->andReturn('For example, 10 12 2019.');


        $form = m::mock(Form::class);

        $fieldset = m::mock(Fieldset::class);

        $expectedMarkup = '<div class="govuk-hint">For example, 10 12 2019.</div>';

        $htmlAdder = m::mock(HtmlAdder::class);
        $htmlAdder->shouldReceive('add')
            ->with($fieldset, 'hint', $expectedMarkup)
            ->once()
            ->globally()
            ->ordered();

        $expectedElementSpecification = [
            'name' => 'qaElement',
            'type' => DateSelectMustBeBefore::class,
            'options' => [
                'dateMustBeBefore' => $dateMustBeBefore,
                'invalidDateKey' => 'qanda.certificate-of-roadworthiness.mot-expiry-date.error.date-invalid',
                'dateInPastKey' => 'qanda.certificate-of-roadworthiness.mot-expiry-date.error.date-in-past',
                'dateNotBeforeKey' => 'qanda.certificate-of-roadworthiness.mot-expiry-date.error.date-too-far'
            ],
            'attributes' => [
                'value' => $requestedDate
            ]
        ];

        $fieldset->shouldReceive('add')
            ->with($expectedElementSpecification)
            ->once()
            ->globally()
            ->ordered();

        $motExpiryDateFieldsetPopulator = new MotExpiryDateFieldsetPopulator($translator, $htmlAdder);

        $options = [
            'dateThreshold' => $dateMustBeBefore,
            'date' => [
                'value' => $requestedDate
            ]
        ];

        $motExpiryDateFieldsetPopulator->populate($form, $fieldset, $options);
    }
}
