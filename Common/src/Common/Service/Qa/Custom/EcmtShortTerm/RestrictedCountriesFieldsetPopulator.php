<?php

namespace Common\Service\Qa\Custom\EcmtShortTerm;

use Common\Service\Qa\FieldsetPopulatorInterface;
use Zend\Form\Fieldset;

class RestrictedCountriesFieldsetPopulator implements FieldsetPopulatorInterface
{
    /** @var YesNoRadioFactory */
    private $yesNoRadioFactory;

    /** @var RestrictedCountriesMultiCheckboxFactory */
    private $restrictedCountriesMultiCheckboxFactory;

    /**
     * Create service instance
     *
     * @param YesNoRadioFactory $yesNoRadioFactory
     * @param RestrictedCountriesMultiCheckboxFactory $restrictedCountriesMultiCheckboxFactory
     *
     * @return RestrictedCountriesFieldsetPopulator
     */
    public function __construct(
        YesNoRadioFactory $yesNoRadioFactory,
        RestrictedCountriesMultiCheckboxFactory $restrictedCountriesMultiCheckboxFactory
    ) {
        $this->yesNoRadioFactory = $yesNoRadioFactory;
        $this->restrictedCountriesMultiCheckboxFactory = $restrictedCountriesMultiCheckboxFactory;
    }

    /**
     * Populate the fieldset with elements based on the supplied options array
     *
     * @param mixed $form
     * @param Fieldset $fieldset
     * @param array $options
     */
    public function populate($form, Fieldset $fieldset, array $options)
    {
        $yesNoRadio = $this->yesNoRadioFactory->create('restrictedCountries');
        $yesNoRadio->setStandardValueOptions();

        $optionsYesNo = $options['yesNo'];
        $yesNoRadio->setValue(is_null($optionsYesNo) ? null : $optionsYesNo === true);

        $valueOptions = [];
        foreach ($options['countries'] as $country) {
            $valueOptions[] = [
                'value' => $country['code'],
                'label' => $country['labelTranslationKey'],
                'selected' => $country['checked'],
            ];
        }

        $restrictedCountries = $this->restrictedCountriesMultiCheckboxFactory->create('yesContent');
        $restrictedCountries->setValueOptions($valueOptions);
        $yesNoRadio->setOption('yesContentElement', $restrictedCountries);

        $fieldset->add($yesNoRadio);
        $fieldset->add($restrictedCountries);
        $fieldset->setOption('radio-element', 'restrictedCountries');
    }
}
