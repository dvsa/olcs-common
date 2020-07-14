<?php

namespace Common\Service\Qa\Custom\Bilateral;

use Common\Form\Elements\Types\Radio;

class YesNoRadioOptionsApplier
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
            'value' => 'Y',
        ],
        'no' => [
            'label' => 'No',
            'value' => 'N',
        ]
    ];

    /**
     * Set the required options and attributes against the specified element
     *
     * @param Radio $radio
     * @param mixed $value
     * @param string $notSelectedMessage
     */
    public function applyTo(Radio $radio, $value, $notSelectedMessage)
    {
        $radio->setValueOptions($this->standardValueOptions);
        $radio->setAttributes($this->attributes);
        $radio->setValue($value);
        $radio->setOption('not_selected_message', $notSelectedMessage);
    }
}
