<?php

namespace Common\Service\Qa\Custom\EcmtRemoval;

use Common\Form\Elements\Types\Html;
use Common\Service\Helper\TranslationHelperService;
use Common\Service\Qa\FieldsetPopulatorInterface;
use Common\Service\Qa\BaseDateFieldsetPopulator;
use Zend\Form\Fieldset;

class PermitStartDateFieldsetPopulator implements FieldsetPopulatorInterface
{
    /** @var BaseDateFieldsetPopulator */
    private $baseDateFieldsetPopulator;

    /** @var TranslationHelperService */
    private $translator;

    /**
     * Create service instance
     *
     * @param BaseDateFieldsetPopulator $baseDateFieldsetPopulator
     * @param TranslationHelperService $translator
     *
     * @return PermitStartDateFieldsetPopulator
     */
    public function __construct(
        BaseDateFieldsetPopulator $baseDateFieldsetPopulator,
        TranslationHelperService $translator
    ) {
        $this->baseDateFieldsetPopulator = $baseDateFieldsetPopulator;
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
            '<div class="govuk-hint">%s<br>%s</div>',
            $this->translator->translate('qanda.ecmt-removal.permit-start-date.hint.line-1'),
            $this->translator->translate('qanda.ecmt-removal.permit-start-date.hint.line-2')
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

        $this->baseDateFieldsetPopulator->populate(
            $fieldset,
            DateSelect::class,
            [
                'dateMustBeBefore' => $options['dateMustBeBefore']
            ],
            $options['date']['value']
        );
    }
}
