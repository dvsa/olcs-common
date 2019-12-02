<?php

namespace Common\Service\Qa\Custom\CertRoadworthiness;

use Common\Service\Qa\Custom\Common\DateSelectMustBeBefore;
use Common\Form\Elements\Types\Html;
use Common\Service\Helper\TranslationHelperService;
use Common\Service\Qa\FieldsetPopulatorInterface;
use Zend\Form\Fieldset;

class MotExpiryDateFieldsetPopulator implements FieldsetPopulatorInterface
{
    /** @var TranslationHelperService */
    private $translator;

    /**
     * Create service instance
     *
     * @param TranslationHelperService $translator
     *
     * @return MotExpiryDateFieldsetPopulator
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
        $markup = sprintf(
            '<div class="govuk-hint">%s</div>',
            $this->translator->translate('qanda.certificate-of-roadworthiness.mot-expiry-date.hint')
        );

        $fieldset->add(
            [
                'name' => 'hint',
                'type' => Html::class,
                'attributes' => [
                    'value' => $markup
                ]
            ]
        );

        $fieldset->add(
            [
                'name' => 'qaElement',
                'type' => DateSelectMustBeBefore::class,
                'options' => [
                    'dateMustBeBefore' => $options['dateThreshold'],
                    'invalidDateKey' => 'qanda.certificate-of-roadworthiness.mot-expiry-date.error.date-invalid',
                    'dateInPastKey' => 'qanda.certificate-of-roadworthiness.mot-expiry-date.error.date-in-past',
                    'dateNotBeforeKey' => 'qanda.certificate-of-roadworthiness.mot-expiry-date.error.date-too-far'
                ],
                'attributes' => [
                    'value' => $options['date']['value']
                ]
            ]
        );
    }
}
