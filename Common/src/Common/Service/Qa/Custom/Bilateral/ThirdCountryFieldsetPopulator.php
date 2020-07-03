<?php

namespace Common\Service\Qa\Custom\Bilateral;

use Common\Form\Elements\Types\Html;
use Common\Service\Helper\TranslationHelperService;
use Common\Service\Qa\FieldsetPopulatorInterface;
use Common\Service\Qa\RadioFactory;
use Zend\Form\Fieldset;

class ThirdCountryFieldsetPopulator implements FieldsetPopulatorInterface
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
     * @return ThirdCountryFieldsetPopulator
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
        $yesNoValue = is_null($options['yesNo']) ? null : 'Y';

        $this->yesNoRadioOptionsApplier->applyTo(
            $yesNoRadio,
            $yesNoValue,
            'qanda.bilaterals.third-country.not-selected-message'
        );

        $noMarkup = sprintf(
            '<div class="govuk-hint">%s</div>',
            $this->translator->translate('qanda.bilaterals.third-country.no-blurb')
        );

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
