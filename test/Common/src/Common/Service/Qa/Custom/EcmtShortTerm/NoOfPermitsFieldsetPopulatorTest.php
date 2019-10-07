<?php

namespace CommonTest\Service\Qa\Custom\EcmtShortTerm;

use Common\Form\Elements\Custom\EcmtShortTermNoOfPermitsCombinedTotalElement;
use Common\Form\Elements\Types\Html;
use Common\Service\Helper\TranslationHelperService;
use Common\Service\Qa\Custom\EcmtShortTerm\NoOfPermitsFieldsetPopulator;
use Common\Service\Qa\Custom\EcmtShortTerm\NoOfPermitsElement;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\Form\Fieldset;
use Zend\Form\Form;

/**
 * NoOfPermitsFieldsetPopulatorTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class NoOfPermitsFieldsetPopulatorTest extends MockeryTestCase
{
    private $maxPermitted = 17;

    private $expectedHtml;

    private $translatedInset = '<p>You must choose between Euro 5 and Euro 6.</p>';
    private $translatedLine1 = 'Number of permits';
    private $translatedLine2 = '17 is the maximum you can apply for.';

    private $emissionsCategory1Name = 'requiredEuro5';
    private $emissionsCategory1LabelTranslationKey = 'qanda.ecmt-short-term.number-of-permits.label.euro5';
    private $emissionsCategory1MaxValue = '27';
    private $emissionsCategory1Value = '14';
    private $emissionsCategory1MaxExceededError = 'qanda.ecmt-short-term.number-of-permits.error.authorisation-max-exceeded';

    private $emissionsCategory2Name = 'requiredEuro6';
    private $emissionsCategory2LabelTranslationKey = 'qanda.ecmt-short-term.number-of-permits.label.euro6';
    private $emissionsCategory2MaxValue = '12';
    private $emissionsCategory2Value = '3';
    private $emissionsCategory2MaxExceededError = 'There are only 12 permits available for the selected emissions standard';

    private $translator;

    private $fieldset;

    private $noOfPermitsFieldsetPopulator;

    private $form;

    public function setUp()
    {
        $this->expectedHtml = '<div class="govuk-inset-text"><p>You must choose between Euro 5 and Euro 6.</p></div>' .
            '<p><strong>Number of permits</strong><br>' .
            '<span class="govuk-hint">17 is the maximum you can apply for.</span></p>';

        $this->translator = m::mock(TranslationHelperService::class);
        $this->translator->shouldReceive('translate')
            ->with('qanda.ecmt-short-term.number-of-permits.inset')
            ->andReturn($this->translatedInset);
        $this->translator->shouldReceive('translate')
            ->with('qanda.ecmt-short-term.number-of-permits.annotation.line-1')
            ->andReturn($this->translatedLine1);
        $this->translator->shouldReceive('translateReplace')
            ->with(
                'qanda.ecmt-short-term.number-of-permits.annotation.line-2',
                [$this->maxPermitted]
            )
            ->andReturn($this->translatedLine2);
        $this->translator->shouldReceive('translateReplace')
            ->with(
                'qanda.ecmt-short-term.number-of-permits.error.category-max-exceeded',
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
                    'type' => EcmtShortTermNoOfPermitsCombinedTotalElement::class,
                    'options' => [
                        'label_attributes' => [
                            'id' => $this->emissionsCategory1Name
                        ]
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
                        'max' => $this->maxPermitted,
                        'maxExceededErrorMessage' => $this->emissionsCategory1MaxExceededError
                    ],
                    'attributes' => [
                        'id' => $this->emissionsCategory1Name,
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
                        'id' => $this->emissionsCategory2Name,
                        'value' => $this->emissionsCategory2Value
                    ]
                ]
            )
            ->once()
            ->ordered();

        $this->form = m::mock(Form::class);

        $this->noOfPermitsFieldsetPopulator = new NoOfPermitsFieldsetPopulator($this->translator);
    }

    public function testPopulate2019()
    {
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

        $this->noOfPermitsFieldsetPopulator->populate($this->form, $this->fieldset, $options);
    }

    /**
     * @dataProvider dpTestPopulateNot2019
     */
    public function testPopulateNot2019($year)
    {
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

        $this->noOfPermitsFieldsetPopulator->populate($this->form, $this->fieldset, $options);
    }

    public function dpTestPopulateNot2019()
    {
        return [
            [2020],
            [2021]
        ];
    }
}
