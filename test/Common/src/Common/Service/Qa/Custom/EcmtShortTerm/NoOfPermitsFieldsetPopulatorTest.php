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
    private $maxPermitted;

    private $expectedHtml;

    private $translatedLine1;
    private $translatedLine2;

    private $emissionsCategory1Name;
    private $emissionsCategory1LabelTranslationKey;
    private $emissionsCategory1MaxValue;
    private $emissionsCategory1Value;
    private $emissionsCategory1MaxExceededError;

    private $emissionsCategory2Name;
    private $emissionsCategory2LabelTranslationKey;
    private $emissionsCategory2MaxValue;
    private $emissionsCategory2Value;
    private $emissionsCategory2MaxExceededError;

    private $translator;

    private $fieldset;

    private $noOfPermitsFieldsetPopulator;

    public function setUp()
    {
        $this->maxPermitted = 17;

        $this->expectedHtml = '<p><strong>Number of permits for 2015</strong><br>' .
            '<span class="govuk-hint">17 is the maximum you can apply for this year</span></p>';

        $this->translatedLine1 = 'Number of permits for 2015';
        $this->translatedLine2 = '17 is the maximum you can apply for this year';

        $this->emissionsCategory1Name = 'requiredEuro5';
        $this->emissionsCategory1LabelTranslationKey = 'qanda.ecmt-short-term.number-of-permits.label.euro5';
        $this->emissionsCategory1MaxValue = '27';
        $this->emissionsCategory1Value = '14';
        $this->emissionsCategory1MaxExceededError = 'There are only 27 permits available for the selected emissions standard';

        $this->emissionsCategory2Name = 'requiredEuro6';
        $this->emissionsCategory2LabelTranslationKey = 'qanda.ecmt-short-term.number-of-permits.label.euro6';
        $this->emissionsCategory2MaxValue = '12';
        $this->emissionsCategory2Value = '3';
        $this->emissionsCategory2MaxExceededError = 'There are only 12 permits available for the selected emissions standard';

        $this->translator = m::mock(TranslationHelperService::class);
        $this->translator->shouldReceive('translateReplace')
            ->with(
                'qanda.ecmt-short-term.number-of-permits.annotation.line-2',
                [$this->maxPermitted]
            )
            ->andReturn($this->translatedLine2);
        $this->translator->shouldReceive('translateReplace')
            ->with(
                'qanda-ecmt-short-term.number-of-permits.error.category-max-exceeded',
                [$this->emissionsCategory1MaxValue]
            )
            ->andReturn($this->emissionsCategory1MaxExceededError);
        $this->translator->shouldReceive('translateReplace')
            ->with(
                'qanda-ecmt-short-term.number-of-permits.error.category-max-exceeded',
                [$this->emissionsCategory2MaxValue]
            )
            ->andReturn($this->emissionsCategory2MaxExceededError);

        $this->fieldset = m::mock(Fieldset::class);

        $this->fieldset->shouldReceive('add')
            ->with(
                [
                    'name' => 'annotation',
                    'type' => Html::class,
                    'attributes' => [
                        'value' => $this->expectedHtml
                    ]
                ]
            )
            ->once()
            ->ordered();

        $this->fieldset->shouldReceive('add')
            ->with(
                [
                    'name' => 'combinedTotalChecker',
                    'type' => NoOfPermitsCombinedTotalElement::class,
                    'options' => [
                        'maxPermitted' => $this->maxPermitted
                    ]
                ]
            )
            ->once()
            ->ordered();

        $this->fieldset->shouldReceive('add')
            ->with(
                [
                    'type' => NoOfPermitsElement::class,
                    'name' => $this->emissionsCategory1Name,
                    'options' => [
                        'label' => $this->emissionsCategory1LabelTranslationKey,
                        'max' => $this->emissionsCategory1MaxValue,
                        'maxExceededErrorMessage' => $this->emissionsCategory1MaxExceededError
                    ],
                    'attributes' => [
                        'value' => $this->emissionsCategory1Value
                    ]
                ]
            )
            ->once()
            ->ordered();

        $this->fieldset->shouldReceive('add')
            ->with(
                [
                    'type' => NoOfPermitsElement::class,
                    'name' => $this->emissionsCategory2Name,
                    'options' => [
                        'label' => $this->emissionsCategory2LabelTranslationKey,
                        'max' => $this->emissionsCategory2MaxValue,
                        'maxExceededErrorMessage' => $this->emissionsCategory2MaxExceededError
                    ],
                    'attributes' => [
                        'value' => $this->emissionsCategory2Value
                    ]
                ]
            )
            ->once()
            ->ordered();

        $this->noOfPermitsFieldsetPopulator = new NoOfPermitsFieldsetPopulator($this->translator);
    }

    public function testPopulate2019()
    {
        $this->translator->shouldReceive('translateReplace')
            ->with(
                'qanda.ecmt-short-term.number-of-permits.annotation.line-1.2019',
                [2019]
            )
            ->andReturn($this->translatedLine1);

        $this->translator->shouldReceive('translate')
            ->with('qanda.ecmt-short-term.number-of-permits.euro5-2019-info')
            ->andReturn('euro5 2019 info');

        $this->fieldset->shouldReceive('add')
            ->with(
                [
                    'name' => 'euro52019Info',
                    'type' => Html::class,
                    'attributes' => [
                        'value' => '<p><strong>euro5 2019 info</strong></p>'
                    ]
                ]
            )
            ->once()
            ->ordered();

        $options = [
            'year' => 2019,
            'maxPermitted' => $this->maxPermitted,
            'emissionsCategories' => [
                [
                    'name' => $this->emissionsCategory1Name,
                    'labelTranslationKey' => $this->emissionsCategory1LabelTranslationKey,
                    'maxValue' => $this->emissionsCategory1MaxValue,
                    'value' => $this->emissionsCategory1Value
                ],
                [
                    'name' => $this->emissionsCategory2Name,
                    'labelTranslationKey' => $this->emissionsCategory2LabelTranslationKey,
                    'maxValue' => $this->emissionsCategory2MaxValue,
                    'value' => $this->emissionsCategory2Value
                ]
            ]
        ];

        $this->noOfPermitsFieldsetPopulator->populate($this->fieldset, $options);
    }

    /**
     * @dataProvider dpTestPopulateNot2019
     */
    public function testPopulateNot2019($year)
    {
        $this->translator->shouldReceive('translateReplace')
            ->with(
                'qanda.ecmt-short-term.number-of-permits.annotation.line-1.other',
                [$year]
            )
            ->andReturn($this->translatedLine1);

        $options = [
            'year' => $year,
            'maxPermitted' => $this->maxPermitted,
            'emissionsCategories' => [
                [
                    'name' => $this->emissionsCategory1Name,
                    'labelTranslationKey' => $this->emissionsCategory1LabelTranslationKey,
                    'maxValue' => $this->emissionsCategory1MaxValue,
                    'value' => $this->emissionsCategory1Value
                ],
                [
                    'name' => $this->emissionsCategory2Name,
                    'labelTranslationKey' => $this->emissionsCategory2LabelTranslationKey,
                    'maxValue' => $this->emissionsCategory2MaxValue,
                    'value' => $this->emissionsCategory2Value
                ]
            ]
        ];

        $this->noOfPermitsFieldsetPopulator->populate($this->fieldset, $options);
    }

    public function dpTestPopulateNot2019()
    {
        return [
            [2018],
            [2020]
        ];
    }
}
