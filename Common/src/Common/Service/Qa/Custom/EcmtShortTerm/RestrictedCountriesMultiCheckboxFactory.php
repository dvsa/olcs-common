<?php

namespace Common\Service\Qa\Custom\EcmtShortTerm;

use Zend\Form\Element\MultiCheckbox;

class RestrictedCountriesMultiCheckboxFactory
{
    /**
     * Create a MultiCheckbox element instance with attributes and options required to display as restricted countries
     *
     * @param string $name
     *
     * @return MultiCheckbox
     */
    public function create($name)
    {
        $restrictedCountries = new MultiCheckbox($name);

        $restrictedCountries->setOptions(
            [
                'label' => 'markup-ecmt-restricted-countries-list-label',
                'label_attributes' => [
                    'class' => 'form-control form-control--checkbox'
                ]
            ]
        );

        $restrictedCountries->setAttributes(
            [
                'class' => 'input--trips',
                'id' => 'RestrictedCountriesList',
                'allowWrap' => true,
                'data-container-class' => 'form-control__container',
                'aria-label' => 'permits.page.restricted-countries.hint'
            ]
        );

        return $restrictedCountries;
    }
}
