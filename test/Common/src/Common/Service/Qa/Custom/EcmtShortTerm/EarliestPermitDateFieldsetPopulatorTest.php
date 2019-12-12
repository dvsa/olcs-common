<?php

namespace CommonTest\Service\Qa\Custom\EcmtShortTerm;

use Common\Form\Elements\Types\Html;
use Common\Service\Helper\TranslationHelperService;
use Common\Service\Qa\Custom\EcmtShortTerm\EarliestPermitDateFieldsetPopulator;
use Common\Service\Qa\DateSelect;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\Form\Fieldset;
use Zend\Form\Form;

/**
 * EarliestPermitDateFieldsetPopulatorTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class EarliestPermitDateFieldsetPopulatorTest extends MockeryTestCase
{
    public function testPopulate()
    {
        $requestedDate = '2020-03-15';

        $translator = m::mock(TranslationHelperService::class);
        $translator->shouldReceive('translate')
            ->with('qanda.ecmt-short-term.earliest-permit-date.inset')
            ->andReturn('Earliest permit date inset');
        $translator->shouldReceive('translate')
            ->with('qanda.ecmt-short-term.earliest-permit-date.hint.line-1')
            ->andReturn('Earliest permit date hint line 1');
        $translator->shouldReceive('translate')
            ->with('qanda.ecmt-short-term.earliest-permit-date.hint.line-2')
            ->andReturn('Earliest permit date hint line 2');

        $expectedMarkup = '<div class="govuk-inset-text">Earliest permit date inset</div>' .
            '<div class="govuk-hint">Earliest permit date hint line 1<br>Earliest permit date hint line 2</div>';

        $expectedHtmlSpecification = [
            'name' => 'insetAndHint',
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
            'type' => DateSelect::class,
            'options' => [
                'invalidDateKey' => 'qanda.ecmt-short-term.earliest-permit-date.error.date-invalid',
                'dateInPastKey' => 'qanda.ecmt-short-term.earliest-permit-date.error.date-in-past',
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

        $earliestPermitDateFieldsetPopulator = new EarliestPermitDateFieldsetPopulator($translator);

        $options = [
            'value' => $requestedDate
        ];

        $earliestPermitDateFieldsetPopulator->populate($form, $fieldset, $options);
    }
}
