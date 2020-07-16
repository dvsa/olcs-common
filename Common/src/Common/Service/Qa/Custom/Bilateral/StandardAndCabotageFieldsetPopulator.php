<?php

namespace Common\Service\Qa\Custom\Bilateral;

use Common\Service\Qa\FieldsetPopulatorInterface;
use Zend\Form\Fieldset;

class StandardAndCabotageFieldsetPopulator implements FieldsetPopulatorInterface
{
    const ANSWER_CABOTAGE_ONLY = 'qanda.bilaterals.cabotage.answer.cabotage-only';
    const ANSWER_STANDARD_AND_CABOTAGE = 'qanda.bilaterals.cabotage.answer.standard-and-cabotage';
    const ANSWER_STANDARD_ONLY = 'qanda.bilaterals.cabotage.answer.standard-only';

    const CABOTAGE_VALUE_OPTIONS = [
        self::ANSWER_CABOTAGE_ONLY => self::ANSWER_CABOTAGE_ONLY,
        self::ANSWER_STANDARD_AND_CABOTAGE => self::ANSWER_STANDARD_AND_CABOTAGE
    ];

    /** @var RadioFactory */
    private $radioFactory;

    /** @var StandardAndCabotageYesNoRadioFactory */
    private $standardAndCabotageYesNoRadioFactory;

    /** @var YesNoRadioOptionsApplier */
    private $yesNoRadioOptionsApplier;

    /** @var StandardYesNoValueOptionsGenerator */
    private $standardYesNoValueOptionsGenerator;

    /**
     * Create service instance
     *
     * @param RadioFactory $radioFactory
     * @param StandardAndCabotageYesNoRadioFactory $standardAndCabotageYesNoRadioFactory
     * @param YesNoRadioOptionsApplier $yesNoRadioOptionsApplier
     * @param StandardYesNoValueOptionsGenerator $standardYesNoValueOptionsGenerator
     *
     * @return StandardAndCabotageFieldsetPopulator
     */
    public function __construct(
        RadioFactory $radioFactory,
        StandardAndCabotageYesNoRadioFactory $standardAndCabotageYesNoRadioFactory,
        YesNoRadioOptionsApplier $yesNoRadioOptionsApplier,
        StandardYesNoValueOptionsGenerator $standardYesNoValueOptionsGenerator
    ) {
        $this->radioFactory = $radioFactory;
        $this->standardAndCabotageYesNoRadioFactory = $standardAndCabotageYesNoRadioFactory;
        $this->yesNoRadioOptionsApplier = $yesNoRadioOptionsApplier;
        $this->standardYesNoValueOptionsGenerator = $standardYesNoValueOptionsGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public function populate($form, Fieldset $fieldset, array $options)
    {
        $cabotageOptions = $this->radioFactory->create('yesContent');
        $cabotageOptions->setValueOptions(self::CABOTAGE_VALUE_OPTIONS);

        $yesNoRadio = $this->standardAndCabotageYesNoRadioFactory->create('qaElement');
        $yesNoRadio->setOption('yesContentElement', $cabotageOptions);

        $optionsValue = $options['value'];
        $yesNoValue = null;
        if (!is_null($optionsValue)) {
            $yesNoValue = 'N';

            if ($optionsValue != self::ANSWER_STANDARD_ONLY) {
                $yesNoValue = 'Y';
                $cabotageOptions->setValue($optionsValue);
            }
        }

        $valueOptions = $this->standardYesNoValueOptionsGenerator->generate();

        $this->yesNoRadioOptionsApplier->applyTo(
            $yesNoRadio,
            $valueOptions,
            $yesNoValue,
            'qanda.bilaterals.cabotage.not-selected-message'
        );

        $fieldset->add($yesNoRadio);
        $fieldset->add($cabotageOptions);

        $fieldset->setOption('radio-element', 'qaElement');
    }
}
