<?php

namespace Common\Service\Qa\Custom\Ecmt;

use Common\Service\Helper\TranslationHelperService;
use Common\Service\Qa\Custom\Common\HtmlAdder;
use Laminas\Form\Fieldset;

class InfoIconAdder
{
    /** @var TranslationHelperService */
    private $translator;

    /** @var HtmlAdder */
    private $htmlAdder;

    /**
     * Create service instance
     *
     *
     * @return InfoIconAdder
     */
    public function __construct(TranslationHelperService $translator, HtmlAdder $htmlAdder)
    {
        $this->translator = $translator;
        $this->htmlAdder = $htmlAdder;
    }

    /**
     * Add a blue info icon and the text represented by the supplied translation key to the specified fieldset
     *
     * @param string $translationKey
     */
    public function add(Fieldset $fieldset, $translationKey): void
    {
        $markup = sprintf(
            '<p class="govuk-!-margin-top-7 info-box__icon-wrapper info-box__text">' .
            '<i class="info-box__icon selfserve-important"></i>%s</p>',
            $this->translator->translate($translationKey)
        );

        $this->htmlAdder->add($fieldset, 'infoIcon', $markup);
    }
}
