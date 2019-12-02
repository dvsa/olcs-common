<?php

namespace CommonTest\Service\Qa\Custom\EcmtRemoval;

use Common\Form\Elements\Types\Html;
use Common\Service\Helper\TranslationHelperService;
use Common\Service\Qa\Custom\Common\DateSelectMustBeBefore;
use Common\Service\Qa\Custom\EcmtRemoval\PermitStartDateFieldsetPopulator;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\Form\Fieldset;
use Zend\Form\Form;

/**
 * PermitStartDateFieldsetPopulatorTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class PermitStartDateFieldsetPopulatorTest extends MockeryTestCase
{
    public function testPopulate()
    {
        $requestedDate = '2020-03-15';
        $dateMustBeBefore = '2020-05-01';

        $translator = m::mock(TranslationHelperService::class);
        $translator->shouldReceive('translate')
            ->with('qanda.ecmt-removal.permit-start-date.hint.line-1')
            ->andReturn('Choose any date up to 60 days ahead.');
        $translator->shouldReceive('translate')
            ->with('qanda.ecmt-removal.permit-start-date.hint.line-2')
            ->andReturn('For example, 10 12 2019.');

        $expectedMarkup = '<div class="govuk-hint">Choose any date up to 60 days ahead.<br>' .
            'For example, 10 12 2019.</div>';

        $expectedHtmlSpecification = [
            'name' => 'hint',
            'type' => Html::class,
            'attributes' => [
                'value' => $expectedMarkup
            ]
        ];

        $form = m::mock(Form::class);

        $fieldset = m::mock(Fieldset::class);
        $fieldset->shouldReceive('add')
            ->with($expectedHtmlSpecification)
            ->once()
            ->globally()
            ->ordered();

        $expectedElementSpecification = [
            'name' => 'qaElement',
            'type' => DateSelectMustBeBefore::class,
            'options' => [
                'dateMustBeBefore' => $dateMustBeBefore,
                'invalidDateKey' => 'qanda.ecmt-removal.permit-start-date.error.date-invalid',
                'dateInPastKey' => 'qanda.ecmt-removal.permit-start-date.error.date-in-past',
                'dateNotBeforeKey' => 'qanda.ecmt-removal.permit-start-date.error.date-too-far'
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

        $permitStartDateFieldsetPopulator = new PermitStartDateFieldsetPopulator($translator);

        $options = [
            'dateThreshold' => $dateMustBeBefore,
            'date' => [
                'value' => $requestedDate
            ]
        ];

        $permitStartDateFieldsetPopulator->populate($form, $fieldset, $options);
    }
}
