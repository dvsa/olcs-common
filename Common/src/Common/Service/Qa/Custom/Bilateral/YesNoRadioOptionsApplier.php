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

    /**
     * Set the required options and attributes against the specified element
     *
     * @param Radio $radio
     * @param array $valueOptions
     * @param mixed $value
     * @param string $notSelectedMessage
     */
    public function applyTo(Radio $radio, array $valueOptions, $value, $notSelectedMessage)
    {
        $radio->setValueOptions($valueOptions);
        $radio->setAttributes($this->attributes);
        $radio->setValue($value);
        $radio->setOption('not_selected_message', $notSelectedMessage);
    }
}
