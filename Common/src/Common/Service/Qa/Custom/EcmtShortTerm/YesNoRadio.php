<?php

namespace Common\Service\Qa\Custom\EcmtShortTerm;

use Common\Form\Elements\Types\Radio;

class YesNoRadio extends Radio
{
    /** @var array */
    protected $attributes = [
        'radios_wrapper_attributes' => [
            'class' => 'govuk-radios--conditional',
            'data-module' => 'radios',
        ]
    ];

    /** @var array */
    protected $standardValueOptions = [
        'yes' => [
            'label' => 'Yes',
            'value' => 1,
            'attributes' => [
                'data-aria-controls' => 'RestrictedCountriesList',
            ],
        ],
        'no' => [
            'label' => 'No',
            'value' => 0,
        ]
    ];

    /**
     * Set the standard value options for this type
     */
    public function setStandardValueOptions()
    {
        $this->setValueOptions($this->standardValueOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function getInputSpecification()
    {
        $spec = parent::getInputSpecification();

        $spec['validators'] = [
            new YesNoRadioValidator(
                $this->options['yesContentElement']
            )
        ];

        return $spec;
    }
}
