<?php

namespace Common\Service\Qa\Custom\EcmtShortTerm;

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
     * @param Fieldset $fieldset
     * @param array $options
     */
    public function populate(Fieldset $fieldset, array $options)
    {
        $htmlValue = $this->translator->translateReplace(
            'qanda.ecmt-short-term.number-of-permits.annotation',
            [
                $options['year'],
                $options['maxPermitted']
            ]
        );

        $fieldset->add(
            [
                'name' => 'annotation',
                'type' => Html::class,
                'attributes' => [
                    'value' => $htmlValue
                ]
            ]
        );

        $fieldset->add(
            [
                'name' => 'combinedTotalChecker',
                'type' => NoOfPermitsCombinedTotalElement::class,
                'options' => [
                    'maxPermitted' => $options['maxPermitted']
                ]
            ]
        );

        foreach ($options['emissionsCategories'] as $category) {
            $categoryMaxValue = $category['maxValue'];

            $maxExceededErrorMessage = $this->translator->translateReplace(
                'qanda-ecmt-short-term.number-of-permits.error.category-max-exceeded',
                [$categoryMaxValue]
            );

            $fieldset->add(
                [
                    'type' => NoOfPermitsElement::class,
                    'name' => $category['name'],
                    'options' => [
                        'label' => $category['labelTranslationKey'],
                        'max' => $categoryMaxValue,
                        'maxExceededErrorMessage' => $maxExceededErrorMessage
                    ],
                    'attributes' => [
                        'value' => $category['value']
                    ]
                ]
            );
        }
    }
}
