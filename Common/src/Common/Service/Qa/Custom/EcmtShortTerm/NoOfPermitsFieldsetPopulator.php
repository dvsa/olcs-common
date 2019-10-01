<?php

namespace Common\Service\Qa\Custom\EcmtShortTerm;

use Common\Form\Elements\Custom\NoOfPermitsCombinedTotalElement;
use Common\Form\Elements\Types\Html;
use Common\Service\Helper\TranslationHelperService;
use Common\Service\Qa\FieldsetPopulatorInterface;
use Zend\Form\Fieldset;

class NoOfPermitsFieldsetPopulator implements FieldsetPopulatorInterface
{
    /** @var TranslationHelperService */
    private $translator;

    /**
     * Create service instance
     *
     * @param TranslationHelperService $translator
     *
     * @return NoOfPermitsFieldsetPopulator
     */
    public function __construct(TranslationHelperService $translator)
    {
        $this->translator = $translator;
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
        $inset = $this->translator->translate('qanda.ecmt-short-term.number-of-permits.inset');
        $line1 = $this->translator->translate('qanda.ecmt-short-term.number-of-permits.annotation.line-1');

        $line2 = $this->translator->translateReplace(
            'qanda.ecmt-short-term.number-of-permits.annotation.line-2',
            [$options['maxPermitted']]
        );

        $html = sprintf(
            '<div class="govuk-inset-text">%s</div><p><strong>%s</strong><br><span class="govuk-hint">%s</span></p>',
            $inset,
            $line1,
            $line2
        );

        $this->addHtml($fieldset, 'annotation', $html);

        $fieldset->add(
            [
                'name' => 'combinedTotalChecker',
                'type' => NoOfPermitsCombinedTotalElement::class,
                'options' => [
                    'label_attributes' => [
                        'id' => $options['emissionsCategories'][0]['name']
                    ]
                ],
            ]
        );

        $maxAuthorisedVehicles = $options['maxPermitted'];

        foreach ($options['emissionsCategories'] as $category) {
            $categoryMaxValue = $category['maxValue'];

            $fieldMaxValue = $maxAuthorisedVehicles;
            $maxExceededErrorMessage = 'qanda.ecmt-short-term.number-of-permits.error.authorisation-max-exceeded';

            if ($maxAuthorisedVehicles > $categoryMaxValue) {
                $fieldMaxValue = $categoryMaxValue;
                $maxExceededErrorMessage = $this->translator->translateReplace(
                    'qanda.ecmt-short-term.number-of-permits.error.category-max-exceeded',
                    [$categoryMaxValue]
                );
            }

            $fieldset->add(
                [
                    'type' => NoOfPermitsElement::class,
                    'name' => $category['name'],
                    'options' => [
                        'label' => $category['labelTranslationKey'],
                        'max' => $fieldMaxValue,
                        'maxExceededErrorMessage' => $maxExceededErrorMessage
                    ],
                    'attributes' => [
                        'id' => $category['name'],
                        'value' => $category['value']
                    ]
                ]
            );
        }

        if ($options['year'] == 2019) {
            $this->addHtml(
                $fieldset,
                'euro52019Info',
                sprintf(
                    '<p><strong>%s</strong></p>',
                    $this->translator->translate('qanda.ecmt-short-term.number-of-permits.euro5-2019-info')
                )
            );
        }
    }

    /**
     * Populate the fieldset with a HTML element containing the specified markup
     *
     * @param Fieldset $fieldset
     * @param string $name
     * @param string $markup
     */
    private function addHtml(Fieldset $fieldset, $name, $markup)
    {
        $fieldset->add(
            [
                'name' => $name,
                'type' => Html::class,
                'attributes' => [
                    'value' => $markup
                ]
            ]
        );
    }
}
