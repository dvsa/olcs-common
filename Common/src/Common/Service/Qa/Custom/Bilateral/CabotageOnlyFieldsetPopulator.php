<?php

namespace Common\Service\Qa\Custom\Bilateral;

use Common\Service\Helper\TranslationHelperService;
use Common\Service\Qa\FieldsetPopulatorInterface;
use Zend\Form\Fieldset;

class CabotageOnlyFieldsetPopulator implements FieldsetPopulatorInterface
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
     * @param TranslationHelperService $translator
     * @param YesNoWithMarkupForNoPopulator $yesNoWithMarkupForNoPopulator
     * @param StandardYesNoValueOptionsGenerator $standardYesNoValueOptionsGenerator
     *
     * @return CabotageOnlyFieldsetPopulator
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
    public function populate($form, Fieldset $fieldset, array $options)
    {
        $valueOptions = $this->standardYesNoValueOptionsGenerator->generate();

        $noMarkup = sprintf(
            $this->translator->translate('qanda.bilaterals.cabotage-only.no-blurb'),
            $this->translator->translate($options['countryName'])
        );

        $this->yesNoWithMarkupForNoPopulator->populate(
            $fieldset,
            $valueOptions,
            $noMarkup,
            $options['yesNo'],
            'qanda.bilaterals.cabotage.not-selected-message'
        );
    }
}
