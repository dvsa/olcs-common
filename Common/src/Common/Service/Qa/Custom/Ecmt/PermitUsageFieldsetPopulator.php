<?php

namespace Common\Service\Qa\Custom\Ecmt;

use Common\Service\Helper\TranslationHelperService;
use Common\Service\Qa\Custom\Common\HtmlAdder;
use Common\Service\Qa\FieldsetPopulatorInterface;
use Common\Service\Qa\RadioFieldsetPopulator;
use Zend\Form\Fieldset;

class PermitUsageFieldsetPopulator implements FieldsetPopulatorInterface
{
    /** @var RadioFieldsetPopulator */
    private $radioFieldsetPopulator;

    /** @var TranslationHelperService */
    private $translator;

    /** @var HtmlAdder */
    private $htmlAdder;

    /**
     * Create service instance
     *
     * @param RadioFieldsetPopulator $radioFieldsetPopulator
     * @param TranslationHelperService $translator
     * @param HtmlAdder $htmlAdder
     *
     * @return PermitUsageFieldsetPopulator
     */
    public function __construct(
        RadioFieldsetPopulator $radioFieldsetPopulator,
        TranslationHelperService $translator,
        HtmlAdder $htmlAdder
    ) {
        $this->radioFieldsetPopulator = $radioFieldsetPopulator;
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
        $this->radioFieldsetPopulator->populate($form, $fieldset, $options);

        $markup = sprintf(
            '<p class="govuk-!-margin-top-7 info-box__icon-wrapper info-box__text">' .
            '<i class="info-box__icon selfserve-important"></i>%s</p>',
            $this->translator->translate('qanda.ecmt.permit-usage.footer-annotation')
        );

        $this->htmlAdder->add($fieldset, 'footerAnnotation', $markup);
    }
}
