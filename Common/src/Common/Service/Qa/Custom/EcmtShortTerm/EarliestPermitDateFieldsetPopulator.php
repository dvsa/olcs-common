<?php

namespace Common\Service\Qa\Custom\EcmtShortTerm;

use Common\Service\Helper\TranslationHelperService;
use Common\Service\Qa\Custom\Common\HtmlAdder;
use Common\Service\Qa\DateSelect;
use Common\Service\Qa\FieldsetPopulatorInterface;
use Laminas\Form\Fieldset;

class EarliestPermitDateFieldsetPopulator implements FieldsetPopulatorInterface
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
     * @return EarliestPermitDateFieldsetPopulator
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
            '<div class="govuk-inset-text">%s</div><div class="govuk-hint">%s<br>%s</div>',
            $this->translator->translate('qanda.ecmt-short-term.earliest-permit-date.inset'),
            $this->translator->translate('qanda.ecmt-short-term.earliest-permit-date.hint.line-1'),
            $this->translator->translate('qanda.ecmt-short-term.earliest-permit-date.hint.line-2')
        );

        $this->htmlAdder->add($fieldset, 'insetAndHint', $markup);

        $fieldset->add(
            [
                'name' => 'qaElement',
                'type' => DateSelect::class,
                'options' => [
                    'invalidDateKey' => 'qanda.ecmt-short-term.earliest-permit-date.error.date-invalid',
                    'dateInPastKey' => 'qanda.ecmt-short-term.earliest-permit-date.error.date-in-past',
                ],
                'attributes' => [
                    'value' => $options['value']
                ]
            ]
        );
    }
}
