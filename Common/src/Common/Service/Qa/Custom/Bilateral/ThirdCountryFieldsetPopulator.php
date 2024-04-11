<?php

namespace Common\Service\Qa\Custom\Bilateral;

use Common\Service\Helper\TranslationHelperService;
use Common\Service\Qa\FieldsetPopulatorInterface;
use Laminas\Form\Fieldset;

class ThirdCountryFieldsetPopulator implements FieldsetPopulatorInterface
{
    /**
     * Create service instance
     *
     *
     * @return ThirdCountryFieldsetPopulator
     */
    public function __construct(private TranslationHelperService $translator, private YesNoWithMarkupForNoPopulator $yesNoWithMarkupForNoPopulator, private StandardYesNoValueOptionsGenerator $standardYesNoValueOptionsGenerator)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function populate($form, Fieldset $fieldset, array $options): void
    {
        $valueOptions = $this->standardYesNoValueOptionsGenerator->generate();

        $noMarkup = $this->translator->translate('qanda.bilaterals.third-country.no-blurb');

        $this->yesNoWithMarkupForNoPopulator->populate(
            $fieldset,
            $valueOptions,
            $noMarkup,
            $options['yesNo'],
            'qanda.bilaterals.third-country.not-selected-message'
        );
    }
}
