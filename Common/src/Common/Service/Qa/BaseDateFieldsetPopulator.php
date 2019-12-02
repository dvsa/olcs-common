<?php

namespace Common\Service\Qa;

use Zend\Form\Fieldset;

class BaseDateFieldsetPopulator
{
    /**
     * Populate the fieldset with the specified element based on the supplied options array
     *
     * @param Fieldset $fieldset
     * @param string $elementClass
     * @param array $elementOptions
     * @param string|null $elementValue
     */
    public function populate(Fieldset $fieldset, $elementClass, array $elementOptions, $elementValue)
    {
        $fieldset->add(
            [
                'name' => 'qaElement',
                'type' => $elementClass,
                'options' => $elementOptions,
                'attributes' => [
                    'value' => $elementValue
                ]
            ]
        );
    }
}
