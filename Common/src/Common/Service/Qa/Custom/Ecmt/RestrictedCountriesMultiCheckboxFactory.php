<?php

namespace Common\Service\Qa\Custom\Ecmt;

class RestrictedCountriesMultiCheckboxFactory
{
    /**
     * Create a MultiCheckbox element instance with attributes and options required to display as restricted countries
     *
     * @param string $name
     *
     * @return RestrictedCountriesMultiCheckbox
     */
    public function create($name)
    {
        $restrictedCountries = new RestrictedCountriesMultiCheckbox($name);

        $restrictedCountries->setOptions(
            [
                'label' => 'markup-ecmt-restricted-countries-list-label',
                'label_attributes' => [
                    'class' => 'govuk-label govuk-checkboxes__label'
                ]
            ]
        );

        $restrictedCountries->setAttributes(
            [
                'class' => 'input--trips govuk-checkboxes__input',
                'id' => 'RestrictedCountriesList',
                'allowWrap' => true,
                'data-container-class' => 'form-control__container',
                'aria-label' => 'permits.page.restricted-countries.hint'
            ]
        );

        return $restrictedCountries;
    }
}
