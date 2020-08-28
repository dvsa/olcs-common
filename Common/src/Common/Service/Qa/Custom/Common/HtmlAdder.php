<?php

namespace Common\Service\Qa\Custom\Common;

use Common\Form\Elements\Types\Html;
use Zend\Form\Fieldset;

class HtmlAdder
{
    /**
     * Populate the fieldset with a HTML element containing the specified markup
     *
     * @param Fieldset $fieldset
     * @param string $name
     * @param string $markup
     */
    public function add(Fieldset $fieldset, $name, $markup)
    {
        $fieldset->add(
            [
                'name' => $name,
                'type' => Html::class,
                'attributes' => [
                    'value' => $markup
                ]
            ]
        );
    }
}
