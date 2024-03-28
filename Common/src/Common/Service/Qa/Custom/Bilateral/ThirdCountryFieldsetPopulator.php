<?php

namespace Common\Service\Qa\Custom\Bilateral;

use Common\Service\Helper\TranslationHelperService;
use Common\Service\Qa\FieldsetPopulatorInterface;
use Laminas\Form\Fieldset;

class ThirdCountryFieldsetPopulator implements FieldsetPopulatorInterface
{
    /** @var TranslationHelperService */
    private $translator;

    /** @var YesNoWithMarkupForNoPopulator */
    private $yesNoWithMarkupForNoPopulator;

    /** @var StandardYesNoValueOptionsGenerator */
    private $standardYesNoValueOptionsGenerator;

    /**
     * Create service instance
     *
     *
     * @return ThirdCountryFieldsetPopulator
     */
    public function __construct(
        TranslationHelperService $translator,
        YesNoWithMarkupForNoPopulator $yesNoWithMarkupForNoPopulator,
        StandardYesNoValueOptionsGenerator $standardYesNoValueOptionsGenerator
    ) {
        $this->translator = $translator;
        $this->yesNoWithMarkupForNoPopulator = $yesNoWithMarkupForNoPopulator;
        $this->standardYesNoValueOptionsGenerator = $standardYesNoValueOptionsGenerator;
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
