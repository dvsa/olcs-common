<?php

namespace Common\Service\Qa\Custom\Bilateral;

use Common\Form\Elements\Types\Html;
use Common\Service\Helper\TranslationHelperService;
use Common\Service\Qa\FieldsetPopulatorInterface;
use Common\Service\Qa\RadioFactory;
use Zend\Form\Fieldset;

class CabotageOnlyFieldsetPopulator implements FieldsetPopulatorInterface
{
    /** @var TranslationHelperService */
    private $translator;

    /** @var RadioFactory */
    private $radioFactory;

    /** @var YesNoRadioOptionsApplier */
    private $yesNoRadioOptionsApplier;

    /**
     * Create service instance
     *
     * @param TranslationHelperService $translator
     * @param RadioFactory $radioFactory
     * @param YesNoRadioOptionsApplier $yesNoRadioOptionsApplier
     *
     * @return CabotageOnlyFieldsetPopulator
     */
    public function __construct(
        TranslationHelperService $translator,
        RadioFactory $radioFactory,
        YesNoRadioOptionsApplier $yesNoRadioOptionsApplier
    ) {
        $this->translator = $translator;
        $this->radioFactory = $radioFactory;
        $this->yesNoRadioOptionsApplier = $yesNoRadioOptionsApplier;
    }

    /**
     * {@inheritdoc}
     */
    public function populate($form, Fieldset $fieldset, array $options)
    {
        $yesNoRadio = $this->radioFactory->create('qaElement');
        $this->yesNoRadioOptionsApplier->applyTo($yesNoRadio);

        $optionsValue = $options['yesNo'];
        $yesNoRadio->setValue(is_null($optionsValue) ? null : 'Y');

        $noCaption = sprintf(
            $this->translator->translate('qanda.bilaterals.cabotage-only.no-blurb'),
            $this->translator->translate($options['countryName'])
        );

        $noMarkup = sprintf('<div class="govuk-hint">%s</div>', $noCaption);

        $fieldset->add($yesNoRadio);

        $fieldset->add(
            [
                'name' => 'noContent',
                'type' => Html::class,
                'attributes' => [
                    'value' => $noMarkup
                ]
            ]
        );

        $fieldset->setOption('radio-element', 'qaElement');
    }
}
