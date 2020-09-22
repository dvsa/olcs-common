<?php

namespace Common\Service\Qa\Custom\Ecmt;

use Common\Form\Elements\Custom\EcmtNoOfPermitsBothElement;
use Common\Form\Elements\Custom\EcmtNoOfPermitsCombinedTotalElement;
use Common\Service\Helper\TranslationHelperService;
use Common\Service\Qa\Custom\Common\HtmlAdder;
use Common\Service\Qa\FieldsetPopulatorInterface;
use Zend\Form\Fieldset;

class NoOfPermitsBothFieldsetPopulator implements FieldsetPopulatorInterface
{
    /** @var TranslationHelperService */
    private $translator;

    /** @var NoOfPermitsBaseInsetTextGenerator */
    private $noOfPermitsBaseInsetTextGenerator;

    /** @var HtmlAdder */
    private $htmlAdder;

    /**
     * Create service instance
     *
     * @param TranslationHelperService $translator
     * @param NoOfPermitsBaseInsetTextGenerator $noOfPermitsBaseInsetTextGenerator
     * @param HtmlAdder $htmlAdder
     *
     * @return NoOfPermitsBothFieldsetPopulator
     */
    public function __construct(
        TranslationHelperService $translator,
        NoOfPermitsBaseInsetTextGenerator $noOfPermitsBaseInsetTextGenerator,
        HtmlAdder $htmlAdder
    ) {
        $this->translator = $translator;
        $this->noOfPermitsBaseInsetTextGenerator = $noOfPermitsBaseInsetTextGenerator;
        $this->htmlAdder = $htmlAdder;
    }

    /**
     * Populate the fieldset with elements based on the supplied options array
     *
     * @param mixed $form
     * @param Fieldset $fieldset
     * @param array $options
     */
    public function populate($form, Fieldset $fieldset, array $options)
    {
        $maxCanApplyFor = $options['maxCanApplyFor'];
        $maxPermitted = $options['maxPermitted'];

        $insetAndBlurbTemplate = '<div class="govuk-inset-text">%s<br><br>%s</div>' .
            '<p class="govuk-body"><strong>%s</strong><br><span class="hint">%s</span></p>';

        $maxPermittedHint = sprintf(
            $this->translator->translate('qanda.ecmt.number-of-permits.hint'),
            $maxCanApplyFor
        );

        $insetAndBlurb = sprintf(
            $insetAndBlurbTemplate,
            $this->noOfPermitsBaseInsetTextGenerator->generate($options),
            $this->translator->translate('qanda.ecmt.number-of-permits.both.inset.supplement'),
            $this->translator->translate('qanda.ecmt.number-of-permits.caption'),
            $maxPermittedHint
        );

        $this->htmlAdder->add($fieldset, 'insetAndBlurb', $insetAndBlurb);

        $fieldset->add(
            [
                'name' => 'combinedTotalChecker',
                'type' => EcmtNoOfPermitsCombinedTotalElement::class,
                'options' => [
                    'maxPermitted' => $maxPermitted
                ]
            ]
        );

        foreach ($options['emissionsCategories'] as $emissionsCategory) {
            $emissionsCategoryType = $emissionsCategory['type'];

            $textboxLabel = sprintf(
                'qanda.ecmt.number-of-permits.textbox.label.%s',
                $emissionsCategoryType
            );

            $fieldset->add(
                [
                    'type' => EcmtNoOfPermitsBothElement::class,
                    'name' => $emissionsCategoryType,
                    'options' => [
                        'label' => $textboxLabel,
                        'permitsRemaining' => $emissionsCategory['permitsRemaining'],
                        'emissionsCategory' => $emissionsCategoryType
                    ],
                    'attributes' => [
                        'value' => $emissionsCategory['value']
                    ]
                ]
            );
        }
    }
}
