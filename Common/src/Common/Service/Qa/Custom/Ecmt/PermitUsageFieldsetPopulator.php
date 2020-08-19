<?php

namespace Common\Service\Qa\Custom\Ecmt;

use Common\Form\Elements\Types\Html;
use Common\Service\Helper\TranslationHelperService;
use Common\Service\Qa\FieldsetPopulatorInterface;
use Common\Service\Qa\RadioFieldsetPopulator;
use Zend\Form\Fieldset;

class PermitUsageFieldsetPopulator implements FieldsetPopulatorInterface
{
    /** @var RadioFieldsetPopulator */
    private $radioFieldsetPopulator;

    /** @var TranslationHelperService */
    private $translator;

    /**
     * Create service instance
     *
     * @param RadioFieldsetPopulator $radioFieldsetPopulator
     * @param TranslationHelperService $translator
     *
     * @return PermitUsageFieldsetPopulator
     */
    public function __construct(
        RadioFieldsetPopulator $radioFieldsetPopulator,
        TranslationHelperService $translator
    ) {
        $this->radioFieldsetPopulator = $radioFieldsetPopulator;
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
        $this->radioFieldsetPopulator->populate($form, $fieldset, $options);

        $markup = sprintf(
            '<p class="govuk-!-padding-top-7"><strong>%s</strong></p>',
            $this->translator->translate('qanda.ecmt.permit-usage.footer-annotation')
        );

        $fieldset->add(
            [
                'name' => 'footerAnnotation',
                'type' => Html::class,
                'attributes' => [
                    'value' => $markup
                ]
            ]
        );
    }
}
