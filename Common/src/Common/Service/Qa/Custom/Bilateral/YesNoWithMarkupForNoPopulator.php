<?php

namespace Common\Service\Qa\Custom\Bilateral;

use Common\Form\Elements\Types\Html;
use Common\Service\Qa\RadioFactory;
use Zend\Form\Fieldset;

class YesNoWithMarkupForNoPopulator
{
    /** @var RadioFactory */
    private $radioFactory;

    /** @var YesNoRadioOptionsApplier */
    private $yesNoRadioOptionsApplier;

    /**
     * Create service instance
     *
     * @param RadioFactory $radioFactory
     * @param YesNoRadioOptionsApplier $yesNoRadioOptionsApplier
     *
     * @return YesNoWithMarkupForNoPopulator
     */
    public function __construct(RadioFactory $radioFactory, YesNoRadioOptionsApplier $yesNoRadioOptionsApplier)
    {
        $this->radioFactory = $radioFactory;
        $this->yesNoRadioOptionsApplier = $yesNoRadioOptionsApplier;
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

        $fieldset->add(
            [
                'name' => 'noContent',
                'type' => Html::class,
                'attributes' => [
                    'value' => sprintf('<div class="govuk-hint">%s</div>', $noMarkup)
                ]
            ]
        );

        $fieldset->setOption('radio-element', 'qaElement');
    }
}
