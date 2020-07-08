<?php

namespace Common\Service\Qa\Custom\Bilateral;

use Common\Service\Qa\FieldsetPopulatorInterface;
use Zend\Form\Fieldset;

class StandardAndCabotageFieldsetPopulator implements FieldsetPopulatorInterface
{
    const ANSWER_CABOTAGE_ONLY = 'qanda.bilaterals.cabotage.answer.cabotage-only';
    const ANSWER_STANDARD_AND_CABOTAGE = 'qanda.bilaterals.cabotage.answer.standard-and-cabotage';
    const ANSWER_STANDARD_ONLY = 'qanda.bilaterals.cabotage.answer.standard-only';

    /** @var RadioFactory */
    private $radioFactory;

    /** @var StandardAndCabotageYesNoRadioFactory */
    private $standardAndCabotageYesNoRadioFactory;

    /** @var YesNoRadioOptionsApplier */
    private $yesNoRadioOptionsApplier;

    /**
     * Create service instance
     *
     * @param RadioFactory $radioFactory
     * @param StandardAndCabotageYesNoRadioFactory $standardAndCabotageYesNoRadioFactory
     * @param YesNoRadioOptionsApplier $yesNoRadioOptionsApplier
     *
     * @return StandardAndCabotageFieldsetPopulator
     */
    public function __construct(
        RadioFactory $radioFactory,
        StandardAndCabotageYesNoRadioFactory $standardAndCabotageYesNoRadioFactory,
        YesNoRadioOptionsApplier $yesNoRadioOptionsApplier
    ) {
        $this->radioFactory = $radioFactory;
        $this->standardAndCabotageYesNoRadioFactory = $standardAndCabotageYesNoRadioFactory;
        $this->yesNoRadioOptionsApplier = $yesNoRadioOptionsApplier;
    }

    /**
     * {@inheritdoc}
     */
    public function populate($form, Fieldset $fieldset, array $options)
    {
        $cabotageValueOptions = [
            self::ANSWER_CABOTAGE_ONLY => self::ANSWER_CABOTAGE_ONLY,
            self::ANSWER_STANDARD_AND_CABOTAGE => self::ANSWER_STANDARD_AND_CABOTAGE
        ];

        $yesNoRadio = $this->standardAndCabotageYesNoRadioFactory->create('qaElement');
        $this->yesNoRadioOptionsApplier->applyTo($yesNoRadio);

        $cabotageOptions = $this->radioFactory->create('yesContent');
        $cabotageOptions->setValueOptions($cabotageValueOptions);
        $yesNoRadio->setOption('yesContentElement', $cabotageOptions);

        $optionsValue = $options['value'];
        if (!is_null($optionsValue)) {
            $yesNoValue = 'N';

            if ($optionsValue != self::ANSWER_STANDARD_ONLY) {
                $yesNoValue = 'Y';
                $cabotageOptions->setValue($optionsValue);
            }

            $yesNoRadio->setValue($yesNoValue);
        }

        $fieldset->add($yesNoRadio);
        $fieldset->add($cabotageOptions);

        $fieldset->setOption('radio-element', 'qaElement');
    }
}
