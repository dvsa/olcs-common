<?php

namespace Common\Form\Elements\InputFilters;

use Zend\Form\Element\Radio;
use Zend\Validator\InArray;

/**
 * QaRadio element
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class QaRadio extends Radio
{
    /**
     * Provide default input rules for radio element.
     *
     * @return array
     */
    public function getInputSpecification()
    {
        return [
            'name' => $this->getName(),
            'continue_if_empty' => true,
            'required' => false,
            'validators' => [
                [
                    'name' => InArray::class,
                    'options' => [
                        'haystack' => $this->getValueOptionsValues(),
                        'strict' => true,
                        'messages' => [
                            InArray::NOT_IN_ARRAY => $this->options['not_selected_message']
                        ]
                    ]
                ]
            ]
        ];
    }
}
