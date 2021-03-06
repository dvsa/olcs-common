<?php

namespace Common\Service\Qa\Custom\Bilateral;

use Common\Service\Qa\Custom\Common\HtmlAdder;
use Common\Service\Qa\RadioFactory;
use Laminas\Form\Fieldset;

class YesNoWithMarkupForNoPopulator
{
    /** @var RadioFactory */
    private $radioFactory;

    /** @var YesNoRadioOptionsApplier */
    private $yesNoRadioOptionsApplier;

    /** @var HtmlAdder */
    private $htmlAdder;

    /**
     * Create service instance
     *
     * @param RadioFactory $radioFactory
     * @param YesNoRadioOptionsApplier $yesNoRadioOptionsApplier
     * @param HtmlAdder $htmlAdder
     *
     * @return YesNoWithMarkupForNoPopulator
     */
    public function __construct(
        RadioFactory $radioFactory,
        YesNoRadioOptionsApplier $yesNoRadioOptionsApplier,
        HtmlAdder $htmlAdder
    ) {
        $this->radioFactory = $radioFactory;
        $this->yesNoRadioOptionsApplier = $yesNoRadioOptionsApplier;
        $this->htmlAdder = $htmlAdder;
    }

    /**
     * Populate the fieldset with yes/no radio buttons, with the yes option being active in accordance with the yesNo
     * parameter, the no option being annotated with the specified markup, and notSelectedMessage being used as the
     * error if the form is submitted with neither option selected
     *
     * @param Fieldset $fieldset
     * @param array $valueOptions
     * @param string $noMarkup
     * @param mixed $yesNo
     * @param string $notSelectedMessage
     */
    public function populate(Fieldset $fieldset, array $valueOptions, $noMarkup, $yesNo, $notSelectedMessage)
    {
        $yesNoRadio = $this->radioFactory->create('qaElement');
        $yesNoValue = is_null($yesNo) ? null : 'Y';

        $this->yesNoRadioOptionsApplier->applyTo($yesNoRadio, $valueOptions, $yesNoValue, $notSelectedMessage);

        $fieldset->add($yesNoRadio);

        $this->htmlAdder->add(
            $fieldset,
            'noContent',
            sprintf('<div class="govuk-hint">%s</div>', $noMarkup)
        );

        $fieldset->setOption('radio-element', 'qaElement');
    }
}
