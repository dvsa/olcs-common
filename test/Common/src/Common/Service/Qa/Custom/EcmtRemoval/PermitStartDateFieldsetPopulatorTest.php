<?php

namespace CommonTest\Service\Qa\Custom\EcmtRemoval;

use Common\Form\Elements\Types\Html;
use Common\Service\Helper\TranslationHelperService;
use Common\Service\Qa\BaseDateFieldsetPopulator;
use Common\Service\Qa\Custom\EcmtRemoval\DateSelect;
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

        $expectedDateSelectOptions = [
            'dateMustBeBefore'
        ];

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

        $expectedDateSelectOptions = [
            'dateMustBeBefore' => $dateMustBeBefore
        ];

        $baseDateFieldsetPopulator = m::mock(BaseDateFieldsetPopulator::class);
        $baseDateFieldsetPopulator->shouldReceive('populate')
            ->with($fieldset, DateSelect::class, $expectedDateSelectOptions, $requestedDate)
            ->once()
            ->globally()
            ->ordered();

        $permitStartDateFieldsetPopulator = new PermitStartDateFieldsetPopulator(
            $baseDateFieldsetPopulator,
            $translator
        );

        $options = [
            'dateMustBeBefore' => $dateMustBeBefore,
            'date' => [
                'value' => $requestedDate
            ]
        ];

        $permitStartDateFieldsetPopulator->populate($form, $fieldset, $options);
    }
}
