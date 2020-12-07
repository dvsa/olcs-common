<?php

namespace Common\Service\Qa\Custom\EcmtRemoval;

use Common\Service\Qa\Custom\Common\DateSelectMustBeBefore;
use Common\Service\Helper\TranslationHelperService;
use Common\Service\Qa\Custom\Common\HtmlAdder;
use Common\Service\Qa\FieldsetPopulatorInterface;
use Laminas\Form\Fieldset;

class PermitStartDateFieldsetPopulator implements FieldsetPopulatorInterface
{
    /** @var TranslationHelperService */
    private $translator;

    /** @var HtmlAdder */
    private $htmlAdder;

    /**
     * Create service instance
     *
     * @param TranslationHelperService $translator
     * @param HtmlAdder $htmlAdder
     *
     * @return PermitStartDateFieldsetPopulator
     */
    public function __construct(TranslationHelperService $translator, HtmlAdder $htmlAdder)
    {
        $this->translator = $translator;
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
        $markup = sprintf(
            '<div class="govuk-hint">%s<br>%s</div>',
            $this->translator->translate('qanda.ecmt-removal.permit-start-date.hint.line-1'),
            $this->translator->translate('qanda.ecmt-removal.permit-start-date.hint.line-2')
        );

        $this->htmlAdder->add($fieldset, 'hint', $markup);

        $fieldset->add(
            [
                'name' => 'qaElement',
                'type' => DateSelectMustBeBefore::class,
                'options' => [
                    'dateMustBeBefore' => $options['dateThreshold'],
                    'invalidDateKey' => 'qanda.ecmt-removal.permit-start-date.error.date-invalid',
                    'dateInPastKey' => 'qanda.ecmt-removal.permit-start-date.error.date-in-past',
                    'dateNotBeforeKey' => 'qanda.ecmt-removal.permit-start-date.error.date-too-far'
                ],
                'attributes' => [
                    'value' => $options['date']['value']
                ]
            ]
        );
    }
}
