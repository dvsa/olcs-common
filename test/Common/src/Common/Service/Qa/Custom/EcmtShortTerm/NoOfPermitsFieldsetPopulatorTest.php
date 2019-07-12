<?php

namespace CommonTest\Service\Qa\Custom\EcmtShortTerm;

use Common\Form\Elements\Types\Html;
use Common\Service\Helper\TranslationHelperService;
use Common\Service\Qa\Custom\EcmtShortTerm\NoOfPermitsFieldsetPopulator;
use Common\Service\Qa\Custom\EcmtShortTerm\NoOfPermitsElement;
use Common\Service\Qa\Custom\EcmtShortTerm\NoOfPermitsCombinedTotalElement;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\Form\Fieldset;

/**
 * NoOfPermitsFieldsetPopulatorTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class NoOfPermitsFieldsetPopulatorTest extends MockeryTestCase
{
    /**
     * @dataProvider dpTestPopulate
     */
    public function testPopulate($year, $line1TranslationKey)
    {
        $maxPermitted = 17;

        $expectedHtml = '<p><strong>Number of permits for 2015</strong><br>' .
            '<span class="govuk-hint">17 is the maximum you can apply for this year</span></p>';

        $translatedLine1 = 'Number of permits for 2015';
        $translatedLine2 = '17 is the maximum you can apply for this year';

        $emissionsCategory1Name = 'requiredEuro5';
        $emissionsCategory1LabelTranslationKey = 'qanda.ecmt-short-term.number-of-permits.label.euro5';
        $emissionsCategory1MaxValue = '27';
        $emissionsCategory1Value = '14';
        $emissionsCategory1MaxExceededError = 'There are only 27 permits available for the selected emissions standard';

        $emissionsCategory2Name = 'requiredEuro6';
        $emissionsCategory2LabelTranslationKey = 'qanda.ecmt-short-term.number-of-permits.label.euro6';
        $emissionsCategory2MaxValue = '12';
        $emissionsCategory2Value = '3';
        $emissionsCategory2MaxExceededError = 'There are only 12 permits available for the selected emissions standard';

        $options = [
            'year' => $year,
            'maxPermitted' => $maxPermitted,
            'emissionsCategories' => [
                [
                    'name' => $emissionsCategory1Name,
                    'labelTranslationKey' => $emissionsCategory1LabelTranslationKey,
                    'maxValue' => $emissionsCategory1MaxValue,
                    'value' => $emissionsCategory1Value
                ],
                [
                    'name' => $emissionsCategory2Name,
                    'labelTranslationKey' => $emissionsCategory2LabelTranslationKey,
                    'maxValue' => $emissionsCategory2MaxValue,
                    'value' => $emissionsCategory2Value
                ]
            ]
        ];

        $translator = m::mock(TranslationHelperService::class);
        $translator->shouldReceive('translateReplace')
            ->with(
                $line1TranslationKey,
                [$year]
            )
            ->andReturn($translatedLine1);
        $translator->shouldReceive('translateReplace')
            ->with(
                'qanda.ecmt-short-term.number-of-permits.annotation.line-2',
                [$maxPermitted]
            )
            ->andReturn($translatedLine2);
        $translator->shouldReceive('translateReplace')
            ->with(
                'qanda-ecmt-short-term.number-of-permits.error.category-max-exceeded',
                [$emissionsCategory1MaxValue]
            )
            ->andReturn($emissionsCategory1MaxExceededError);
        $translator->shouldReceive('translateReplace')
            ->with(
                'qanda-ecmt-short-term.number-of-permits.error.category-max-exceeded',
                [$emissionsCategory2MaxValue]
            )
            ->andReturn($emissionsCategory2MaxExceededError);

        $fieldset = m::mock(Fieldset::class);

        $fieldset->shouldReceive('add')
            ->with(
                [
                    'name' => 'annotation',
                    'type' => Html::class,
                    'attributes' => [
                        'value' => $expectedHtml
                    ]
                ]
            )
            ->once()
            ->ordered();

        $fieldset->shouldReceive('add')
            ->with(
                [
                    'name' => 'combinedTotalChecker',
                    'type' => NoOfPermitsCombinedTotalElement::class,
                    'options' => [
                        'maxPermitted' => $maxPermitted
                    ]
                ]
            )
            ->once()
            ->ordered();

        $fieldset->shouldReceive('add')
            ->with(
                [
                    'type' => NoOfPermitsElement::class,
                    'name' => $emissionsCategory1Name,
                    'options' => [
                        'label' => $emissionsCategory1LabelTranslationKey,
                        'max' => $emissionsCategory1MaxValue,
                        'maxExceededErrorMessage' => $emissionsCategory1MaxExceededError
                    ],
                    'attributes' => [
                        'value' => $emissionsCategory1Value
                    ]
                ]
            )
            ->once()
            ->ordered();

        $fieldset->shouldReceive('add')
            ->with(
                [
                    'type' => NoOfPermitsElement::class,
                    'name' => $emissionsCategory2Name,
                    'options' => [
                        'label' => $emissionsCategory2LabelTranslationKey,
                        'max' => $emissionsCategory2MaxValue,
                        'maxExceededErrorMessage' => $emissionsCategory2MaxExceededError
                    ],
                    'attributes' => [
                        'value' => $emissionsCategory2Value
                    ]
                ]
            )
            ->once()
            ->ordered();

        $noOfPermitsFieldsetPopulator = new NoOfPermitsFieldsetPopulator($translator);
        $noOfPermitsFieldsetPopulator->populate($fieldset, $options);
    }

    public function dpTestPopulate()
    {
        return [
            [2019, 'qanda.ecmt-short-term.number-of-permits.annotation.line-1.2019'],
            [2020, 'qanda.ecmt-short-term.number-of-permits.annotation.line-1.other'],
            [2021, 'qanda.ecmt-short-term.number-of-permits.annotation.line-1.other']
        ];
    }
}
